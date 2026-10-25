<?php
/**
 * Laboratori web: Carta als Reis Mags.
 * Basat en l'«Exercici Special Xmas» (Activitat de Nadal, prof. David Porti): formulari amb POST,
 * validació de les dades i pujada de fins a 3 imatges (JPG, PNG o GIF de 2 MB com a màxim) a uploads/.
 */

$formulari = <<<'HTML'
    <!-- enctype="multipart/form-data": imprescindible per poder pujar fitxers -->
    <form action="processa.php" method="post" enctype="multipart/form-data">
        <p><label>Nom i cognoms: <input type="text" name="nom" required></label></p>
        <p><label>Edat: <input type="number" name="edat" min="1" max="120" required></label></p>
        <p><label>Adreça:<br><textarea name="adreca" rows="3" required></textarea></label></p>
        <p>Ets...
            <label><input type="radio" name="genere" value="noi"> un noi</label>
            <label><input type="radio" name="genere" value="noia"> una noia</label>
        </p>
        <p><label>Com t'has portat aquest any? (1-10)
            <input type="range" name="actitud" min="1" max="10" value="5"></label></p>
        <p>Vols carbó?
            <label><input type="radio" name="carbo" value="si"> Sí</label>
            <label><input type="radio" name="carbo" value="no" checked> No</label>
        </p>
        <fieldset>
            <legend>Joguines: fins a 3 imatges (JPG, PNG o GIF, 2 MB com a màxim)</legend>
            <p><input type="file" name="imatge1" accept="image/*"> <input type="text" name="descripcio1" placeholder="Descripció"></p>
            <p><input type="file" name="imatge2" accept="image/*"> <input type="text" name="descripcio2" placeholder="Descripció"></p>
            <p><input type="file" name="imatge3" accept="image/*"> <input type="text" name="descripcio3" placeholder="Descripció"></p>
        </fieldset>
        <p><label>Missatge per als Reis:<br><textarea name="missatge" rows="4"></textarea></label></p>
        <button type="submit">Envia la carta</button>
    </form>
HTML;

$index1 = <<<HTML
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Carta als Reis Mags</title>
</head>
<body>
    <h1>Carta als Reis Mags</h1>
$formulari
</body>
</html>
HTML;

$index4 = str_replace('    <title>Carta als Reis Mags</title>', "    <title>Carta als Reis Mags</title>\n    <link rel=\"stylesheet\" href=\"estil.css\">", $index1);

// ---- processa.php ----
$inici = <<<'PHP'
<?php
// Rep la carta (POST) i les imatges
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Algú ha obert processa.php directament: el tornem al formulari
    header('Location: index.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$edat = $_POST['edat'] ?? '';
$adreca = trim($_POST['adreca'] ?? '');
$actitud = (int) ($_POST['actitud'] ?? 0);
$carbo = $_POST['carbo'] ?? '';
$missatge = trim($_POST['missatge'] ?? '');

// Validació: el navegador ja comprova els camps «required», però algú se'l pot saltar
$errors = [];
if ($nom === '') {
    $errors[] = 'Falta el nom.';
}
if (!ctype_digit($edat) || (int) $edat < 1) {
    $errors[] = "L'edat no és vàlida.";
}
if ($adreca === '') {
    $errors[] = "Falta l'adreça.";
}
if ($actitud < 1 || $actitud > 10) {
    $errors[] = "L'actitud ha de ser entre 1 i 10.";
}
if (!in_array($carbo, ['si', 'no'], true)) {
    $errors[] = 'Has de dir si vols carbó.';
}

PHP;

$imatges = <<<'PHP'
// Joguines: fins a 3 imatges (imatge1, imatge2, imatge3), cadascuna amb la seva descripció
const MIDA_MAXIMA = 2 * 1024 * 1024;   // 2 MB
const EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

$joguines = [];
for ($i = 1; $i <= 3; $i++) {
    $fitxer = $_FILES["imatge$i"] ?? null;
    if ($fitxer === null || $fitxer['error'] === UPLOAD_ERR_NO_FILE) {
        continue;   // aquest camp s'ha deixat buit
    }
    if ($fitxer['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "La imatge $i no s'ha pogut pujar.";
        continue;
    }
    $extensio = strtolower(pathinfo($fitxer['name'], PATHINFO_EXTENSION));
    if (!in_array($extensio, EXTENSIONS, true)) {
        $errors[] = "La imatge $i té un format no permès (només JPG, PNG o GIF).";
    } elseif ($fitxer['size'] > MIDA_MAXIMA) {
        $errors[] = "La imatge $i supera els 2 MB.";
    } else {
        $joguines[] = [
            'temporal' => $fitxer['tmp_name'],
            'nom' => basename($fitxer['name']),   // basename(): mai rutes com ../../
            'descripcio' => trim($_POST["descripcio$i"] ?? ''),
        ];
    }
}
if ($joguines === []) {
    $errors[] = 'Has de pujar almenys una imatge.';
}

// Només si tot és correcte desem les imatges a uploads/
if ($errors === []) {
    if (!is_dir('uploads')) {
        mkdir('uploads');
    }
    foreach ($joguines as $joguina) {
        move_uploaded_file($joguina['temporal'], 'uploads/' . $joguina['nom']);
    }
}

PHP;

$pagina = <<<'PHP'
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Carta als Reis Mags</title>
</head>
<body>
<?php if ($errors): ?>
    <h1>La carta té errors</h1>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= $error ?></li>
        <?php endforeach; ?>
    </ul>
    <p><a href="index.php">Torna al formulari</a></p>
<?php else: ?>
    <h1>La teva carta ha estat enviada als Reis Mags!</h1>
    <p>Han rebut la següent informació:</p>
    <dl>
        <dt>Nom:</dt> <dd><?= htmlspecialchars($nom) ?></dd>
        <dt>Edat:</dt> <dd><?= (int) $edat ?> anys</dd>
        <dt>Adreça:</dt> <dd><?= nl2br(htmlspecialchars($adreca)) ?></dd>
        <dt>Actitud:</dt> <dd><?= $actitud ?>/10</dd>
        <dt>Carbó:</dt> <dd><?= $carbo === 'si' ? 'Sí' : 'No' ?></dd>
        <dt>Missatge:</dt> <dd><?= nl2br(htmlspecialchars($missatge)) ?></dd>
    </dl>
<!--JOGUINES-->
<?php endif; ?>
</body>
</html>
PHP;

$llistaJoguines = <<<'PHP'
    <h2>Joguines</h2>
    <?php foreach ($joguines as $joguina): ?>
        <figure>
            <img src="uploads/<?= htmlspecialchars($joguina['nom']) ?>" alt="<?= htmlspecialchars($joguina['descripcio']) ?>" width="150">
            <figcaption><?= htmlspecialchars($joguina['descripcio']) ?></figcaption>
        </figure>
    <?php endforeach; ?>
PHP;

$processa2 = $inici . str_replace("<!--JOGUINES-->\n", '', $pagina);
$processa3 = $inici . $imatges . str_replace('<!--JOGUINES-->', $llistaJoguines, $pagina);
$processa4 = str_replace('    <title>Carta als Reis Mags</title>', "    <title>Carta als Reis Mags</title>\n    <link rel=\"stylesheet\" href=\"estil.css\">", $processa3);

$estilInicial = "/* Estils de la carta */\n";
$estil = <<<'CSS'
/* Estils de la carta */
body {
    font-family: Georgia, serif;
    max-width: 640px;
    margin: 2rem auto;
    padding: 0 1rem;
    background: #fdf6e3;
    color: #3b2f2f;
}
h1 {
    color: #b22222;
}
form p, fieldset {
    margin: .8rem 0;
}
figure {
    display: inline-block;
    margin: .5rem;
    text-align: center;
}
img {
    max-width: 150px;
    border-radius: 8px;
}
CSS;

$indexInicial = <<<'HTML'
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Carta als Reis Mags</title>
</head>
<body>
    <h1>Carta als Reis Mags</h1>
    <!-- Aquí va el formulari -->

</body>
</html>
HTML;
$processaInicial = "<?php\n// Rep la carta (POST) i les imatges\n";

// Dades d'una carta correcta per a les proves
$carta = [
    'nom' => 'Anna Puig', 'edat' => '9', 'adreca' => "Carrer Major, 5\nGirona", 'genere' => 'noia',
    'actitud' => '8', 'carbo' => 'no', 'missatge' => 'Vull un tren de fusta, si us plau.',
];
$sense = fn(string $camp) => array_diff_key($carta, [$camp => true]);
$tren = ['imatge1' => ['tren.png', '@png']];
$trenDesc = $carta + ['descripcio1' => 'Un tren elèctric'];

return [
    'id' => 'carta-reis',
    'tipus' => 'web',
    'ordre' => 320,
    'titol' => 'Carta als Reis Mags',
    'fitxer' => 'index.php',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.9 Formularis i pujada de fitxers',
    'origen' => [
        'fitxer' => 'Activitat de Nadal.pdf',
        'descripcio' => 'Activitat de Nadal, «Exercici Special Xmas» (David Porti)',
    ],
    'resum' => 'L\'Activitat de Nadal: un formulari amb POST que valida les dades, puja fins a 3 imatges a uploads/ i mostra la carta.',
    'xp_final' => 60,
    'paraules_clau' => ['$_POST', '$_FILES', 'enctype', 'move_uploaded_file', 'UPLOAD_ERR_OK', 'pathinfo', 'htmlspecialchars'],
    'codi_inicial' => ['index.php' => $indexInicial, 'processa.php' => $processaInicial, 'estil.css' => $estilInicial],

    'context' => <<<'HTML'
        <p>L'<strong>Activitat de Nadal</strong>: una web per escriure la carta als Reis. <code>index.php</code> té el formulari,
        <code>processa.php</code> rep les dades amb <code>POST</code>, les valida, desa les imatges de les joguines a <code>uploads/</code>
        i mostra la carta.</p>
        <p>Al navegador de la dreta pots pujar imatges de veritat des del teu ordinador. A l'inspector veuràs què arriba a
        <code>$_POST</code> i a <code>$_FILES</code>, i els fitxers que queden al servidor.</p>
        <p class="nota">Amb <code>method="post"</code> les dades van dins de la petició, no a la URL. Per pujar fitxers és obligatori, juntament
        amb <code>enctype="multipart/form-data"</code>.</p>
        HTML,

    'resum_apres' => [
        'Un formulari amb method="post" i enctype="multipart/form-data" per enviar dades i fitxers.',
        'Llegir els camps de $_POST i validar-los al servidor (el required del navegador no és suficient).',
        'L\'estructura de $_FILES: name, tmp_name, size i error (UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE...).',
        'Validar l\'extensió i la mida d\'una imatge i desar-la amb move_uploaded_file().',
        'Redirigir amb header(\'Location: ...\') quan algú obre la pàgina sense enviar el formulari.',
        'Mostrar el que ha escrit l\'usuari amb htmlspecialchars() i nl2br().',
    ],

    'passos' => [
        [
            'titol' => 'El formulari',
            'conceptes' => ['formularis', 'POST'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu (Part 1 de l'activitat):</strong> a <code>index.php</code>, crea el formulari:</p>
                <ul>
                  <li><code>&lt;form action="processa.php" method="post" enctype="multipart/form-data"&gt;</code></li>
                  <li>Nom i cognoms: text, <code>name="nom"</code></li>
                  <li>Edat: <code>type="number"</code>, <code>name="edat"</code></li>
                  <li>Adreça: <code>&lt;textarea name="adreca"&gt;</code></li>
                  <li>Noi o noia: dos <code>radio</code> amb <code>name="genere"</code></li>
                  <li>Actitud de l'any: <code>type="range"</code> de 1 a 10, <code>name="actitud"</code></li>
                  <li>Vol carbó?: dos <code>radio</code> amb <code>name="carbo"</code> i valors <code>si</code> / <code>no</code></li>
                  <li>Tres joguines: <code>type="file"</code> amb <code>name="imatge1"</code>, <code>imatge2</code>, <code>imatge3</code>,
                  i al costat un text <code>descripcio1</code>, <code>descripcio2</code>, <code>descripcio3</code></li>
                  <li>Missatge: <code>&lt;textarea name="missatge"&gt;</code> i un botó d'enviar</li>
                </ul>
                <p class="nota">Els radio d'un mateix grup comparteixen el <code>name</code>: així només se'n pot marcar un.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'index.php', 'regex' => '/<form\b/i', 'missatge' => 'index.php ha de tenir un <form>.'],
            ],
            'proves' => [
                ['nom' => 'El formulari envia a processa.php amb POST i pot pujar fitxers', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => [
                     '/<form(?=[^>]*action=["\']processa\.php["\'])(?=[^>]*method=["\']post["\'])[^>]*>/i' => 'un <form action="processa.php" method="post">',
                     '/<form[^>]*enctype=["\']multipart\/form-data["\']/i' => 'enctype="multipart/form-data" al <form> (sense això no es poden pujar fitxers)',
                 ]],
                ['nom' => 'Les dades del nen o la nena', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => [
                     '/<input(?=[^>]*name=["\']nom["\'])[^>]*>/i' => 'un camp name="nom"',
                     '/<input(?=[^>]*type=["\']number["\'])(?=[^>]*name=["\']edat["\'])[^>]*>/i' => 'un <input type="number" name="edat">',
                     '/<textarea[^>]*name=["\']adreca["\']/i' => 'un <textarea name="adreca">',
                     '/<input(?=[^>]*type=["\']radio["\'])(?=[^>]*name=["\']genere["\'])[^>]*>/i' => 'botons radio amb name="genere"',
                 ]],
                ['nom' => 'Actitud i carbó', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => [
                     '/<input(?=[^>]*type=["\']range["\'])(?=[^>]*name=["\']actitud["\'])(?=[^>]*min=["\']1["\'])(?=[^>]*max=["\']10["\'])[^>]*>/i' => 'un <input type="range" name="actitud" min="1" max="10">',
                     '/<input(?=[^>]*type=["\']radio["\'])(?=[^>]*name=["\']carbo["\'])(?=[^>]*value=["\']si["\'])[^>]*>/i' => 'un radio name="carbo" value="si"',
                     '/<input(?=[^>]*type=["\']radio["\'])(?=[^>]*name=["\']carbo["\'])(?=[^>]*value=["\']no["\'])[^>]*>/i' => 'un radio name="carbo" value="no"',
                 ]],
                ['nom' => 'Joguines i missatge', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => [
                     '/<input(?=[^>]*type=["\']file["\'])(?=[^>]*name=["\']imatge1["\'])[^>]*>/i' => 'un <input type="file" name="imatge1">',
                     '/<input(?=[^>]*type=["\']file["\'])(?=[^>]*name=["\']imatge3["\'])[^>]*>/i' => 'un <input type="file" name="imatge3">',
                     '/<input(?=[^>]*name=["\']descripcio1["\'])[^>]*>/i' => 'un camp name="descripcio1"',
                     '/<textarea[^>]*name=["\']missatge["\']/i' => 'un <textarea name="missatge">',
                 ]],
            ],
            'pistes' => [
                'Comença pel <form> (action, method i enctype) i després afegeix els camps un a un. El que importa a PHP és el name de cada camp.',
                'Radio: <input type="radio" name="carbo" value="si">. Rang: <input type="range" name="actitud" min="1" max="10">. Fitxer: <input type="file" name="imatge1">.',
                "<form action=\"processa.php\" method=\"post\" enctype=\"multipart/form-data\">\n    <input type=\"text\" name=\"nom\">\n    <input type=\"number\" name=\"edat\">\n    <textarea name=\"adreca\"></textarea>\n    ...\n    <input type=\"file\" name=\"imatge1\"> <input type=\"text\" name=\"descripcio1\">\n    ...\n</form>",
            ],
            'preguntes' => [
                'Per què els dos radio de «carbó» tenen el mateix name i un value diferent?',
                'Què passaria amb els fitxers si oblidessis l\'enctype?',
            ],
            'solucio' => ['index.php' => $index1, 'processa.php' => $processaInicial, 'estil.css' => $estilInicial],
            'solucio_explicacio' => 'Cada camp té un name, que serà la clau a $_POST (o a $_FILES per als fitxers). Sense enctype="multipart/form-data", el navegador enviaria només el nom dels fitxers.',
        ],
        [
            'titol' => 'Rebre i validar la carta',
            'conceptes' => ['POST', 'validació de formularis', 'htmlspecialchars'],
            'xp' => 35,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu (Part 2, punts 1 i 3):</strong> a <code>processa.php</code>:</p>
                <ol>
                  <li>Si la petició no és POST (<code>$_SERVER['REQUEST_METHOD']</code>), redirigeix a <code>index.php</code> amb
                  <code>header('Location: index.php')</code> i <code>exit</code>.</li>
                  <li>Llegeix els camps de <code>$_POST</code> (amb <code>?? ''</code>) i valida'ls. Afegeix cada error a un array <code>$errors</code>:
                    <ul>
                      <li>nom buit: <code>Falta el nom.</code></li>
                      <li>edat que no és un número enter positiu: <code>L'edat no és vàlida.</code> (<code>ctype_digit()</code> t'hi ajuda)</li>
                      <li>adreça buida: <code>Falta l'adreça.</code></li>
                      <li>actitud fora de 1-10: <code>L'actitud ha de ser entre 1 i 10.</code></li>
                      <li>carbó que no és <code>si</code> ni <code>no</code>: <code>Has de dir si vols carbó.</code></li>
                    </ul></li>
                  <li>Si hi ha errors, mostra'ls en una llista amb un enllaç per tornar al formulari.</li>
                  <li>Si no, mostra <code>La teva carta ha estat enviada als Reis Mags!</code>, <code>Han rebut la següent informació:</code> i les dades
                  en aquest format: <code>Nom: ...</code>, <code>Edat: ... anys</code>, <code>Adreça: ...</code>, <code>Actitud: 8/10</code>,
                  <code>Carbó: Sí</code> (o <code>No</code>) i <code>Missatge: ...</code>.</li>
                </ol>
                <p class="nota">Tot el que ha escrit l'usuari es mostra amb <code>htmlspecialchars()</code>. Per mantenir els salts de línia de l'adreça
                i del missatge, <code>nl2br(htmlspecialchars($adreca))</code>.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'processa.php', 'regex' => '/\$_POST\s*\[/', 'missatge' => 'Llegeix les dades de $_POST.'],
                ['fitxer' => 'processa.php', 'regex' => '/htmlspecialchars\s*\(/', 'missatge' => 'Mostra les dades amb htmlspecialchars().'],
            ],
            'proves' => [
                ['nom' => 'Carta correcta', 'peticions' => [['POST', 'processa.php', 'camps' => $carta]],
                 'text' => ['La teva carta ha estat enviada als Reis Mags!', 'Han rebut la següent informació:', 'Nom: Anna Puig', 'Edat: 9 anys', 'Actitud: 8/10', 'Carbó: No', 'Missatge: Vull un tren de fusta, si us plau.']],
                ['nom' => 'Vol carbó', 'peticions' => [['POST', 'processa.php', 'camps' => ['carbo' => 'si'] + $carta]], 'text' => ['Carbó: Sí']],
                ['nom' => 'Falta el nom', 'peticions' => [['POST', 'processa.php', 'camps' => $sense('nom')]],
                 'text' => ['Falta el nom.'], 'no_text' => ['ha estat enviada']],
                ['nom' => 'Edat no vàlida', 'peticions' => [['POST', 'processa.php', 'camps' => ['edat' => 'nou'] + $carta]],
                 'text' => ["L'edat no és vàlida."], 'no_text' => ['ha estat enviada']],
                ['nom' => 'Falten adreça i carbó', 'peticions' => [['POST', 'processa.php', 'camps' => array_diff_key($carta, ['adreca' => 1, 'carbo' => 1])]],
                 'text' => ["Falta l'adreça.", 'Has de dir si vols carbó.']],
                ['nom' => 'Actitud fora de rang', 'peticions' => [['POST', 'processa.php', 'camps' => ['actitud' => '15'] + $carta]],
                 'text' => ["L'actitud ha de ser entre 1 i 10."]],
                ['nom' => 'El nom no pot injectar HTML', 'peticions' => [['POST', 'processa.php', 'camps' => ['nom' => '<script>alert("hola")</script>'] + $carta]],
                 'no_conte' => ['<script>alert']],
                ['nom' => 'Obrir processa.php sense el formulari', 'peticions' => [['GET', 'processa.php']], 'redirigeix' => 'index.php'],
            ],
            'pistes' => [
                'Tres parts: (1) si no és POST, fora; (2) llegeix i valida cada camp, guardant els errors en un array; (3) segons si l\'array és buit, mostra la carta o els errors.',
                'if ($_SERVER[\'REQUEST_METHOD\'] !== \'POST\') { header(\'Location: index.php\'); exit; }  ·  $errors = []; if ($nom === \'\') { $errors[] = \'Falta el nom.\'; }  ·  <?php if ($errors): ?> ... <?php else: ?> ... <?php endif; ?>',
                "\$nom = trim(\$_POST['nom'] ?? '');\n\$edat = \$_POST['edat'] ?? '';\n\$errors = [];\nif (\$nom === '') {\n    \$errors[] = 'Falta el nom.';\n}\nif (!ctype_digit(\$edat) || (int) \$edat < 1) {\n    \$errors[] = \"L'edat no és vàlida.\";\n}\n// ... la resta de camps\n?>\n<?php if (\$errors): ?>\n    <ul><?php foreach (\$errors as \$e): ?><li><?= \$e ?></li><?php endforeach; ?></ul>\n<?php else: ?>\n    <h1>La teva carta ha estat enviada als Reis Mags!</h1>\n    ...",
            ],
            'preguntes' => [
                'Si el formulari ja té required, per què cal validar també a PHP?',
                'Per què és millor guardar els errors en un array que fer un echo a cada if?',
            ],
            'solucio' => ['index.php' => $index1, 'processa.php' => $processa2, 'estil.css' => $estilInicial],
            'solucio_explicacio' => 'La validació al servidor és la de veritat: qualsevol pot enviar una petició POST sense el formulari. Guardar els errors en un array permet mostrar-los tots junts.',
        ],
        [
            'titol' => 'Pujar les imatges',
            'conceptes' => ['pujada de fitxers', '$_FILES'],
            'xp' => 40,
            'explicacio' => <<<'HTML'
                <p>Cada fitxer arriba a <code>$_FILES['imatge1']</code> com un array:</p>
                <pre class="code">[
                  'name'     =&gt; 'tren.png',              // nom original
                  'type'     =&gt; 'image/png',             // el que diu el navegador (no t'hi refiïs)
                  'tmp_name' =&gt; '/tmp/php8a1b',          // on l'ha deixat PHP, temporalment
                  'error'    =&gt; 0,                       // UPLOAD_ERR_OK, o UPLOAD_ERR_NO_FILE si el camp és buit
                  'size'     =&gt; 48213,                   // bytes
                ]</pre>
                <p><strong>Objectiu (Part 2, punts 1 i 2):</strong> recorre <code>imatge1</code>, <code>imatge2</code> i <code>imatge3</code> amb un <code>for</code>:</p>
                <ul>
                  <li>Si el camp és buit (<code>UPLOAD_ERR_NO_FILE</code>), salta'l.</li>
                  <li>Si l'extensió (<code>pathinfo(..., PATHINFO_EXTENSION)</code>, en minúscules) no és jpg, jpeg, png o gif:
                  <code>La imatge 1 té un format no permès (només JPG, PNG o GIF).</code></li>
                  <li>Si fa més de 2 MB (<code>2 * 1024 * 1024</code> bytes): <code>La imatge 1 supera els 2 MB.</code></li>
                  <li>Si no hi ha cap imatge correcta: <code>Has de pujar almenys una imatge.</code></li>
                  <li>Si no hi ha cap error, crea la carpeta <code>uploads</code> si no existeix i desa-hi cada imatge amb el seu nom
                  (<code>basename()</code>) amb <code>move_uploaded_file()</code>.</li>
                </ul>
                <p>A la carta, mostra cada joguina amb <code>&lt;img src="uploads/tren.png"&gt;</code> i la seva descripció.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'processa.php', 'regex' => '/move_uploaded_file\s*\(/', 'missatge' => 'Desa les imatges amb move_uploaded_file().'],
                ['fitxer' => 'processa.php', 'regex' => '/\$_FILES\b/', 'missatge' => 'Les imatges són a $_FILES.'],
            ],
            'proves' => [
                ['nom' => 'Una joguina', 'peticions' => [['POST', 'processa.php', 'camps' => $trenDesc, 'pujades' => $tren]],
                 'text' => ['La teva carta ha estat enviada als Reis Mags!', 'Un tren elèctric'], 'conte' => ['uploads/tren.png'],
                 'conte_regex' => ['/<img[^>]*src=["\']uploads\/tren\.png["\']/i' => 'una imatge <img src="uploads/tren.png">'],
                 'fitxer_existeix' => ['uploads/tren.png']],
                ['nom' => 'Tres joguines (PNG, GIF i JPG)', 'peticions' => [['POST', 'processa.php',
                    'camps' => $carta + ['descripcio1' => 'Pilota', 'descripcio2' => 'Nina', 'descripcio3' => 'Puzle'],
                    'pujades' => ['imatge1' => ['pilota.png', '@png'], 'imatge2' => ['nina.gif', '@gif'], 'imatge3' => ['puzle.JPG', '@jpg']]]],
                 'text' => ['Pilota', 'Nina', 'Puzle'], 'fitxer_existeix' => ['uploads/pilota.png', 'uploads/nina.gif', 'uploads/puzle.JPG']],
                ['nom' => 'Sense cap imatge', 'peticions' => [['POST', 'processa.php', 'camps' => $carta, 'pujades' => ['imatge1' => ['', '']]]],
                 'text' => ['Has de pujar almenys una imatge.'], 'no_text' => ['ha estat enviada']],
                ['nom' => 'Un fitxer que no és una imatge', 'peticions' => [['POST', 'processa.php', 'camps' => $carta, 'pujades' => ['imatge1' => ['virus.php', '<?php echo "hola";']]]],
                 'text' => ['format no permès'], 'no_text' => ['ha estat enviada'], 'fitxer_no_existeix' => ['uploads/virus.php']],
                ['nom' => 'Una imatge de més de 2 MB', 'peticions' => [['POST', 'processa.php', 'camps' => $carta, 'pujades' => ['imatge1' => ['gran.png', '@gran']]]],
                 'text' => ['supera els 2 MB'], 'no_text' => ['ha estat enviada'], 'fitxer_no_existeix' => ['uploads/gran.png']],
                ['nom' => 'Si falta el nom, no es desa cap imatge', 'peticions' => [['POST', 'processa.php', 'camps' => $sense('nom'), 'pujades' => $tren]],
                 'text' => ['Falta el nom.'], 'fitxer_no_existeix' => ['uploads/tren.png']],
            ],
            'pistes' => [
                'Per a cada camp: si està buit, continua; si no, comprova l\'extensió i la mida. Les que estiguin bé, guarda-les en un array. Al final, si no hi ha errors, mou-les a uploads/.',
                '$fitxer = $_FILES["imatge$i"] ?? null;  ·  $fitxer[\'error\'] === UPLOAD_ERR_NO_FILE  ·  strtolower(pathinfo($fitxer[\'name\'], PATHINFO_EXTENSION))  ·  $fitxer[\'size\'] > 2 * 1024 * 1024  ·  move_uploaded_file($fitxer[\'tmp_name\'], \'uploads/\' . basename($fitxer[\'name\']))',
                "for (\$i = 1; \$i <= 3; \$i++) {\n    \$fitxer = \$_FILES[\"imatge\$i\"] ?? null;\n    if (\$fitxer === null || \$fitxer['error'] === UPLOAD_ERR_NO_FILE) {\n        continue;\n    }\n    \$extensio = strtolower(pathinfo(\$fitxer['name'], PATHINFO_EXTENSION));\n    if (!in_array(\$extensio, ['jpg', 'jpeg', 'png', 'gif'], true)) {\n        \$errors[] = \"La imatge \$i té un format no permès (només JPG, PNG o GIF).\";\n    } elseif (\$fitxer['size'] > 2 * 1024 * 1024) {\n        \$errors[] = \"La imatge \$i supera els 2 MB.\";\n    } else {\n        \$joguines[] = [...];\n    }\n}",
            ],
            'preguntes' => [
                'Per què no podem confiar en $_FILES[...][\'type\']?',
                'Què podria passar si deixéssim pujar un fitxer .php a uploads/?',
                'Per què movem les imatges només quan no hi ha cap error?',
            ],
            'solucio' => ['index.php' => $index1, 'processa.php' => $processa3, 'estil.css' => $estilInicial],
            'solucio_explicacio' => 'PHP deixa cada fitxer en una carpeta temporal i l\'esborra en acabar la petició: move_uploaded_file() el desa de veritat. Comprovar l\'extensió evita que algú pugi un .php i l\'executi al teu servidor.',
        ],
        [
            'titol' => 'Una carta amb estil',
            'conceptes' => ['HTML i CSS'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu (Part 2, punt 4):</strong> la pàgina ha d'estar «ben estructurada i estilitzada mínimament amb CSS».</p>
                <ul>
                  <li>Escriu algunes regles a <code>estil.css</code>: tipus de lletra, colors, marges, la mida de les imatges...</li>
                  <li>Enllaça-hi des de les dues pàgines, dins del <code>&lt;head&gt;</code>: <code>&lt;link rel="stylesheet" href="estil.css"&gt;</code>.</li>
                </ul>
                <p>Mira el resultat al navegador: el formulari i la carta haurien de canviar d'aspecte.</p>
                HTML,
            'codi' => [],
            'proves' => [
                ['nom' => 'El formulari fa servir estil.css', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => ['/<link(?=[^>]*rel=["\']stylesheet["\'])(?=[^>]*href=["\']estil\.css["\'])[^>]*>/i' => '<link rel="stylesheet" href="estil.css"> al <head>']],
                ['nom' => 'La carta fa servir estil.css', 'peticions' => [['POST', 'processa.php', 'camps' => $trenDesc, 'pujades' => $tren]],
                 'text' => ['La teva carta ha estat enviada als Reis Mags!'],
                 'conte_regex' => ['/<link(?=[^>]*rel=["\']stylesheet["\'])(?=[^>]*href=["\']estil\.css["\'])[^>]*>/i' => '<link rel="stylesheet" href="estil.css"> al <head>']],
                ['nom' => 'estil.css té regles', 'peticions' => [['GET', 'estil.css']], 'estat' => 200,
                 'conte_regex' => ['/[a-z][^{}]*\{[^}]*[a-z-]+\s*:[^}]+\}/i' => 'almenys una regla CSS (selector { propietat: valor; })']],
            ],
            'pistes' => [
                'Dues coses: escriure regles a estil.css i enllaçar el fitxer des del <head> de les dues pàgines.',
                'Una regla: body { font-family: Georgia, serif; background: #fdf6e3; }. L\'enllaç: <link rel="stylesheet" href="estil.css">.',
                "<head>\n    <meta charset=\"utf-8\">\n    <title>Carta als Reis Mags</title>\n    <link rel=\"stylesheet\" href=\"estil.css\">\n</head>",
            ],
            'preguntes' => [
                'Per què és millor tenir el CSS en un fitxer a part que dins de cada pàgina?',
            ],
            'solucio' => ['index.php' => $index4, 'processa.php' => $processa4, 'estil.css' => $estil],
            'solucio_explicacio' => 'Un sol fitxer CSS enllaçat des de totes les pàgines: si canvies un color, canvia a tota la web.',
        ],
    ],
];
