<?php
/**
 * Lliçó: Galetes i sessions (Tema 5, apartats 5.10–5.12 del mòdul 0376).
 * Ampliació: encara no hi ha apunts de classe d'aquests apartats.
 * A la consola no hi ha navegador ni galetes: els exemples fan servir arrays per veure la idea.
 */

$origen = 'Temari_M0376.pdf (5.10–5.12)';

return [
    'id' => 'sessions-galetes',
    'ordre' => 330,
    'titol' => 'Galetes, sessions i inici de sessió',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.10–5.12 Autenticació, control d\'accessos i sessions',
    'origen' => $origen,
    'minuts' => 25,
    'xp_final' => 20,
    'lab' => 'zona-privada',
    'resum' => 'Per què HTTP no recorda res, galetes amb setcookie(), sessions amb $_SESSION, contrasenyes amb password_hash() i protegir pàgines.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'HTTP no té memòria',
            'ampliacio' => true,
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>Cada petició HTTP és independent: quan obres <code>privada.php</code>, el servidor no sap que fa un moment has enviat el formulari
                d'inici de sessió. Per «recordar», hi ha dues eines:</p>
                <table class="taula">
                  <tr><th></th><th>Galeta (cookie)</th><th>Sessió</th></tr>
                  <tr><td>On es guarda</td><td>Al navegador</td><td>Al servidor (el navegador només guarda l'identificador, en una galeta)</td></tr>
                  <tr><td>A PHP</td><td><code>setcookie()</code> i <code>$_COOKIE</code></td><td><code>session_start()</code> i <code>$_SESSION</code></td></tr>
                  <tr><td>Qui la pot modificar</td><td>L'usuari (no t'hi refiïs)</td><td>Només el teu codi</td></tr>
                  <tr><td>Per a</td><td>Preferències: idioma, tema fosc...</td><td>Qui ha iniciat la sessió, el carret de la compra...</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Galetes',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <pre class="code">// Desa l'idioma triat durant 30 dies (s'ha de cridar ABANS de qualsevol echo o HTML)
                setcookie('idioma', 'ca', time() + 30 * 24 * 3600, '/');

                // A les peticions següents (no a la mateixa!)
                $idioma = $_COOKIE['idioma'] ?? 'ca';

                // Esborrar-la: data de caducitat en el passat
                setcookie('idioma', '', time() - 3600, '/');</pre>
                <p class="nota"><code>setcookie()</code> envia una capçalera <code>Set-Cookie</code>. El navegador la desa i, a partir de la petició <strong>següent</strong>,
                la torna a enviar: per això <code>$_COOKIE</code> encara no la té a la mateixa pàgina on l'has creada.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Sessions',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <pre class="code">session_start();                  // a l'inici de CADA pàgina que fa servir la sessió
                $_SESSION['usuari'] = 'melcior';  // desar
                echo $_SESSION['usuari'];         // llegir (a qualsevol altra pàgina)
                unset($_SESSION['carret']);       // esborrar una dada</pre>
                <p><code>session_start()</code> mira si el navegador ha enviat la galeta <code>PHPSESSID</code>. Si l'ha enviada, carrega les dades d'aquella sessió
                a <code>$_SESSION</code>. Si no, en crea una de nova i envia la galeta. Al final de la pàgina, PHP desa <code>$_SESSION</code> al servidor.</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['sessions'],
            'ampliacio' => true,
            'pregunta' => 'On es guarden les dades de <code>$_SESSION[\'usuari\']</code>?',
            'opcions' => ['En una galeta del navegador', 'Al servidor; el navegador només en guarda l\'identificador', 'A la URL', 'A $_POST'],
            'correcta' => 1,
            'explicacio' => 'Les dades es queden al servidor i el navegador només té la galeta PHPSESSID, l\'identificador. Per això l\'usuari no pot canviar el seu propi $_SESSION.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Contrasenyes: mai en clar',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <p>Les contrasenyes no es desen mai tal qual (ni a la base de dades ni al codi): es desa el seu <strong>hash</strong>, un resum
                del qual no es pot recuperar la contrasenya.</p>
                <ul>
                  <li><code>password_hash($contrasenya, PASSWORD_DEFAULT)</code> crea el hash. Cada vegada en surt un de diferent (porta «sal»).</li>
                  <li><code>password_verify($escrita, $hash)</code> diu si la contrasenya escrita correspon al hash.</li>
                </ul>
                <p class="nota">No facis servir <code>md5()</code> ni <code>sha1()</code> per a contrasenyes: són massa ràpids i es poden trencar per força bruta.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: password_hash i password_verify',
            'fitxer' => 'hash.php',
            'codi' => <<<'PHP'
                <?php
                $hash = password_hash('or-encens-mirra', PASSWORD_DEFAULT);
                echo "Hash: $hash" . PHP_EOL;
                echo "Un altre cop: " . password_hash('or-encens-mirra', PASSWORD_DEFAULT) . PHP_EOL . PHP_EOL;

                foreach (['or-encens-mirra', 'carbo', 'OR-ENCENS-MIRRA'] as $intent) {
                    echo str_pad($intent, 16) . ': ' . (password_verify($intent, $hash) ? 'correcta' : 'incorrecta') . PHP_EOL;
                }
                PHP,
            'html' => '<p>Executa-ho dues vegades: els hashos canvien, però <code>password_verify()</code> sempre funciona.</p>',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Protegir una pàgina',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <pre class="code">&lt;?php
                session_start();
                if (!isset($_SESSION['usuari'])) {
                    header('Location: index.php');   // fora!
                    exit;                            // imprescindible: header() no atura el programa
                }
                // A partir d'aquí, només hi arriba qui ha iniciat la sessió</pre>
                <p>Per tancar la sessió: <code>$_SESSION = [];</code>, esborrar la galeta i <code>session_destroy();</code>.
                En iniciar la sessió, <code>session_regenerate_id(true)</code> canvia l'identificador (així un identificador robat d'abans no serveix).</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['autenticació'],
            'ampliacio' => true,
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $hash = password_hash('neu', PASSWORD_DEFAULT);
                $intents = ['Neu', 'neu ', 'neu'];
                $bons = 0;
                foreach ($intents as $i) {
                    if (password_verify($i, $hash)) {
                        $bons++;
                    }
                }
                echo $bons . ' ' . (str_starts_with($hash, '$2y$') ? 'bcrypt' : 'altre');
                PHP,
            'resposta' => '1 bcrypt',
            'explicacio' => 'Només «neu» exacte és correcta: majúscules i espais compten. PASSWORD_DEFAULT fa servir bcrypt, i els seus hashos comencen per $2y$.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['sessions'],
            'ampliacio' => true,
            'pregunta' => 'Quina funció s\'ha de cridar a l\'inici de cada pàgina que fa servir <code>$_SESSION</code>?',
            'codi' => "___();\n\$_SESSION['visites'] = (\$_SESSION['visites'] ?? 0) + 1;",
            'respostes' => ['session_start'],
            'explicacio' => 'Sense session_start(), $_SESSION és un array buit que no es desa enlloc.',
        ],
        [
            'tipus' => 'ordenar',
            'id' => 'q4',
            'conceptes' => ['sessions', 'control d\'accessos'],
            'ampliacio' => true,
            'pregunta' => 'Ordena les línies d\'una pàgina protegida:',
            'elements' => [
                'session_start();',
                'if (!isset($_SESSION[\'usuari\'])) {',
                '    header(\'Location: index.php\');',
                '    exit;',
                '}',
                'echo "Benvingut, " . htmlspecialchars($_SESSION[\'usuari\']);',
            ],
            'explicacio' => 'Primer s\'obre la sessió, després es comprova i, si no hi ha usuari, redirecció i exit. Només després es mostra el contingut privat.',
        ],
    ],
];
