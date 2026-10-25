<?php
/**
 * Lliçó: Pujar fitxers amb formularis (Tema 5, Activitat de Nadal).
 * A la consola no es pot pujar res: els exemples omplen $_FILES a mà per veure'n l'estructura i practicar la validació.
 */

$origen = 'Activitat de Nadal.pdf';

return [
    'id' => 'pujar-fitxers',
    'ordre' => 320,
    'titol' => 'Pujar fitxers: $_FILES',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.9 Formularis: pujada de fitxers',
    'origen' => $origen,
    'minuts' => 25,
    'xp_final' => 20,
    'lab' => 'carta-reis',
    'resum' => 'El formulari amb enctype, l\'estructura de $_FILES, els codis d\'error, validar extensió i mida i desar amb move_uploaded_file().',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'El formulari per pujar fitxers',
            'origen' => "$origen · part 1",
            'html' => <<<'HTML'
                <p>Per enviar fitxers, el formulari necessita dues coses: <code>method="post"</code> i <code>enctype="multipart/form-data"</code>.
                Sense l'enctype, el navegador només envia el <em>nom</em> del fitxer.</p>
                <pre class="code">&lt;form action="processa.php" method="post" enctype="multipart/form-data"&gt;
                    &lt;input type="file" name="imatge1" accept="image/*"&gt;
                    &lt;input type="text" name="descripcio1"&gt;
                    &lt;button type="submit"&gt;Envia&lt;/button&gt;
                &lt;/form&gt;</pre>
                <p class="nota"><code>accept="image/*"</code> només filtra el selector de fitxers del navegador: no és cap validació.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Què arriba a $_FILES',
            'origen' => "$origen · part 2",
            'html' => <<<'HTML'
                <p>Els camps de text van a <code>$_POST</code>; els fitxers, a <code>$_FILES</code>. PHP desa cada fitxer en una carpeta temporal i
                <strong>l'esborra en acabar la petició</strong>: si el vols conservar, l'has de moure amb <code>move_uploaded_file()</code>.</p>
                <table class="taula">
                  <tr><th>Clau</th><th>Què és</th></tr>
                  <tr><td>name</td><td>Nom original del fitxer a l'ordinador de l'usuari</td></tr>
                  <tr><td>type</td><td>Tipus MIME que diu el navegador (no t'hi refiïs: el pot falsejar)</td></tr>
                  <tr><td>tmp_name</td><td>On l'ha desat PHP temporalment</td></tr>
                  <tr><td>error</td><td><code>UPLOAD_ERR_OK</code> (0) si tot ha anat bé</td></tr>
                  <tr><td>size</td><td>Mida en bytes</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: recórrer tres camps de fitxer',
            'fitxer' => 'processa.php',
            'codi' => <<<'PHP'
                <?php
                // Simulem el que arribaria si l'usuari omple dos dels tres camps
                $_FILES = [
                    'imatge1' => ['name' => 'tren.png', 'type' => 'image/png', 'tmp_name' => '/tmp/php1', 'error' => UPLOAD_ERR_OK, 'size' => 48213],
                    'imatge2' => ['name' => '', 'type' => '', 'tmp_name' => '', 'error' => UPLOAD_ERR_NO_FILE, 'size' => 0],
                    'imatge3' => ['name' => 'Nina.JPG', 'type' => 'image/jpeg', 'tmp_name' => '/tmp/php3', 'error' => UPLOAD_ERR_OK, 'size' => 3500000],
                ];

                for ($i = 1; $i <= 3; $i++) {
                    $f = $_FILES["imatge$i"];
                    if ($f['error'] === UPLOAD_ERR_NO_FILE) {
                        echo "imatge$i: buit" . PHP_EOL;
                        continue;
                    }
                    $extensio = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                    $kb = round($f['size'] / 1024);
                    echo "imatge$i: {$f['name']} ($extensio, $kb KB)" . PHP_EOL;
                }
                PHP,
            'html' => '<p>Fixa\'t en <code>strtolower()</code>: «Nina.JPG» té l\'extensió en majúscules.</p>',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Els codis d\'error',
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Constant</th><th>Valor</th><th>Què vol dir</th></tr>
                  <tr><td>UPLOAD_ERR_OK</td><td>0</td><td>Tot correcte</td></tr>
                  <tr><td>UPLOAD_ERR_INI_SIZE</td><td>1</td><td>Supera <code>upload_max_filesize</code> del php.ini</td></tr>
                  <tr><td>UPLOAD_ERR_PARTIAL</td><td>3</td><td>S'ha pujat només una part</td></tr>
                  <tr><td>UPLOAD_ERR_NO_FILE</td><td>4</td><td>El camp s'ha deixat buit</td></tr>
                </table>
                <p class="nota">Fes servir les constants i no els números: <code>$f['error'] === UPLOAD_ERR_NO_FILE</code> s'entén molt millor que <code>=== 4</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'El formulari té <code>method="post"</code> però no <code>enctype</code>. Què arriba quan l\'usuari tria <code>tren.png</code>?',
            'opcions' => ['El fitxer a $_FILES, igualment', 'Només el text «tren.png» a $_POST', 'Un error de PHP', 'El fitxer a $_POST'],
            'correcta' => 1,
            'explicacio' => 'Sense enctype="multipart/form-data", el navegador envia el formulari com a text i del fitxer només n\'envia el nom.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Validar abans de desar',
            'origen' => "$origen · part 2, punt 1",
            'html' => <<<'HTML'
                <p>L'activitat demana acceptar només <strong>.jpg, .png i .gif</strong> de <strong>2 MB com a màxim</strong>. Per què és tan important?
                Si algú pujés <code>virus.php</code> a <code>uploads/</code>, podria obrir <code>uploads/virus.php</code> i el teu servidor l'executaria.</p>
                <ul>
                  <li>Extensió: <code>strtolower(pathinfo($nom, PATHINFO_EXTENSION))</code> i una llista d'extensions permeses (<em>llista blanca</em>).</li>
                  <li>Mida: <code>$f['size'] &lt;= 2 * 1024 * 1024</code>.</li>
                  <li>Nom: <code>basename()</code>, perquè un nom com <code>../../index.php</code> no surti de la carpeta.</li>
                  <li>Ampliació: <code>getimagesize($f['tmp_name'])</code> retorna <code>false</code> si el fitxer no és realment una imatge.</li>
                </ul>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: la funció de validació',
            'fitxer' => 'valida.php',
            'codi' => <<<'PHP'
                <?php
                const MIDA_MAXIMA = 2 * 1024 * 1024;   // 2 MB
                const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

                /** Retorna el missatge d'error, o null si la imatge és correcta. */
                function errorImatge(array $f): ?string
                {
                    if ($f['error'] !== UPLOAD_ERR_OK) {
                        return "no s'ha pogut pujar";
                    }
                    $extensio = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
                    if (!in_array($extensio, EXTENSIONS, true)) {
                        return "format no permès ($extensio)";
                    }
                    if ($f['size'] > MIDA_MAXIMA) {
                        return 'supera els 2 MB';
                    }
                    return null;
                }

                $proves = [
                    ['name' => 'tren.png', 'error' => UPLOAD_ERR_OK, 'size' => 50000],
                    ['name' => 'virus.php', 'error' => UPLOAD_ERR_OK, 'size' => 300],
                    ['name' => 'foto.GIF', 'error' => UPLOAD_ERR_OK, 'size' => 2500000],
                    ['name' => 'nina.jpg', 'error' => UPLOAD_ERR_PARTIAL, 'size' => 0],
                ];
                foreach ($proves as $f) {
                    echo str_pad($f['name'], 12) . ': ' . (errorImatge($f) ?? 'correcta') . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $noms = ['Tren.PNG', 'carta.pdf', 'foto.jpeg', 'gif'];
                $ok = 0;
                foreach ($noms as $nom) {
                    $ext = strtolower(pathinfo($nom, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                        $ok++;
                    }
                }
                echo $ok;
                PHP,
            'resposta' => '2',
            'explicacio' => '«Tren.PNG» (png) i «foto.jpeg» sí. «carta.pdf» no és una imatge i «gif» no té extensió: pathinfo() retorna una cadena buida.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['pujada de fitxers', '$_FILES'],
            'pregunta' => 'Quina funció desa el fitxer temporal a la carpeta uploads/?',
            'codi' => "___(\$f['tmp_name'], 'uploads/' . basename(\$f['name']));",
            'respostes' => ['move_uploaded_file'],
            'explicacio' => 'move_uploaded_file() comprova que el fitxer s\'hagi pujat de veritat en aquesta petició i el mou. copy() o rename() no fan aquesta comprovació.',
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Per què no n\'hi ha prou de comprovar <code>$_FILES[\'imatge1\'][\'type\'] === \'image/png\'</code>?',
            'opcions' => [
                'Perquè PHP no omple mai type',
                'Perquè el tipus l\'envia el navegador i es pot falsejar',
                'Perquè type només funciona amb GET',
                'Perquè les imatges PNG no tenen tipus',
            ],
            'correcta' => 1,
            'explicacio' => 'Tot el que ve a la petició ho controla qui l\'envia. L\'extensió (llista blanca), la mida i, millor encara, getimagesize() es comproven al servidor.',
        ],
    ],
];
