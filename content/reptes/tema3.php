<?php
/**
 * Banc de reptes del Tema 3 (Funcions integrades). Format: mira content/reptes/tema2.php.
 */

return [
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'items' => [
        [
            'id' => 't3-mb', 'tipus' => 'sortida', 'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho strlen(\"Català\") . \"-\" . mb_strlen(\"Català\");",
            'resposta' => '7-6',
            'explicacio' => 'strlen compta bytes (la «à» n\'ocupa 2) i mb_strlen compta caràcters.',
        ],
        [
            'id' => 't3-ucwords', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho ucwords(strtolower(\"HOLA A TOTHOM\"));",
            'resposta' => 'Hola A Tothom',
            'explicacio' => 'Primer tot en minúscules i després la primera lletra de cada paraula en majúscula.',
        ],
        [
            'id' => 't3-pad', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho str_pad(\"5\", 3, \"0\", STR_PAD_LEFT);",
            'resposta' => '005',
            'explicacio' => 'Omple amb zeros per l\'esquerra fins a 3 caràcters.',
        ],
        [
            'id' => 't3-filter', 'tipus' => 'sortida', 'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Què imprimeix? (compte amb les claus)',
            'codi' => "<?php\n\$a = array_filter([1, 2, 3, 4, 5, 6], fn(\$n) => \$n % 3 === 0);\necho implode(\",\", array_keys(\$a));",
            'resposta' => '2,5',
            'explicacio' => 'Es queden el 3 i el 6, que eren a les posicions 2 i 5: array_filter conserva les claus originals.',
        ],
        [
            'id' => 't3-reduce', 'tipus' => 'sortida', 'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho array_reduce([2, 3, 4], fn(\$acc, \$n) => \$acc * \$n, 1);",
            'resposta' => '24',
            'explicacio' => 'Comença amb 1 i multiplica: 1 × 2 × 3 × 4 = 24.',
        ],
        [
            'id' => 't3-diff', 'tipus' => 'sortida', 'conceptes' => ['DateTime'],
            'pregunta' => 'Què imprimeix? (el 2026 no és de traspàs)',
            'codi' => "<?php\n\$d1 = new DateTime('2026-02-25');\n\$d2 = new DateTime('2026-03-03');\necho \$d1->diff(\$d2)->days;",
            'resposta' => '6',
            'explicacio' => 'Del 25 al 28 de febrer són 3 dies, i fins al 3 de març, 3 més: 6.',
        ],
        [
            'id' => 't3-date', 'tipus' => 'sortida', 'conceptes' => ['date'],
            'pregunta' => 'El 7 d\'octubre de 2026 és dimecres. Què imprimeix?',
            'codi' => "<?php\necho date(\"D j/n\", strtotime(\"2026-10-07\"));",
            'resposta' => 'Wed 7/10',
            'explicacio' => 'D és el nom curt del dia en anglès, j el dia sense zero i n el mes sense zero.',
        ],
        [
            'id' => 't3-empty', 'tipus' => 'sortida', 'conceptes' => ['isset i empty'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nvar_dump(empty(\"0\"), isset(\$x));",
            'resposta' => "bool(true)\nbool(false)",
            'explicacio' => '"0" es considera buit, i $x no existeix (isset no avisa).',
        ],
        [
            'id' => 't3-strpos', 'tipus' => 'error', 'conceptes' => ['strpos'],
            'pregunta' => 'El text sí que conté «PHP», però el programa diu que no. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $text = "PHP és genial";
                if (strpos($text, "PHP")) {
                    echo "Conté PHP" . PHP_EOL;
                } else {
                    echo "No conté PHP" . PHP_EOL;
                }
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $text = "PHP és genial";
                if (strpos($text, "PHP") !== false) {
                    echo "Conté PHP" . PHP_EOL;
                } else {
                    echo "No conté PHP" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Conté PHP',
            'explicacio' => '«PHP» és a la posició 0, que l\'if considera fals. Cal comparar amb !== false (o fer servir str_contains).',
        ],
        [
            'id' => 't3-rsort', 'tipus' => 'error', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Volíem el rànquing amb els noms, però surten números (0, 1, 2). On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $notes = ["Anna" => 8, "Pau" => 6, "Laia" => 9];
                rsort($notes);
                foreach ($notes as $nom => $nota) {
                    echo "$nom: $nota" . PHP_EOL;
                }
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $notes = ["Anna" => 8, "Pau" => 6, "Laia" => 9];
                arsort($notes);
                foreach ($notes as $nom => $nota) {
                    echo "$nom: $nota" . PHP_EOL;
                }
                PHP,
            'esperat' => "Laia: 9\nAnna: 8\nPau: 6",
            'explicacio' => 'rsort() reindexa i perd les claus. arsort() ordena de més a menys conservant clau => valor.',
        ],
        [
            'id' => 't3-map', 'tipus' => 'error', 'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Hauria de mostrar els preus amb IVA. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $preus = [10, 20, 30];
                $ambIva = array_map($preus, fn($p) => $p * 1.21);
                echo implode(" ", $ambIva) . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $preus = [10, 20, 30];
                $ambIva = array_map(fn($p) => $p * 1.21, $preus);
                echo implode(" ", $ambIva) . PHP_EOL;
                PHP,
            'esperat' => '12.1 24.2 36.3',
            'explicacio' => 'array_map rep primer la funció i després l\'array (al revés que array_filter).',
        ],
        [
            'id' => 't3-majuscules', 'tipus' => 'error', 'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Hauria d\'escriure «ÀVILA», però surt «àVILA». On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $ciutat = "àvila";
                echo strtoupper($ciutat) . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $ciutat = "àvila";
                echo mb_strtoupper($ciutat) . PHP_EOL;
                PHP,
            'esperat' => 'ÀVILA',
            'explicacio' => 'strtoupper() no entén els caràcters de 2 bytes com la «à». Amb accents cal mb_strtoupper().',
        ],
        [
            'id' => 't3-ord-usort', 'tipus' => 'ordenar', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Ordena les línies perquè mostri el nom de l\'alumne amb més nota (Laia):',
            'linies' => [
                '<?php',
                '$alumnes = [["nom" => "Pau", "nota" => 6], ["nom" => "Laia", "nota" => 9]];',
                'usort($alumnes, fn($a, $b) => $b["nota"] <=> $a["nota"]);',
                'echo $alumnes[0]["nom"] . PHP_EOL;',
            ],
            'sortida' => 'Laia',
            'explicacio' => 'Primer les dades, després ordenar (de més a menys amb $b <=> $a) i finalment mostrar el primer.',
        ],
        [
            'id' => 't3-test-mb', 'tipus' => 'test', 'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Quina funció dona el nombre de <strong>caràcters</strong> de «Pàgina»?',
            'opcions' => ['strlen', 'count', 'mb_strlen', 'sizeof'],
            'correcta' => 2,
            'explicacio' => 'mb_strlen compta caràcters (6). strlen comptaria bytes (7), i count/sizeof són per a arrays.',
        ],
        [
            'id' => 't3-number-format', 'tipus' => 'sortida', 'conceptes' => ['number_format'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho number_format(1234.5, 2, \",\", \".\");",
            'resposta' => '1.234,50',
            'explicacio' => 'number_format(número, decimals, separador decimal, separador de milers): 2 decimals amb coma i punt als milers, com en català.',
        ],
        [
            'id' => 't3-usort-desc', 'tipus' => 'sortida', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$n = [3, 10, 7];\nusort(\$n, fn(\$a, \$b) => \$b <=> \$a);\necho implode(\"-\", \$n);",
            'resposta' => '10-7-3',
            'explicacio' => 'Amb $b <=> $a (al revés del normal), usort() ordena de més gran a més petit.',
        ],
        [
            'id' => 't3-nau', 'tipus' => 'sortida', 'conceptes' => ['operador nau espacial'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho (5 <=> 8) . (8 <=> 8) . (9 <=> 8);",
            'resposta' => '-101',
            'explicacio' => '<=> retorna -1 si l\'esquerra és més petita, 0 si són iguals i 1 si és més gran: «-1», «0» i «1» enganxats.',
        ],
        [
            'id' => 't3-explode-buits', 'tipus' => 'sortida', 'conceptes' => ['explode', 'map, filter i reduce'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$trossos = explode(\" \", \" a  b \");\n\$paraules = array_filter(\$trossos);\necho count(\$trossos) . \"/\" . count(\$paraules);",
            'resposta' => '5/2',
            'explicacio' => 'Cada espai talla: queden "", "a", "", "b" i "". array_filter() sense funció treu els valors buits i en deixa 2.',
        ],
        [
            'id' => 't3-contains-buit', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nvar_dump(str_contains(\"Hola\", \"\"));",
            'resposta' => 'bool(true)',
            'explicacio' => 'Per a str_contains(), el text buit és dins de qualsevol text. Si la paraula clau ve de l\'usuari, comprova abans que no sigui buida.',
        ],
        [
            'id' => 't3-number-format-args', 'tipus' => 'error', 'conceptes' => ['number_format'],
            'pregunta' => 'Fatal error: TypeError. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $preu = 1234.5;
                echo number_format($preu, ",", 2) . " €" . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $preu = 1234.5;
                echo number_format($preu, 2, ",", ".") . " €" . PHP_EOL;
                PHP,
            'esperat' => '1.234,50 €',
            'explicacio' => 'El segon argument de number_format() és el nombre de decimals (un enter), i després van els separadors. L\'ordre dels arguments importa.',
        ],
        [
            'id' => 't3-usort-retorn', 'tipus' => 'error', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'implode() dona un TypeError. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $n = [3, 1, 2];
                $n = usort($n, fn($a, $b) => $a <=> $b);
                echo implode(",", $n) . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $n = [3, 1, 2];
                usort($n, fn($a, $b) => $a <=> $b);
                echo implode(",", $n) . PHP_EOL;
                PHP,
            'esperat' => '1,2,3',
            'explicacio' => 'usort() ordena l\'array que rep (per referència) i retorna true. En desar-ne el resultat a $n, l\'array es perd i queda un booleà.',
        ],
        [
            'id' => 't3-ord-mitjana', 'tipus' => 'ordenar', 'conceptes' => ['number_format', 'arrays'],
            'pregunta' => 'Ordena les línies perquè mostri la mitjana amb un decimal i coma:',
            'linies' => [
                '<?php',
                '$notes = [7, 8, 9.5];',
                '$mitjana = array_sum($notes) / count($notes);',
                'echo number_format($mitjana, 1, ",", ".");',
            ],
            'sortida' => '8,2',
            'explicacio' => 'Primer les dades, després el càlcul (array_sum / count = 8,1666...) i finalment mostrar-lo arrodonit a 1 decimal.',
        ],
    ],
];
