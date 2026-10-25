<?php
/**
 * API JSON per al JavaScript de les pàgines: api.php?a=accio
 * Totes les accions: POST, sessió iniciada i capçalera X-CSRF-Token.
 */
declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use PhpLab\{Auth, Sandbox, ErrorExplainer, Labs, Lessons, Tutor, Progress};

header('Content-Type: application/json; charset=utf-8');

function respond(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (Auth::user() === null) {
    respond(['error' => 'Has de tornar a entrar (la sessió ha caducat).'], 401);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Auth::checkCsrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
    respond(['error' => 'Petició no vàlida (CSRF). Recarrega la pàgina.'], 400);
}

$in = json_decode((string) file_get_contents('php://input'), true);
if (!is_array($in)) {
    respond(['error' => 'Format no vàlid'], 400);
}
$code = (string) ($in['code'] ?? '');
if (strlen($code) > 50000) {
    respond(['error' => 'El codi és massa llarg (màxim 50 KB).'], 400);
}

/** Carrega el laboratori indicat o respon amb error. */
function lab(array $in): array
{
    return Labs::get((string) ($in['lab'] ?? '')) ?? respond(['error' => 'Laboratori no trobat'], 404);
}

// Les sessions PHP bloquegen peticions simultànies; com que aquí ja no escrivim a $_SESSION, l'alliberem.
session_write_close();

try {
    switch ($_GET['a'] ?? '') {
        case 'run':
            $args = Sandbox::parseArgs((string) ($in['args'] ?? ''));
            // Als laboratoris, els fitxers que crea el programa es conserven entre execucions
            $lab = isset($in['lab']) ? Labs::get((string) $in['lab']) : null;
            // Als exemples de les lliçons, els fitxers de partida els defineix el contingut (no el navegador)
            $lesson = isset($in['lesson']) ? Lessons::get((string) $in['lesson']) : null;
            $files = $lesson['blocs'][(int) ($in['bloc'] ?? -1)]['fitxers'] ?? [];
            $r = Sandbox::run($code, $args, (string) ($in['stdin'] ?? ''), (string) ($in['fitxer'] ?? 'main.php'), null,
                              $files, $lab ? Sandbox::workspace($lab['id']) : null);
            $r['explicacions'] = ErrorExplainer::explain($r['stderr'], $r['timeout'], $r['truncated']);
            $r['argv'] = array_merge([basename((string) ($in['fitxer'] ?? 'main.php'))], $args);
            respond($r);

        case 'lesson_answer':
            $lesson = Lessons::get((string) ($in['lesson'] ?? '')) ?? respond(['error' => 'Lliçó no trobada'], 404);
            $res = Lessons::answer($lesson, (string) ($in['q'] ?? ''), (string) ($in['answer'] ?? ''));
            respond($res + ['progress' => Progress::summary()]);

        case 'lab_save':
            Labs::saveCode(lab($in)['id'], $code);
            respond(['ok' => true]);

        case 'lab_check':
            $res = Labs::check(lab($in), (int) ($in['step'] ?? 0), $code);
            respond($res + ['progress' => Progress::summary()]);

        case 'lab_hint':
            respond(Labs::hint(lab($in), (int) ($in['step'] ?? 0), (int) ($in['level'] ?? 1)));

        case 'lab_solution':
            respond(Labs::solution(lab($in), (int) ($in['step'] ?? 0)));

        case 'lab_files':
            $lab = lab($in);
            if (!empty($in['buidar'])) {
                Sandbox::clearWorkspace($lab['id']);
            }
            respond(['files' => Sandbox::listFiles(Sandbox::workspace($lab['id']))]);

        case 'lab_summary':
            respond(Labs::summary(lab($in)));

        case 'tutor':
            $lab = lab($in);
            $step = (int) ($in['step'] ?? 0);
            if (!isset($lab['passos'][$step])) {
                respond(['error' => 'Pas inexistent'], 400);
            }
            $ctx = [
                'code' => $code,
                'args' => (string) ($in['args'] ?? ''),
                'stdout' => mb_substr((string) ($in['stdout'] ?? ''), 0, 4000),
                'stderr' => mb_substr((string) ($in['stderr'] ?? ''), 0, 4000),
                'proves' => is_array($in['proves'] ?? null) ? array_slice($in['proves'], 0, 10) : [],
            ];
            $res = Tutor::reply($lab, $step, (string) ($in['message'] ?? ''), $ctx);
            respond($res + ['html' => mini_markdown($res['text'])]);

        default:
            respond(['error' => 'Acció desconeguda'], 404);
    }
} catch (InvalidArgumentException $e) {
    respond(['error' => $e->getMessage()], 400);
} catch (Throwable $e) {
    error_log('PHPLab API: ' . $e);
    respond(['error' => 'Error intern del servidor. Mira el log d\'Apache (C:\\xampp\\apache\\logs\\error.log).'], 500);
}
