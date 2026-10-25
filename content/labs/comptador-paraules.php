<?php
/**
 * Laboratori: Comptador de paraules d'un text.
 * Basat en l'Exercici 15 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Comptador de paraules d'un text

echo "Escriu un text:" . PHP_EOL;
$text = readline();

// Minúscules (amb accents) i fora signes de puntuació
$text = mb_strtolower($text);
$signes = [',', '.', ';', ':', '!', '?', '¡', '¿', '"', '(', ')'];
$text = str_replace($signes, '', $text);

$paraules = explode(' ', $text);

// Les paraules buides surten dels espais dobles: no les comptem
$netes = [];
foreach ($paraules as $paraula) {
    if ($paraula !== '') {
        $netes[] = $paraula;
    }
}
echo PHP_EOL . "Paraules totals: " . count($netes) . PHP_EOL;

PHP;

$sol2 = str_replace(
    'echo PHP_EOL . "Paraules totals: " . count($netes) . PHP_EOL;' . "\n",
    <<<'PHP'
// Diccionari construït a mà: paraula => vegades
$frequencies = [];
foreach ($netes as $paraula) {
    if (isset($frequencies[$paraula])) {
        $frequencies[$paraula]++;
    } else {
        $frequencies[$paraula] = 1;
    }
}

// arsort i no rsort: rsort perdria les claus (les paraules)
arsort($frequencies);
$top = array_slice($frequencies, 0, 5, true);

echo PHP_EOL . "--- TOP 5 PARAULES ---" . PHP_EOL;
foreach ($top as $paraula => $vegades) {
    echo str_pad($paraula, 15) . " " . $vegades . PHP_EOL;
}

PHP,
    $sol1
);

$sol3 = $sol2 . <<<'PHP'

echo PHP_EOL . "Paraules totals: " . array_sum($frequencies) . PHP_EOL;
echo "Paraules diferents: " . count($frequencies) . PHP_EOL;

$comprovacio = array_count_values($netes);
echo "Comprovació amb array_count_values: " . count($comprovacio) . " paraules diferents." . PHP_EOL;

PHP;

$exemple = "el gat i el gos, i el ratolí.\n";

return [
    'id' => 'comptador-paraules',
    'ordre' => 140,
    'titol' => 'Comptador de paraules d\'un text',
    'fitxer' => 'Exercici15_comptador_paraules.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.2 Cadenes · 3.3 Arrays',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 15 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Quines paraules es repeteixen més en un text? Netejaràs el text, construiràs un diccionari de freqüències i el compararàs amb array_count_values().',
    'xp_final' => 40,
    'paraules_clau' => ['explode', 'str_replace', 'arsort', 'array_slice', 'array_count_values', 'mb_strtolower'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Comptador de paraules d'un text

        PHP,

    'context' => <<<'HTML'
        <p>Els correctors de text sovint avisen quan repetim massa una paraula. Farem un programa que analitzi un text i ens digui
        quines paraules hi apareixen més vegades.</p>
        <pre class="term">Escriu un text:
        el gat i el gos, i el ratolí.

        --- TOP 5 PARAULES ---
        el              3
        i               2
        gat             1
        gos             1
        ratolí          1

        Paraules totals: 8
        Paraules diferents: 5
        Comprovació amb array_count_values: 5 paraules diferents.</pre>
        <p class="nota">Escriu el text a la caixa «Entrada per a readline()», tot en una línia.</p>
        HTML,

    'resum_apres' => [
        'Netejar un text: mb_strtolower() per als accents i str_replace() amb un array de signes.',
        'Separar un text en paraules amb explode() i descartar les buides.',
        'Construir un diccionari de freqüències a mà amb isset().',
        'Ordenar per valor conservant les claus (arsort) i agafar-ne una porció (array_slice amb true).',
        'Comprovar el resultat amb la funció nativa array_count_values().',
    ],

    'passos' => [
        [
            'titol' => 'Netejar i separar el text',
            'conceptes' => ['funcions de cadenes', 'UTF-8 i mb_*', 'explode'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>Mostra <code>Escriu un text:</code> i llegeix el text amb <code>readline()</code>.</p>
                <p><strong>Objectiu:</strong></p>
                <ol>
                  <li>Passa'l a minúscules amb <code>mb_strtolower()</code> (per als accents).</li>
                  <li>Elimina els signes <code>, . ; : ! ? ¡ ¿ " ( )</code> amb <code>str_replace()</code> i un array de símbols.</li>
                  <li>Separa'l en paraules amb <code>explode(' ', $text)</code> i descarta les paraules buides (surten dels espais dobles).</li>
                  <li>De moment, mostra (després d'una línia en blanc) <code>Paraules totals: 8</code>.</li>
                </ol>
                HTML,
            'codi' => [
                ['regex' => '/\bmb_strtolower\s*\(/', 'missatge' => 'Passa el text a minúscules amb mb_strtolower().'],
                ['regex' => '/\bstr_replace\s*\(/', 'missatge' => 'Elimina els signes amb str_replace() i un array.'],
                ['regex' => '/\bexplode\s*\(/', 'missatge' => 'Separa les paraules amb explode().'],
            ],
            'proves' => [
                ['nom' => 'Text de l\'enunciat', 'stdin' => $exemple, 'conte' => ['Paraules totals: 8']],
                ['nom' => 'Espais dobles i signes', 'stdin' => "¡Hola,   món!  Hola?\n", 'conte' => ['Paraules totals: 3']],
            ],
            'pistes' => [
                'Fes-ho en ordre: primer minúscules, després treure signes, després separar. Al final, compta només les paraules que no siguin "".',
                'str_replace($arrayDeSignes, \'\', $text) treu tots els signes d\'un cop. Després d\'explode, recorre les paraules i guarda només les que no són buides en un array nou.',
                "\$signes = [',', '.', ';', ':', '!', '?', '¡', '¿', '\"', '(', ')'];\n\$text = str_replace(\$signes, '', mb_strtolower(\$text));\n\$paraules = explode(' ', \$text);",
            ],
            'preguntes' => [
                'Què retorna explode(\' \', "a  b") (amb dos espais)? Quants elements té?',
                'Per què mb_strtolower() i no strtolower()?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Després d\'explode, les cadenes buides que surten dels espais dobles es descarten.',
        ],
        [
            'titol' => 'El diccionari de freqüències',
            'conceptes' => ['arrays associatius', 'isset i empty', 'ordenar arrays'],
            'xp' => 35,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> construeix <strong>a mà</strong> el diccionari <code>$frequencies</code> (paraula => vegades):
                si una paraula ja hi és, incrementa'n el comptador; si no, afegeix-la amb el valor 1.</p>
                <p>Ordena'l de major a menor amb <code>arsort()</code>, agafa les 5 primeres amb <code>array_slice(..., 0, 5, true)</code> i mostra:</p>
                <pre class="term">--- TOP 5 PARAULES ---
                el              3
                i               2
                gat             1</pre>
                <p class="nota">Encara no pots fer servir <code>array_count_values()</code>: l'enunciat demana construir-lo primer amb el bucle.
                L'espai entre la paraula i el número no compta.</p>
                HTML,
            'codi' => [
                ['regex' => '/\barsort\s*\(/', 'missatge' => 'Ordena amb arsort() (no rsort: perdries les paraules).'],
                ['regex' => '/\barray_slice\s*\(/', 'missatge' => 'Agafa les 5 primeres amb array_slice(..., 0, 5, true).'],
                ['no_regex' => '/\barray_count_values\s*\(/', 'missatge' => 'De moment no facis servir array_count_values(): construeix el diccionari amb el bucle.'],
            ],
            'proves' => [
                ['nom' => 'Text de l\'enunciat', 'stdin' => $exemple, 'conte' => ["--- TOP 5 PARAULES ---\nel 3\ni 2\ngat 1\ngos 1\nratolí 1"]],
                ['nom' => 'Més de 5 paraules diferents', 'stdin' => "a b c d e f g a\n", 'conte' => ["a 2\nb 1\nc 1\nd 1\ne 1"], 'no_conte' => ['f 1']],
            ],
            'pistes' => [
                'Per a cada paraula: ja és al diccionari? Si sí, suma-li 1. Si no, crea-la amb 1. Després, ordena i talla.',
                'isset($frequencies[$paraula]) et diu si la paraula ja hi és. El quart paràmetre de array_slice (true) conserva les claus.',
                "if (isset(\$frequencies[\$paraula])) {\n    \$frequencies[\$paraula]++;\n} else {\n    \$frequencies[\$paraula] = 1;\n}",
            ],
            'preguntes' => [
                'Què passa si fas $frequencies[$paraula]++ quan la paraula encara no hi és?',
                'Què perdries si fessis servir rsort() en lloc d\'arsort()?',
                'Per què cal el true a array_slice?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'isset() evita l\'avís d\'una clau inexistent. arsort() i array_slice(..., true) conserven les paraules com a claus.',
        ],
        [
            'titol' => 'Resum i comprovació',
            'conceptes' => ['count i array_sum', 'funcions d\'arrays'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> mostra (en comptes de la línia provisional del pas 1) el total de paraules (<code>array_sum</code>),
                les paraules diferents (<code>count</code>) i, com a comprovació, el mateix resultat amb <code>array_count_values()</code>:</p>
                <pre class="term">Paraules totals: 8
                Paraules diferents: 5
                Comprovació amb array_count_values: 5 paraules diferents.</pre>
                HTML,
            'codi' => [
                ['regex' => '/\barray_sum\s*\(/', 'missatge' => 'Calcula el total amb array_sum().'],
                ['regex' => '/\barray_count_values\s*\(/', 'missatge' => 'Comprova el resultat amb array_count_values().'],
            ],
            'proves' => [
                ['nom' => 'Text de l\'enunciat', 'stdin' => $exemple, 'conte' => ["Paraules totals: 8\nParaules diferents: 5\nComprovació amb array_count_values: 5 paraules diferents."]],
                ['nom' => 'Amb majúscules i accents', 'stdin' => "Òscar i òscar, i ÒSCAR.\n", 'conte' => ['Paraules totals: 5', 'Paraules diferents: 2']],
            ],
            'pistes' => [
                'El diccionari ja ho sap tot: la suma dels seus valors és el total i el nombre de claus són les paraules diferents.',
                'array_sum($frequencies) i count($frequencies). array_count_values($netes) fa el diccionari d\'un cop a partir de la llista de paraules.',
                "\$comprovacio = array_count_values(\$netes);\necho \"Comprovació amb array_count_values: \" . count(\$comprovacio) . \" paraules diferents.\" . PHP_EOL;",
            ],
            'preguntes' => [
                'Per què array_sum($frequencies) dona el total de paraules?',
                'Amb «Òscar i òscar», per què han de sortir 2 paraules diferents i no 3?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'array_count_values() fa exactament el que hem programat a mà: per això els dos resultats coincideixen.',
        ],
    ],
];
