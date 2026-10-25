<?php
/**
 * Lliçó: Funcions d'arrays, map/filter/reduce i novetats de PHP 8 (Tema 3, apartats 3.3 i 3.4).
 */

$origen = 'Tema 3 - Funcions integrades.pdf';

return [
    'id' => 'arrays-avancats',
    'ordre' => 140,
    'titol' => 'map, filter, reduce i novetats de PHP 8',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.3 Arrays · 3.4 Novetats de PHP 8',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'comptador-paraules',
    'resum' => 'Afegir i treure elements, array_unique, ordenar amb usort i <=>, transformar arrays sense bucles i les funcions noves de PHP 8.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Consultar i modificar',
            'origen' => "$origen · pàg. 19-21",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Final de l'array</th><th>Inici de l'array</th></tr>
                  <tr><td>array_push($a, $v) · $a[] = $v (afegeix)</td><td>array_unshift($a, $v) (afegeix)</td></tr>
                  <tr><td>array_pop($a) (treu i retorna l'últim)</td><td>array_shift($a) (treu i retorna el primer)</td></tr>
                </table>
                <p>També: <code>array_slice($a, $i, $n)</code> (una porció), <code>array_reverse</code>, <code>array_product</code>, <code>shuffle</code>
                i <code>array_unique</code> (treu duplicats, però <strong>conserva les claus</strong>). <code>array_search</code> pot retornar 0:
                compara amb <code>!== false</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: posar i treure',
            'fitxer' => 'arrays2.php',
            'codi' => <<<'PHP'
                <?php
                $nums = [10, 20, 30, 40, 50];

                array_unshift($nums, 5);          // [5, 10, 20, 30, 40, 50]
                $primer = array_shift($nums);     // 5
                $ultim = array_pop($nums);        // 50
                echo "Primer: $primer · Últim: $ultim" . PHP_EOL;
                echo implode(", ", $nums) . PHP_EOL;

                print_r(array_slice($nums, 1, 2));         // [20, 30]
                print_r(array_unique([1, 2, 2, 3, 4, 4])); // conserva les claus!
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['funcions d\'arrays'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $cua = ["Anna", "Pau", "Laia"];
                $cua[] = "Joan";
                $atesa = array_shift($cua);
                echo $atesa . " " . count($cua) . " " . $cua[0];
                PHP,
            'resposta' => 'Anna 3 Pau',
            'explicacio' => 'array_shift treu el primer (Anna) i reindexa: queden 3 persones i ara la primera és Pau. És com una cua (FIFO).',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Ordenar amb usort i <=>',
            'origen' => "$origen · pàg. 22",
            'html' => <<<'HTML'
                <p>Totes les funcions d'ordenar <strong>modifiquen l'array original</strong> i retornen <code>true</code>, no l'array ordenat.
                Per a un criteri propi: <code>usort</code> (reindexa), <code>uasort</code> (manté claus) o <code>uksort</code> (per clau).
                La funció rep dos elements i retorna -1, 0 o 1: exactament el que fa <code>&lt;=&gt;</code>.
                Per ordenar de més a menys, gira l'ordre: <code>$b &lt;=&gt; $a</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: usort',
            'fitxer' => 'usort.php',
            'codi' => <<<'PHP'
                <?php
                $alumnes = [
                    ["nom" => "Pau",  "nota" => 7],
                    ["nom" => "Laia", "nota" => 9],
                    ["nom" => "Anna", "nota" => 8],
                ];

                usort($alumnes, fn($a, $b) => $b["nota"] <=> $a["nota"]);   // de més a menys
                foreach ($alumnes as $al) {
                    echo $al["nom"] . ": " . $al["nota"] . PHP_EOL;
                }

                usort($alumnes, fn($a, $b) => $a["nom"] <=> $b["nom"]);     // per nom
                echo $alumnes[0]["nom"] . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'map, filter i reduce',
            'origen' => "$origen · pàg. 23",
            'html' => <<<'HTML'
                <ul>
                  <li><code>array_map(fn($n) =&gt; $n * $n, $nums)</code>: <strong>transforma</strong> cada element.</li>
                  <li><code>array_filter($nums, fn($n) =&gt; $n % 2 === 0)</code>: es queda amb els que <strong>compleixen</strong> la condició
                  (conserva les claus: fes <code>array_values()</code> per reindexar).</li>
                  <li><code>array_reduce($nums, fn($acc, $n) =&gt; $acc + $n, 0)</code>: ho <strong>redueix</strong> a un sol valor (el 0 és el valor inicial de l'acumulador).</li>
                </ul>
                <p>Compte amb l'ordre dels paràmetres: <code>array_map</code> rep primer la funció i <code>array_filter</code> primer l'array.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: sense bucles',
            'fitxer' => 'mapfilter.php',
            'codi' => <<<'PHP'
                <?php
                $nums = [1, 2, 3, 4, 5, 6];

                $quadrats = array_map(fn($n) => $n * $n, $nums);
                echo implode(" ", $quadrats) . PHP_EOL;

                $parells = array_filter($nums, fn($n) => $n % 2 === 0);
                print_r($parells);                       // [1=>2, 3=>4, 5=>6]
                print_r(array_values($parells));         // [2, 4, 6]

                $total = array_reduce($nums, fn($acc, $n) => $acc + $n, 0);
                echo "Total: $total" . PHP_EOL;          // 21
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $preus = [12, 5, 30, 8];
                $cars = array_filter($preus, fn($p) => $p > 10);
                $ambIva = array_map(fn($p) => $p * 2, $cars);
                echo array_reduce($ambIva, fn($acc, $p) => $acc + $p, 0);
                PHP,
            'resposta' => '84',
            'explicacio' => 'filter es queda amb 12 i 30, map els duplica (24 i 60) i reduce els suma: 84.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Volem quedar-nos només amb les notes aprovades (≥ 5). Quina funció?',
            'codi' => "\$aprovades = ___(\$notes, fn(\$n) => \$n >= 5);",
            'respostes' => ['array_filter'],
            'explicacio' => 'array_filter conserva només els elements per als quals la funció retorna true.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Novetats de PHP 8',
            'origen' => "$origen · pàg. 25-27",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Funció</th><th>Per a què</th></tr>
                  <tr><td>str_contains / str_starts_with / str_ends_with</td><td>Conté / comença per / acaba en (distingeixen majúscules)</td></tr>
                  <tr><td>array_key_first / array_key_last</td><td>Primera / última clau d'un array</td></tr>
                  <tr><td>nom: valor</td><td>Arguments amb nom</td></tr>
                  <tr><td>get_debug_type($v)</td><td>Tipus llegible</td></tr>
                  <tr><td>array_is_list($a) (8.1)</td><td>Claus 0, 1, 2... consecutives?</td></tr>
                  <tr><td>$obj?-&gt;metode()</td><td>Operador nullsafe: null si l'objecte és null</td></tr>
                </table>
                <p>I <code>match</code>, que ja coneixes: amb <code>switch</code>, <code>"0" == false</code> és cert; <code>match</code> compara amb <code>===</code> i evita aquestes sorpreses.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: PHP 8',
            'fitxer' => 'php8.php',
            'codi' => <<<'PHP'
                <?php
                echo str_contains("Implantació web", "web") ? "Sí" : "No", PHP_EOL;
                echo str_starts_with("Hola món", "Hola") ? "Sí" : "No", PHP_EOL;
                echo str_ends_with("Hola món", "Món") ? "Sí" : "No", PHP_EOL;   // No: distingeix majúscules

                $arr = ['a' => 10, 'b' => 20, 'c' => 30];
                echo array_key_first($arr) . " " . array_key_last($arr) . PHP_EOL;

                echo number_format(1234.5, decimals: 2, thousands_separator: '.', decimal_separator: ',') . PHP_EOL;
                var_dump(array_is_list([1, 2, 3]), array_is_list($arr));
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['novetats de PHP 8'],
            'pregunta' => 'Què retorna <code>str_ends_with("informe.PDF", ".pdf")</code>?',
            'opcions' => ['true', 'false', '4', 'null'],
            'correcta' => 1,
            'explicacio' => 'Les funcions str_* distingeixen majúscules: ".PDF" no és ".pdf". Per no distingir-les, passa abans el text a minúscules.',
        ],
    ],
];
