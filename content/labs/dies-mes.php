<?php
/**
 * Laboratori: Quants dies té aquest mes?
 * Basat en l'Exercici 5 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Quants dies té aquest mes?

$any = (int) readline("Introdueix l'any: ");

// Divisible entre 4 i no entre 100, o bé divisible entre 400
$esTraspas = ($any % 4 == 0 && $any % 100 != 0) || $any % 400 == 0;
echo "L'any $any " . ($esTraspas ? "és" : "no és") . " de traspàs." . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

$mes = (int) readline("Introdueix el mes (1-12): ");
if ($mes < 1 || $mes > 12) {
    echo "Error: el mes ha de ser un número de l'1 al 12." . PHP_EOL;
    exit(1);
}

PHP;

$sol3 = $sol2 . <<<'PHP'

if ($mes == 2) {
    $dies = $esTraspas ? 29 : 28;
} elseif ($mes == 4 || $mes == 6 || $mes == 9 || $mes == 11) {
    $dies = 30;
} else {
    $dies = 31;
}
echo "El mes $mes de l'any $any té $dies dies." . PHP_EOL;

PHP;

$traspas = fn(string $any, bool $si) => ['nom' => "$any " . ($si ? '(sí)' : '(no)'), 'stdin' => "$any\n1\n", 'conte' => ["L'any $any " . ($si ? 'és' : 'no és') . ' de traspàs.']];
$dies = fn(string $any, string $mes, int $n) => ['nom' => "Mes $mes de $any → $n dies", 'stdin' => "$any\n$mes\n", 'conte' => ["El mes $mes de l'any $any té $n dies."]];

return [
    'id' => 'dies-mes',
    'ordre' => 20,
    'titol' => 'Quants dies té aquest mes?',
    'fitxer' => 'Exercici05_dies_del_mes.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.3 Operadors · 2.4 Condicionals',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 5 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Calcula si un any és de traspàs amb una sola expressió amb && i ||, i quants dies té un mes d\'aquell any.',
    'xp_final' => 35,
    'paraules_clau' => ['traspàs', '%', '&&', '||', 'ternari', 'booleà'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Quants dies té aquest mes?

        PHP,

    'context' => <<<'HTML'
        <p>Moltes aplicacions de calendari necessiten saber quants dies té un mes concret. El cas complicat és el febrer,
        que depèn de si l'any és de traspàs.</p>
        <p><strong>Com es prova:</strong> el programa llegeix dues respostes amb <code>readline()</code>: escriu l'any i el mes
        en dues línies a la caixa «Entrada per a readline()».</p>
        <pre class="term">Introdueix l'any: 2024
        L'any 2024 és de traspàs.
        Introdueix el mes (1-12): 2
        El mes 2 de l'any 2024 té 29 dies.</pre>
        HTML,

    'resum_apres' => [
        '$n % 4 == 0 vol dir que $n és divisible entre 4: el residu de la divisió és 0.',
        'Una condició complexa es pot desar en una variable booleana ($esTraspas) i fer-la servir després.',
        'Combinar condicions amb && i ||, i agrupar-les amb parèntesis.',
        'L\'operador ternari (condició ? a : b) tria entre dos valors en una sola línia.',
    ],

    'passos' => [
        [
            'titol' => 'Any de traspàs',
            'conceptes' => ['operadors lògics', 'mòdul', 'booleans'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Un any és de traspàs si és <strong>divisible entre 4 i no ho és entre 100</strong>, o bé si és <strong>divisible entre 400</strong>.</p>
                <p><strong>Objectiu:</strong> demana l'any amb <code>readline("Introdueix l'any: ")</code>, converteix-lo a enter i desa
                el resultat en una variable booleana <code>$esTraspas</code>, escrita en <strong>una sola expressió</strong> amb
                <code>&amp;&amp;</code> i <code>||</code>. Després mostra una d'aquestes frases:</p>
                <pre class="term">L'any 2024 és de traspàs.
                L'any 2023 no és de traspàs.</pre>
                <p class="nota">L'enunciat demana provar-lo amb 2024 (sí), 2023 (no), 1900 (no) i 2000 (sí).</p>
                HTML,
            'codi' => [
                ['regex' => '/\$esTraspas\s*=/', 'missatge' => 'Desa el resultat en una variable booleana $esTraspas.'],
                ['regex' => '/%\s*4\b/', 'missatge' => 'Fes servir el mòdul (% 4) per saber si és divisible entre 4.'],
                ['regex' => '/&&/', 'missatge' => 'La condició ha de fer servir &&.'],
                ['regex' => '/\|\|/', 'missatge' => 'La condició ha de fer servir ||.'],
            ],
            'proves' => [
                $traspas('2024', true),
                $traspas('2023', false),
                $traspas('1900', false),
                $traspas('2000', true),
            ],
            'pistes' => [
                'Hi ha dues maneres de ser de traspàs; n\'hi ha prou que es compleixi una de les dues. La primera té dues condicions alhora.',
                '$any % 4 == 0 és «divisible entre 4» i $any % 100 != 0 és «no divisible entre 100». Ajunta-les amb && i, amb ||, afegeix la del 400.',
                "\$esTraspas = (\$any % 4 == 0 && \$any % 100 != 0) || \$any % 400 == 0;\necho \"L'any \$any \" . (\$esTraspas ? \"és\" : \"no és\") . \" de traspàs.\" . PHP_EOL;",
            ],
            'preguntes' => [
                'Per què 1900 no és de traspàs si és divisible entre 4?',
                'Què val 2023 % 4? I 2024 % 4?',
                'Quins parèntesis canvien el resultat de la condició i quins no?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'La condició es calcula una vegada i queda a $esTraspas (true o false). El ternari tria «és» o «no és» per a la frase.',
        ],
        [
            'titol' => 'Llegir i validar el mes',
            'conceptes' => ['readline', 'validació de dades', 'operadors lògics'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> després de la frase del traspàs, demana el mes amb
                <code>readline("Introdueix el mes (1-12): ")</code> i converteix-lo a enter. Si no és entre 1 i 12, mostra
                <code>Error: el mes ha de ser un número de l'1 al 12.</code> i atura el programa amb <code>exit(1)</code>.</p>
                <pre class="term">Introdueix l'any: 2024
                L'any 2024 és de traspàs.
                Introdueix el mes (1-12): 13
                Error: el mes ha de ser un número de l'1 al 12.</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bexit\b/', 'missatge' => 'Atura el programa amb exit(1) quan el mes no és vàlid.'],
            ],
            'proves' => [
                ['nom' => 'Mes 13', 'stdin' => "2024\n13\n", 'conte' => ["Error: el mes ha de ser un número de l'1 al 12."], 'codi_sortida' => 1],
                ['nom' => 'Mes 0', 'stdin' => "2024\n0\n", 'conte' => ["Error: el mes ha de ser un número de l'1 al 12."], 'codi_sortida' => 1],
                ['nom' => 'Mes 12: cap error', 'stdin' => "2024\n12\n", 'no_conte' => ['Error'], 'codi_sortida' => 0],
            ],
            'pistes' => [
                'Un mes és incorrecte si és massa petit o massa gran. N\'hi ha prou que passi una de les dues coses.',
                'if ($mes < 1 || $mes > 12) { ... exit(1); }',
                "\$mes = (int) readline(\"Introdueix el mes (1-12): \");\nif (\$mes < 1 || \$mes > 12) {\n    echo \"Error: el mes ha de ser un número de l'1 al 12.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Per què aquí fem servir || i no &&?',
                'Què val (int) "febrer"? Per què el programa també el rebutja?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Amb || n\'hi ha prou que una de les dues condicions sigui certa perquè el mes sigui incorrecte.',
        ],
        [
            'titol' => 'Quants dies té',
            'conceptes' => ['condicionals', 'operador ternari'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p>El febrer (mes 2) té 29 dies si l'any és de traspàs i 28 si no ho és. Els mesos 4, 6, 9 i 11 en tenen 30. La resta, 31.</p>
                <p><strong>Objectiu:</strong> calcula els dies i mostra:</p>
                <pre class="term">El mes 2 de l'any 2024 té 29 dies.</pre>
                HTML,
            'codi' => [
                ['regex' => '/\$esTraspas\s*\?/', 'missatge' => 'Per al febrer, reaprofita $esTraspas amb l\'operador ternari ($esTraspas ? 29 : 28).'],
            ],
            'proves' => [
                $dies('2024', '2', 29),
                $dies('2023', '2', 28),
                $dies('1900', '2', 28),
                $dies('2000', '2', 29),
                $dies('2025', '4', 30),
                $dies('2025', '11', 30),
                $dies('2025', '1', 31),
                $dies('2025', '12', 31),
            ],
            'pistes' => [
                'Primer mira si és el febrer; després, si és un dels quatre mesos de 30 dies; i si no, en té 31.',
                'Per al febrer ja tens la resposta a $esTraspas: $esTraspas ? 29 : 28. Per als de 30 dies, ajunta les quatre comparacions amb ||.',
                "if (\$mes == 2) {\n    \$dies = \$esTraspas ? 29 : 28;\n} elseif (\$mes == 4 || \$mes == 6 || \$mes == 9 || \$mes == 11) {\n    \$dies = 30;\n} else {\n    // ...\n}",
            ],
            'preguntes' => [
                'Com escriuries la comparació dels mesos de 30 dies amb un switch?',
                'Per què és útil haver desat $esTraspas en una variable al primer pas?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'El febrer reaprofita la variable booleana amb el ternari; els mesos de 30 dies s\'ajunten amb ||; la resta va a l\'else.',
        ],
    ],
];
