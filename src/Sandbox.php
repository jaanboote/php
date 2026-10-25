<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Executa el codi de l'alumne en un procés PHP CLI separat i limitat.
 *
 * Mesures de seguretat (explicades al README):
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
     */
    public static function run(string $code, array $args = [], string $stdin = '', string $script = 'main.php', ?string $append = null): array
    {
        $result = [
            'stdout' => '', 'stderr' => '', 'exit_code' => null,
            'timeout' => false, 'truncated' => false, 'busy' => false, 'ms' => 0,
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

            // Fem servir fitxers (i no "pipes") per a l'entrada i la sortida:
            // a Windows els pipes no es poden llegir sense bloquejar.
            $descriptors = [
                0 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stdin.txt', 'r'],
                1 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stdout.txt', 'w'],
                2 => ['file', $ioDir . DIRECTORY_SEPARATOR . 'stderr.txt', 'w'],
            ];
            $env = [
                'TEMP' => $jobDir, 'TMP' => $jobDir, 'TMPDIR' => $jobDir,
                'PATH' => dirname($cmd[0]),
            ];
            if (getenv('SystemRoot')) {
                $env['SystemRoot'] = getenv('SystemRoot'); // Windows el necessita per arrencar processos
            }

            $start = microtime(true);
            $proc = proc_open($cmd, $descriptors, $pipes, $jobDir, $env, ['bypass_shell' => true]);
            if (!is_resource($proc)) {
                $result['stderr'] = "No s'ha pogut executar PHP CLI. Revisa 'sandbox.php_cli' a config.php.";
                return $result;
            }

            $timeout   = (float) Config::get('sandbox.timeout', 5);
            $maxOutput = (int) Config::get('sandbox.max_output', 100000);
            $outFile   = $ioDir . DIRECTORY_SEPARATOR . 'stdout.txt';

            while (true) {
                $status = proc_get_status($proc);
                if (!$status['running']) {
                    $result['exit_code'] = $status['exitcode'];
                    break;
                }
                clearstatcache(true, $outFile);
                if (microtime(true) - $start > $timeout) {
                    $result['timeout'] = true;
                } elseif (@filesize($outFile) > $maxOutput) {
                    $result['truncated'] = true;
                }
                if ($result['timeout'] || $result['truncated']) {
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
            $result['ms'] = (int) round((microtime(true) - $start) * 1000);

            $result['stdout'] = self::readLimited($outFile, $maxOutput);
            $result['stderr'] = self::readLimited($ioDir . DIRECTORY_SEPARATOR . 'stderr.txt', 20000);

            // Amaguem les rutes internes: l'alumne només ha de veure "permisos.php"
            foreach (['stdout', 'stderr'] as $k) {
                $result[$k] = str_replace([$jobDir . DIRECTORY_SEPARATOR, $jobDir], '', $result[$k]);
            }
            return $result;
        } finally {
            self::removeDir($jobDir);
            self::removeDir($ioDir);
            flock($lock, LOCK_UN);
            fclose($lock);
        }
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
