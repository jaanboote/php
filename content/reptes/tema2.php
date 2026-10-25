<?php
/**
 * Banc de reptes del Tema 2 (Fonaments de PHP).
 *
 * Tipus:
 *  - sortida: 'codi' + 'resposta' (el que imprimeix)
 *  - error:   'codi' amb un error, 'linia' on és, 'corregit' (codi bo) i 'esperat' (sortida del codi bo)
 *  - ordenar: 'linies' en l'ordre correcte + 'sortida' (s'accepta qualsevol ordre que funcioni igual)
 *  - test:    'opcions' + 'correcta' (índex)
 * 'errata' indica que l'error surt tal qual al temari. eines/verifica-contingut.php ho comprova tot.
 */

return [
    'tema' => 'Tema 2 – Fonaments de PHP',
    'items' => [
        [
            'id' => 't2-concat', 'tipus' => 'sortida', 'conceptes' => ['concatenació', 'tipus i casting'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$x = \"5\";\n\$y = 3;\necho \$x . \$y, \" \", \$x + \$y;",
            'resposta' => '53 8',
            'explicacio' => 'El punt concatena ("5" . 3 = "53") i el + suma convertint el text en número (5 + 3 = 8).',
        ],
        [
            'id' => 't2-claus', 'tipus' => 'sortida', 'conceptes' => ['arrays indexats'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = [3 => \"a\", \"b\"];\n\$a[] = \"c\";\necho count(\$a) . max(array_keys(\$a));",
            'resposta' => '35',
            'explicacio' => 'Les claus són 3, 4 i 5 (cada clau automàtica és la més gran + 1): 3 elements i la clau màxima és 5.',
        ],
        [
            'id' => 't2-for', 'tipus' => 'sortida', 'conceptes' => ['bucle for'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfor (\$i = 1; \$i <= 10; \$i += 3) {\n    echo \$i;\n}",
            'resposta' => '14710',
            'explicacio' => '$i val 1, 4, 7 i 10 (10 <= 10 encara és cert). El següent, 13, ja no compleix la condició.',
        ],
        [
            'id' => 't2-switch', 'tipus' => 'sortida', 'conceptes' => ['switch'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$n = 2;\nswitch (\$n) {\n    case 1:\n        echo \"u\";\n    case 2:\n        echo \"d\";\n    case 3:\n        echo \"t\";\n        break;\n    default:\n        echo \"x\";\n}",
            'resposta' => 'dt',
            'explicacio' => 'Entra pel case 2 i, com que no hi ha break, continua amb el case 3 fins al break.',
        ],
        [
            'id' => 't2-match', 'tipus' => 'sortida', 'conceptes' => ['match'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho match (true) {\n    7 > 9 => \"A\",\n    7 > 5 => \"B\",\n    default => \"C\",\n};",
            'resposta' => 'B',
            'explicacio' => 'match (true) tria la primera branca la condició de la qual és certa: 7 > 5.',
        ],
        [
            'id' => 't2-static', 'tipus' => 'sortida', 'conceptes' => ['àmbit de variables'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfunction comptador() {\n    static \$n = 0;\n    return ++\$n;\n}\ncomptador();\ncomptador();\necho comptador();",
            'resposta' => '3',
            'explicacio' => 'static conserva el valor entre crides: 1, 2 i, a la tercera, 3.',
        ],
        [
            'id' => 't2-ref', 'tipus' => 'sortida', 'conceptes' => ['referències', 'foreach'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = [1, 2, 3];\nforeach (\$a as &\$v) {\n    \$v *= 10;\n}\nunset(\$v);\necho implode(\",\", \$a);",
            'resposta' => '10,20,30',
            'explicacio' => 'Amb & el foreach modifica l\'array original. L\'unset() trenca la referència quan s\'acaba.',
        ],
        [
            'id' => 't2-intdiv', 'tipus' => 'sortida', 'conceptes' => ['intdiv i %'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho intdiv(17, 5) . \"r\" . 17 % 5;",
            'resposta' => '3r2',
            'explicacio' => '17 entre 5 dona 3 i en sobren 2.',
        ],
        [
            'id' => 't2-cast', 'tipus' => 'sortida', 'conceptes' => ['tipus i casting'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nvar_dump((int) \"12abc\");",
            'resposta' => 'int(12)',
            'explicacio' => '(int) agafa els números de l\'inici del text i ignora la resta.',
        ],
        [
            'id' => 't2-errata-p77', 'tipus' => 'error', 'conceptes' => ['sintaxi'], 'errata' => 'Tema 2, pàgina 77',
            'pregunta' => 'Aquest script de la pàgina 77 del temari té un error de sintaxi. A quina línia?',
            'args' => '4 5',
            'codi' => <<<'PHP'
                <?php
                if ($argc !== 3) {
                    echo "Ús: php suma.php n1 n2" . PHP_EOL;
                    exit(1);
                }
                if (!is_numeric($argv[1]) || !is_numeric($argv[2])) {
                    echo "Error: calen dos números" . PHP_EOL);
                    exit(2);
                }
                echo ($argv[1] + $argv[2]) . PHP_EOL;
                PHP,
            'linia' => 7,
            'corregit' => <<<'PHP'
                <?php
                if ($argc !== 3) {
                    echo "Ús: php suma.php n1 n2" . PHP_EOL;
                    exit(1);
                }
                if (!is_numeric($argv[1]) || !is_numeric($argv[2])) {
                    echo "Error: calen dos números" . PHP_EOL;
                    exit(2);
                }
                echo ($argv[1] + $argv[2]) . PHP_EOL;
                PHP,
            'esperat' => '9',
            'explicacio' => 'Sobra el parèntesi de tancament abans del punt i coma: «. PHP_EOL);». És una errata real del temari. Com que és un Parse error, l\'script no s\'executa gens.',
        ],
        [
            'id' => 't2-ambit', 'tipus' => 'error', 'conceptes' => ['àmbit de variables'],
            'pregunta' => 'Hauria de mostrar 121, però dona un avís. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $iva = 0.21;
                function ambIva(float $preu): float
                {
                    return $preu * (1 + $iva);
                }
                echo ambIva(100) . PHP_EOL;
                PHP,
            'linia' => 5,
            'corregit' => <<<'PHP'
                <?php
                $iva = 0.21;
                function ambIva(float $preu): float
                {
                    return $preu * (1 + $GLOBALS['iva']);
                }
                echo ambIva(100) . PHP_EOL;
                PHP,
            'esperat' => '121',
            'explicacio' => 'Dins de la funció, $iva no existeix: les funcions no veuen les variables de fora. Es pot arreglar amb $GLOBALS[\'iva\'] (com a la solució), però és millor passar-la com a paràmetre: function ambIva(float $preu, float $iva).',
        ],
        [
            'id' => 't2-assignacio', 'tipus' => 'error', 'conceptes' => ['operadors de comparació'],
            'pregunta' => 'La nota és un 4, però el programa diu «Matrícula!». On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $nota = 4;
                if ($nota = 10) {
                    echo "Matrícula!" . PHP_EOL;
                } else {
                    echo "Nota: $nota" . PHP_EOL;
                }
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $nota = 4;
                if ($nota === 10) {
                    echo "Matrícula!" . PHP_EOL;
                } else {
                    echo "Nota: $nota" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Nota: 4',
            'explicacio' => '= assigna (i 10 és cert), no compara. Per comparar cal === (o ==).',
        ],
        [
            'id' => 't2-limit', 'tipus' => 'error', 'conceptes' => ['bucle for', 'arrays indexats'],
            'pregunta' => 'Mostra els dies, però acaba amb un avís. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $dies = ["dl", "dt", "dc"];
                for ($i = 0; $i <= count($dies); $i++) {
                    echo $dies[$i] . " ";
                }
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $dies = ["dl", "dt", "dc"];
                for ($i = 0; $i < count($dies); $i++) {
                    echo $dies[$i] . " ";
                }
                PHP,
            'esperat' => 'dl dt dc',
            'explicacio' => 'Amb <= el bucle arriba a $i = 3, i l\'últim índex és count - 1 = 2: «Undefined array key 3».',
        ],
        [
            'id' => 't2-mes', 'tipus' => 'error', 'conceptes' => ['concatenació'],
            'pregunta' => 'Hauria de saludar l\'Anna. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $nom = "Anna";
                $salutacio = "Hola, " + $nom;
                echo $salutacio . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $nom = "Anna";
                $salutacio = "Hola, " . $nom;
                echo $salutacio . PHP_EOL;
                PHP,
            'esperat' => 'Hola, Anna',
            'explicacio' => 'A PHP els textos s\'uneixen amb el punt. El + només suma: amb textos no numèrics dona TypeError.',
        ],
        [
            'id' => 't2-ord-mitjana', 'tipus' => 'ordenar', 'conceptes' => ['foreach'],
            'pregunta' => 'Ordena les línies perquè el programa mostri la mitjana de les notes (7):',
            'linies' => [
                '<?php',
                '$notes = [7, 5, 9];',
                '$suma = 0;',
                'foreach ($notes as $nota) {',
                '    $suma += $nota;',
                '}',
                'echo $suma / count($notes) . PHP_EOL;',
            ],
            'sortida' => '7',
            'explicacio' => 'Primer les dades i l\'acumulador, després el bucle que suma i, al final, el càlcul de la mitjana.',
        ],
        [
            'id' => 't2-ord-funcio', 'tipus' => 'ordenar', 'conceptes' => ['funcions'],
            'pregunta' => 'Ordena les línies perquè el programa mostri 42:',
            'linies' => [
                '<?php',
                'function doble(int $n): int',
                '{',
                '    return $n * 2;',
                '}',
                'echo doble(21) . PHP_EOL;',
            ],
            'sortida' => '42',
            'explicacio' => 'La definició de la funció té la signatura, la clau d\'obertura, el cos i la clau de tancament. La crida pot anar després (o abans: PHP coneix les funcions de tot el fitxer).',
        ],
        [
            'id' => 't2-comparacio', 'tipus' => 'test', 'conceptes' => ['comparacions'],
            'pregunta' => 'A PHP 8, què retorna <code>"abc" == 0</code>?',
            'opcions' => ['true', 'false', 'Un error', 'null'],
            'correcta' => 1,
            'explicacio' => 'A PHP 8 un text no numèric ja no es converteix en 0: la comparació és false. A PHP 7 donava true!',
        ],
    ],
];
