<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Executa el codi de l'alumne en un procés PHP CLI separat i limitat.
 *
 * Mesures de seguretat (explicades a docs/GUIA.md):
 *  - Procés separat (proc_open): si el codi peta o es penja, l'aplicació no cau.
 *  - Timeout de paret: si triga més de N segons, es mata el procés (bucles infinits).
 *  - memory_limit: el procés no pot gastar més memòria de la indicada.
 *  - open_basedir: el codi només pot llegir/escriure fitxers dins de la seva carpeta temporal.
 *  - disable_functions: no pot executar ordres del sistema, obrir sockets ni canviar límits.
 *  - allow_url_fopen=Off: no pot descarregar res d'Internet amb file_get_contents().
 *  - Entorn net: no rep les variables d'entorn d'Apache.
 *  - Com a molt N execucions alhora (bloquejos amb flock).
 */
final class Sandbox
{
    /** Funcions perilloses que el codi de l'alumne no pot fer servir. */
    public const DISABLED_FUNCTIONS = [
        // Executar ordres del sistema
        'exec', 'shell_exec', 'system', 'passthru', 'popen',
        'proc_open', 'proc_close', 'proc_get_status', 'proc_terminate', 'proc_nice',
        'pcntl_exec', 'pcntl_fork', 'pcntl_signal', 'pcntl_alarm',
        'posix_kill', 'posix_mkfifo', 'posix_setuid', 'posix_setgid', 'posix_setsid', 'posix_setpgid',
        // Xarxa
        'fsockopen', 'pfsockopen', 'stream_socket_client', 'stream_socket_server', 'stream_socket_pair',
        'socket_create', 'socket_create_listen', 'socket_create_pair', 'socket_connect',
        'curl_init', 'curl_exec', 'curl_multi_init', 'curl_multi_exec',
        'ftp_connect', 'ftp_ssl_connect', 'mail', 'mb_send_mail',
        'gethostbyname', 'gethostbynamel', 'dns_get_record', 'checkdnsrr', 'getmxrr',
        // Canviar la configuració o els límits
        'ini_set', 'ini_alter', 'ini_restore', 'set_time_limit', 'ignore_user_abort', 'putenv', 'dl',
        // Enllaços i propietaris de fitxers
        'symlink', 'link', 'chown', 'chgrp', 'lchown', 'lchgrp',
        'openlog', 'syslog',
    ];

    /**
     * @param string      $code   Codi PHP de l'alumne
     * @param string[]    $args   Arguments de consola ($argv[1], $argv[2]...)
     * @param string      $stdin  Text que llegirà readline() / fgets(STDIN), una línia per resposta
     * @param string      $script Nom del fitxer ($argv[0]), p. ex. "permisos.php"
     * @param string|null $append Codi PHP extra que s'executa després (per a les comprovacions)
     * @param array       $files  Fitxers que hi ha d'haver abans d'executar: ['dades.txt' => "contingut"]
     *                            (un nom acabat en "/" crea una carpeta)
     * @param string|null $workspace Carpeta on es conserven els fitxers entre execucions (una per laboratori)
     */
    public static function run(string $code, array $args = [], string $stdin = '', string $script = 'main.php',
                               ?string $append = null, array $files = [], ?string $workspace = null): array
    {
        $result = [
            'stdout' => '', 'stderr' => '', 'exit_code' => null,
            'timeout' => false, 'truncated' => false, 'busy' => false, 'ms' => 0,
            'files' => [], 'avis_fitxers' => null,
        ];

        $lock = self::acquireSlot();
        if ($lock === null) {
            $result['busy'] = true;
            $result['stderr'] = 'Hi ha massa execucions en curs. Torna-ho a provar en un moment.';
            return $result;
        }

        $id     = bin2hex(random_bytes(8));
        $jobDir = Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'jobs') . DIRECTORY_SEPARATOR . $id;
        $ioDir  = $jobDir . '-io'; // fora de l'open_basedir: el codi de l'alumne no hi pot tocar
        mkdir($jobDir, 0770);
        mkdir($ioDir, 0770);
        $jobDir = realpath($jobDir);

        try {
            $script = self::safeScriptName($script);

            // Fitxers de partida: els de la carpeta del laboratori i els que demani la prova
            if ($workspace !== null && is_dir($workspace)) {
                self::copyDir($workspace, $jobDir);
            }
            foreach ($files as $name => $content) {
                $path = self::safeRelativePath((string) $name);
                if ($path === null) {
                    continue;
                }
                $full = $jobDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
                if (str_ends_with((string) $name, '/')) {
                    @mkdir($full, 0770, true);
                } else {
                    @mkdir(dirname($full), 0770, true);
                    file_put_contents($full, (string) $content);
                }
            }

            file_put_contents($jobDir . DIRECTORY_SEPARATOR . $script, self::prepareCode($code, $append));
            file_put_contents($jobDir . DIRECTORY_SEPARATOR . '_phplab_prepend.php', self::prependCode());
            file_put_contents($ioDir . DIRECTORY_SEPARATOR . 'stdin.txt', self::normalizeStdin($stdin));

            $cmd = [(string) Config::get('sandbox.php_cli', PHP_BINARY)];
            foreach (self::iniSettings($jobDir) as $key => $value) {
                $cmd[] = '-d';
                $cmd[] = $key . '=' . $value;
            }
            $cmd[] = $script;
            foreach ($args as $a) {
                $cmd[] = (string) $a;
            }

            $maxOutput = (int) Config::get('sandbox.max_output', 100000);
            $outFile   = $ioDir . DIRECTORY_SEPARATOR . 'stdout.txt';
            $proc = self::spawn($cmd, $ioDir, $jobDir, []);
            if ($proc === null) {
                $result['stderr'] = "No s'ha pogut executar PHP CLI. Revisa 'sandbox.php_cli' a config.php.";
                return $result;
            }
            $result = $proc + $result;

            $result['stdout'] = self::readLimited($outFile, $maxOutput);
            $result['stderr'] = self::readLimited($ioDir . DIRECTORY_SEPARATOR . 'stderr.txt', 20000);

            // Amaguem les rutes internes: l'alumne només ha de veure "permisos.php"
            foreach (['stdout', 'stderr'] as $k) {
                $result[$k] = str_replace([$jobDir . DIRECTORY_SEPARATOR, $jobDir], '', $result[$k]);
            }

            // Fitxers que han quedat a la carpeta (sense el programa ni el fitxer intern)
            $result['files'] = self::listFiles($jobDir, [$script, '_phplab_prepend.php']);
            if ($workspace !== null) {
                $total = array_sum(array_column($result['files'], 'size'));
                if ($total > self::WORKSPACE_MAX_BYTES) {
                    $result['avis_fitxers'] = 'Els fitxers ocupen massa (més d\'1 MB): no s\'han desat per a la propera execució.';
                } else {
                    self::removeDir($workspace);
                    @mkdir($workspace, 0770, true);
                    self::copyDir($jobDir, $workspace, [$script, '_phplab_prepend.php']);
                }
            }
            return $result;
        } finally {
            self::removeDir($jobDir);
            self::removeDir($ioDir);
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    // ------------------------------------------------------------------ Laboratoris web (php-cgi)

    /** Ruta de php-cgi: la de config.php o, si no n'hi ha, al costat de php.exe (a XAMPP: C:\xampp\php\php-cgi.exe). */
    public static function phpCgi(): string
    {
        $cfg = (string) Config::get('sandbox.php_cgi', '');
        if ($cfg !== '') {
            return $cfg;
        }
        $cli = (string) Config::get('sandbox.php_cli', PHP_BINARY);
        return dirname($cli) . DIRECTORY_SEPARATOR . (DIRECTORY_SEPARATOR === '\\' ? 'php-cgi.exe' : 'php-cgi');
    }

    /** Tipus MIME dels fitxers estàtics que pot servir una web de laboratori. */
    private const MIME = [
        'html' => 'text/html; charset=UTF-8', 'htm' => 'text/html; charset=UTF-8', 'css' => 'text/css; charset=UTF-8',
        'js' => 'text/javascript; charset=UTF-8', 'json' => 'application/json', 'txt' => 'text/plain; charset=UTF-8',
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
    ];

    /** Fitxers interns que no formen part de la web de l'alumne. */
    private const WEB_INTERNAL = ['_phplab_errors.log'];

    /**
     * Executa una petició HTTP contra la web de l'alumne, com ho faria Apache amb PHP en mode CGI.
     *
     * @param array  $sources   Fitxers que escriu l'alumne: ['index.php' => '...', 'estil.css' => '...']
     * @param array  $req       method (GET|POST), path ('processa.php'), query ('a=1&b=2'), body (cos de la petició),
     *                          content_type, cookies (['PHPSESSID' => '...']), base (prefix de la URL per a $_SERVER['PHP_SELF'])
     * @param string $workspace Carpeta del «servidor» que es conserva entre peticions: uploads/, sessions, fitxers de dades...
     * @return array status, content_type, body, location, set_cookies, errors, ms, timeout, files
     */
    public static function runWeb(array $sources, array $req, string $workspace): array
    {
        $res = [
            'status' => 200, 'content_type' => 'text/html; charset=UTF-8', 'body' => '', 'location' => null,
            'set_cookies' => [], 'errors' => '', 'ms' => 0, 'timeout' => false, 'truncated' => false, 'busy' => false,
        ];
        $method = strtoupper((string) ($req['method'] ?? 'GET')) === 'POST' ? 'POST' : 'GET';
        $path = self::safeRelativePath((string) ($req['path'] ?? '')) ?? 'index.php';
        $base = rtrim((string) ($req['base'] ?? ''), '/');
        $query = (string) ($req['query'] ?? '');
        @mkdir($workspace, 0770, true);

        $sources = array_filter($sources, fn($n) => self::safeRelativePath((string) $n) !== null, ARRAY_FILTER_USE_KEY);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $inWorkspace = is_file($workspace . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));

        // Fitxers estàtics (CSS, imatges pujades...): es serveixen tal qual, sense executar res
        if ($ext !== 'php') {
            if (isset($sources[$path])) {
                $res['body'] = (string) $sources[$path];
            } elseif ($inWorkspace && !in_array($path, self::WEB_INTERNAL, true) && !str_starts_with($path, '.')) {
                $res['body'] = (string) file_get_contents($workspace . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path));
            } else {
                return ['status' => 404, 'body' => self::notFound($path)] + $res;
            }
            $res['content_type'] = self::MIME[$ext] ?? 'application/octet-stream';
            return $res;
        }
        if (!isset($sources[$path]) && !$inWorkspace) {
            return ['status' => 404, 'body' => self::notFound($path)] + $res;
        }

        $lock = self::acquireSlot();
        if ($lock === null) {
            return ['status' => 503, 'busy' => true, 'body' => 'Hi ha massa execucions en curs. Torna-ho a provar en un moment.'] + $res;
        }
        $id     = bin2hex(random_bytes(8));
        $jobDir = Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'jobs') . DIRECTORY_SEPARATOR . $id;
        $ioDir  = $jobDir . '-io';
        mkdir($jobDir, 0770);
        mkdir($ioDir, 0770);
        $jobDir = realpath($jobDir);
        $sep = DIRECTORY_SEPARATOR;

        try {
            self::copyDir($workspace, $jobDir);
            foreach ($sources as $name => $content) {
                $full = $jobDir . $sep . str_replace('/', $sep, (string) $name);
                @mkdir(dirname($full), 0770, true);
                file_put_contents($full, (string) $content);
            }
            @mkdir($jobDir . $sep . '.sessions', 0770);
            $body = (string) ($req['body'] ?? '');
            file_put_contents($ioDir . $sep . 'stdin.txt', $body);

            $ini = [
                'auto_prepend_file' => '',
                'display_errors' => '1',              // com a XAMPP: els errors surten a la pàgina...
                'log_errors' => '1',                  // ...i també els recollim per explicar-los
                'error_log' => $jobDir . $sep . '_phplab_errors.log',
                'file_uploads' => '1',
                'upload_max_filesize' => '3M',
                'post_max_size' => '8M',
                'max_file_uploads' => '10',
                'output_buffering' => '4096',         // com a XAMPP: header() funciona encara que ja s'hagi escrit una mica
                'session.save_path' => $jobDir . $sep . '.sessions',
                'session.use_strict_mode' => '1',
                'expose_php' => '0',
            ] + self::iniSettings($jobDir);
            $cmd = [self::phpCgi()];
            foreach ($ini as $key => $value) {
                $cmd[] = '-d';
                $cmd[] = $key . '=' . $value;
            }
            $cookies = [];
            foreach ((array) ($req['cookies'] ?? []) as $k => $v) {
                $cookies[] = rawurlencode((string) $k) . '=' . rawurlencode((string) $v);
            }
            $env = [
                'GATEWAY_INTERFACE' => 'CGI/1.1',
                'SERVER_SOFTWARE' => 'Apache (PHPLab)',
                'SERVER_NAME' => 'localhost', 'SERVER_PORT' => '80', 'SERVER_PROTOCOL' => 'HTTP/1.1',
                'HTTP_HOST' => 'localhost', 'REMOTE_ADDR' => '127.0.0.1',
                'HTTP_USER_AGENT' => 'Mozilla/5.0 (PHPLab)',
                'REQUEST_METHOD' => $method,
                'SCRIPT_FILENAME' => $jobDir . $sep . str_replace('/', $sep, $path),
                'SCRIPT_NAME' => $base . '/' . $path,
                'REQUEST_URI' => $base . '/' . $path . ($query !== '' ? '?' . $query : ''),
                'DOCUMENT_ROOT' => $jobDir,
                'QUERY_STRING' => $query,
                'REDIRECT_STATUS' => '200',           // php-cgi s'hi nega si no ve d'un servidor web
                'CONTENT_TYPE' => $method === 'POST' ? (string) ($req['content_type'] ?? 'application/x-www-form-urlencoded') : '',
                'CONTENT_LENGTH' => $method === 'POST' ? (string) strlen($body) : '',
                'HTTP_COOKIE' => implode('; ', $cookies),
            ];

            $proc = self::spawn($cmd, $ioDir, $jobDir, $env);
            if ($proc === null) {
                return ['status' => 500, 'body' => "No s'ha pogut executar php-cgi. Revisa 'sandbox.php_cgi' a config.php."] + $res;
            }
            $res = ['ms' => $proc['ms'], 'timeout' => $proc['timeout'], 'truncated' => $proc['truncated']] + $res;
            $raw = self::readLimited($ioDir . $sep . 'stdout.txt', (int) Config::get('sandbox.max_output', 100000) + 8000);
            $res['errors'] = self::readLimited($jobDir . $sep . '_phplab_errors.log', 20000)
                . self::readLimited($ioDir . $sep . 'stderr.txt', 5000);

            // Capçaleres CGI + cos
            $parts = preg_split("/\r?\n\r?\n/", $raw, 2);
            $headerText = count($parts) === 2 ? $parts[0] : '';
            $res['body'] = count($parts) === 2 ? $parts[1] : $raw;
            foreach (preg_split("/\r?\n/", $headerText) as $line) {
                if (!str_contains($line, ':')) {
                    continue;
                }
                [$name, $value] = array_map('trim', explode(':', $line, 2));
                match (strtolower($name)) {
                    'status' => $res['status'] = (int) $value ?: 200,
                    'location' => $res['location'] = $value,
                    'content-type' => $res['content_type'] = $value,
                    'set-cookie' => $res['set_cookies'][] = $value,
                    default => null,
                };
            }
            if ($res['location'] !== null && $res['status'] === 200) {
                $res['status'] = 302;   // com Apache: Location sense Status és una redirecció
            }
            if ($res['timeout']) {
                $res['status'] = 500;
                $res['body'] .= "\n\n[PHPLab] La pàgina ha trigat més de " . (int) Config::get('sandbox.timeout', 5) . " segons i s'ha aturat (possible bucle infinit).";
            }

            // Amaguem les rutes internes
            foreach (['body', 'errors'] as $k) {
                $res[$k] = str_replace([$jobDir . $sep, $jobDir], ['', ''], $res[$k]);
            }
            // Format del log («[data] PHP Warning:  ...») → el de la pantalla («Warning: ...»), el que entén ErrorExplainer
            $res['errors'] = preg_replace(['/^\[[^\]]*\] PHP /m', '/^(Parse error|Fatal error|Warning|Notice|Deprecated):\s+/m'], ['', '$1: '], $res['errors']);

            // Desem l'estat del «servidor» (fitxers pujats, sessions...) per a la petició següent
            $exclude = array_merge(array_keys($sources), self::WEB_INTERNAL);
            $total = array_sum(array_column(self::listFiles($jobDir, $exclude), 'size'));
            if ($total <= 8 * self::WORKSPACE_MAX_BYTES) {
                self::removeDir($workspace);
                @mkdir($workspace, 0770, true);
                self::copyDir($jobDir, $workspace, $exclude);
            } else {
                $res['errors'] .= "\n[PHPLab] Els fitxers del servidor ocupen més de 8 MB: no s'han desat.";
            }
            return $res;
        } finally {
            self::removeDir($jobDir);
            self::removeDir($ioDir);
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private static function notFound(string $path): string
    {
        return '<!doctype html><title>404 Not Found</title><h1>Not Found</h1><p>The requested URL /' . htmlspecialchars($path) . ' was not found on this server.</p>';
    }

    /** Fitxers del «servidor» d'un laboratori web, sense les sessions (que són internes de PHP). */
    public static function webFiles(string $workspace): array
    {
        return array_values(array_filter(self::listFiles($workspace), fn($f) => !str_starts_with($f['nom'], '.')));
    }

    /**
     * Cos multipart/form-data, com el que envia el navegador amb enctype="multipart/form-data".
     * @param array $fields [[nom, valor], ...]
     * @param array $files  [[camp, nom_fitxer, tipus, contingut], ...]
     * @return array{body:string, content_type:string}
     */
    public static function multipart(array $fields, array $files): array
    {
        $boundary = '----PHPLab' . bin2hex(random_bytes(8));
        $body = '';
        foreach ($fields as [$name, $value]) {
            $body .= "--$boundary\r\nContent-Disposition: form-data; name=\"" . addcslashes((string) $name, '"') . "\"\r\n\r\n$value\r\n";
        }
        foreach ($files as [$field, $filename, $type, $content]) {
            $body .= "--$boundary\r\nContent-Disposition: form-data; name=\"" . addcslashes((string) $field, '"') . '"; filename="'
                . addcslashes((string) $filename, '"') . "\"\r\nContent-Type: $type\r\n\r\n$content\r\n";
        }
        $body .= "--$boundary--\r\n";
        return ['body' => $body, 'content_type' => "multipart/form-data; boundary=$boundary"];
    }

    /** Converteix $_POST (amb arrays: nom[]...) en parells [nom, valor] per tornar-los a enviar. */
    public static function flattenFields(array $data, string $prefix = ''): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            $name = $prefix === '' ? (string) $k : $prefix . '[' . (is_int($k) ? '' : $k) . ']';
            if (is_array($v)) {
                $out = array_merge($out, self::flattenFields($v, $name));
            } else {
                $out[] = [$name, (string) $v];
            }
        }
        return $out;
    }

    /** Converteix $_FILES (també amb camps nom[]) en [camp, nom_fitxer, tipus, contingut] per tornar-los a enviar. */
    public static function flattenFiles(array $files): array
    {
        $out = [];
        foreach ($files as $field => $f) {
            if (is_array($f['name'])) {
                foreach (array_keys($f['name']) as $i) {
                    if ((int) $f['error'][$i] === UPLOAD_ERR_NO_FILE) {
                        $out[] = [$field . '[]', '', 'application/octet-stream', ''];
                    } elseif (is_uploaded_file($f['tmp_name'][$i])) {
                        $out[] = [$field . '[]', $f['name'][$i], $f['type'][$i] ?: 'application/octet-stream', (string) file_get_contents($f['tmp_name'][$i])];
                    }
                }
            } elseif ((int) $f['error'] === UPLOAD_ERR_NO_FILE) {
                $out[] = [$field, '', 'application/octet-stream', ''];
            } elseif (is_uploaded_file($f['tmp_name'])) {
                $out[] = [$field, $f['name'], $f['type'] ?: 'application/octet-stream', (string) file_get_contents($f['tmp_name'])];
            }
        }
        return $out;
    }

    /**
     * Executa l'ordre amb els límits de temps i de sortida. L'entrada és stdin.txt i la sortida
     * stdout.txt / stderr.txt de $ioDir. Fem servir fitxers (i no "pipes"): a Windows els pipes
     * no es poden llegir sense bloquejar.
     * @return array{exit_code:?int, timeout:bool, truncated:bool, ms:int}|null  null si no s'ha pogut arrencar
     */
    private static function spawn(array $cmd, string $ioDir, string $cwd, array $extraEnv): ?array
    {
        $descriptors = [
            0 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stdin.txt', 'r'],
            1 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stdout.txt', 'w'],
            2 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stderr.txt', 'w'],
        ];
        $env = [
            'TEMP' => $cwd, 'TMP' => $cwd, 'TMPDIR' => $cwd,
            'PATH' => dirname($cmd[0]),
        ] + $extraEnv;
        if (getenv('SystemRoot')) {
            $env['SystemRoot'] = getenv('SystemRoot'); // Windows el necessita per arrencar processos
        }

        $res = ['exit_code' => null, 'timeout' => false, 'truncated' => false, 'ms' => 0];
        $start = microtime(true);
        $proc = proc_open($cmd, $descriptors, $pipes, $cwd, $env, ['bypass_shell' => true]);
        if (!is_resource($proc)) {
            return null;
        }
        $timeout   = (float) Config::get('sandbox.timeout', 5);
        $maxOutput = (int) Config::get('sandbox.max_output', 100000);
        $outFile   = $ioDir . DIRECTORY_SEPARATOR . 'stdout.txt';
        while (true) {
            $status = proc_get_status($proc);
            if (!$status['running']) {
                $res['exit_code'] = $status['exitcode'];
                break;
            }
            clearstatcache(true, $outFile);
            if (microtime(true) - $start > $timeout) {
                $res['timeout'] = true;
            } elseif (@filesize($outFile) > $maxOutput) {
                $res['truncated'] = true;
            }
            if ($res['timeout'] || $res['truncated']) {
                proc_terminate($proc, 9);
                // Esperem que acabi de morir abans d'esborrar els fitxers
                for ($i = 0; $i < 50 && proc_get_status($proc)['running']; $i++) {
                    usleep(20000);
                }
                break;
            }
            usleep(15000);
        }
        proc_close($proc);
        $res['ms'] = (int) round((microtime(true) - $start) * 1000);
        return $res;
    }

    /** Mida màxima dels fitxers que es conserven entre execucions d'un laboratori. */
    private const WORKSPACE_MAX_BYTES = 1048576;

    /** Carpeta de fitxers persistents d'un laboratori (data_dir/sandbox/workspaces/<id>). */
    public static function workspace(string $labId): string
    {
        return Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'workspaces') . DIRECTORY_SEPARATOR . preg_replace('/[^a-z0-9\-]/', '', $labId);
    }

    public static function clearWorkspace(string $labId): void
    {
        self::removeDir(self::workspace($labId));
    }

    /** Esborra una carpeta de treball temporal (la fan servir les proves dels laboratoris web). */
    public static function clearDir(string $dir): void
    {
        self::removeDir($dir);
    }

    /** Llista els fitxers d'una carpeta amb el contingut dels petits (per mostrar-los a l'alumne). */
    public static function listFiles(string $dir, array $exclude = []): array
    {
        if (!is_dir($dir)) {
            return [];
        }
        $out = [];
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $f) {
            /** @var \SplFileInfo $f */
            $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($dir) + 1));
            if (in_array($rel, $exclude, true)) {
                continue;
            }
            if ($f->isDir()) {
                $out[] = ['nom' => $rel . '/', 'size' => 0, 'contingut' => null];
                continue;
            }
            $content = (string) file_get_contents($f->getPathname(), false, null, 0, 4000);
            $out[] = [
                'nom' => $rel,
                'size' => $f->getSize(),
                'contingut' => mb_check_encoding($content, 'UTF-8') ? $content : null,   // null = binari
            ];
            if (count($out) >= 50) {
                break;
            }
        }
        usort($out, fn($a, $b) => strcmp($a['nom'], $b['nom']));
        return $out;
    }

    /** Copia el contingut d'una carpeta a una altra (recursiu). */
    private static function copyDir(string $from, string $to, array $exclude = []): void
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($it as $f) {
            $rel = substr($f->getPathname(), strlen($from) + 1);
            if (in_array(str_replace('\\', '/', $rel), $exclude, true) || $f->isLink()) {
                continue;
            }
            $target = $to . DIRECTORY_SEPARATOR . $rel;
            $f->isDir() ? @mkdir($target, 0770, true) : @copy($f->getPathname(), $target);
        }
    }

    /** Ruta relativa segura dins de la carpeta del programa (sense .. ni rutes absolutes). */
    private static function safeRelativePath(string $name): ?string
    {
        $name = trim(str_replace('\\', '/', $name), '/');
        if ($name === '' || str_contains($name, '..') || preg_match('/^[a-zA-Z]:/', $name)) {
            return null;
        }
        return $name;
    }

    /** Directives php.ini que s'apliquen al procés de l'alumne (amb -d). */
    private static function iniSettings(string $jobDir): array
    {
        $sep = DIRECTORY_SEPARATOR;
        return [
            'disable_functions'       => implode(',', self::DISABLED_FUNCTIONS),
            'open_basedir'            => $jobDir . $sep,
            'memory_limit'            => (string) Config::get('sandbox.memory_limit', '64M'),
            'max_execution_time'      => (string) (int) Config::get('sandbox.timeout', 5),
            'allow_url_fopen'         => '0',
            'allow_url_include'       => '0',
            'enable_dl'               => '0',
            'file_uploads'            => '0',
            'display_errors'          => 'stderr',
            'display_startup_errors'  => '0',
            'log_errors'              => '0',
            'html_errors'             => '0',
            'error_reporting'         => (string) E_ALL,
            'output_buffering'        => '0',
            'auto_prepend_file'       => $jobDir . $sep . '_phplab_prepend.php',
            'auto_append_file'        => '',
            'error_log'               => $jobDir . $sep . 'php_errors.log',   // error_log() escriu aquí, com un log real
            'sys_temp_dir'            => $jobDir,
            'upload_tmp_dir'          => $jobDir,
            'session.save_path'       => $jobDir,
            'default_charset'         => 'UTF-8',
            'date.timezone'           => 'Europe/Madrid',
            'opcache.enable_cli'      => '0',
            'xdebug.mode'             => 'off',
        ];
    }

    /**
     * Fitxer que s'executa abans del codi de l'alumne. Defineix una versió pròpia de readline()
     * que llegeix de l'entrada que ha escrit l'alumne i la mostra, com faria un terminal.
     * (Així funciona igual a Windows i a Linux.)
     */
    private static function prependCode(): string
    {
        return <<<'PHP'
            <?php
            function __phplab_readline(?string $prompt = null): string|false
            {
                if ($prompt !== null && $prompt !== '') {
                    echo $prompt;
                }
                $line = fgets(STDIN);
                if ($line === false) {
                    echo PHP_EOL;
                    return false;
                }
                $line = rtrim($line, "\r\n");
                echo $line, PHP_EOL; // eco de la resposta, com al terminal
                return $line;
            }
            PHP;
    }

    /** Canvia les crides a readline() per __phplab_readline() i hi afegeix el codi de comprovació. */
    private static function prepareCode(string $code, ?string $append): string
    {
        $tokens = @token_get_all($code);
        $out = '';
        $prevSignificant = null;
        $count = count($tokens);
        for ($i = 0; $i < $count; $i++) {
            $t = $tokens[$i];
            $text = is_array($t) ? $t[1] : $t;
            if (is_array($t) && in_array($t[0], [T_STRING, T_NAME_FULLY_QUALIFIED], true)
                && strtolower(ltrim($t[1], '\\')) === 'readline'
                && !in_array($prevSignificant, [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW, T_CONST], true)
                && self::nextSignificant($tokens, $i) === '(') {
                $text = '__phplab_readline';
            }
            $out .= $text;
            if (!is_array($t) || !in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                $prevSignificant = is_array($t) ? $t[0] : $t;
            }
        }

        if ($append !== null && $append !== '') {
            // Si el codi acaba fora de PHP (després de l'etiqueta de tancament), cal tornar a obrir-la.
            $last = end($tokens);
            $outsidePhp = $tokens === [] || (is_array($last) && in_array($last[0], [T_INLINE_HTML, T_CLOSE_TAG], true));
            $out .= ($outsidePhp ? "\n<?php\n" : "\n;\n") . $append . "\n";
        }
        return $out;
    }

    private static function nextSignificant(array $tokens, int $i): ?string
    {
        for ($j = $i + 1, $n = count($tokens); $j < $n; $j++) {
            $t = $tokens[$j];
            if (is_array($t) && in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            return is_array($t) ? $t[1] : $t;
        }
        return null;
    }

    /**
     * Converteix "754 'Fer la compra' \"hola\"" en ['754', 'Fer la compra', 'hola'],
     * igual que ho fa el terminal: l'espai separa i les cometes agrupen.
     */
    public static function parseArgs(string $line): array
    {
        $args = [];
        $current = '';
        $inArg = false;
        $quote = null;
        $len = strlen($line);
        for ($i = 0; $i < $len; $i++) {
            $c = $line[$i];
            if ($quote !== null) {
                if ($c === $quote) {
                    $quote = null;
                } else {
                    $current .= $c;
                }
            } elseif ($c === '"' || $c === "'") {
                $quote = $c;
                $inArg = true;
            } elseif (ctype_space($c)) {
                if ($inArg) {
                    $args[] = $current;
                    $current = '';
                    $inArg = false;
                }
            } else {
                $current .= $c;
                $inArg = true;
            }
        }
        if ($inArg) {
            $args[] = $current;
        }
        return $args;
    }

    private static function normalizeStdin(string $stdin): string
    {
        $stdin = str_replace("\r\n", "\n", $stdin);
        if ($stdin !== '' && !str_ends_with($stdin, "\n")) {
            $stdin .= "\n";
        }
        return $stdin;
    }

    private static function safeScriptName(string $name): string
    {
        $name = basename($name);
        return preg_match('/^[a-zA-Z0-9_\-]{1,40}\.php$/', $name) ? $name : 'main.php';
    }

    /** Agafa una de les N "places" d'execució. Espera fins a 3 s si totes estan ocupades. */
    private static function acquireSlot()
    {
        $dir = Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'locks');
        $slots = max(1, (int) Config::get('sandbox.max_parallel', 2));
        $deadline = microtime(true) + 3;
        do {
            for ($i = 0; $i < $slots; $i++) {
                $fh = fopen($dir . DIRECTORY_SEPARATOR . "slot$i.lock", 'c');
                if ($fh && flock($fh, LOCK_EX | LOCK_NB)) {
                    return $fh;
                }
                if ($fh) {
                    fclose($fh);
                }
            }
            usleep(100000);
        } while (microtime(true) < $deadline);
        return null;
    }

    private static function readLimited(string $file, int $max): string
    {
        if (!is_file($file)) {
            return '';
        }
        $data = (string) file_get_contents($file, false, null, 0, $max);
        // Si la sortida no és UTF-8 vàlid (p. ex. s'ha tallat a mitja lletra), la reparem
        return mb_check_encoding($data, 'UTF-8') ? $data : mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }

    private static function removeDir(string|false $dir): void
    {
        if (!$dir || !is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() && !$item->isLink() ? @rmdir($item->getPathname()) : @unlink($item->getPathname());
        }
        @rmdir($dir);
    }
}
