<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Indexador del temari.
 *
 * Recorre data_dir/temari, calcula el hash (sha256) de cada fitxer i només
 * torna a extreure el text dels fitxers nous o modificats. Els fitxers que ja
 * no hi són s'esborren de l'índex.
 */
final class Indexer
{
    public static function temariDir(): string
    {
        return Config::dataPath('temari');
    }

    /**
     * @return array{nous:string[], modificats:string[], esborrats:string[], errors:array, temes_nous:string[], sense_canvis:int}
     */
    public static function reindex(): array
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M'); // els PDF grans necessiten memòria

        $report = ['nous' => [], 'modificats' => [], 'esborrats' => [], 'errors' => [], 'temes_nous' => [], 'sense_canvis' => 0];
        $topicsBefore = array_column(Db::all('SELECT DISTINCT topic_name FROM files'), 'topic_name');

        $base = self::temariDir();
        $known = [];
        foreach (Db::all('SELECT id, path, hash FROM files') as $row) {
            $known[$row['path']] = $row;
        }

        $seen = [];
        foreach (self::listFiles($base) as $abs) {
            $rel = str_replace('\\', '/', substr($abs, strlen($base) + 1));
            $seen[$rel] = true;
            $hash = hash_file('sha256', $abs);

            if (isset($known[$rel]) && $known[$rel]['hash'] === $hash) {
                $report['sense_canvis']++;
                continue;
            }

            $res = TextExtractor::extract($abs);
            [$num, $name] = self::detectTopic($res['text'], $rel);
            $data = [
                'path' => $rel,
                'ext' => strtolower(pathinfo($abs, PATHINFO_EXTENSION)),
                'size' => filesize($abs),
                'hash' => $hash,
                'topic_num' => $num,
                'topic_name' => $name,
                'kind' => self::detectKind($res['text'], $rel),
                'status' => $res['status'],
                'message' => $res['message'],
                'text' => $res['text'],
                'indexed_at' => date('Y-m-d H:i:s'),
            ];

            $pdo = Db::pdo();
            $pdo->beginTransaction();
            if (isset($known[$rel])) {
                $id = (int) $known[$rel]['id'];
                $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
                Db::run("UPDATE files SET $sets WHERE id = :id", $data + ['id' => $id]);
                Db::run('DELETE FROM chunks WHERE file_id = ?', [$id]);
                $report['modificats'][] = $rel;
            } else {
                $cols = implode(', ', array_keys($data));
                $vals = ':' . implode(', :', array_keys($data));
                Db::run("INSERT INTO files ($cols) VALUES ($vals)", $data);
                $id = (int) $pdo->lastInsertId();
                $report['nous'][] = $rel;
            }
            foreach (self::chunk($res['text']) as $i => $chunk) {
                Db::run('INSERT INTO chunks (file_id, idx, text) VALUES (?, ?, ?)', [$id, $i, $chunk]);
            }
            $pdo->commit();

            if ($res['status'] !== 'ok') {
                $report['errors'][] = ['path' => $rel, 'message' => $res['message']];
            }
        }

        foreach ($known as $rel => $row) {
            if (!isset($seen[$rel])) {
                Db::run('DELETE FROM files WHERE id = ?', [$row['id']]);
                $report['esborrats'][] = $rel;
            }
        }

        $topicsAfter = array_column(Db::all('SELECT DISTINCT topic_name FROM files'), 'topic_name');
        $report['temes_nous'] = array_values(array_diff($topicsAfter, $topicsBefore));
        return $report;
    }

    /** Llista recursiva de fitxers amb extensió suportada. */
    private static function listFiles(string $dir): array
    {
        $files = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($it as $f) {
            /** @var \SplFileInfo $f */
            if ($f->isFile() && in_array(strtolower($f->getExtension()), TextExtractor::EXTENSIONS, true)) {
                $files[] = $f->getPathname();
            }
        }
        sort($files);
        return $files;
    }

    /**
     * Endevina el tema. Els PDF del professor porten a la capçalera "Tema 2 – Fonaments PHP".
     * Si no, es mira el nom del fitxer o de la carpeta.
     * @return array{0:?int, 1:string}
     */
    public static function detectTopic(string $text, string $relPath): array
    {
        $head = mb_substr($text, 0, 4000);
        if (preg_match('/Tema\s*(\d{1,2})\s*[–—\-:.]\s*([^\n]{3,60})/u', $head, $m)) {
            $name = trim(preg_replace('/\s+/', ' ', $m[2]));
            return [(int) $m[1], 'Tema ' . (int) $m[1] . ' – ' . $name];
        }
        if (preg_match('/tema[\s_\-]*(\d{1,2})/i', $relPath, $m)) {
            return [(int) $m[1], 'Tema ' . (int) $m[1]];
        }
        $parts = explode('/', $relPath);
        if (count($parts) > 1) {
            return [null, $parts[0]];
        }
        return [null, 'Sense classificar'];
    }

    private static function detectKind(string $text, string $relPath): string
    {
        $ext = strtolower(pathinfo($relPath, PATHINFO_EXTENSION));
        if (in_array($ext, ['php', 'html', 'htm'], true)) {
            return 'codi';
        }
        if (preg_match('/exercici|activitat|enunciat|pr[àa]ctica/iu', $relPath . ' ' . mb_substr($text, 0, 1500))) {
            return 'exercicis';
        }
        return 'teoria';
    }

    /** Divideix el text en trossos d'uns 1200 caràcters, respectant els paràgrafs. */
    public static function chunk(string $text, int $size = 1200): array
    {
        $chunks = [];
        $current = '';
        foreach (preg_split("/\n\s*\n/", $text) as $para) {
            $para = trim($para);
            if ($para === '') {
                continue;
            }
            if ($current !== '' && mb_strlen($current) + mb_strlen($para) > $size) {
                $chunks[] = $current;
                $current = '';
            }
            // Paràgrafs molt llargs es tallen
            while (mb_strlen($para) > $size * 2) {
                $chunks[] = mb_substr($para, 0, $size);
                $para = mb_substr($para, $size);
            }
            $current .= ($current === '' ? '' : "\n\n") . $para;
        }
        if ($current !== '') {
            $chunks[] = $current;
        }
        return $chunks;
    }

    /**
     * Busca els fragments del temari més relacionats amb unes paraules clau.
     * Puntuació senzilla: quantes vegades hi apareix cada paraula.
     */
    public static function search(array $keywords, int $limit = 3, ?string $preferFile = null): array
    {
        $keywords = array_values(array_filter(array_map(fn($k) => mb_strtolower(trim($k)), $keywords), fn($k) => mb_strlen($k) >= 3));
        if ($keywords === []) {
            return [];
        }
        $rows = Db::all('SELECT c.text, f.path, f.topic_name FROM chunks c JOIN files f ON f.id = c.file_id WHERE f.status = \'ok\'');
        $scored = [];
        foreach ($rows as $r) {
            $lower = mb_strtolower($r['text']);
            $score = 0;
            foreach ($keywords as $k) {
                $score += substr_count($lower, $k);
            }
            if ($preferFile !== null && str_contains(mb_strtolower($r['path']), mb_strtolower($preferFile))) {
                $score *= 2;
            }
            if ($score > 0) {
                $scored[] = $r + ['score' => $score];
            }
        }
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scored, 0, $limit);
    }
}
