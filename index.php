<?php
/**
 * Punt d'entrada de les pàgines: index.php?p=nom
 */
declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';

use PhpLab\{Auth, View, Labs, Lessons, Indexer, Uploader, Progress, Tutor, Db, Config};

$page = $_GET['p'] ?? 'inici';

// ---- Pàgines públiques: configuració inicial i login ----
if (!Auth::hasUser()) {
    if (!Auth::isLocalRequest()) {
        http_response_code(403);
        exit('PHPLab encara no està configurat. Obre http://localhost/phplab des del PC servidor per crear l\'usuari.');
    }
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = trim($_POST['usuari'] ?? '');
        $pass = (string) ($_POST['contrasenya'] ?? '');
        if (!Auth::checkCsrf($_POST['csrf'] ?? null)) {
            $error = 'La sessió ha caducat. Torna-ho a provar.';
        } elseif (!preg_match('/^[a-zA-Z0-9_.\-]{3,30}$/', $user)) {
            $error = 'L\'usuari ha de tenir entre 3 i 30 caràcters (lletres, números, _ . -).';
        } elseif (mb_strlen($pass) < 10) {
            $error = 'La contrasenya ha de tenir com a mínim 10 caràcters.';
        } elseif ($pass !== ($_POST['contrasenya2'] ?? '')) {
            $error = 'Les contrasenyes no coincideixen.';
        } else {
            Auth::createUser($user, $pass);
            Auth::attempt($user, $pass, $_SERVER['REMOTE_ADDR']);
            header('Location: index.php');
            exit;
        }
    }
    View::render('setup', ['error' => $error, 'title' => 'Configuració inicial'], false);
    exit;
}

if ($page === 'login' || Auth::user() === null) {
    $error = null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $locked = Auth::lockedMinutes($ip);
        if (!Auth::checkCsrf($_POST['csrf'] ?? null)) {
            $error = 'La sessió ha caducat. Torna-ho a provar.';
        } elseif ($locked > 0) {
            $error = "Massa intents fallits. Espera $locked minut(s).";
        } elseif (Auth::attempt(trim($_POST['usuari'] ?? ''), (string) ($_POST['contrasenya'] ?? ''), $ip)) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuari o contrasenya incorrectes.';
        }
    }
    View::render('login', ['error' => $error, 'title' => 'Entrar'], false);
    exit;
}

// ---- A partir d'aquí, cal estar identificat ----

if ($page === 'sortir') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::checkCsrf($_POST['csrf'] ?? null)) {
        Auth::logout();
    }
    header('Location: index.php');
    exit;
}

// Formularis POST de les pàgines (no JSON): pujar temari, reindexar, reiniciar laboratori
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::checkCsrf($_POST['csrf'] ?? null)) {
        http_response_code(400);
        exit('Token CSRF no vàlid. Torna enrere i recarrega la pàgina.');
    }
    $action = $_POST['accio'] ?? '';
    if ($action === 'pujar') {
        if (empty($_FILES['fitxers']) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            View::flash('error', 'La pujada és massa gran per a la configuració de PHP (post_max_size). Mira el README.');
        } else {
            $up = Uploader::handle($_FILES['fitxers'] ?? []);
            $idx = Indexer::reindex();
            View::flash('ok', ['pujada' => $up, 'index' => $idx]);
        }
        header('Location: index.php?p=temari');
        exit;
    }
    if ($action === 'reindexar') {
        View::flash('ok', ['index' => Indexer::reindex()]);
        header('Location: index.php?p=temari');
        exit;
    }
    if ($action === 'reiniciar_lab' && ($lab = Labs::get($_POST['lab'] ?? ''))) {
        Labs::reset($lab);
        header('Location: index.php?p=lab&id=' . urlencode($lab['id']));
        exit;
    }
    header('Location: index.php');
    exit;
}

$common = ['progress' => Progress::summary(), 'page' => $page];

switch ($page) {
    case 'inici':
        $topics = Db::all("SELECT topic_name, topic_num, COUNT(*) n, SUM(status = 'ok') ok
                           FROM files GROUP BY topic_name ORDER BY topic_num IS NULL, topic_num, topic_name");
        $lessons = [];
        foreach (Lessons::all() as $l) {
            $lessons[] = $l + ['progres' => Lessons::progress($l)];
        }
        $labs = [];
        foreach (Labs::all() as $lab) {
            $st = Labs::state($lab);
            $labs[] = $lab + ['estat' => $st];
        }
        $concepts = Db::all('SELECT * FROM concept_stats ORDER BY (fails + hints) DESC LIMIT 6');
        View::render('inici', $common + ['title' => 'Inici', 'topics' => $topics, 'lessons' => $lessons, 'labs' => $labs, 'concepts' => $concepts]);
        break;

    case 'leccio':
        $lesson = Lessons::get($_GET['id'] ?? '');
        if (!$lesson) {
            http_response_code(404);
            exit('Lliçó no trobada');
        }
        View::render('leccio', $common + [
            'title' => $lesson['titol'],
            'lesson' => $lesson,
            'answers' => Lessons::answers($lesson['id']),
            'progres' => Lessons::progress($lesson),
        ]);
        break;

    case 'lab':
        $lab = Labs::get($_GET['id'] ?? '');
        if (!$lab) {
            http_response_code(404);
            exit('Laboratori no trobat');
        }
        $state = Labs::state($lab);
        View::render('lab', $common + [
            'title' => $lab['titol'],
            'lab' => $lab,
            'state' => $state,
            'summary' => $state['completed_at'] ? Labs::summary($lab) : null,
            'history' => array_reverse(Tutor::history($lab['id'])),
            'ai' => Tutor::aiAvailable(),
        ]);
        break;

    case 'temari':
        $files = Db::all('SELECT id, path, ext, size, topic_name, topic_num, kind, status, message, indexed_at, length(text) chars
                          FROM files ORDER BY topic_num IS NULL, topic_num, topic_name, path');
        View::render('temari', $common + ['title' => 'Temari', 'files' => $files, 'flash' => View::flash()]);
        break;

    case 'diagnostic':
        View::render('diagnostic', $common + ['title' => 'Diagnòstic']);
        break;

    default:
        http_response_code(404);
        echo 'Pàgina no trobada';
}
