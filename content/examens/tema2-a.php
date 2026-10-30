<?php
/**
 * Simulacre A del Tema 2 (Fonaments de PHP).
 * Format: mira .claude/skills/afegir-contingut (apartat «Exàmens»).
 */

return [
    'id' => 'tema2-a',
    'ordre' => 21,
    'titol' => 'Tema 2 · Simulacre A',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'resum' => 'Variables, tipus, operadors, condicionals, bucles, arrays i funcions, i un programa amb arguments per consola.',
    'minuts' => 45,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'sortida', 'conceptes' => ['concatenació', 'tipus i casting'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = \"7\";\n\$b = 2;\necho \$a . \$b . \" \" . \$a * \$b;",
            'resposta' => '72 14',
            'explicacio' => 'El punt concatena ("7" . 2 = "72") i el * multiplica convertint el text en número (7 × 2 = 14).',
        ],
        [
            'id' => 'q2', 'tipus' => 'test', 'conceptes' => ['comparacions'],
            'pregunta' => 'Què retorna <code>"10" === 10</code>?',
            'opcions' => ['true, perquè valen el mateix', 'false, perquè un és string i l\'altre int', 'Un error de tipus', 'null'],
            'correcta' => 1,
            'explicacio' => '=== compara valor i tipus. "10" és un string i 10 un int: és false. Amb == seria true.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['bucle for'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfor (\$i = 10; \$i > 0; \$i -= 4) {\n    echo \$i . \" \";\n}",
            'resposta' => '10 6 2',
            'explicacio' => '$i val 10, 6 i 2. El següent valor, -2, ja no compleix $i > 0.',
        ],
        [
            'id' => 'q4', 'tipus' => 'completa', 'conceptes' => ['arrays associatius'],
            'pregunta' => 'Completa el codi perquè mostri «Anna té 17 anys» per a cada persona de l\'array.',
            'codi' => "<?php\n\$edats = [\"Anna\" => 17, \"Pau\" => 20];\nforeach (\$edats as ___) {\n    echo \"\$nom té \$edat anys\" . PHP_EOL;\n}",
            'respostes' => ['$nom => $edat'],
            'explicacio' => 'Per recórrer un array associatiu amb la clau i el valor: foreach ($array as $clau => $valor).',
        ],
        [
            'id' => 'q5', 'tipus' => 'error', 'conceptes' => ['condicionals'],
            'pregunta' => 'Hauria de mostrar «Aprovat», però sempre surt «Excel·lent». On és l\'error?',
            'codi' => "<?php\n\$nota = 6;\nif (\$nota = 10) {\n    echo \"Excel·lent\" . PHP_EOL;\n} elseif (\$nota >= 5) {\n    echo \"Aprovat\" . PHP_EOL;\n}",
            'linia' => 3,
            'corregit' => "<?php\n\$nota = 6;\nif (\$nota == 10) {\n    echo \"Excel·lent\" . PHP_EOL;\n} elseif (\$nota >= 5) {\n    echo \"Aprovat\" . PHP_EOL;\n}",
            'esperat' => 'Aprovat',
            'explicacio' => 'Amb un sol = s\'assigna 10 a $nota (i 10 és cert). Per comparar calen == o ===.',
        ],
        [
            'id' => 'q6', 'tipus' => 'sortida', 'conceptes' => ['switch'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$dia = 6;\nswitch (\$dia) {\n    case 6:\n    case 7:\n        echo \"Cap de setmana\";\n        break;\n    default:\n        echo \"Feiner\";\n}",
            'resposta' => 'Cap de setmana',
            'explicacio' => 'Dos case seguits sense res entremig comparteixen el mateix codi: el 6 i el 7 mostren «Cap de setmana».',
        ],
        [
            'id' => 'q7', 'tipus' => 'test', 'conceptes' => ['arrays indexats'],
            'pregunta' => 'Tens <code>$fruites = ["poma", "pera", "kiwi"];</code>. Com afegeixes «plàtan» al final?',
            'opcions' => ['$fruites[3] = "plàtan" és l\'única manera', '$fruites[] = "plàtan";', '$fruites + "plàtan";', 'add($fruites, "plàtan");'],
            'correcta' => 1,
            'explicacio' => 'Amb els claudàtors buits, PHP afegeix l\'element al final amb la clau següent. També existeix array_push().',
        ],
        [
            'id' => 'q8', 'tipus' => 'sortida', 'conceptes' => ['funcions', 'paràmetres per defecte'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfunction preu(float \$base, float \$iva = 21): float\n{\n    return \$base * (1 + \$iva / 100);\n}\necho preu(100) . \" \" . preu(100, 10);",
            'resposta' => '121 110',
            'explicacio' => 'Si no es passa el segon argument, $iva val 21 (el valor per defecte). A la segona crida val 10.',
        ],
        [
            'id' => 'q9', 'tipus' => 'ordenar', 'conceptes' => ['bucle while'],
            'pregunta' => 'Ordena les línies perquè compti enrere de 3 a 1 i després mostri «Ja!».',
            'linies' => [
                '<?php',
                '$n = 3;',
                'while ($n > 0) {',
                '    echo $n . PHP_EOL;',
                '    $n--;',
                '}',
                'echo "Ja!";',
            ],
            'sortida' => "3\n2\n1\nJa!",
            'explicacio' => 'Inicialitzar, comprovar la condició, fer la feina i actualitzar el comptador dins del bucle; el missatge final, fora.',
        ],
        [
            'id' => 'q10', 'tipus' => 'test', 'conceptes' => ['arguments per consola'],
            'pregunta' => 'Executes <code>php nota.php 7.5 Anna</code>. Quant val <code>$argc</code>?',
            'opcions' => ['2', '3', '1', 'Depèn de php.ini'],
            'correcta' => 1,
            'explicacio' => '$argv conté el nom del programa i els 2 arguments: ["nota.php", "7.5", "Anna"]. $argc val 3.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Calculadora de descomptes',
            'fitxer' => 'descompte.php',
            'punts' => 4,
            'enunciat' => <<<'HTML'
                <p>Una botiga vol un programa que calculi el preu final d'un producte amb descompte. Rep dos arguments:
                el preu i el percentatge de descompte.</p>
                <pre class="term">$ php descompte.php 80 25
                Preu original: 80 €
                Descompte: 25 %
                Preu final: 60 €</pre>
                <ul>
                  <li>Si no hi ha exactament 2 arguments: <code>Ús: php descompte.php &lt;preu&gt; &lt;descompte&gt;</code> i <code>exit(1)</code>.</li>
                  <li>Si algun no és un número: <code>ERROR: els dos valors han de ser números.</code> i <code>exit(1)</code>.</li>
                  <li>Si el descompte no és entre 0 i 100: <code>ERROR: el descompte ha de ser entre 0 i 100.</code> i <code>exit(1)</code>.</li>
                  <li>El preu final s'arrodoneix a 2 decimals amb <code>round()</code>.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Calculadora de descomptes: php descompte.php <preu> <descompte>\n\n",
            'proves' => [
                ['nom' => '80 € amb un 25 %', 'args' => ['80', '25'], 'conte' => ['Preu original: 80 €', 'Descompte: 25 %', 'Preu final: 60 €']],
                ['nom' => 'Amb decimals', 'args' => ['19.99', '15'], 'conte' => ['Preu final: 16.99 €']],
                ['nom' => 'Sense descompte', 'args' => ['50', '0'], 'conte' => ['Preu final: 50 €']],
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php descompte.php <preu> <descompte>'], 'codi_sortida' => 1],
                ['nom' => 'Un text', 'args' => ['80', 'molt'], 'conte' => ['ERROR: els dos valors han de ser números.'], 'codi_sortida' => 1],
                ['nom' => 'Descompte de 120 %', 'args' => ['80', '120'], 'conte' => ['ERROR: el descompte ha de ser entre 0 i 100.'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Calculadora de descomptes: php descompte.php <preu> <descompte>

                if ($argc != 3) {
                    echo "Ús: php descompte.php <preu> <descompte>" . PHP_EOL;
                    exit(1);
                }
                if (!is_numeric($argv[1]) || !is_numeric($argv[2])) {
                    echo "ERROR: els dos valors han de ser números." . PHP_EOL;
                    exit(1);
                }
                $preu = (float) $argv[1];
                $descompte = (float) $argv[2];
                if ($descompte < 0 || $descompte > 100) {
                    echo "ERROR: el descompte ha de ser entre 0 i 100." . PHP_EOL;
                    exit(1);
                }

                $final = round($preu * (1 - $descompte / 100), 2);
                echo "Preu original: $preu €" . PHP_EOL;
                echo "Descompte: $descompte %" . PHP_EOL;
                echo "Preu final: $final €" . PHP_EOL;
                PHP,
            'solucio_explicacio' => 'Primer es valida tot (nombre d\'arguments, que siguin números i el rang) i només després es calcula. El preu final és preu × (1 − descompte / 100).',
        ],
    ],
];
