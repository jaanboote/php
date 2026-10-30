<?php
/**
 * Laboratori: Codis d'estat HTTP.
 * Basat en el «Cicle de petició web» del Tema 1, diapositiva 34 (prof. David Porti): el servidor retorna una resposta
 * amb un codi d'estat (200 OK, 404 Not Found, 500 Internal Server Error...). La classificació per famílies és ampliació.
 */

$sol1 = <<<'PHP'
<?php
// Codis d'estat HTTP: php codi_http.php <codi>

if ($argc != 2) {
    echo "Ús: php codi_http.php <codi>" . PHP_EOL;
    exit(1);
}
$codi = $argv[1];
if (!is_numeric($codi) || $codi < 100 || $codi > 599) {
    echo "ERROR: el codi ha de ser un número entre 100 i 599." . PHP_EOL;
    exit(1);
}
$codi = (int) $codi;
echo "Codi: $codi" . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

// La centena diu de quina família és
if ($codi < 200) {
    $familia = "1xx Informació";
} elseif ($codi < 300) {
    $familia = "2xx Èxit";
} elseif ($codi < 400) {
    $familia = "3xx Redirecció";
} elseif ($codi < 500) {
    $familia = "4xx Error del client";
} else {
    $familia = "5xx Error del servidor";
}
echo "Família: $familia" . PHP_EOL;

PHP;

$sol3 = $sol2 . <<<'PHP'

switch ($codi) {
    case 200:
        $nom = "OK";
        break;
    case 301:
        $nom = "Moved Permanently";
        break;
    case 302:
        $nom = "Found";
        break;
    case 403:
        $nom = "Forbidden";
        break;
    case 404:
        $nom = "Not Found";
        break;
    case 500:
        $nom = "Internal Server Error";
        break;
    default:
        $nom = "(no és dels més habituals)";
}
echo "Nom: $nom" . PHP_EOL;

PHP;

$familia = fn(string $c, string $f) => ['nom' => "$c → $f", 'args' => [$c], 'conte' => ["Família: $f"]];
$nom = fn(string $c, string $n) => ['nom' => "$c → $n", 'args' => [$c], 'conte' => ["Nom: $n"]];

return [
    'id' => 'codis-http',
    'ordre' => 2,
    'titol' => 'Codis d\'estat HTTP',
    'fitxer' => 'codi_http.php',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.3 Model client-servidor',
    'origen' => [
        'fitxer' => 'Tema 1 - Introducció al desenvolupament web.pdf',
        'descripcio' => 'Cicle de petició web del Tema 1, diapositiva 34 (David Porti). Ampliació: la classificació per famílies.',
    ],
    'resum' => 'Un programa que rep un codi d\'estat HTTP com a argument i diu de quina família és (èxit, redirecció, error...) i com es diu.',
    'xp_final' => 30,
    'paraules_clau' => ['HTTP', 'codi d\'estat', '404', 'resposta', 'client-servidor'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Codis d'estat HTTP: php codi_http.php <codi>

        PHP,

    'context' => <<<'HTML'
        <p>Al pas 6 del cicle de petició web, el servidor retorna una resposta amb un <strong>codi d'estat</strong>: un número
        de tres xifres que diu al navegador com ha anat la petició. La primera xifra indica la família.</p>
        <table class="taula">
          <tr><th>Família</th><th>Significat</th><th>Exemple</th></tr>
          <tr><td>1xx</td><td>Informació</td><td>100 Continue</td></tr>
          <tr><td>2xx</td><td>Èxit</td><td>200 OK</td></tr>
          <tr><td>3xx</td><td>Redirecció</td><td>301 Moved Permanently</td></tr>
          <tr><td>4xx</td><td>Error del client</td><td>404 Not Found</td></tr>
          <tr><td>5xx</td><td>Error del servidor</td><td>500 Internal Server Error</td></tr>
        </table>
        <p>El codi arriba com a argument: escriu-lo a la caixa d'arguments abans de prémer Executa.</p>
        <pre class="term">$ php codi_http.php 404
        Codi: 404
        Família: 4xx Error del client
        Nom: Not Found</pre>
        HTML,

    'resum_apres' => [
        'Al cicle client-servidor, cada resposta porta un codi d\'estat que diu com ha anat la petició.',
        'Els 4xx són culpa del client (adreça que no existeix, sense permís) i els 5xx, del servidor.',
        'Llegir un argument amb $argv i validar-lo abans de fer-lo servir.',
        'Triar entre trams amb if / elseif i entre valors concrets amb switch.',
    ],

    'passos' => [
        [
            'titol' => 'Llegir i validar el codi',
            'conceptes' => ['arguments per consola', 'validació de dades'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p>El codi arriba a <code>$argv[1]</code>. <code>$argc</code> diu quants arguments hi ha (comptant el nom del programa).</p>
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Si no hi ha exactament un argument, mostra <code>Ús: php codi_http.php &lt;codi&gt;</code> i atura el programa amb <code>exit(1)</code>.</li>
                  <li>Si no és un número entre 100 i 599, mostra <code>ERROR: el codi ha de ser un número entre 100 i 599.</code> i <code>exit(1)</code>.</li>
                  <li>Si és correcte, mostra <code>Codi: 404</code>.</li>
                </ul>
                HTML,
            'codi' => [
                ['regex' => '/\$argc|\$argv/', 'missatge' => 'Llegeix el codi de $argv[1] (i comprova $argc).'],
                ['regex' => '/\bis_numeric\s*\(/', 'missatge' => 'Comprova que és un número amb is_numeric().'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php codi_http.php <codi>'], 'codi_sortida' => 1],
                ['nom' => 'Un text', 'args' => ['hola'], 'conte' => ['ERROR: el codi ha de ser un número entre 100 i 599.'], 'codi_sortida' => 1],
                ['nom' => 'Massa petit (99)', 'args' => ['99'], 'conte' => ['ERROR: el codi ha de ser un número entre 100 i 599.'], 'codi_sortida' => 1],
                ['nom' => 'Massa gran (600)', 'args' => ['600'], 'conte' => ['ERROR'], 'codi_sortida' => 1],
                ['nom' => '404 és vàlid', 'args' => ['404'], 'conte' => ['Codi: 404'], 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'Primer mira si hi ha l\'argument; després, si és un número; i finalment, si és dins del rang. A cada error, el programa s\'ha d\'aturar.',
                'Amb $argc != 2 saps que falta (o sobra) l\'argument. is_numeric($codi) diu si és un número, i amb || pots ajuntar les tres condicions d\'error.',
                "if (!is_numeric(\$codi) || \$codi < 100 || \$codi > 599) {\n    echo \"ERROR: el codi ha de ser un número entre 100 i 599.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Per què $argc val 2 quan escrius php codi_http.php 404?',
                'Què passaria si fessis la comparació $codi < 100 sense haver comprovat abans que és un número?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Validem en ordre: que hi sigui, que sigui un número i que sigui al rang. Cada error atura el programa amb exit(1).',
        ],
        [
            'titol' => 'La família del codi',
            'conceptes' => ['condicionals'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> després de la línia <code>Codi: ...</code>, mostra la família segons la taula de dalt,
                amb aquests textos exactes:</p>
                <pre class="term">Família: 1xx Informació
                Família: 2xx Èxit
                Família: 3xx Redirecció
                Família: 4xx Error del client
                Família: 5xx Error del servidor</pre>
                HTML,
            'codi' => [
                ['regex' => '/\belseif\b|\bswitch\b|\bmatch\b/', 'missatge' => 'Tria la família amb if / elseif (o un switch).'],
            ],
            'proves' => [
                $familia('100', '1xx Informació'),
                $familia('200', '2xx Èxit'),
                $familia('204', '2xx Èxit'),
                $familia('301', '3xx Redirecció'),
                $familia('418', '4xx Error del client'),
                $familia('599', '5xx Error del servidor'),
            ],
            'pistes' => [
                'Són trams de 100 en 100. Si vas de menor a major, a cada condició només cal mirar el límit de dalt.',
                'Amb if ($codi < 200) ... elseif ($codi < 300) ... ja tens les dues primeres famílies. L\'última pot anar a l\'else.',
                "if (\$codi < 200) {\n    \$familia = \"1xx Informació\";\n} elseif (\$codi < 300) {\n    \$familia = \"2xx Èxit\";\n} // ...",
            ],
            'preguntes' => [
                'Per què a l\'elseif ($codi < 300) no cal escriure també $codi >= 200?',
                'Si una pàgina et dona un 404, el problema és del navegador o del servidor?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Com que el codi ja està validat entre 100 i 599, els trams de menor a major cobreixen tots els casos i l\'últim pot anar a l\'else.',
        ],
        [
            'titol' => 'El nom dels codis habituals',
            'conceptes' => ['switch'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> amb un <code>switch</code>, mostra el nom dels codis més habituals. Per a la resta, mostra
                <code>Nom: (no és dels més habituals)</code>.</p>
                <table class="taula">
                  <tr><th>Codi</th><th>Nom</th></tr>
                  <tr><td>200</td><td>OK</td></tr>
                  <tr><td>301</td><td>Moved Permanently</td></tr>
                  <tr><td>302</td><td>Found</td></tr>
                  <tr><td>403</td><td>Forbidden</td></tr>
                  <tr><td>404</td><td>Not Found</td></tr>
                  <tr><td>500</td><td>Internal Server Error</td></tr>
                </table>
                <pre class="term">Nom: Not Found</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'Fes servir un switch per triar el nom.'],
                ['regex' => '/\bdefault\s*:/', 'missatge' => 'Afegeix un default per als codis que no són a la taula.'],
            ],
            'proves' => [
                $nom('200', 'OK'),
                $nom('301', 'Moved Permanently'),
                $nom('302', 'Found'),
                $nom('403', 'Forbidden'),
                $nom('404', 'Not Found'),
                $nom('500', 'Internal Server Error'),
                $nom('418', '(no és dels més habituals)'),
            ],
            'pistes' => [
                'Cada codi de la taula és un cas. Els que no hi són han d\'anar a parar a un cas per defecte.',
                'switch ($codi) { case 200: ... break; ... default: ... }. No t\'oblidis el break a cada cas!',
                "switch (\$codi) {\n    case 200:\n        \$nom = \"OK\";\n        break;\n    // ...\n    default:\n        \$nom = \"(no és dels més habituals)\";\n}",
            ],
            'preguntes' => [
                'Què passa si t\'oblides un break dins del switch?',
                'Quina diferència hi ha entre un 301 i un 302 per a un cercador com Google?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'El switch compara el codi amb cada case; el default recull tots els que no són a la llista.',
        ],
    ],
];
