<?php
declare(strict_types=1);

namespace PhpLab;

use PDO;

/**
 * Connexió a SQLite amb PDO. La base de dades és un sol fitxer: data_dir/phplab.sqlite.
 * Les taules es creen automàticament la primera vegada (migracions).
 */
final class Db
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $file = Config::dataPath() . DIRECTORY_SEPARATOR . 'phplab.sqlite';
            self::$pdo = new PDO('sqlite:' . $file, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
            self::$pdo->exec('PRAGMA journal_mode = WAL');
            self::$pdo->exec('PRAGMA busy_timeout = 5000');
            self::migrate(self::$pdo);
        }
        return self::$pdo;
    }

    /** Executa una consulta preparada i retorna el PDOStatement. */
    public static function run(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    public static function value(string $sql, array $params = []): mixed
    {
        $v = self::run($sql, $params)->fetchColumn();
        return $v === false ? null : $v;
    }

    /**
     * Cada migració s'aplica una sola vegada. Per afegir taules en fases noves,
     * s'afegeix un element nou al final de l'array (no es modifiquen els antics).
     */
    private static function migrate(PDO $pdo): void
    {
        $migrations = [
            1 => <<<'SQL'
                CREATE TABLE users (
                    id INTEGER PRIMARY KEY,
                    username TEXT NOT NULL UNIQUE,
                    password_hash TEXT NOT NULL,
                    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
                );
                CREATE TABLE login_attempts (
                    id INTEGER PRIMARY KEY,
                    ip TEXT NOT NULL,
                    success INTEGER NOT NULL,
                    created_at INTEGER NOT NULL
                );
                CREATE INDEX idx_login_ip ON login_attempts(ip, created_at);

                -- Temari: un registre per fitxer indexat
                CREATE TABLE files (
                    id INTEGER PRIMARY KEY,
                    path TEXT NOT NULL UNIQUE,          -- relatiu a data_dir/temari
                    ext TEXT NOT NULL,
                    size INTEGER NOT NULL,
                    hash TEXT NOT NULL,                 -- sha256 del contingut
                    topic_num INTEGER,                  -- número de tema detectat (pot ser NULL)
                    topic_name TEXT NOT NULL,
                    kind TEXT NOT NULL DEFAULT 'teoria',-- teoria | exercicis | codi
                    status TEXT NOT NULL,               -- ok | error | pendent
                    message TEXT,
                    text TEXT,
                    indexed_at TEXT NOT NULL
                );
                CREATE TABLE chunks (
                    id INTEGER PRIMARY KEY,
                    file_id INTEGER NOT NULL REFERENCES files(id) ON DELETE CASCADE,
                    idx INTEGER NOT NULL,
                    text TEXT NOT NULL
                );
                CREATE INDEX idx_chunks_file ON chunks(file_id);

                -- Progrés i gamificació
                CREATE TABLE xp_events (
                    id INTEGER PRIMARY KEY,
                    kind TEXT NOT NULL,       -- lesson_q, lesson_done, lab_step, lab_done...
                    ref TEXT NOT NULL,        -- identificador únic de l'element
                    amount INTEGER NOT NULL,
                    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                    UNIQUE(kind, ref)
                );
                CREATE TABLE lab_state (
                    lab_id TEXT PRIMARY KEY,
                    current_step INTEGER NOT NULL DEFAULT 0,
                    code TEXT,
                    started_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
                    completed_at TEXT,
                    seconds INTEGER NOT NULL DEFAULT 0
                );
                CREATE TABLE lab_steps (
                    lab_id TEXT NOT NULL,
                    step INTEGER NOT NULL,
                    attempts INTEGER NOT NULL DEFAULT 0,
                    fails INTEGER NOT NULL DEFAULT 0,
                    hints_used INTEGER NOT NULL DEFAULT 0,
                    solution_seen INTEGER NOT NULL DEFAULT 0,
                    passed_at TEXT,
                    PRIMARY KEY (lab_id, step)
                );
                CREATE TABLE concept_stats (
                    concept TEXT PRIMARY KEY,
                    fails INTEGER NOT NULL DEFAULT 0,
                    hints INTEGER NOT NULL DEFAULT 0,
                    successes INTEGER NOT NULL DEFAULT 0
                );
                CREATE TABLE lesson_answers (
                    lesson_id TEXT NOT NULL,
                    question_id TEXT NOT NULL,
                    attempts INTEGER NOT NULL DEFAULT 0,
                    correct INTEGER NOT NULL DEFAULT 0,
                    -- per al repàs espaciat (fase 3)
                    last_wrong_at TEXT,
                    PRIMARY KEY (lesson_id, question_id)
                );
                CREATE TABLE lesson_state (
                    lesson_id TEXT PRIMARY KEY,
                    completed_at TEXT
                );

                -- Tutor IA
                CREATE TABLE tutor_messages (
                    id INTEGER PRIMARY KEY,
                    lab_id TEXT NOT NULL,
                    step INTEGER NOT NULL,
                    role TEXT NOT NULL,       -- user | assistant
                    content TEXT NOT NULL,
                    mode TEXT NOT NULL,       -- ia | offline
                    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
                );
                CREATE TABLE ai_usage (
                    day TEXT PRIMARY KEY,
                    requests INTEGER NOT NULL DEFAULT 0,
                    input_tokens INTEGER NOT NULL DEFAULT 0,
                    output_tokens INTEGER NOT NULL DEFAULT 0
                );

                -- Fase 2: contingut generat per la IA (caché)
                CREATE TABLE generated (
                    id INTEGER PRIMARY KEY,
                    kind TEXT NOT NULL,
                    cache_key TEXT NOT NULL UNIQUE,
                    source TEXT,
                    json TEXT NOT NULL,
                    created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
                );
                SQL,
        ];

        $pdo->exec('CREATE TABLE IF NOT EXISTS schema_version (version INTEGER NOT NULL)');
        $current = (int) $pdo->query('SELECT COALESCE(MAX(version), 0) FROM schema_version')->fetchColumn();

        foreach ($migrations as $version => $sql) {
            if ($version > $current) {
                $pdo->beginTransaction();
                $pdo->exec($sql);
                $pdo->prepare('INSERT INTO schema_version (version) VALUES (?)')->execute([$version]);
                $pdo->commit();
            }
        }
    }
}
