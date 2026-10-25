<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Laboratoris web (Fase 5): l'alumne escriu una petita web (formulari + PHP) i la prova en un
 * «navegador» dins de la pàgina, igual que si l'obrís a XAMPP.
 *
 * Com funciona:
 *  - El navegador és un <iframe> que carrega web.php/<testimoni>/<laboratori>/<fitxer>.
 *    Els formularis, enllaços i redireccions funcionen sols perquè les rutes són relatives.
 *  - web.php executa el fitxer amb php-cgi (Sandbox::runWeb) i en retorna la resposta.
 *  - L'iframe està aïllat (CSP «sandbox»): el codi de l'alumne no pot tocar la app ni les seves galetes.
 *    Com que no hi ha galetes de la app, web.php s'autentica amb un testimoni signat a la URL.
 *  - Les galetes i la sessió de la web de l'alumne es guarden al servidor (un «pot de galetes» per laboratori),
 *    així es poden ensenyar a l'inspector.
 */
final class WebLab
{
    /** Hores que val el testimoni de la URL del navegador. */
    private const TOKEN_HOURS = 12;

    public static function isWeb(array $lab): bool
    {
        return ($lab['tipus'] ?? 'cli') === 'web';
    }

    // ------------------------------------------------------------------ Testimoni de la URL

    private static function secret(): string
    {
        $file = Config::dataPath() . DIRECTORY_SEPARATOR . 'secret.key';
        if (!is_file($file)) {
            file_put_contents($file, bin2hex(random_bytes(32)));
        }
        return (string) file_get_contents($file);
    }

    public static function token(string $labId): string
    {
        $exp = time() + self::TOKEN_HOURS * 3600;
        return $exp . '-' . substr(hash_hmac('sha256', "$labId|$exp", self::secret()), 0, 32);
    }

    public static function checkToken(string $labId, string $token): bool
    {
        if (!preg_match('/^(\d{10})-([0-9a-f]{32})$/', $token, $m) || (int) $m[1] < time()) {
            return false;
        }
        return hash_equals(substr(hash_hmac('sha256', "$labId|{$m[1]}", self::secret()), 0, 32), $m[2]);
    }

    /** URL (relativa a la app) per obrir un fitxer de la web de l'alumne al navegador integrat. */
    public static function url(string $labId, string $path = 'index.php'): string
    {
        return 'web.php/' . self::token($labId) . '/' . $labId . '/' . ltrim($path, '/');
    }

    // ------------------------------------------------------------------ Fitxers de l'alumne

    /** Fitxers que ha escrit l'alumne (els desats o, si no n'hi ha, els inicials). */
    public static function sources(array $lab, ?string $code): array
    {
        $files = is_string($code) ? json_decode($code, true) : null;
        return self::cleanSources($lab, is_array($files) ? $files : $lab['codi_inicial']);
    }

    /** Només els fitxers que defineix el laboratori (l'alumne no en pot crear d'altres des de l'editor). */
    public static function cleanSources(array $lab, array $files): array
    {
        $out = [];
        foreach (array_keys($lab['codi_inicial']) as $name) {
            $out[$name] = (string) ($files[$name] ?? '');
        }
        return $out;
    }

    // ------------------------------------------------------------------ Estat del «navegador»

    private static function stateFile(string $kind, string $labId): string
    {
        $dir = Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'web');
        return $dir . DIRECTORY_SEPARATOR . preg_replace('/[^a-z0-9\-]/', '', $labId) . ".$kind.json";
    }

    private static function load(string $kind, string $labId): array
    {
        $file = self::stateFile($kind, $labId);
        return is_file($file) ? (json_decode((string) file_get_contents($file), true) ?: []) : [];
    }

    private static function save(string $kind, string $labId, array $data): void
    {
        file_put_contents(self::stateFile($kind, $labId), json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
    }

    /** Aplica les capçaleres Set-Cookie al pot de galetes (esborra les caducades o buides). */
    public static function applyCookies(array $jar, array $setCookies): array
    {
        foreach ($setCookies as $line) {
            $parts = array_map('trim', explode(';', $line));
            if (!str_contains($parts[0], '=')) {
                continue;
            }
            [$name, $value] = explode('=', $parts[0], 2);
            $name = rawurldecode($name);
            $value = rawurldecode($value);
            $expired = $value === '' || $value === 'deleted';
            foreach (array_slice($parts, 1) as $attr) {
                if (preg_match('/^max-age=(-?\d+)$/i', $attr, $m) && (int) $m[1] <= 0) {
                    $expired = true;
                }
                if (preg_match('/^expires=(.+)$/i', $attr, $m) && ($t = strtotime($m[1])) !== false && $t < time()) {
                    $expired = true;
                }
            }
            if ($expired) {
                unset($jar[$name]);
            } else {
                $jar[$name] = $value;
            }
        }
        return $jar;
    }

    /**
     * Petició des del navegador integrat (la crida web.php): executa, desa galetes i registre.
     * @param string $base Prefix de la URL fins al laboratori, per a $_SERVER['PHP_SELF'] i les redireccions absolutes
     */
    public static function handle(array $lab, string $code, array $req, string $base): array
    {
        $jar = self::load('cookies', $lab['id']);
        $r = Sandbox::runWeb(self::sources($lab, $code), $req + ['cookies' => $jar, 'base' => $base], Sandbox::workspace($lab['id']));
        self::save('cookies', $lab['id'], self::applyCookies($jar, $r['set_cookies']));

        // Registre de les últimes peticions per a l'inspector. Els fitxers estàtics (CSS, imatges) no hi surten:
        // els demana la pàgina, no la persona, i canviarien l'adreça del navegador.
        if (strtolower(pathinfo((string) $req['path'], PATHINFO_EXTENSION)) !== 'php') {
            return $r;
        }
        $log = self::load('log', $lab['id']);
        $log[] = [
            'hora' => date('H:i:s'),
            'method' => $req['method'],
            'path' => $req['path'],
            'query' => $req['query'],
            'get' => self::parseQuery((string) $req['query']),
            'post' => array_map(fn($f) => [$f[0], mb_substr($f[1], 0, 300)], $req['fields'] ?? []),
            'files' => array_map(fn($f) => ['camp' => $f[0], 'nom' => $f[1], 'tipus' => $f[2], 'mida' => strlen($f[3])], $req['files'] ?? []),
            'cookies' => $jar,
            'set_cookies' => $r['set_cookies'],
            'status' => $r['status'],
            'location' => $r['location'],
            'errors' => trim($r['errors']),
            'ms' => $r['ms'],
        ];
        self::save('log', $lab['id'], array_slice($log, -15));
        return $r;
    }

    private static function parseQuery(string $q): array
    {
        $out = [];
        foreach (array_filter(explode('&', $q), 'strlen') as $pair) {
            [$k, $v] = array_pad(explode('=', $pair, 2), 2, '');
            $out[] = [urldecode($k), mb_substr(urldecode($v), 0, 300)];
        }
        return $out;
    }

    /** Dades per a l'inspector: última petició (amb errors explicats), galetes, sessió i fitxers del servidor. */
    public static function inspector(array $lab): array
    {
        $log = self::load('log', $lab['id']);
        $last = $log ? end($log) : null;
        if ($last) {
            $last['explicacions'] = ErrorExplainer::explain($last['errors']);
        }
        $ws = Sandbox::workspace($lab['id']);
        $sessio = null;
        $jar = self::load('cookies', $lab['id']);
        if (isset($jar['PHPSESSID']) && preg_match('/^[A-Za-z0-9,\-]{1,128}$/', $jar['PHPSESSID'])) {
            $file = $ws . DIRECTORY_SEPARATOR . '.sessions' . DIRECTORY_SEPARATOR . 'sess_' . $jar['PHPSESSID'];
            $sessio = is_file($file) ? (string) file_get_contents($file, false, null, 0, 3000) : '';
        }
        return [
            'ultima' => $last,
            'historial' => array_map(fn($l) => ['hora' => $l['hora'], 'method' => $l['method'], 'path' => $l['path'] . ($l['query'] !== '' ? '?' . $l['query'] : ''), 'status' => $l['status']], array_reverse($log)),
            'galetes' => $jar,
            'sessio' => $sessio,
            'fitxers' => Sandbox::webFiles($ws),
        ];
    }

    /** Esborra els fitxers del servidor, les galetes i el registre (com un servidor i un navegador acabats d'estrenar). */
    public static function reset(string $labId): void
    {
        Sandbox::clearWorkspace($labId);
        foreach (['cookies', 'log'] as $kind) {
            @unlink(self::stateFile($kind, $labId));
        }
    }

    // ------------------------------------------------------------------ Proves automàtiques

    /** Imatges mínimes vàlides per a les proves de pujada de fitxers. */
    private static function sample(string $spec): string
    {
        return match ($spec) {
            '@png' => base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='),
            '@gif' => base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7'),
            '@jpg' => base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA='),
            '@gran' => base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==') . str_repeat("\0", 2_500_000),
            default => $spec,
        };
    }

    private static function mimeFor(string $filename): string
    {
        return match (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            'png' => 'image/png', 'gif' => 'image/gif', 'jpg', 'jpeg' => 'image/jpeg', 'pdf' => 'application/pdf',
            'txt' => 'text/plain', 'php' => 'application/x-php', default => 'application/octet-stream',
        };
    }

    /**
     * Executa una prova: una seqüència de peticions (com faria una persona amb el navegador) i comprovacions
     * sobre l'última resposta. Les redireccions se segueixen automàticament, com fa el navegador.
     */
    public static function runTest(array $lab, array $t, array $sources): array
    {
        $ws = Config::dataPath('sandbox' . DIRECTORY_SEPARATOR . 'jobs') . DIRECTORY_SEPARATOR . 'test-' . bin2hex(random_bytes(6));
        @mkdir($ws, 0770, true);
        foreach ($t['fitxers'] ?? [] as $name => $content) {
            $full = $ws . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, (string) $name);
            if (str_ends_with((string) $name, '/')) {
                @mkdir($full, 0770, true);
            } else {
                @mkdir(dirname($full), 0770, true);
                file_put_contents($full, self::sample((string) $content));
            }
        }

        $jar = [];
        $errors = '';
        $locations = [];
        $passos = [];
        $last = null;
        try {
            foreach ($t['peticions'] as $p) {
                [$method, $url] = $p;
                [$path, $query] = array_pad(explode('?', $url, 2), 2, '');
                if (isset($p['query'])) {
                    $query = http_build_query($p['query']);
                }
                $req = ['method' => $method, 'path' => $path, 'query' => $query];
                $desc = "$method $path" . ($query !== '' ? "?$query" : '');
                if ($method === 'POST') {
                    $fields = Sandbox::flattenFields($p['camps'] ?? []);
                    $files = [];
                    foreach ($p['pujades'] ?? [] as $field => $spec) {
                        foreach (str_ends_with($field, '[]') ? $spec : [$spec] as $f) {
                            $files[] = [$field, $f[0], $f[2] ?? self::mimeFor($f[0]), $f[0] === '' ? '' : self::sample($f[1])];
                        }
                    }
                    if ($files !== []) {
                        $m = Sandbox::multipart($fields, $files);
                        $req += ['body' => $m['body'], 'content_type' => $m['content_type']];
                    } else {
                        $req += ['body' => http_build_query(array_column($fields, 1, 0)), 'content_type' => 'application/x-www-form-urlencoded'];
                    }
                    $bits = array_map(fn($f) => $f[0] . '=' . mb_strimwidth($f[1], 0, 25, '…'), $fields);
                    foreach ($files as $f) {
                        $bits[] = $f[0] . ': ' . ($f[1] !== '' ? $f[1] : '(cap fitxer)');
                    }
                    $desc .= $bits ? ' (' . implode(', ', $bits) . ')' : '';
                }
                $passos[] = $desc;

                // La petició i les redireccions que vinguin (com a molt 3)
                for ($hop = 0; $hop < 4; $hop++) {
                    $r = Sandbox::runWeb($sources, $req + ['cookies' => $jar], $ws);
                    $jar = self::applyCookies($jar, $r['set_cookies']);
                    $errors .= $r['errors'];
                    if ($r['busy'] || $r['timeout']) {
                        break 2;
                    }
                    if ($r['location'] === null || $r['status'] < 300 || $r['status'] >= 400) {
                        break;
                    }
                    $locations[] = $r['location'];
                    [$lp, $lq] = array_pad(explode('?', ltrim(preg_replace('#^https?://[^/]+#', '', $r['location']), '/'), 2), 2, '');
                    $req = ['method' => 'GET', 'path' => $lp === '' ? 'index.php' : $lp, 'query' => $lq];
                    $passos[] = '↪ redirecció a ' . $r['location'];
                }
                $last = $r;
            }
            $files = Sandbox::webFiles($ws);
        } finally {
            Sandbox::clearDir($ws);
        }

        $body = (string) ($last['body'] ?? '');
        $html = self::squash($body);
        $text = self::visibleText($body);
        $res = [
            'nom' => $t['nom'],
            'ordre' => implode(' → ', $passos),
            'ok' => true,
            'motiu' => '',
            'esperat' => null,
            'obtingut' => mb_substr($body, 0, 3000),
            'text' => mb_substr($text, 0, 1500),
            'errors' => ErrorExplainer::explain($errors, (bool) ($last['timeout'] ?? false)),
            'fitxers_inicials' => array_keys($t['fitxers'] ?? []),
            'fitxers' => $files,
        ];
        $fail = fn(string $why) => ['ok' => false, 'motiu' => $why] + $res;

        if ($last === null || $last['busy']) {
            return $fail('No s\'ha pogut fer la prova (servidor ocupat). Torna-ho a provar.');
        }
        if ($last['timeout']) {
            return $fail('La pàgina no ha acabat (possible bucle infinit).');
        }
        if (trim($errors) !== '' && empty($t['permet_avisos'])) {
            return $fail('PHP ha mostrat un error o un avís. Una pàgina ben feta no n\'ha de mostrar mai.');
        }
        if (isset($t['estat']) && $last['status'] !== $t['estat']) {
            return $fail("La resposta ha tornat el codi HTTP {$last['status']} i s'esperava {$t['estat']}.");
        }
        if (isset($t['redirigeix']) && !array_filter($locations, fn($l) => str_contains($l, $t['redirigeix']))) {
            return $fail("Després d'enviar el formulari, la pàgina hauria de redirigir a «{$t['redirigeix']}» amb header('Location: ...').");
        }
        if (!empty($t['sense_redireccio']) && $locations) {
            return $fail('La pàgina no hauria de redirigir aquí.');
        }
        foreach ($t['conte'] ?? [] as $needle) {
            if (!str_contains($html, self::squash($needle))) {
                return $fail("L'HTML de la pàgina hauria de contenir: «{$needle}»");
            }
        }
        foreach ($t['text'] ?? [] as $needle) {
            if (!str_contains(mb_strtolower($text), mb_strtolower(self::squash($needle)))) {
                return $fail("La pàgina hauria de mostrar el text: «{$needle}»");
            }
        }
        foreach ($t['conte_regex'] ?? [] as $regex => $descripcio) {
            if (!preg_match($regex, $body)) {
                return $fail("La pàgina hauria de tenir $descripcio");
            }
        }
        foreach ($t['no_conte'] ?? [] as $needle) {
            if (str_contains($html, self::squash($needle))) {
                return $fail("L'HTML de la pàgina NO hauria de contenir: «{$needle}»");
            }
        }
        foreach ($t['no_text'] ?? [] as $needle) {
            if (str_contains(mb_strtolower($text), mb_strtolower(self::squash($needle)))) {
                return $fail("La pàgina NO hauria de mostrar: «{$needle}»");
            }
        }
        $names = array_column($files, 'nom');
        foreach ($t['fitxer_existeix'] ?? [] as $name) {
            if (!in_array($name, $names, true)) {
                return $fail("Al servidor hi hauria d'haver el fitxer «{$name}».");
            }
        }
        foreach ($t['fitxer_no_existeix'] ?? [] as $name) {
            if (in_array($name, $names, true)) {
                return $fail("El fitxer «{$name}» NO s'hauria d'haver desat al servidor.");
            }
        }
        foreach ($t['fitxer_regex'] ?? [] as $name => $rules) {
            $content = array_column($files, 'contingut', 'nom')[$name] ?? null;
            if ($content === null) {
                return $fail("Al servidor hi hauria d'haver el fitxer «{$name}».");
            }
            foreach ($rules as $regex => $descripcio) {
                if (!preg_match($regex, $content)) {
                    return $fail("El fitxer «{$name}» hauria de tenir $descripcio");
                }
            }
        }
        return $res;
    }

    /**
     * El text que es veu a la pàgina: sense etiquetes, scripts ni estils. Les etiquetes de bloc (p, li, dd, br...)
     * compten com un espai («<dt>Edat:</dt><dd>9</dd>» → «Edat: 9»); les de línia no («<b>Anna</b>!» → «Anna!»).
     */
    public static function visibleText(string $html): string
    {
        $html = preg_replace('#<(script|style)\b.*?</\1>#is', '', $html);
        $html = preg_replace('#</?(p|div|h[1-6]|ul|ol|li|dl|dt|dd|br|hr|tr|td|th|table|section|article|header|footer|main|nav|form|label|figure|figcaption|img|input|select|option|textarea|button|fieldset|legend|pre|blockquote)\b[^>]*>#i', ' ', $html);
        return self::squash(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    /** Espais seguits (salts de línia inclosos) com un de sol, per comparar HTML sense dependre del format. */
    private static function squash(string $s): string
    {
        return trim(preg_replace('/\s+/u', ' ', str_replace(['’', '“', '”'], ["'", '"', '"'], $s)));
    }
}
