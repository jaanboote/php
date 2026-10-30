<?php
/**
 * Laboratori: Fitxa de notes.
 * Basat en l'activitat proposada 1 del Tema 3 (prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Fitxa de notes: php fitxa_notes.php Nom:nota,nota,... [Nom:nota,...]

if ($argc < 2) {
    echo "Ús: php fitxa_notes.php Nom:nota,nota,... [Nom:nota,...]" . PHP_EOL;
    exit(1);
}

// Nom => mitjana, en l'ordre en què arriben
$mitjanes = [];
for ($i = 1; $i < $argc; $i++) {
    $parts = explode(":", $argv[$i]);
    if (count($parts) != 2 || $parts[0] === "") {
        echo "ERROR: «{$argv[$i]}» no té el format Nom:notes." . PHP_EOL;
        exit(1);
    }
    [$nom, $text] = $parts;
    $notes = explode(",", $text);
    foreach ($notes as $nota) {
        if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
            echo "ERROR: la nota «{$nota}» de $nom no és vàlida." . PHP_EOL;
            exit(1);
        }
    }
    $mitjanes[$nom] = array_sum($notes) / count($notes);
}

foreach ($mitjanes as $nom => $mitjana) {
    echo "$nom: " . number_format($mitjana, 2, ",", ".") . PHP_EOL;
}

PHP;

$sol2 = $sol1 . <<<'PHP'

// usort necessita una llista: cada element és [nom, mitjana]
$llista = [];
foreach ($mitjanes as $nom => $mitjana) {
    $llista[] = ["nom" => $nom, "mitjana" => $mitjana];
}
usort($llista, fn($a, $b) => $b["mitjana"] <=> $a["mitjana"]);

echo PHP_EOL . "Classificació:" . PHP_EOL;
foreach ($llista as $posicio => $alumne) {
    echo ($posicio + 1) . ". {$alumne['nom']} - " . number_format($alumne["mitjana"], 2, ",", ".") . PHP_EOL;
}

PHP;

$sol3 = str_replace(
    "    echo (\$posicio + 1) . \". {\$alumne['nom']} - \" . number_format(\$alumne[\"mitjana\"], 2, \",\", \".\") . PHP_EOL;\n}\n",
    "    \$suspes = \$alumne[\"mitjana\"] < 5 ? \" (suspès)\" : \"\";\n"
    . "    echo (\$posicio + 1) . \". {\$alumne['nom']} - \" . number_format(\$alumne[\"mitjana\"], 2, \",\", \".\") . \$suspes . PHP_EOL;\n}\n"
    . "\n\$aprovats = count(array_filter(\$mitjanes, fn(\$m) => \$m >= 5));\n"
    . "echo PHP_EOL . \"Mitjana de la classe: \" . number_format(array_sum(\$mitjanes) / count(\$mitjanes), 2, \",\", \".\") . PHP_EOL;\n"
    . "echo \"Aprovats: \$aprovats de \" . count(\$mitjanes) . PHP_EOL;\n",
    $sol2
);

$classe = ['Anna:7.5,8,9.25', 'Biel:5,6.5,4', 'Carla:9,9.5,10', 'Dani:4,5,3.5'];

return [
    'id' => 'fitxa-notes',
    'ordre' => 140,
    'titol' => 'Fitxa de notes',
    'fitxer' => 'fitxa_notes.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.1 Números · 3.3 Arrays',
    'origen' => [
        'fitxer' => 'Tema 3 - Funcions integrades de PHP.pdf',
        'descripcio' => 'Activitat proposada 1 del Tema 3, «Fitxa de notes» (David Porti)',
    ],
    'resum' => 'Calcula la mitjana de cada alumne, mostra-la amb number_format() i ordena\'ls de més a menys nota amb usort().',
    'xp_final' => 40,
    'paraules_clau' => ['number_format', 'usort', 'array_sum', 'explode', 'mitjana', '<=>'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Fitxa de notes: php fitxa_notes.php Nom:nota,nota,... [Nom:nota,...]

        PHP,

    'context' => <<<'HTML'
        <p>L'activitat parteix d'un array d'alumnes amb les seves notes: cal calcular la mitjana de cadascú, mostrar-la amb
        <code>number_format()</code> i ordenar-los de més a menys nota amb <code>usort()</code>.</p>
        <p><strong>Adaptació:</strong> perquè les proves puguin canviar la classe, cada alumne arriba com un argument amb el format
        <code>Nom:nota,nota,...</code> (els decimals de les notes amb punt).</p>
        <pre class="term">$ php fitxa_notes.php Anna:7.5,8,9.25 Biel:5,6.5,4 Carla:9,9.5,10 Dani:4,5,3.5
        Anna: 8,25
        Biel: 5,17
        Carla: 9,50
        Dani: 4,17

        Classificació:
        1. Carla - 9,50
        2. Anna - 8,25
        3. Biel - 5,17
        4. Dani - 4,17 (suspès)

        Mitjana de la classe: 6,77
        Aprovats: 3 de 4</pre>
        HTML,

    'resum_apres' => [
        'explode() parteix un text en trossos: primer pel «:» i després per les comes.',
        'array_sum() / count() és la mitjana d\'un array de números.',
        'number_format($n, 2, ",", ".") mostra 2 decimals amb coma, com fem en català.',
        'usort() ordena amb una funció que compara dos elements; amb $b <=> $a l\'ordre és de més a menys.',
    ],

    'passos' => [
        [
            'titol' => 'Llegir les notes i fer les mitjanes',
            'conceptes' => ['explode', 'array_sum', 'number_format', 'validació de dades'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Sense arguments: <code>Ús: php fitxa_notes.php Nom:nota,nota,... [Nom:nota,...]</code> i <code>exit(1)</code>.</li>
                  <li>Per a cada argument, parteix-lo pel <code>:</code> amb <code>explode()</code>. Si no surten exactament 2 trossos
                      (o el nom és buit): <code>ERROR: «Anna» no té el format Nom:notes.</code> i <code>exit(1)</code>.</li>
                  <li>Parteix les notes per les comes. Cada nota ha de ser un número de 0 a 10; si no:
                      <code>ERROR: la nota «deu» de Anna no és vàlida.</code> i <code>exit(1)</code>.</li>
                  <li>Calcula la mitjana amb <code>array_sum()</code> i <code>count()</code> i mostra-les en l'ordre en què arriben, amb
                      <strong>2 decimals i coma</strong>: <code>Anna: 8,25</code>.</li>
                </ul>
                HTML,
            'codi' => [
                ['regex' => '/\bexplode\s*\(/', 'missatge' => 'Parteix cada argument amb explode().'],
                ['regex' => '/\barray_sum\s*\(/', 'missatge' => 'Suma les notes amb array_sum().'],
                ['regex' => '/\bnumber_format\s*\(/', 'missatge' => 'Mostra la mitjana amb number_format().'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php fitxa_notes.php Nom:nota,nota,... [Nom:nota,...]'], 'codi_sortida' => 1],
                ['nom' => 'La classe de l\'exemple', 'args' => $classe, 'conte' => ['Anna: 8,25', 'Biel: 5,17', 'Carla: 9,50', 'Dani: 4,17']],
                ['nom' => 'Una sola nota', 'args' => ['Eva:10'], 'conte' => ['Eva: 10,00']],
                ['nom' => 'Sense dos punts', 'args' => ['Anna'], 'conte' => ['ERROR: «Anna» no té el format Nom:notes.'], 'codi_sortida' => 1],
                ['nom' => 'Nota que no és un número', 'args' => ['Anna:7,deu'], 'conte' => ['ERROR: la nota «deu» de Anna no és vàlida.'], 'codi_sortida' => 1],
                ['nom' => 'Nota més gran de 10', 'args' => ['Anna:7,11'], 'conte' => ['ERROR: la nota «11» de Anna no és vàlida.'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Recorre els arguments des de l\'1. Cada un té dues parts (nom i notes) i les notes són una llista. Valida-ho tot abans de calcular.',
                'explode(":", $argv[$i]) dona [nom, notes]; explode(",", $notes) dona les notes. array_sum($notes) / count($notes) és la mitjana, i number_format($m, 2, ",", ".") la mostra amb coma.',
                "for (\$i = 1; \$i < \$argc; \$i++) {\n    \$parts = explode(\":\", \$argv[\$i]);\n    // ... comprovar que n'hi ha 2 ...\n    \$notes = explode(\",\", \$parts[1]);\n    \$mitjanes[\$parts[0]] = array_sum(\$notes) / count(\$notes);\n}",
            ],
            'preguntes' => [
                'Per què les notes es poden sumar amb array_sum() si són textos com "7.5"?',
                'Quins són els 4 arguments de number_format() i què fa cada un?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Dos explode: un pel «:» i un per les comes. Validem cada nota abans de fer la mitjana, i number_format() la mostra a la catalana.',
        ],
        [
            'titol' => 'Classificació amb usort()',
            'conceptes' => ['usort', 'operador nau espacial'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><code>usort()</code> ordena una <strong>llista</strong> fent servir una funció que compara dos elements.
                L'operador <code>&lt;=&gt;</code> retorna -1, 0 o 1, que és just el que espera.</p>
                <p><strong>Objectiu:</strong> després de les mitjanes, deixa una línia en blanc i mostra la classificació, de més a
                menys nota:</p>
                <pre class="term">Classificació:
                1. Carla - 9,50
                2. Anna - 8,25
                3. Biel - 5,17
                4. Dani - 4,17</pre>
                HTML,
            'codi' => [
                ['regex' => '/\busort\s*\(/', 'missatge' => 'Ordena la llista amb usort().'],
                ['regex' => '/<=>/', 'missatge' => 'Compara les mitjanes amb l\'operador <=>.'],
            ],
            'proves' => [
                ['nom' => 'La classe de l\'exemple', 'args' => $classe, 'conte' => ['Classificació:', '1. Carla - 9,50', '2. Anna - 8,25', '3. Biel - 5,17', '4. Dani - 4,17']],
                ['nom' => 'Dos alumnes al revés', 'args' => ['Pau:3', 'Rita:8'], 'conte' => ['1. Rita - 8,00', '2. Pau - 3,00']],
            ],
            'pistes' => [
                'Passa les mitjanes a una llista d\'elements amb nom i mitjana, ordena-la i recorre-la mostrant la posició.',
                'usort($llista, fn($a, $b) => $b["mitjana"] <=> $a["mitjana"]); Si poses $a <=> $b, l\'ordre és de menys a més.',
                "\$llista = [];\nforeach (\$mitjanes as \$nom => \$mitjana) {\n    \$llista[] = [\"nom\" => \$nom, \"mitjana\" => \$mitjana];\n}\nusort(\$llista, fn(\$a, \$b) => \$b[\"mitjana\"] <=> \$a[\"mitjana\"]);",
            ],
            'preguntes' => [
                'Què retorna 8 <=> 5? I 5 <=> 8?',
                'Per què usort() no conserva les claus (els noms) de l\'array?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'usort() reordena la llista amb la funció de comparació; $b <=> $a dona l\'ordre de més a menys.',
        ],
        [
            'titol' => 'Suspesos i resum de la classe',
            'conceptes' => ['array_filter', 'operador ternari'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>A la classificació, afegeix <code> (suspès)</code> darrere de la nota de qui tingui menys d'un 5.</li>
                  <li>Al final, deixa una línia en blanc i mostra la mitjana de la classe (la mitjana de les mitjanes) i quants
                      aprovats hi ha:</li>
                </ul>
                <pre class="term">4. Dani - 4,17 (suspès)

                Mitjana de la classe: 6,77
                Aprovats: 3 de 4</pre>
                HTML,
            'codi' => [],
            'proves' => [
                ['nom' => 'La classe de l\'exemple', 'args' => $classe, 'conte' => ['4. Dani - 4,17 (suspès)', 'Mitjana de la classe: 6,77', 'Aprovats: 3 de 4'], 'no_conte' => ['Biel - 5,17 (suspès)']],
                ['nom' => 'Tots aprovats', 'args' => ['Pau:5', 'Rita:8'], 'conte' => ['Mitjana de la classe: 6,50', 'Aprovats: 2 de 2'], 'no_conte' => ['suspès']],
            ],
            'pistes' => [
                'El «(suspès)» es decideix a cada línia de la classificació. El resum surt de l\'array de mitjanes que ja tens.',
                'Un ternari: $mitjana < 5 ? " (suspès)" : "". Per comptar aprovats: count(array_filter($mitjanes, fn($m) => $m >= 5)).',
                "\$suspes = \$alumne[\"mitjana\"] < 5 ? \" (suspès)\" : \"\";\n// ...\n\$aprovats = count(array_filter(\$mitjanes, fn(\$m) => \$m >= 5));",
            ],
            'preguntes' => [
                'Un 5,00 és aprovat o suspès amb la condició < 5?',
                'Què retorna array_filter() i per què cal fer-ne un count()?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'El ternari afegeix el text només quan cal. array_filter() es queda amb les mitjanes aprovades i count() les compta.',
        ],
    ],
];
