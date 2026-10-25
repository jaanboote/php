<?php
/**
 * Laboratori web: Formulari de salutació (GET).
 * Primer contacte amb formularis: el formulari HTML envia les dades a un altre fitxer PHP per la URL ($_GET).
 * Prepara el terreny per a l'Activitat de Nadal (POST i pujada d'imatges).
 */

$index = <<<'HTML'
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Salutació</title>
</head>
<body>
    <h1>Com et dius?</h1>
    <form action="salutacio.php" method="get">
        <label>Nom: <input type="text" name="nom"></label>
        <label>Idioma:
            <select name="idioma">
                <option value="ca">Català</option>
                <option value="es">Castellà</option>
                <option value="en">Anglès</option>
            </select>
        </label>
        <button type="submit">Saluda'm</button>
    </form>
</body>
</html>
HTML;

$salutacio2 = <<<'PHP'
<?php
// Rep les dades del formulari per la URL: salutacio.php?nom=Anna&idioma=ca
$nom = $_GET['nom'] ?? '';
$idioma = $_GET['idioma'] ?? 'ca';

$salutacio = match ($idioma) {
    'es' => 'Buenos días',
    'en' => 'Good morning',
    default => 'Bon dia',
};
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Salutació</title>
</head>
<body>
    <h1><?= $salutacio ?>, <?= $nom ?>!</h1>
    <p><a href="index.php">Torna</a></p>
</body>
</html>
PHP;

$salutacio3 = <<<'PHP'
<?php
// Rep les dades del formulari per la URL: salutacio.php?nom=Anna&idioma=ca
$nom = trim($_GET['nom'] ?? '');
$idioma = $_GET['idioma'] ?? 'ca';

// Un idioma que no coneixem (algú ha tocat la URL) fa servir el català
$salutacio = match ($idioma) {
    'es' => 'Buenos días',
    'en' => 'Good morning',
    default => 'Bon dia',
};
?>
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Salutació</title>
</head>
<body>
    <?php if ($nom === ''): ?>
        <p>Has d'escriure el teu nom.</p>
    <?php else: ?>
        <!-- htmlspecialchars(): el que escriu l'usuari es mostra com a text, mai com a HTML -->
        <h1><?= $salutacio ?>, <?= htmlspecialchars($nom) ?>!</h1>
    <?php endif; ?>
    <p><a href="index.php">Torna</a></p>
</body>
</html>
PHP;

$inicialIndex = <<<'HTML'
<!doctype html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Salutació</title>
</head>
<body>
    <h1>Com et dius?</h1>
    <!-- Aquí va el formulari -->

</body>
</html>
HTML;

$inicialSalutacio = <<<'PHP'
<?php
// Aquest fitxer rep les dades del formulari

PHP;

return [
    'id' => 'salutacio-web',
    'tipus' => 'web',
    'ordre' => 310,
    'titol' => 'Formulari de salutació (GET)',
    'fitxer' => 'index.php',
    'tema' => 'Tema 5 – Formularis, fitxers i sessions',
    'apartat' => '5.9 Formularis',
    'origen' => [
        'fitxer' => 'Temari_M0376.pdf',
        'descripcio' => 'Mòdul 0376, apartat 5.9 «Mecanismes d\'introducció d\'informació: formularis». Preparació per a l\'Activitat de Nadal.',
    ],
    'resum' => 'La teva primera web amb formulari: index.php demana el nom i l\'idioma i salutacio.php saluda llegint $_GET.',
    'xp_final' => 40,
    'paraules_clau' => ['form', 'action', 'method', '$_GET', 'htmlspecialchars', 'trim'],
    'codi_inicial' => ['index.php' => $inicialIndex, 'salutacio.php' => $inicialSalutacio],

    'context' => <<<'HTML'
        <p>Una web de dues pàgines. <code>index.php</code> té un formulari que demana el nom i l'idioma; quan l'envies, el navegador
        va a <code>salutacio.php?nom=Anna&amp;idioma=ca</code> i PHP llegeix les dades de <code>$_GET</code>.</p>
        <p>A la dreta tens un <strong>navegador</strong> on la teva web funciona de veritat: omple el formulari, envia'l i mira
        a l'<strong>inspector</strong> què ha rebut PHP.</p>
        <p class="nota">Amb <code>method="get"</code> les dades viatgen a la URL: es veuen, es poden copiar i es queden a l'historial.
        Per això és el mètode per a cerques i filtres, però no per a contrasenyes.</p>
        HTML,

    'resum_apres' => [
        'Un formulari HTML envia les dades a la pàgina d\'action, amb el mètode de method.',
        'Cada camp arriba a PHP amb la clau del seu atribut name: $_GET[\'nom\'].',
        'Fer servir ?? per als camps que poden no arribar (algú obre la pàgina sense el formulari).',
        'Validar amb trim() i mostrar un missatge si falta una dada.',
        'Escapar amb htmlspecialchars() tot el que ve de l\'usuari abans de posar-ho a l\'HTML (XSS).',
    ],

    'passos' => [
        [
            'titol' => 'El formulari',
            'conceptes' => ['formularis', 'GET'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> a <code>index.php</code>, afegeix un formulari que enviï les dades a <code>salutacio.php</code> amb el mètode GET:</p>
                <ul>
                  <li><code>&lt;form action="salutacio.php" method="get"&gt;</code></li>
                  <li>un camp de text amb <code>name="nom"</code></li>
                  <li>un <code>&lt;select name="idioma"&gt;</code> amb tres opcions: <code>ca</code>, <code>es</code> i <code>en</code> (atribut <code>value</code>)</li>
                  <li>un botó per enviar</li>
                </ul>
                <p>Prem <strong>Desa i obre</strong> i mira el formulari al navegador. Si l'envies, veuràs la URL amb les dades
                (encara que <code>salutacio.php</code> de moment no fa res).</p>
                HTML,
            'codi' => [
                ['fitxer' => 'index.php', 'regex' => '/<form\b/i', 'missatge' => 'index.php ha de tenir un <form>.'],
            ],
            'proves' => [
                ['nom' => 'El formulari envia a salutacio.php per GET', 'peticions' => [['GET', 'index.php']],
                 'conte_regex' => [
                     '/<form(?=[^>]*action=["\']salutacio\.php["\'])(?=[^>]*method=["\']get["\'])[^>]*>/i' => 'un <form action="salutacio.php" method="get">',
                     '/<input(?=[^>]*name=["\']nom["\'])[^>]*>/i' => 'un <input name="nom">',
                     '/<select[^>]*name=["\']idioma["\']/i' => 'un <select name="idioma">',
                     '/<option[^>]*value=["\']ca["\']/i' => 'l\'opció value="ca"',
                     '/<option[^>]*value=["\']es["\']/i' => 'l\'opció value="es"',
                     '/<option[^>]*value=["\']en["\']/i' => 'l\'opció value="en"',
                     '/<(button|input)[^>]*(type=["\']submit["\']|<\/button>)|<button\b/i' => 'un botó per enviar',
                 ]],
            ],
            'pistes' => [
                'El formulari necessita on enviar les dades (action) i com (method). Cada camp necessita un name: és la clau amb què arribarà a PHP.',
                'Dins del <form>: un <input type="text" name="nom">, un <select name="idioma"> amb tres <option value="..."> i un <button type="submit">.',
                "<form action=\"salutacio.php\" method=\"get\">\n    <input type=\"text\" name=\"nom\">\n    <select name=\"idioma\">\n        <option value=\"ca\">Català</option>\n        ...\n    </select>\n    <button type=\"submit\">Saluda'm</button>\n</form>",
            ],
            'preguntes' => [
                'Què passa amb l\'atribut name? Què veuria PHP si el camp no en tingués?',
                'Per què el value d\'una opció pot ser diferent del text que es veu?',
            ],
            'solucio' => ['index.php' => $index, 'salutacio.php' => $inicialSalutacio],
            'solucio_explicacio' => 'L\'action diu a quina pàgina van les dades, i el method com hi viatgen. Cada name es convertirà en una clau de $_GET.',
        ],
        [
            'titol' => 'Rebre les dades',
            'conceptes' => ['GET', 'match'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> a <code>salutacio.php</code>, llegeix <code>$_GET['nom']</code> i <code>$_GET['idioma']</code> i mostra un títol:</p>
                <table class="taula">
                  <tr><th>idioma</th><th>Mostra</th></tr>
                  <tr><td>ca</td><td>Bon dia, Anna!</td></tr>
                  <tr><td>es</td><td>Buenos días, Anna!</td></tr>
                  <tr><td>en</td><td>Good morning, Anna!</td></tr>
                </table>
                <p>Afegeix també un enllaç per tornar: <code>&lt;a href="index.php"&gt;Torna&lt;/a&gt;</code>.</p>
                <p class="nota">Fes servir <code>$_GET['nom'] ?? ''</code>: si algú obre <code>salutacio.php</code> sense passar pel formulari,
                <code>$_GET['nom']</code> no existeix i sense el <code>??</code> sortiria un Warning.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'salutacio.php', 'regex' => '/\$_GET\s*\[\s*[\'"]nom[\'"]\s*\]/', 'missatge' => 'Llegeix el nom de $_GET[\'nom\'].'],
            ],
            'proves' => [
                ['nom' => 'En català', 'peticions' => [['GET', 'salutacio.php?nom=Anna&idioma=ca']], 'text' => ['Bon dia, Anna!']],
                ['nom' => 'En castellà', 'peticions' => [['GET', 'salutacio.php?nom=Lucía&idioma=es']], 'text' => ['Buenos días, Lucía!']],
                ['nom' => 'En anglès', 'peticions' => [['GET', 'salutacio.php?nom=Joan&idioma=en']], 'text' => ['Good morning, Joan!']],
                ['nom' => 'Enllaç per tornar', 'peticions' => [['GET', 'salutacio.php?nom=Anna&idioma=ca']],
                 'conte_regex' => ['/<a[^>]*href=["\']index\.php["\']/i' => 'un enllaç <a href="index.php">']],
            ],
            'pistes' => [
                'El que s\'ha escrit al camp name="nom" és a $_GET[\'nom\']. Tria la salutació segons l\'idioma i mostra-la amb el nom.',
                'match ($idioma) { \'es\' => \'Buenos días\', \'en\' => \'Good morning\', default => \'Bon dia\' } i després <h1><?= $salutacio ?>, <?= $nom ?>!</h1>.',
                "\$nom = \$_GET['nom'] ?? '';\n\$idioma = \$_GET['idioma'] ?? 'ca';\n\$salutacio = match (\$idioma) {\n    'es' => 'Buenos días',\n    'en' => 'Good morning',\n    default => 'Bon dia',\n};\n?>\n<h1><?= \$salutacio ?>, <?= \$nom ?>!</h1>",
            ],
            'preguntes' => [
                'On pots veure les dades que ha enviat el formulari, sense mirar el codi?',
                'Què hauria de passar si algú escriu a mà salutacio.php?idioma=fr?',
            ],
            'solucio' => ['index.php' => $index, 'salutacio.php' => $salutacio2],
            'solucio_explicacio' => 'Les dades del formulari són a $_GET amb la clau del name. match tria el text segons l\'idioma i default cobreix qualsevol altre valor.',
        ],
        [
            'titol' => 'Validar i protegir',
            'conceptes' => ['validació de formularis', 'htmlspecialchars'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Ara mateix, si envies el formulari buit surt «Bon dia, !». I si algú escriu <code>&lt;b&gt;Anna&lt;/b&gt;</code> com a nom,
                el navegador ho interpreta com a HTML. Amb <code>&lt;script&gt;</code> podria executar JavaScript a la teva pàgina: és un atac <strong>XSS</strong>.</p>
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Treu els espais del principi i el final del nom amb <code>trim()</code>.</li>
                  <li>Si el nom queda buit, mostra <code>Has d'escriure el teu nom.</code> (i l'enllaç per tornar) en lloc de la salutació.</li>
                  <li>Mostra el nom amb <code>htmlspecialchars($nom)</code>: <code>&lt;</code> es converteix en <code>&amp;lt;</code> i el navegador el mostra com a text.</li>
                </ul>
                <p>Prova-ho al navegador amb un nom com <code>&lt;i&gt;Anna&lt;/i&gt;</code> abans i després del canvi.</p>
                HTML,
            'codi' => [
                ['fitxer' => 'salutacio.php', 'regex' => '/htmlspecialchars\s*\(/', 'missatge' => 'Escapa el nom amb htmlspecialchars().'],
                ['fitxer' => 'salutacio.php', 'regex' => '/\btrim\s*\(/', 'missatge' => 'Fes servir trim() per treure els espais.'],
            ],
            'proves' => [
                ['nom' => 'Formulari buit', 'peticions' => [['GET', 'salutacio.php?nom=&idioma=ca']], 'text' => ["Has d'escriure el teu nom."], 'no_text' => ['Bon dia']],
                ['nom' => 'Només espais', 'peticions' => [['GET', 'salutacio.php?nom=+++&idioma=ca']], 'text' => ["Has d'escriure el teu nom."]],
                ['nom' => 'Sense passar pel formulari', 'peticions' => [['GET', 'salutacio.php']], 'text' => ["Has d'escriure el teu nom."]],
                ['nom' => 'Espais al voltant del nom', 'peticions' => [['GET', 'salutacio.php', 'query' => ['nom' => '  Anna  ', 'idioma' => 'ca']]], 'text' => ['Bon dia, Anna!']],
                ['nom' => 'Un nom amb HTML es mostra com a text', 'peticions' => [['GET', 'salutacio.php', 'query' => ['nom' => '<b>Anna</b>', 'idioma' => 'ca']]],
                 'no_conte' => ['<b>Anna</b>'], 'conte' => ['&lt;b&gt;Anna&lt;/b&gt;']],
                ['nom' => 'Idioma desconegut', 'peticions' => [['GET', 'salutacio.php?nom=Pau&idioma=fr']], 'text' => ['Bon dia, Pau!']],
            ],
            'pistes' => [
                'Primer neteja el nom i comprova si ha quedat buit. Després, a l\'hora de mostrar-lo, que no es pugui interpretar com a HTML.',
                '$nom = trim($_GET[\'nom\'] ?? \'\'); i un if ($nom === \'\') per decidir què mostres. Per mostrar-lo: htmlspecialchars($nom).',
                "<?php if (\$nom === ''): ?>\n    <p>Has d'escriure el teu nom.</p>\n<?php else: ?>\n    <h1><?= \$salutacio ?>, <?= htmlspecialchars(\$nom) ?>!</h1>\n<?php endif; ?>",
            ],
            'preguntes' => [
                'Què faria un <script> dins del nom si no l\'escapéssim?',
                'Per què s\'ha d\'escapar en el moment de mostrar i no en el de rebre?',
            ],
            'solucio' => ['index.php' => $index, 'salutacio.php' => $salutacio3],
            'solucio_explicacio' => 'trim() evita que uns espais comptin com a nom. htmlspecialchars() converteix < > & " en entitats: el navegador les mostra com a text i no executa res.',
        ],
    ],
];
