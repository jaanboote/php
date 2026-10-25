<?php
/**
 * Laboratori web: Zona privada amb sessions.
 * Apartats 5.10 (autenticació d'usuaris), 5.11 (control d'accessos) i 5.12 (sessions) del mòdul 0376.
 * Encara no hi ha material de classe d'aquests apartats: és contingut d'ampliació.
 */

$index = <<<'HTML'
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Taller dels Reis</title>
</head>
<body>
    <h1>Inicia la sessió</h1>
    <p>Només els Reis poden entrar al taller.</p>
    <form action="login.php" method="post">
        <p><label>Usuari: <input type="text" name="usuari"></label></p>
        <p><label>Contrasenya: <input type="password" name="contrasenya"></label></p>
        <button type="submit">Entra</button>
    </form>
</body>
</html>
HTML;

$login = <<<'PHP'
<?php
// Comprova l'usuari i la contrasenya i, si són correctes, obre la sessió
session_start();

const USUARI = 'melcior';
// La contrasenya mai es desa tal qual: només el seu hash (password_hash("or-encens-mirra", PASSWORD_DEFAULT))
const HASH = '$2y$10$FDf9eXcRupu50xX0dymcae4hNm18LfzNHV9Rdoa3VgxK3TTSaBKUm';

$usuari = trim($_POST['usuari'] ?? '');
$contrasenya = $_POST['contrasenya'] ?? '';

if ($usuari === USUARI && password_verify($contrasenya, HASH)) {
    session_regenerate_id(true);   // identificador de sessió nou en entrar (seguretat)
    $_SESSION['usuari'] = $usuari;
    header('Location: privada.php');
    exit;
}
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Taller dels Reis</title>
</head>
<body>
    <p>Usuari o contrasenya incorrectes.</p>
    <p><a href="index.php">Torna-ho a provar</a></p>
</body>
</html>
PHP;

$privada2 = <<<'PHP'
<?php
// Zona privada: només s'hi pot entrar amb la sessió iniciada
session_start();

if (!isset($_SESSION['usuari'])) {
    header('Location: index.php');
    exit;
}

// Comptador de visites guardat a la sessió
$_SESSION['visites'] = ($_SESSION['visites'] ?? 0) + 1;
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Taller dels Reis</title>
</head>
<body>
    <h1>Benvingut, <?= htmlspecialchars($_SESSION['usuari']) ?>!</h1>
    <p>Visites al taller en aquesta sessió: <?= $_SESSION['visites'] ?></p>
    <p><a href="sortir.php">Tanca la sessió</a></p>
</body>
</html>
PHP;

$sortir = <<<'PHP'
<?php
// Tanca la sessió: buida les dades, esborra la galeta i destrueix la sessió al servidor
session_start();
$_SESSION = [];
setcookie(session_name(), '', time() - 3600, '/');
session_destroy();

header('Location: index.php');
exit;
PHP;

$inicialLogin = "<?php\n// Comprova l'usuari i la contrasenya\n";
$inicialPrivada = "<?php\n// Zona privada: només per a qui ha iniciat la sessió\n?>\n<h1>Taller dels Reis</h1>\n";
$inicialSortir = "<?php\n// Tanca la sessió\n";

$bo = ['usuari' => 'melcior', 'contrasenya' => 'or-encens-mirra'];
$entra = ['POST', 'login.php', 'camps' => $bo];

return [
    'id' => 'zona-privada',
    'tipus' => 'web',
    'ordre' => 330,
    'titol' => 'Zona privada amb sessions',
    'fitxer' => 'index.php',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.10–5.12 Autenticació, control d\'accessos i sessions',
    'origen' => [
        'fitxer' => 'Temari_M0376.pdf',
        'descripcio' => 'Ampliació: apartats 5.10, 5.11 i 5.12 del mòdul 0376 (encara no hi ha material de classe)',
    ],
    'resum' => 'Un inici de sessió amb password_verify(), una pàgina protegida que recorda qui ets amb $_SESSION i un botó per sortir.',
    'xp_final' => 50,
    'paraules_clau' => ['session_start', '$_SESSION', 'password_verify', 'header', 'session_destroy'],
    'codi_inicial' => ['index.php' => $index, 'login.php' => $inicialLogin, 'privada.php' => $inicialPrivada, 'sortir.php' => $inicialSortir],

    'context' => <<<'HTML'
        <p>El taller dels Reis és secret: només hi pot entrar en <strong>melcior</strong> (contrasenya <code>or-encens-mirra</code>).
        El formulari d'<code>index.php</code> ja està fet: tu has d'escriure la resta.</p>
        <p>HTTP no té memòria: cada petició és nova. Per recordar qui ha entrat, PHP fa servir una <strong>sessió</strong>. Al servidor
        guarda les dades de <code>$_SESSION</code>, i al navegador una galeta (<code>PHPSESSID</code>) que les identifica.
        A l'inspector veuràs les dues coses.</p>
        <p class="nota">Ampliació: el temari del mòdul inclou autenticació, control d'accessos i sessions (5.10–5.12), però encara no en tens
        els apunts de classe.</p>
        HTML,

    'resum_apres' => [
        'session_start() a l\'inici de cada pàgina que fa servir la sessió.',
        'Guardar dades a $_SESSION i recuperar-les a qualsevol altra pàgina.',
        'Comprovar una contrasenya amb password_verify() contra un hash (mai la contrasenya en clar).',
        'Protegir una pàgina: si no hi ha sessió, redirigir amb header(\'Location: ...\') i exit.',
        'Tancar la sessió: buidar $_SESSION, esborrar la galeta i session_destroy().',
    ],

    'passos' => [
        [
            'titol' => 'Iniciar la sessió',
            'conceptes' => ['sessions', 'autenticació'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> a <code>login.php</code>:</p>
                <ul>
                  <li>Crida <code>session_start()</code> al principi.</li>
                  <li>Comprova que l'usuari sigui <code>melcior</code> i que la contrasenya coincideixi amb aquest hash amb
                  <code>password_verify()</code>:<br><code>$2y$10$FDf9eXcRupu50xX0dymcae4hNm18LfzNHV9Rdoa3VgxK3TTSaBKUm</code></li>
                  <li>Si és correcte: guarda l'usuari a <code>$_SESSION['usuari']</code> i redirigeix a <code>privada.php</code>.</li>
                  <li>Si no: mostra <code>Usuari o contrasenya incorrectes.</code> i un enllaç a <code>index.php</code>.</li>
                </ul>
                <p class="nota">Desa el hash amb cometes simples: dins de cometes dobles, PHP intentaria llegir <code>$2y</code> com una variable.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'login.php', 'regex' => '/password_verify\s*\(/', 'missatge' => 'Comprova la contrasenya amb password_verify().'],
                ['fitxer' => 'login.php', 'no_regex' => '/or-encens-mirra/', 'missatge' => 'La contrasenya no pot aparèixer al codi: compara amb el hash.'],
            ],
            'proves' => [
                ['nom' => 'Usuari i contrasenya correctes', 'peticions' => [$entra], 'redirigeix' => 'privada.php'],
                ['nom' => 'Contrasenya incorrecta', 'peticions' => [['POST', 'login.php', 'camps' => ['contrasenya' => 'carbo'] + $bo]],
                 'text' => ['Usuari o contrasenya incorrectes.'], 'sense_redireccio' => true],
                ['nom' => 'Usuari incorrecte', 'peticions' => [['POST', 'login.php', 'camps' => ['usuari' => 'gaspar'] + $bo]],
                 'text' => ['Usuari o contrasenya incorrectes.'], 'sense_redireccio' => true],
                ['nom' => 'Formulari buit', 'peticions' => [['POST', 'login.php', 'camps' => []]], 'text' => ['Usuari o contrasenya incorrectes.']],
            ],
            'pistes' => [
                'Obre la sessió, llegeix usuari i contrasenya de $_POST i compara. Si és correcte, desa l\'usuari a la sessió i envia el navegador a privada.php.',
                'password_verify($contrasenya, HASH) retorna true si la contrasenya correspon al hash. Després: $_SESSION[\'usuari\'] = $usuari; header(\'Location: privada.php\'); exit;',
                "session_start();\nconst HASH = '\$2y\$10\$FDf9...';\n\$usuari = trim(\$_POST['usuari'] ?? '');\n\$contrasenya = \$_POST['contrasenya'] ?? '';\nif (\$usuari === 'melcior' && password_verify(\$contrasenya, HASH)) {\n    \$_SESSION['usuari'] = \$usuari;\n    header('Location: privada.php');\n    exit;\n}\n?>\n<p>Usuari o contrasenya incorrectes.</p>",
            ],
            'preguntes' => [
                'Per què es desa un hash i no la contrasenya?',
                'Què fa exit després de header(\'Location: ...\')?',
            ],
            'solucio' => ['index.php' => $index, 'login.php' => $login, 'privada.php' => $inicialPrivada, 'sortir.php' => $inicialSortir],
            'solucio_explicacio' => 'password_verify() calcula el hash de la contrasenya escrita i el compara amb el desat. La sessió guarda l\'usuari al servidor, i el navegador només en té l\'identificador.',
        ],
        [
            'titol' => 'Protegir la zona privada',
            'conceptes' => ['sessions', 'control d\'accessos'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Ara mateix qualsevol pot escriure <code>privada.php</code> a l'adreça i entrar-hi. <strong>Objectiu:</strong> a <code>privada.php</code>:</p>
                <ul>
                  <li><code>session_start()</code> i, si no hi ha <code>$_SESSION['usuari']</code>, redirigeix a <code>index.php</code> (i <code>exit</code>).</li>
                  <li>Si hi és, mostra <code>Benvingut, melcior!</code> (amb <code>htmlspecialchars</code>).</li>
                  <li>Compta les visites a <code>$_SESSION['visites']</code> i mostra <code>Visites al taller en aquesta sessió: 1</code> (2, 3...).</li>
                  <li>Afegeix un enllaç a <code>sortir.php</code>.</li>
                </ul>
                <p>Prova-ho: entra, recarrega la pàgina unes quantes vegades i mira com canvia <code>$_SESSION</code> a l'inspector.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'privada.php', 'regex' => '/session_start\s*\(/', 'missatge' => 'privada.php també ha de cridar session_start().'],
            ],
            'proves' => [
                ['nom' => 'Sense iniciar la sessió', 'peticions' => [['GET', 'privada.php']], 'redirigeix' => 'index.php', 'no_text' => ['Benvingut']],
                ['nom' => 'Després d\'entrar', 'peticions' => [$entra], 'text' => ['Benvingut, melcior!', 'Visites al taller en aquesta sessió: 1'],
                 'conte_regex' => ['/<a[^>]*href=["\']sortir\.php["\']/i' => 'un enllaç a sortir.php']],
                ['nom' => 'Les visites es compten', 'peticions' => [$entra, ['GET', 'privada.php'], ['GET', 'privada.php']],
                 'text' => ['Visites al taller en aquesta sessió: 3']],
                ['nom' => 'Una contrasenya incorrecta no obre la porta', 'peticions' => [['POST', 'login.php', 'camps' => ['contrasenya' => 'x'] + $bo], ['GET', 'privada.php']],
                 'redirigeix' => 'index.php', 'no_text' => ['Benvingut']],
            ],
            'pistes' => [
                'La primera cosa que fa la pàgina és mirar si a la sessió hi ha un usuari. Si no n\'hi ha, la porta es tanca (redirecció). Si n\'hi ha, suma una visita i saluda.',
                'if (!isset($_SESSION[\'usuari\'])) { header(\'Location: index.php\'); exit; }  ·  $_SESSION[\'visites\'] = ($_SESSION[\'visites\'] ?? 0) + 1;',
                "session_start();\nif (!isset(\$_SESSION['usuari'])) {\n    header('Location: index.php');\n    exit;\n}\n\$_SESSION['visites'] = (\$_SESSION['visites'] ?? 0) + 1;\n?>\n<h1>Benvingut, <?= htmlspecialchars(\$_SESSION['usuari']) ?>!</h1>\n<p>Visites al taller en aquesta sessió: <?= \$_SESSION['visites'] ?></p>\n<a href=\"sortir.php\">Tanca la sessió</a>",
            ],
            'preguntes' => [
                'On es guarden les dades de $_SESSION: al navegador o al servidor?',
                'Què passaria si oblidessis l\'exit després de la redirecció?',
            ],
            'solucio' => ['index.php' => $index, 'login.php' => $login, 'privada.php' => $privada2, 'sortir.php' => $inicialSortir],
            'solucio_explicacio' => 'Cada pàgina protegida comença comprovant la sessió. L\'exit és imprescindible: header() només afegeix una capçalera, i sense exit la resta de la pàgina s\'enviaria igualment.',
        ],
        [
            'titol' => 'Tancar la sessió',
            'conceptes' => ['sessions'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> a <code>sortir.php</code>, tanca la sessió del tot i torna a <code>index.php</code>:</p>
                <ol>
                  <li><code>session_start()</code> (per poder-la tancar, primer cal obrir-la)</li>
                  <li><code>$_SESSION = [];</code> buida les dades</li>
                  <li><code>setcookie(session_name(), '', time() - 3600, '/');</code> esborra la galeta del navegador</li>
                  <li><code>session_destroy();</code> esborra la sessió del servidor</li>
                  <li>redirigeix a <code>index.php</code></li>
                </ol>
                <p>Comprova a l'inspector que, després de sortir, la galeta <code>PHPSESSID</code> desapareix i <code>privada.php</code> ja no et deixa entrar.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'sortir.php', 'regex' => '/session_destroy\s*\(/', 'missatge' => 'Destrueix la sessió amb session_destroy().'],
            ],
            'proves' => [
                ['nom' => 'Sortir torna a l\'inici', 'peticions' => [$entra, ['GET', 'sortir.php']], 'redirigeix' => 'index.php', 'text' => ['Inicia la sessió']],
                ['nom' => 'Després de sortir, la zona privada torna a estar tancada', 'peticions' => [$entra, ['GET', 'sortir.php'], ['GET', 'privada.php']],
                 'text' => ['Inicia la sessió'], 'no_text' => ['Benvingut']],
                ['nom' => 'Es pot tornar a entrar i el comptador comença de nou', 'peticions' => [$entra, ['GET', 'privada.php'], ['GET', 'sortir.php'], $entra],
                 'text' => ['Visites al taller en aquesta sessió: 1']],
            ],
            'pistes' => [
                'Tancar una sessió té tres parts: les dades ($_SESSION), la galeta del navegador i la sessió del servidor. Després, de tornada a l\'inici.',
                'session_start(); $_SESSION = []; setcookie(session_name(), \'\', time() - 3600, \'/\'); session_destroy(); header(\'Location: index.php\'); exit;',
                "<?php\nsession_start();\n\$_SESSION = [];\nsetcookie(session_name(), '', time() - 3600, '/');\nsession_destroy();\nheader('Location: index.php');\nexit;",
            ],
            'preguntes' => [
                'Per què cal session_start() per tancar una sessió?',
                'Per què posem una data de caducitat en el passat a la galeta?',
            ],
            'solucio' => ['index.php' => $index, 'login.php' => $login, 'privada.php' => $privada2, 'sortir.php' => $sortir],
            'solucio_explicacio' => 'Buidar $_SESSION treu les dades de la petició actual, session_destroy() esborra el fitxer de sessió del servidor i la galeta caducada fa que el navegador oblidi l\'identificador.',
        ],
    ],
];
