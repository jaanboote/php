<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Autenticació d'un sol usuari, sessions segures, CSRF i límit d'intents.
 */
final class Auth
{
    /** Comprova si una IP és dins d'alguna de les xarxes de 'allowed_networks' (CIDR). */
    public static function ipAllowed(string $ip): bool
    {
        foreach ((array) Config::get('allowed_networks', []) as $cidr) {
            if (self::ipInCidr($ip, $cidr)) {
                return true;
            }
        }
        return false;
    }

    public static function ipInCidr(string $ip, string $cidr): bool
    {
        [$net, $bits] = array_pad(explode('/', $cidr, 2), 2, null);
        $ipBin  = @inet_pton($ip);
        $netBin = @inet_pton((string) $net);
        if ($ipBin === false || $netBin === false || strlen($ipBin) !== strlen($netBin)) {
            return false;
        }
        $bits = $bits === null ? strlen($ipBin) * 8 : (int) $bits;
        // Compara byte a byte els primers $bits bits.
        $bytes = intdiv($bits, 8);
        if (substr($ipBin, 0, $bytes) !== substr($netBin, 0, $bytes)) {
            return false;
        }
        $rest = $bits % 8;
        if ($rest === 0) {
            return true;
        }
        $mask = (0xFF << (8 - $rest)) & 0xFF;
        return (ord($ipBin[$bytes]) & $mask) === (ord($netBin[$bytes]) & $mask);
    }

    /** Sessió amb cookie HttpOnly (JS no la pot llegir) i SameSite=Strict (protecció CSRF). */
    public static function startSession(): void
    {
        session_name('PHPLABSESS');
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_start();

        // Caducitat per inactivitat
        $max = (int) Config::get('auth.session_minutes', 480) * 60;
        if (isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > $max) {
            $_SESSION = [];
            session_regenerate_id(true);
        }
        $_SESSION['last_seen'] = time();
    }

    public static function user(): ?string
    {
        return $_SESSION['user'] ?? null;
    }

    public static function hasUser(): bool
    {
        return (int) Db::value('SELECT COUNT(*) FROM users') > 0;
    }

    /** El primer usuari només es pot crear des del mateix PC servidor. */
    public static function isLocalRequest(): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        return $ip === '127.0.0.1' || $ip === '::1';
    }

    public static function createUser(string $username, string $password): void
    {
        Db::run('INSERT INTO users (username, password_hash) VALUES (?, ?)', [
            $username,
            password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    /** Minuts que falten per poder tornar a provar (0 si no està bloquejat). */
    public static function lockedMinutes(string $ip): int
    {
        $window = (int) Config::get('auth.lockout_minutes', 15) * 60;
        $fails = Db::all(
            'SELECT created_at FROM login_attempts WHERE ip = ? AND success = 0 AND created_at > ? ORDER BY created_at',
            [$ip, time() - $window]
        );
        if (count($fails) < (int) Config::get('auth.max_attempts', 5)) {
            return 0;
        }
        $oldest = (int) $fails[0]['created_at'];
        return max(1, (int) ceil(($oldest + $window - time()) / 60));
    }

    public static function attempt(string $username, string $password, string $ip): bool
    {
        $row = Db::one('SELECT * FROM users WHERE username = ?', [$username]);
        // password_verify es fa igualment (amb un hash fals) per no revelar si l'usuari existeix pel temps de resposta.
        $hash = $row['password_hash'] ?? password_hash('usuari-inexistent', PASSWORD_DEFAULT);
        $ok = password_verify($password, $hash) && $row !== null;

        Db::run('INSERT INTO login_attempts (ip, success, created_at) VALUES (?, ?, ?)', [$ip, $ok ? 1 : 0, time()]);

        if ($ok) {
            if (password_needs_rehash($row['password_hash'], PASSWORD_DEFAULT)) {
                Db::run('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $row['id']]);
            }
            session_regenerate_id(true); // evita la fixació de sessió
            $_SESSION['user'] = $row['username'];
            // Neteja intents fallits antics d'aquesta IP
            Db::run('DELETE FROM login_attempts WHERE ip = ? AND success = 0', [$ip]);
        }
        return $ok;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        session_destroy();
    }

    // ---------- CSRF ----------

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function checkCsrf(?string $token): bool
    {
        return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }
}
