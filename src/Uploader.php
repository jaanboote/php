<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Gestiona la pujada de temari: fitxers solts i ZIP.
 *
 * Seguretat:
 *  - Només s'accepten les extensions de la llista blanca.
 *  - Els noms de fitxer es netegen (res de "../", rutes absolutes ni lletres d'unitat).
 *  - Els ZIP es comproven entrada per entrada contra el "zip slip" i les "zip bomb".
 *  - Tot es guarda a data_dir/temari, fora del webroot: no es pot executar des del navegador.
 *  - Si un fitxer amb el mateix contingut (mateix hash) ja existeix, no es duplica.
 */
final class Uploader
{
    private array $report = ['guardats' => [], 'duplicats' => [], 'rebutjats' => []];
    private array $knownHashes = [];
    private int $unzippedBytes = 0;

    public static function handle(array $files): array
    {
        $u = new self();
        foreach (Db::all('SELECT hash FROM files') as $r) {
            $u->knownHashes[$r['hash']] = true;
        }
        foreach (self::normalizeFilesArray($files) as $f) {
            $u->handleOne($f);
        }
        return $u->report;
    }

    /** $_FILES amb "multiple" té un format estrany: el convertim en una llista de fitxers. */
    private static function normalizeFilesArray(array $f): array
    {
        if (!isset($f['name'])) {
            return [];
        }
        if (!is_array($f['name'])) {
            return [$f];
        }
        $list = [];
        foreach ($f['name'] as $i => $name) {
            $list[] = ['name' => $name, 'tmp_name' => $f['tmp_name'][$i], 'error' => $f['error'][$i], 'size' => $f['size'][$i]];
        }
        return $list;
    }

    private function handleOne(array $f): void
    {
        $name = (string) $f['name'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $msg = match ($f['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'és massa gran (revisa upload_max_filesize i post_max_size al php.ini)',
                UPLOAD_ERR_NO_FILE => 'no s\'ha triat cap fitxer',
                default => 'error de pujada (codi ' . $f['error'] . ')',
            };
            $this->reject($name, $msg);
            return;
        }
        if (!is_uploaded_file($f['tmp_name'])) {
            $this->reject($name, 'pujada no vàlida');
            return;
        }
        if ($f['size'] > (int) Config::get('upload.max_file_mb', 50) * 1024 * 1024) {
            $this->reject($name, 'supera la mida màxima');
            return;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if ($ext === 'zip') {
            $this->extractZip($f['tmp_name'], pathinfo(self::cleanSegment($name), PATHINFO_FILENAME), 0);
            return;
        }
        if (!in_array($ext, TextExtractor::EXTENSIONS, true)) {
            $this->reject($name, 'tipus de fitxer no permès');
            return;
        }
        $this->store($f['tmp_name'], self::cleanSegment($name), $name, true);
    }

    /** Guarda un fitxer a temari/$relPath si no és un duplicat. */
    private function store(string $source, string $relPath, string $displayName, bool $isUpload): void
    {
        $hash = hash_file('sha256', $source);
        if (isset($this->knownHashes[$hash])) {
            $this->report['duplicats'][] = $displayName;
            return;
        }
        $target = self::uniquePath(Indexer::temariDir() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relPath));
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0770, true);
        }
        $ok = $isUpload ? move_uploaded_file($source, $target) : rename($source, $target);
        if (!$ok) {
            $this->reject($displayName, 'no s\'ha pogut guardar');
            return;
        }
        $this->knownHashes[$hash] = true;
        $this->report['guardats'][] = $relPath;
    }

    private function extractZip(string $zipPath, string $folder, int $depth): void
    {
        if (!class_exists(\ZipArchive::class)) {
            $this->reject($folder . '.zip', 'cal activar extension=zip al php.ini de XAMPP');
            return;
        }
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->reject($folder . '.zip', 'ZIP malmès o no vàlid');
            return;
        }
        if ($zip->numFiles > (int) Config::get('upload.max_zip_entries', 500)) {
            $this->reject($folder . '.zip', 'té massa fitxers');
            $zip->close();
            return;
        }
        $maxBytes = (int) Config::get('upload.max_unzipped_mb', 200) * 1024 * 1024;
        $tmpDir = Config::dataPath('tmp');

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $entry = (string) $stat['name'];
            if (!mb_check_encoding($entry, 'UTF-8')) {
                $entry = mb_convert_encoding($entry, 'UTF-8', 'CP850'); // ZIP creats amb Windows antics
            }
            if (str_ends_with($entry, '/')) {
                continue; // carpeta
            }
            $safe = self::safeZipPath($entry);
            if ($safe === null) {
                $this->reject($entry, 'ruta perillosa dins del ZIP (zip slip)');
                continue;
            }
            $base = basename($safe);
            if (str_starts_with($base, '.') || str_starts_with($safe, '__MACOSX/')) {
                continue;
            }
            $ext = strtolower(pathinfo($safe, PATHINFO_EXTENSION));
            $isZip = $ext === 'zip';
            if (!$isZip && !in_array($ext, TextExtractor::EXTENSIONS, true)) {
                $this->reject($entry, 'tipus de fitxer no permès');
                continue;
            }
            if ($this->unzippedBytes + $stat['size'] > $maxBytes) {
                $this->reject($entry, 'el ZIP descomprimit és massa gran');
                break;
            }

            // Copiem en blocs comptant els bytes reals (la mida declarada pot mentir)
            $tmp = $tmpDir . DIRECTORY_SEPARATOR . bin2hex(random_bytes(8));
            $in = $zip->getStream($stat['name']);
            $out = fopen($tmp, 'wb');
            $written = 0;
            $tooBig = false;
            while ($in && !feof($in)) {
                $buf = fread($in, 65536);
                $written += strlen($buf);
                if ($this->unzippedBytes + $written > $maxBytes) {
                    $tooBig = true;
                    break;
                }
                fwrite($out, $buf);
            }
            if ($in) {
                fclose($in);
            }
            fclose($out);
            if ($tooBig) {
                @unlink($tmp);
                $this->reject($entry, 'el ZIP descomprimit és massa gran');
                break;
            }
            $this->unzippedBytes += $written;

            if ($isZip) {
                if ($depth < 1) {
                    $this->extractZip($tmp, $folder . '/' . pathinfo($safe, PATHINFO_FILENAME), $depth + 1);
                } else {
                    $this->reject($entry, 'ZIP dins de ZIP dins de ZIP: no s\'extreu');
                }
                @unlink($tmp);
                continue;
            }
            $this->store($tmp, $folder . '/' . $safe, $entry, false);
            @unlink($tmp);
        }
        $zip->close();
    }

    /**
     * Valida una ruta d'un ZIP. Retorna la ruta neta o null si és perillosa.
     * Zip slip: una entrada com "../../htdocs/shell.php" escriuria fora de la carpeta.
     */
    public static function safeZipPath(string $entry): ?string
    {
        $entry = str_replace('\\', '/', $entry);
        if ($entry === '' || str_contains($entry, "\0") || str_starts_with($entry, '/') || preg_match('/^[a-zA-Z]:/', $entry)) {
            return null;
        }
        $parts = [];
        foreach (explode('/', $entry) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                return null;
            }
            $parts[] = self::cleanSegment($seg);
        }
        return $parts === [] ? null : implode('/', $parts);
    }

    /** Deixa només caràcters segurs en un nom de fitxer o carpeta. */
    public static function cleanSegment(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[^\p{L}\p{N} ._()\-]/u', '_', $name) ?? 'fitxer';
        $name = trim($name, ' .');
        return $name === '' ? 'fitxer' : mb_substr($name, 0, 120);
    }

    private static function uniquePath(string $path): string
    {
        if (!file_exists($path)) {
            return $path;
        }
        $info = pathinfo($path);
        for ($i = 2; ; $i++) {
            $candidate = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . " ($i)." . ($info['extension'] ?? '');
            if (!file_exists($candidate)) {
                return $candidate;
            }
        }
    }

    private function reject(string $name, string $why): void
    {
        $this->report['rebutjats'][] = ['nom' => $name, 'motiu' => $why];
    }
}
