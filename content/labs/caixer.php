<?php
/**
 * Laboratori: Caixer automàtic.
 * Basat en l'Exercici 13 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Caixer automàtic

$valors = [500, 200, 100, 50, 20, 10, 5, 2, 1];   // de més gran a més petit

$import = (int) readline("Quin import vols retirar (€)? ");
if ($import <= 0) {
    echo "Error: l'import ha de ser més gran que 0." . PHP_EOL;
    exit(1);
}
$restant = $import;

PHP;

$sol2 = $sol1 . <<<'PHP'

echo PHP_EOL . "--- DESGLOSSAMENT DE $import € ---" . PHP_EOL;
$pecesUsades = [];

// Algorisme voraç: sempre la peça més gran que hi càpiga
foreach ($valors as $valor) {
    $quantitat = intdiv($restant, $valor);
    $restant = $restant % $valor;
    if ($quantitat > 0) {
        $tipus = $valor >= 5 ? "bitllet" : "moneda";
        echo "$quantitat x $valor € ($tipus)" . PHP_EOL;
        $pecesUsades[] = $quantitat;
    }
}

PHP;

$sol3 = $sol2 . <<<'PHP'

echo PHP_EOL . "Tipus de peces diferents: " . count($pecesUsades) . PHP_EOL;
echo "Total de peces: " . array_sum($pecesUsades) . PHP_EOL;

PHP;

return [
    'id' => 'caixer',
    'ordre' => 30,
    'titol' => 'Caixer automàtic',
    'fitxer' => 'Exercici13_caixer_automatic.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.5 Arrays indexats',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 13 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Desglossa un import en el mínim de bitllets i monedes. Practicaràs foreach, intdiv(), %, count() i array_sum().',
    'xp_final' => 40,
    'paraules_clau' => ['foreach', 'intdiv', 'array_sum', 'count', 'bitllets'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Caixer automàtic

        PHP,

    'context' => <<<'HTML'
        <p>Un caixer automàtic ha de donar els diners amb el mínim nombre de bitllets i monedes possible. Per aconseguir-ho,
        comença pels bitllets més grans i va baixant (algorisme <strong>voraç</strong>).</p>
        <pre class="term">Quin import vols retirar (€)? 387

        --- DESGLOSSAMENT DE 387 € ---
        1 x 200 € (bitllet)
        1 x 100 € (bitllet)
        1 x 50 € (bitllet)
        1 x 20 € (bitllet)
        1 x 10 € (bitllet)
        1 x 5 € (bitllet)
        1 x 2 € (moneda)

        Tipus de peces diferents: 7
        Total de peces: 7</pre>
        HTML,

    'resum_apres' => [
        'Recórrer un array indexat amb foreach.',
        'Separar quocient i residu amb intdiv() i l\'operador %.',
        'Anar omplint un array amb $array[] = valor i resumir-lo amb count() i array_sum().',
        'Un algorisme voraç: triar sempre l\'opció més gran possible a cada pas.',
    ],

    'passos' => [
        [
            'titol' => 'Els valors i l\'import',
            'conceptes' => ['arrays indexats', 'validació de dades'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Crea l'array <code>$valors = [500, 200, 100, 50, 20, 10, 5, 2, 1]</code>, ordenat de més gran a més petit.</p>
                <p><strong>Objectiu:</strong> demana un import enter amb <code>readline("Quin import vols retirar (€)? ")</code>.
                Si és 0 o negatiu, mostra un missatge que comenci per <code>Error</code> i surt. Si és correcte, guarda'l també a <code>$restant</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\$valors\s*=\s*\[\s*500\s*,\s*200/', 'missatge' => 'Crea l\'array $valors = [500, 200, 100, ...].'],
            ],
            'proves' => [
                ['nom' => 'Import 0', 'stdin' => "0\n", 'conte' => ['Error']],
                ['nom' => 'Import negatiu', 'stdin' => "-20\n", 'conte' => ['Error']],
                ['nom' => 'Import correcte: cap error', 'stdin' => "387\n", 'no_conte' => ['Error']],
            ],
            'pistes' => [
                'Comprova l\'import abans de fer res més. Recorda convertir el que retorna readline() a número.',
                '(int) readline(...) et dona un enter. if ($import <= 0) { ... exit; }',
                "\$import = (int) readline(\"Quin import vols retirar (€)? \");\nif (\$import <= 0) {\n    echo \"Error: l'import ha de ser més gran que 0.\" . PHP_EOL;\n    exit(1);\n}\n\$restant = \$import;",
            ],
            'preguntes' => [
                'Per què l\'array ha d\'estar ordenat de més gran a més petit?',
                'Què retorna (int) "abc"? Seria un import vàlid?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Validem abans de calcular. $restant és la quantitat que encara queda per repartir.',
        ],
        [
            'titol' => 'El desglossament',
            'conceptes' => ['foreach', 'intdiv i %'],
            'xp' => 35,
            'explicacio' => <<<'HTML'
                <p>Mostra el títol <code>--- DESGLOSSAMENT DE 387 € ---</code> (després d'una línia en blanc) i recorre <code>$valors</code> amb <strong>foreach</strong>.
                Per a cada valor:</p>
                <ul>
                  <li>quantes peces hi caben: <code>intdiv($restant, $valor)</code></li>
                  <li>el que queda: <code>$restant % $valor</code></li>
                </ul>
                <p><strong>Objectiu:</strong> si la quantitat és més gran que 0, mostra la línia (els valors de 5 o més són bitllets; la resta, monedes)
                i desa la quantitat a l'array <code>$pecesUsades</code>:</p>
                <pre class="term">2 x 50 € (bitllet)
                1 x 2 € (moneda)</pre>
                <p class="nota">No s'han de mostrar les peces que no es fan servir (res de «0 x 500 €»).</p>
                HTML,
            'codi' => [
                ['regex' => '/\bforeach\s*\(/', 'missatge' => 'Recorre els valors amb foreach.'],
                ['regex' => '/\bintdiv\s*\(/', 'missatge' => 'Calcula quantes peces hi caben amb intdiv().'],
                ['regex' => '/%/', 'missatge' => 'Calcula el que queda amb l\'operador %.'],
            ],
            'proves' => [
                ['nom' => '387 €', 'stdin' => "387\n", 'conte' => [
                    '--- DESGLOSSAMENT DE 387 € ---', "1 x 200 € (bitllet)\n1 x 100 € (bitllet)\n1 x 50 € (bitllet)\n1 x 20 € (bitllet)\n1 x 10 € (bitllet)\n1 x 5 € (bitllet)\n1 x 2 € (moneda)",
                ], 'no_conte' => ['0 x']],
                ['nom' => '1000 €', 'stdin' => "1000\n", 'conte' => ['2 x 500 € (bitllet)'], 'no_conte' => ['0 x', '200 €']],
                ['nom' => '3 €: monedes', 'stdin' => "3\n", 'conte' => ["1 x 2 € (moneda)\n1 x 1 € (moneda)"]],
                ['nom' => '99 €', 'stdin' => "99\n", 'conte' => ["1 x 50 € (bitllet)\n2 x 20 € (bitllet)\n1 x 5 € (bitllet)\n2 x 2 € (moneda)"]],
            ],
            'pistes' => [
                'Per a cada valor de l\'array, la pregunta és: quantes vegades cap aquest valor dins del que queda? I què sobra després?',
                'intdiv(387, 200) és 1 i 387 % 200 és 187. Actualitza $restant a cada volta. Per saber si és bitllet: $valor >= 5 (pots fer servir el ternari).',
                "foreach (\$valors as \$valor) {\n    \$quantitat = intdiv(\$restant, \$valor);\n    \$restant = \$restant % \$valor;\n    if (\$quantitat > 0) {\n        // mostra la línia i \$pecesUsades[] = \$quantitat;\n    }\n}",
            ],
            'preguntes' => [
                'Quant val $restant després de treure els bitllets de 200 de 387?',
                'Com evites mostrar les línies amb 0 peces?',
                'Com pots decidir «bitllet» o «moneda» en una sola línia?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'A cada volta, intdiv dona les peces i % el que queda. Només es mostren i es desen les quantitats més grans que 0.',
        ],
        [
            'titol' => 'El resum',
            'conceptes' => ['count i array_sum'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> al final (després d'una línia en blanc), mostra quants tipus de peces diferents s'han fet servir
                (<code>count</code>) i el total de peces (<code>array_sum</code>):</p>
                <pre class="term">Tipus de peces diferents: 4
                Total de peces: 6</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bcount\s*\(/', 'missatge' => 'Compta els tipus de peces amb count().'],
                ['regex' => '/\barray_sum\s*\(/', 'missatge' => 'Suma el total de peces amb array_sum().'],
            ],
            'proves' => [
                ['nom' => '387 €', 'stdin' => "387\n", 'conte' => ['Tipus de peces diferents: 7', 'Total de peces: 7']],
                ['nom' => '1000 €', 'stdin' => "1000\n", 'conte' => ['Tipus de peces diferents: 1', 'Total de peces: 2']],
                ['nom' => '99 €', 'stdin' => "99\n", 'conte' => ['Tipus de peces diferents: 4', 'Total de peces: 6']],
            ],
            'pistes' => [
                '$pecesUsades ja té una entrada per cada tipus de peça utilitzat, i cada entrada és el nombre de peces d\'aquell tipus.',
                'count($pecesUsades) dona quants tipus; array_sum($pecesUsades) dona quantes peces en total.',
                "echo PHP_EOL . \"Tipus de peces diferents: \" . count(\$pecesUsades) . PHP_EOL;\necho \"Total de peces: \" . array_sum(\$pecesUsades) . PHP_EOL;",
            ],
            'preguntes' => [
                'Amb 99 €, quins elements té $pecesUsades?',
                'Quina diferència hi ha entre count() i array_sum()?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'count() compta els elements de l\'array i array_sum() els suma.',
        ],
    ],
];
