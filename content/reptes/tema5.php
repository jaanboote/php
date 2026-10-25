<?php
/**
 * Banc de reptes del Tema 5 (Formularis, fitxers i sessions). Format: mira content/reptes/tema2.php.
 * Els codis s'executen a la consola: simulen $_GET, $_POST i $_FILES amb arrays.
 */

return [
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'items' => [
        [
            'id' => 't5-get-defecte', 'tipus' => 'sortida', 'conceptes' => ['GET'],
            'pregunta' => 'La pàgina s\'ha obert amb <code>?nom=Pau</code>. Què imprimeix?',
            'codi' => "<?php\n\$_GET = ['nom' => 'Pau'];\necho (\$_GET['edat'] ?? 'sense edat') . ' ' . \$_GET['nom'];",
            'resposta' => 'sense edat Pau',
            'explicacio' => 'edat no ha arribat a la URL: el ?? fa servir el valor per defecte (i evita el Warning «Undefined array key»).',
        ],
        [
            'id' => 't5-escapar', 'tipus' => 'sortida', 'conceptes' => ['htmlspecialchars'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho htmlspecialchars('\"Hola\" <3');",
            'resposta' => '&quot;Hola&quot; &lt;3',
            'explicacio' => 'htmlspecialchars() converteix les cometes dobles en &quot; i el < en &lt;. Al navegador es veu «"Hola" <3» com a text.',
        ],
        [
            'id' => 't5-extensio', 'tipus' => 'sortida', 'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$nom = 'carta.final.JPG';\necho strtolower(pathinfo(\$nom, PATHINFO_EXTENSION));",
            'resposta' => 'jpg',
            'explicacio' => 'L\'extensió és el que hi ha després de l\'últim punt («JPG»), i strtolower() la passa a minúscules per comparar-la amb la llista blanca.',
        ],
        [
            'id' => 't5-post-sense-defecte', 'tipus' => 'error', 'conceptes' => ['POST'],
            'pregunta' => 'Hauria de mostrar «Hola, desconegut!», però surt un Warning. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $_POST = ['cognom' => 'Puig'];
                $nom = $_POST['nom'];
                echo "Hola, $nom!" . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $_POST = ['cognom' => 'Puig'];
                $nom = $_POST['nom'] ?? 'desconegut';
                echo "Hola, $nom!" . PHP_EOL;
                PHP,
            'esperat' => 'Hola, desconegut!',
            'explicacio' => 'Un camp que no s\'ha enviat no existeix a $_POST. Amb ?? \'desconegut\' tens un valor per defecte i cap Warning.',
        ],
        [
            'id' => 't5-mida', 'tipus' => 'error', 'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Una imatge de 50 KB surt com a «Massa gran». On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                const MIDA_MAXIMA = 2 * 1024 * 1024;
                $f = ['name' => 'tren.png', 'size' => 50000];
                if ($f['size'] < MIDA_MAXIMA) {
                    echo "Massa gran" . PHP_EOL;
                } else {
                    echo "Correcta" . PHP_EOL;
                }
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                const MIDA_MAXIMA = 2 * 1024 * 1024;
                $f = ['name' => 'tren.png', 'size' => 50000];
                if ($f['size'] > MIDA_MAXIMA) {
                    echo "Massa gran" . PHP_EOL;
                } else {
                    echo "Correcta" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Correcta',
            'explicacio' => 'La comparació està girada: és massa gran quan la mida és MÉS GRAN que el màxim (>).',
        ],
        [
            'id' => 't5-no-fitxer', 'tipus' => 'error', 'conceptes' => ['$_FILES'],
            'pregunta' => 'El camp imatge2 s\'ha deixat buit, però el programa diu que té un format no permès. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $_FILES = ['imatge2' => ['name' => '', 'error' => UPLOAD_ERR_NO_FILE, 'size' => 0]];
                $f = $_FILES['imatge2'];
                if ($f['error'] === UPLOAD_ERR_OK) {
                    echo "Camp buit" . PHP_EOL;
                } elseif (!in_array(pathinfo($f['name'], PATHINFO_EXTENSION), ['png', 'jpg', 'gif'], true)) {
                    echo "Format no permès" . PHP_EOL;
                }
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                $_FILES = ['imatge2' => ['name' => '', 'error' => UPLOAD_ERR_NO_FILE, 'size' => 0]];
                $f = $_FILES['imatge2'];
                if ($f['error'] === UPLOAD_ERR_NO_FILE) {
                    echo "Camp buit" . PHP_EOL;
                } elseif (!in_array(pathinfo($f['name'], PATHINFO_EXTENSION), ['png', 'jpg', 'gif'], true)) {
                    echo "Format no permès" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Camp buit',
            'explicacio' => 'Un camp de fitxer buit arriba amb error UPLOAD_ERR_NO_FILE (4), no UPLOAD_ERR_OK. Primer es mira si està buit i després es valida.',
        ],
        [
            'id' => 't5-ord-validacio', 'tipus' => 'ordenar', 'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Ordena les línies perquè validi l\'extensió de la imatge pujada:',
            'linies' => [
                '<?php',
                '$_FILES = [\'foto\' => [\'name\' => \'tren.PNG\', \'error\' => UPLOAD_ERR_OK]];',
                '$f = $_FILES[\'foto\'];',
                '$ext = strtolower(pathinfo($f[\'name\'], PATHINFO_EXTENSION));',
                'if (in_array($ext, [\'jpg\', \'png\', \'gif\'], true)) {',
                '    echo "Imatge $ext acceptada";',
                '}',
            ],
            'sortida' => 'Imatge png acceptada',
            'explicacio' => 'Primer les dades, després l\'extensió en minúscules i, al final, la comprovació amb la llista blanca.',
        ],
        [
            'id' => 't5-test-enctype', 'tipus' => 'test', 'conceptes' => ['pujada de fitxers'],
            'pregunta' => 'Què ha de tenir un <code>&lt;form&gt;</code> per poder pujar fitxers?',
            'opcions' => [
                'method="get" i enctype="multipart/form-data"',
                'method="post" i enctype="multipart/form-data"',
                'method="post" i accept="image/*"',
                'Només un <input type="file">',
            ],
            'correcta' => 1,
            'explicacio' => 'Els fitxers només viatgen amb POST i codificats com a multipart/form-data. accept només filtra el selector de fitxers.',
        ],
        [
            'id' => 't5-test-metode', 'tipus' => 'test', 'conceptes' => ['GET', 'POST'],
            'pregunta' => 'Quin mètode faries servir per a un formulari d\'inici de sessió?',
            'opcions' => ['GET, perquè és més ràpid', 'POST, perquè les dades no queden a la URL ni a l\'historial', 'GET, perquè es pot desar com a enllaç', 'És igual'],
            'correcta' => 1,
            'explicacio' => 'Amb GET la contrasenya quedaria a la URL, a l\'historial i als logs del servidor. POST la posa dins de la petició (i HTTPS la xifra).',
        ],
        [
            'id' => 't5-test-sessio', 'tipus' => 'test', 'conceptes' => ['sessions'],
            'pregunta' => 'Què guarda el navegador quan fas servir sessions de PHP?',
            'opcions' => ['Totes les dades de $_SESSION', 'Només una galeta amb l\'identificador de la sessió', 'La contrasenya', 'Res'],
            'correcta' => 1,
            'explicacio' => 'El navegador només té la galeta PHPSESSID. Les dades són al servidor, i per això l\'usuari no les pot modificar.',
        ],
    ],
];
