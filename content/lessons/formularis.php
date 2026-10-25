<?php
/**
 * Lliçó: Formularis amb GET i POST (Tema 5, apartat 5.9 del mòdul 0376).
 * Els exemples s'executen a la consola, sense navegador: per això omplen $_GET o $_POST a mà
 * («simulem el que enviaria el formulari»). Als laboratoris web, en canvi, ho envia un formulari de veritat.
 */

$origen = 'Temari_M0376.pdf (5.9) i Activitat de Nadal';

return [
    'id' => 'formularis',
    'ordre' => 310,
    'titol' => 'Formularis: GET i POST',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.9 Mecanismes d\'introducció d\'informació: formularis',
    'origen' => $origen,
    'minuts' => 25,
    'xp_final' => 20,
    'lab' => 'salutacio-web',
    'resum' => 'Com viatgen les dades d\'un formulari fins a PHP, la diferència entre GET i POST, validar al servidor i escapar amb htmlspecialchars().',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Del formulari a PHP',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>Un formulari HTML diu <strong>on</strong> s'envien les dades (<code>action</code>) i <strong>com</strong> (<code>method</code>).
                Cada camp arriba a PHP amb la clau del seu atribut <code>name</code>:</p>
                <pre class="code">&lt;form action="salutacio.php" method="get"&gt;
                    &lt;input type="text" name="nom"&gt;
                    &lt;button type="submit"&gt;Envia&lt;/button&gt;
                &lt;/form&gt;</pre>
                <p>Quan s'envia, el navegador demana <code>salutacio.php?nom=Anna</code> i, a <code>salutacio.php</code>, <code>$_GET['nom']</code> val <code>"Anna"</code>.
                Un camp sense <code>name</code> no s'envia.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'GET o POST?',
            'origen' => $origen,
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th></th><th>GET</th><th>POST</th></tr>
                  <tr><td>On van les dades</td><td>A la URL: <code>?nom=Anna&amp;edat=9</code></td><td>Dins de la petició (no es veuen a la URL)</td></tr>
                  <tr><td>A PHP</td><td><code>$_GET</code></td><td><code>$_POST</code> (i <code>$_FILES</code> per als fitxers)</td></tr>
                  <tr><td>Es pot desar com a enllaç</td><td>Sí</td><td>No</td></tr>
                  <tr><td>Per a</td><td>Cerques, filtres, paginació</td><td>Enviar dades: altes, contrasenyes, fitxers</td></tr>
                </table>
                <p>Per saber com ha arribat la petició: <code>$_SERVER['REQUEST_METHOD']</code> val <code>"GET"</code> o <code>"POST"</code>.</p>
                <p class="nota">POST no és «segur» pel sol fet de no veure's a la URL: qualsevol pot veure i modificar el que s'envia. La seguretat la dona HTTPS.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: llegir $_GET sense errors',
            'fitxer' => 'salutacio.php',
            'codi' => <<<'PHP'
                <?php
                // A la consola no hi ha formulari: simulem el que enviaria salutacio.php?nom=Anna
                $_GET = ['nom' => 'Anna'];

                $nom = $_GET['nom'] ?? 'desconegut';
                $idioma = $_GET['idioma'] ?? 'ca';   // aquest camp no ha arribat: fem servir el valor per defecte

                echo "Nom: $nom" . PHP_EOL;
                echo "Idioma: $idioma" . PHP_EOL;
                echo "Hi ha 'edat'? " . (isset($_GET['edat']) ? 'sí' : 'no') . PHP_EOL;
                PHP,
            'html' => '<p>Canvia el contingut de <code>$_GET</code> i torna a executar. Prova també de treure el <code>??</code>: veuràs el Warning «Undefined array key».</p>',
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['formularis', 'GET'],
            'pregunta' => 'Un formulari té <code>&lt;input type="text" name="ciutat"&gt;</code> i <code>method="post"</code>. On és el valor a PHP?',
            'opcions' => ['$_GET[\'ciutat\']', '$_POST[\'ciutat\']', '$_POST[\'text\']', '$_SERVER[\'ciutat\']'],
            'correcta' => 1,
            'explicacio' => 'El mètode diu quin array s\'omple ($_POST) i el name diu la clau (ciutat).',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Validar sempre al servidor',
            'origen' => 'Activitat de Nadal, part 2',
            'html' => <<<'HTML'
                <p>Els atributs <code>required</code>, <code>type="number"</code> o <code>min</code>/<code>max</code> ajuden l'usuari, però <strong>no protegeixen res</strong>:
                qualsevol pot enviar una petició sense passar pel teu formulari. La validació de veritat es fa a PHP.</p>
                <p>Patró habitual: guardar els errors en un array i, al final, decidir què es mostra.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: validar un formulari',
            'fitxer' => 'processa.php',
            'codi' => <<<'PHP'
                <?php
                // Simulem un formulari enviat amb POST amb algunes dades malament
                $_POST = ['nom' => '   ', 'edat' => 'nou', 'actitud' => '12'];

                $nom = trim($_POST['nom'] ?? '');
                $edat = $_POST['edat'] ?? '';
                $actitud = (int) ($_POST['actitud'] ?? 0);

                $errors = [];
                if ($nom === '') {
                    $errors[] = 'Falta el nom.';
                }
                if (!ctype_digit($edat)) {
                    $errors[] = "L'edat no és vàlida.";
                }
                if ($actitud < 1 || $actitud > 10) {
                    $errors[] = "L'actitud ha de ser entre 1 i 10.";
                }

                if ($errors) {
                    echo "Hi ha " . count($errors) . " errors:" . PHP_EOL;
                    foreach ($errors as $error) {
                        echo "- $error" . PHP_EOL;
                    }
                } else {
                    echo "Tot correcte!" . PHP_EOL;
                }
                PHP,
            'html' => '<p>Corregeix les dades de <code>$_POST</code> fins que surti «Tot correcte!».</p>',
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['validació de formularis'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $_POST = ['edat' => '9', 'nom' => ' Pau '];
                $errors = [];
                if (!ctype_digit($_POST['edat'] ?? '')) {
                    $errors[] = 'edat';
                }
                if (trim($_POST['nom'] ?? '') === '') {
                    $errors[] = 'nom';
                }
                if (($_POST['adreca'] ?? '') === '') {
                    $errors[] = 'adreca';
                }
                echo count($errors) . ' ' . implode(',', $errors);
                PHP,
            'resposta' => '1 adreca',
            'explicacio' => 'L\'edat «9» és un número i el nom, sense espais, és «Pau». L\'adreça no ha arribat: ?? \'\' la deixa buida i és l\'únic error.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'htmlspecialchars(): el que escriu l\'usuari, sempre com a text',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>Si mostres el que ha escrit l'usuari tal qual i algú escriu <code>&lt;script&gt;...&lt;/script&gt;</code>, el navegador ho executarà a la teva pàgina.
                Aquest atac s'anomena <strong>XSS</strong> (<em>cross-site scripting</em>).</p>
                <p><code>htmlspecialchars()</code> converteix els caràcters especials en entitats: <code>&lt;</code> → <code>&amp;lt;</code>, <code>&gt;</code> → <code>&amp;gt;</code>,
                <code>&amp;</code> → <code>&amp;amp;</code>, <code>"</code> → <code>&amp;quot;</code>. El navegador els mostra però no els interpreta.</p>
                <p>Regla: <strong>escapa en el moment de mostrar</strong>, a cada <code>echo</code> o <code>&lt;?= ?&gt;</code> amb dades de l'usuari.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q3',
            'conceptes' => ['htmlspecialchars'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $nom = '<b>Anna</b> & Joan';
                echo htmlspecialchars($nom);
                PHP,
            'resposta' => '&lt;b&gt;Anna&lt;/b&gt; &amp; Joan',
            'explicacio' => 'Cada < i > es converteix en &lt; i &gt;, i el & en &amp;. Al navegador es veuria «<b>Anna</b> & Joan» com a text, sense negreta.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['formularis', 'POST'],
            'pregunta' => 'Volem saber si la pàgina s\'ha obert enviant un formulari amb POST. Què hi falta?',
            'codi' => "if (\$_SERVER['___'] === 'POST') {",
            'respostes' => ['REQUEST_METHOD'],
            'explicacio' => '$_SERVER[\'REQUEST_METHOD\'] diu el mètode de la petició: "GET" quan obres l\'enllaç, "POST" quan s\'envia un formulari amb method="post".',
        ],
        [
            'tipus' => 'ordenar',
            'id' => 'q5',
            'conceptes' => ['formularis'],
            'pregunta' => 'Ordena què passa des que omples un formulari amb method="get" fins que veus la resposta:',
            'elements' => [
                'L\'usuari omple els camps i prem el botó d\'enviar',
                'El navegador posa les dades a la URL (?nom=Anna) i demana la pàgina de l\'action',
                'El servidor executa el PHP, que llegeix $_GET[\'nom\']',
                'PHP genera l\'HTML de la resposta',
                'El navegador mostra la pàgina nova',
            ],
            'explicacio' => 'El formulari només prepara la petició. Qui fa la feina és el PHP de la pàgina d\'action, al servidor, i el navegador només rep l\'HTML resultant.',
        ],
    ],
];
