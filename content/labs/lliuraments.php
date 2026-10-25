<?php
/**
 * Laboratori: Calendari de lliuraments.
 * Basat en l'activitat 3 del Tema 3 (prof. David Porti). Adaptació: la data d'avui es pot passar com a argument
 * perquè el programa (i les proves) donin sempre el mateix resultat.
 */

$sol1 = <<<'PHP'
<?php
// Calendari de lliuraments
// Ús: php lliuraments.php [data d'avui, AAAA-MM-DD]

const DIES = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];

$lliuraments = [
    "Exercicis 01"       => "2026-10-16",
    "Exercicis 02"       => "2026-11-04",
    "Activitat de Nadal" => "2026-12-18",
    "Projecte final"     => "2027-05-24",
];

// Si no ens passen cap data, agafem la d'avui
$avui = new DateTime($argv[1] ?? 'today');

// format('N') dona 1 per a dilluns ... 7 per a diumenge; l'array comença a 0
echo "Avui és " . DIES[$avui->format('N') - 1] . ", " . $avui->format('d/m/Y') . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

echo PHP_EOL . "--- LLIURAMENTS ---" . PHP_EOL;
foreach ($lliuraments as $nom => $data) {
    $dataLliurament = new DateTime($data);
    $dia = DIES[$dataLliurament->format('N') - 1];
    echo "- $nom: $dia " . $dataLliurament->format('d/m/Y') . PHP_EOL;
}

PHP;

$sol3 = str_replace(
    '    echo "- $nom: $dia " . $dataLliurament->format(\'d/m/Y\') . PHP_EOL;',
    <<<'PHP'
    $diferencia = $avui->diff($dataLliurament);

    if ($diferencia->days === 0) {
        $estat = "és avui!";
    } elseif ($diferencia->invert === 1) {   // invert = 1 vol dir que la data ja ha passat
        $estat = "ja ha passat";
    } elseif ($diferencia->days === 1) {
        $estat = "falta 1 dia";
    } else {
        $estat = "falten {$diferencia->days} dies";
    }
    echo "- $nom: $dia " . $dataLliurament->format('d/m/Y') . " ($estat)" . PHP_EOL;
PHP,
    $sol2
);

return [
    'id' => 'lliuraments',
    'ordre' => 120,
    'titol' => 'Calendari de lliuraments',
    'fitxer' => 'lliuraments.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.1 Data i hora',
    'origen' => [
        'fitxer' => 'Tema 3 - Funcions integrades.pdf',
        'descripcio' => 'Activitat 3 del Tema 3, «Calendari de lliuraments» (David Porti)',
    ],
    'resum' => 'Mostra les dates de lliurament amb el dia de la setmana i calcula quants dies falten per a cadascuna amb DateTime::diff().',
    'xp_final' => 40,
    'paraules_clau' => ['DateTime', 'diff', 'format', 'date', 'dies'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Calendari de lliuraments
        // Ús: php lliuraments.php [data d'avui, AAAA-MM-DD]

        PHP,

    'context' => <<<'HTML'
        <p>A partir d'un array de dates de lliurament, mostra-les en format d/m/Y amb el dia de la setmana i calcula
        quants dies falten per a cadascuna amb <code>DateTime::diff()</code>.</p>
        <p><strong>Adaptació per poder-ho provar:</strong> la data d'avui es pot passar com a argument (<code>php lliuraments.php 2026-10-07</code>).
        Si no se'n passa cap, s'agafa la data real.</p>
        <pre class="term">$ php lliuraments.php 2026-10-07
        Avui és dimecres, 07/10/2026

        --- LLIURAMENTS ---
        - Exercicis 01: divendres 16/10/2026 (falten 9 dies)
        - Exercicis 02: dimecres 04/11/2026 (falten 28 dies)
        - Activitat de Nadal: divendres 18/12/2026 (falten 72 dies)
        - Projecte final: dilluns 24/05/2027 (falten 229 dies)</pre>
        HTML,

    'resum_apres' => [
        'Crear dates amb new DateTime() i mostrar-les amb format().',
        'Traduir el dia de la setmana: format(\'N\') dona un número de l\'1 al 7 que serveix d\'índex d\'un array.',
        'Calcular diferències amb diff(): days és el total de dies i invert diu si la data és en el passat.',
        'Tenir en compte els casos especials (avui, 1 dia, ja passat) per donar missatges correctes.',
    ],

    'passos' => [
        [
            'titol' => 'La data d\'avui en català',
            'conceptes' => ['DateTime', 'arrays indexats'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>Crea l'array associatiu <code>$lliuraments</code> amb aquestes dates (nom => data):</p>
                <table class="taula">
                  <tr><td>Exercicis 01</td><td>2026-10-16</td></tr>
                  <tr><td>Exercicis 02</td><td>2026-11-04</td></tr>
                  <tr><td>Activitat de Nadal</td><td>2026-12-18</td></tr>
                  <tr><td>Projecte final</td><td>2027-05-24</td></tr>
                </table>
                <p>Crea la data d'avui amb <code>new DateTime($argv[1] ?? 'today')</code>.</p>
                <p><strong>Objectiu:</strong> mostra <code>Avui és dimecres, 07/10/2026</code>. Els noms dels dies han d'estar en català:
                guarda'ls en un array (o una constant) i fes servir <code>format('N')</code>, que dona 1 per a dilluns i 7 per a diumenge.</p>
                HTML,
            'codi' => [
                ['regex' => '/new\s+DateTime\s*\(/', 'missatge' => 'Crea les dates amb new DateTime().'],
                ['regex' => '/format\s*\(\s*[\'"]N[\'"]\s*\)/', 'missatge' => 'Obtén el dia de la setmana amb format(\'N\').'],
            ],
            'proves' => [
                ['nom' => 'Dimecres 7 d\'octubre', 'args' => ['2026-10-07'], 'conte' => ['Avui és dimecres, 07/10/2026']],
                ['nom' => 'Divendres de Nadal', 'args' => ['2026-12-25'], 'conte' => ['Avui és divendres, 25/12/2026']],
                ['nom' => 'Un diumenge', 'args' => ['2026-10-11'], 'conte' => ['Avui és diumenge, 11/10/2026']],
            ],
            'pistes' => [
                'PHP et dona el dia de la setmana com a número. Si tens un array amb els 7 noms en català, el número et diu quina posició has d\'agafar.',
                'format(\'N\') va de 1 (dilluns) a 7 (diumenge), però els arrays comencen per 0: resta-li 1. Per a la data: format(\'d/m/Y\').',
                "\$dies = ['dilluns', 'dimarts', 'dimecres', 'dijous', 'divendres', 'dissabte', 'diumenge'];\n\$avui = new DateTime(\$argv[1] ?? 'today');\necho \"Avui és \" . \$dies[\$avui->format('N') - 1] . \", \" . \$avui->format('d/m/Y') . PHP_EOL;",
            ],
            'preguntes' => [
                'Si format(\'N\') dona 3, quina posició de l\'array has de llegir?',
                'Què fa l\'operador ?? a $argv[1] ?? \'today\'?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'L\'array de dies fa de «traductor»: format(\'N\') - 1 és la posició del dia.',
        ],
        [
            'titol' => 'Llistar els lliuraments',
            'conceptes' => ['DateTime', 'foreach'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> després d'una línia en blanc i el títol <code>--- LLIURAMENTS ---</code>, mostra cada lliurament
                amb el dia de la setmana i la data en format d/m/Y:</p>
                <pre class="term">- Exercicis 01: divendres 16/10/2026</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bforeach\s*\(/', 'missatge' => 'Recorre els lliuraments amb foreach ($lliuraments as $nom => $data).'],
            ],
            'proves' => [
                ['nom' => 'Llista de lliuraments', 'args' => ['2026-10-07'], 'conte' => [
                    "--- LLIURAMENTS ---\n- Exercicis 01: divendres 16/10/2026",
                    '- Exercicis 02: dimecres 04/11/2026',
                    '- Activitat de Nadal: divendres 18/12/2026',
                    '- Projecte final: dilluns 24/05/2027',
                ]],
            ],
            'pistes' => [
                'Per a cada lliurament has de fer el mateix que has fet amb la data d\'avui: crear la data i mostrar el dia en català.',
                'foreach ($lliuraments as $nom => $data) { $d = new DateTime($data); ... }',
                "foreach (\$lliuraments as \$nom => \$data) {\n    \$dataLliurament = new DateTime(\$data);\n    \$dia = DIES[\$dataLliurament->format('N') - 1];\n    echo \"- \$nom: \$dia \" . \$dataLliurament->format('d/m/Y') . PHP_EOL;\n}",
            ],
            'preguntes' => [
                'Al foreach, què hi ha a $nom i què hi ha a $data?',
                'Podries reutilitzar el codi del dia de la setmana sense copiar-lo? (pista: una funció)',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'El mateix truc de l\'array de dies serveix per a cada data del foreach.',
        ],
        [
            'titol' => 'Quants dies falten?',
            'conceptes' => ['DateTime', 'if / elseif / else'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><code>$avui-&gt;diff($data)</code> retorna la diferència. <code>-&gt;days</code> és el nombre total de dies (sempre positiu) i
                <code>-&gt;invert</code> val 1 si la data ja ha passat.</p>
                <p><strong>Objectiu:</strong> afegeix entre parèntesis, al final de cada línia:</p>
                <table class="taula">
                  <tr><td>Data passada</td><td><code>(ja ha passat)</code></td></tr>
                  <tr><td>Avui mateix</td><td><code>(és avui!)</code></td></tr>
                  <tr><td>Demà</td><td><code>(falta 1 dia)</code></td></tr>
                  <tr><td>Més endavant</td><td><code>(falten 9 dies)</code></td></tr>
                </table>
                HTML,
            'codi' => [
                ['regex' => '/->\s*diff\s*\(/', 'missatge' => 'Calcula la diferència amb ->diff() (requisit de l\'activitat).'],
            ],
            'proves' => [
                ['nom' => 'Des del 07/10/2026', 'args' => ['2026-10-07'], 'conte' => [
                    '- Exercicis 01: divendres 16/10/2026 (falten 9 dies)',
                    '- Exercicis 02: dimecres 04/11/2026 (falten 28 dies)',
                    '- Activitat de Nadal: divendres 18/12/2026 (falten 72 dies)',
                    '- Projecte final: dilluns 24/05/2027 (falten 229 dies)',
                ]],
                ['nom' => 'El dia abans d\'un lliurament', 'args' => ['2026-10-15'], 'conte' => ['- Exercicis 01: divendres 16/10/2026 (falta 1 dia)']],
                ['nom' => 'El mateix dia d\'un lliurament', 'args' => ['2026-11-04'], 'conte' => ['- Exercicis 02: dimecres 04/11/2026 (és avui!)']],
                ['nom' => 'Lliuraments passats', 'args' => ['2026-12-20'], 'conte' => [
                    '- Exercicis 01: divendres 16/10/2026 (ja ha passat)',
                    '- Activitat de Nadal: divendres 18/12/2026 (ja ha passat)',
                    '- Projecte final: dilluns 24/05/2027 (falten 155 dies)',
                ]],
            ],
            'pistes' => [
                'Hi ha quatre casos possibles. Pensa en quin ordre els has de comprovar perquè cada lliurament caigui en un sol cas.',
                '$dif = $avui->diff($dataLliurament); Comprova primer $dif->days === 0, després $dif->invert === 1, després $dif->days === 1, i si no, la resta.',
                "\$diferencia = \$avui->diff(\$dataLliurament);\nif (\$diferencia->days === 0) {\n    \$estat = \"és avui!\";\n} elseif (\$diferencia->invert === 1) {\n    \$estat = \"ja ha passat\";\n} elseif ...",
            ],
            'preguntes' => [
                'Si una data va passar fa 3 dies, quant val ->days? I ->invert?',
                'Per què cal comprovar «és avui» abans que «ja ha passat»?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'days sempre és positiu: és invert el que diu si la data és abans o després d\'avui.',
        ],
    ],
];
