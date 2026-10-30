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
        [
            'id' => 't2-ternari', 'tipus' => 'sortida', 'conceptes' => ['operador ternari'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$edat = 17;\necho \$edat >= 18 ? \"major\" : \"menor\";",
            'resposta' => 'menor',
            'explicacio' => 'condició ? si_cert : si_fals. 17 >= 18 és fals, així que tria «menor».',
        ],
        [
            'id' => 't2-traspas', 'tipus' => 'sortida', 'conceptes' => ['operadors lògics', 'mòdul'],
            'pregunta' => 'Què imprimeix? (S = de traspàs, N = no.)',
            'codi' => "<?php\nforeach ([1900, 2000, 2024] as \$any) {\n    echo (\$any % 4 == 0 && \$any % 100 != 0) || \$any % 400 == 0 ? \"S\" : \"N\";\n}",
            'resposta' => 'NSS',
            'explicacio' => '1900 és divisible entre 100 i no entre 400: no. 2000 és divisible entre 400: sí. 2024 és divisible entre 4 i no entre 100: sí. (Exercici 5 d\'Exercicis 01.)',
        ],
        [
            'id' => 't2-dowhile', 'tipus' => 'sortida', 'conceptes' => ['bucle do...while'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$i = 10;\ndo {\n    echo \$i;\n    \$i++;\n} while (\$i < 5);",
            'resposta' => '10',
            'explicacio' => 'do...while executa el bloc almenys una vegada i comprova la condició al final. Un while normal no hauria imprès res.',
        ],
        [
            'id' => 't2-caracters', 'tipus' => 'sortida', 'conceptes' => ['bucle for', 'cadenes de text'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$p = \"Hola1\";\n\$digits = 0;\nfor (\$i = 0; \$i < strlen(\$p); \$i++) {\n    if (ctype_digit(\$p[\$i])) {\n        \$digits++;\n    }\n}\necho \$digits . \$p[0];",
            'resposta' => '1H',
            'explicacio' => 'Un string es recorre com un array: només «1» és un dígit. $p[0] és el primer caràcter, «H». (Exercici 10 d\'Exercicis 01.)',
        ],
        [
            'id' => 't2-argc', 'tipus' => 'sortida', 'conceptes' => ['arguments per consola'],
            'pregunta' => 'S\'executa amb <code>php htaccess.php bloqueja 10.0.0.1 10.0.0.2</code>. Què imprimeix?',
            'codi' => "<?php\necho \$argc . \" \" . \$argv[\$argc - 1];",
            'args' => 'bloqueja 10.0.0.1 10.0.0.2',
            'resposta' => '4 10.0.0.2',
            'explicacio' => '$argv té el nom del programa i els 3 arguments: $argc val 4 i l\'últim element és $argv[3].',
        ],
        [
            'id' => 't2-dowhile-pc', 'tipus' => 'error', 'conceptes' => ['bucle do...while', 'sintaxi'],
            'pregunta' => 'Parse error! A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $i = 1;
                do {
                    echo $i;
                    $i++;
                } while ($i <= 3)
                echo PHP_EOL . "Fi" . PHP_EOL;
                PHP,
            'linia' => 6,
            'corregit' => <<<'PHP'
                <?php
                $i = 1;
                do {
                    echo $i;
                    $i++;
                } while ($i <= 3);
                echo PHP_EOL . "Fi" . PHP_EOL;
                PHP,
            'esperat' => "123\nFi",
            'explicacio' => 'A diferència del while normal, el do...while acaba amb punt i coma després de la condició.',
        ],
        [
            'id' => 't2-return', 'tipus' => 'error', 'conceptes' => ['funcions'],
            'pregunta' => 'Fatal error: «Return value must be of type int, none returned». On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                function doble(int $n): int
                {
                    $n * 2;
                }
                echo doble(4) . PHP_EOL;
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                function doble(int $n): int
                {
                    return $n * 2;
                }
                echo doble(4) . PHP_EOL;
                PHP,
            'esperat' => '8',
            'explicacio' => 'La funció calcula $n * 2 però no el retorna. Sense return, una funció declarada amb : int dona un error de tipus.',
        ],
        [
            'id' => 't2-ord-saluda', 'tipus' => 'ordenar', 'conceptes' => ['funcions'],
            'pregunta' => 'Ordena les línies perquè el programa mostri «Hola, Anna!»:',
            'linies' => [
                '<?php',
                'function saluda(string $nom): string',
                '{',
                '    return "Hola, $nom!";',
                '}',
                'echo saluda("Anna");',
            ],
            'sortida' => 'Hola, Anna!',
            'explicacio' => 'La capçalera de la funció, el cos entre claus amb el return, i la crida. (PHP permet cridar una funció abans de definir-la al mateix fitxer.)',
        ],
    ],
];
