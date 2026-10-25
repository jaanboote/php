<?php
/**
 * Banc de reptes del Tema 4 (Errors, excepcions i fitxers). Format: mira content/reptes/tema2.php.
 */

return [
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'items' => [
        [
            'id' => 't4-finally', 'tipus' => 'sortida', 'conceptes' => ['try / catch / finally'],
            'pregunta' => 'Què imprimeix? (pensa quan s\'executa el finally)',
            'codi' => "<?php\nfunction f() {\n    try {\n        return \"A\";\n    } finally {\n        echo \"B\";\n    }\n}\necho f();",
            'resposta' => 'BA',
            'explicacio' => 'El finally s\'executa sempre, fins i tot quan el try fa return: primer surt B i després l\'echo mostra el valor retornat, A.',
        ],
        [
            'id' => 't4-error-exception', 'tipus' => 'sortida', 'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\ntry {\n    echo intdiv(1, 0);\n} catch (Exception \$e) {\n    echo \"E\";\n} catch (Error \$e) {\n    echo \"R\";\n}",
            'resposta' => 'R',
            'explicacio' => 'DivisionByZeroError és fill d\'Error, no d\'Exception: el primer catch no el captura.',
        ],
        [
            'id' => 't4-json-unicode', 'tipus' => 'sortida', 'conceptes' => ['JSON'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho json_encode([\"nom\" => \"Àlex\", \"edat\" => 20]);",
            'resposta' => '{"nom":"\u00c0lex","edat":20}',
            'explicacio' => 'Sense JSON_UNESCAPED_UNICODE, els caràcters no ASCII s\'escriuen com a \\u00c0.',
        ],
        [
            'id' => 't4-json-niat', 'tipus' => 'sortida', 'conceptes' => ['JSON'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$d = json_decode('{\"a\": {\"b\": [1, 2, 3]}}', true);\necho \$d[\"a\"][\"b\"][2];",
            'resposta' => '3',
            'explicacio' => 'Amb true tot són arrays: la clau "a", dins la clau "b", la posició 2.',
        ],
        [
            'id' => 't4-append', 'tipus' => 'sortida', 'conceptes' => ['fitxers'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfile_put_contents(\"t.txt\", \"a\\nb\\n\");\nfile_put_contents(\"t.txt\", \"c\\n\", FILE_APPEND);\necho count(file(\"t.txt\"));",
            'resposta' => '3',
            'explicacio' => 'El primer crea el fitxer amb 2 línies i el segon n\'hi afegeix una (FILE_APPEND).',
        ],
        [
            'id' => 't4-ordre-catch', 'tipus' => 'error', 'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Volíem el missatge «Dada incorrecta», però surt «Error general». On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                try {
                    throw new InvalidArgumentException("Dada incorrecta");
                } catch (Exception $e) {
                    echo "Error general" . PHP_EOL;
                } catch (InvalidArgumentException $e) {
                    echo "Dada incorrecta" . PHP_EOL;
                }
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                try {
                    throw new InvalidArgumentException("Dada incorrecta");
                } catch (InvalidArgumentException $e) {
                    echo "Dada incorrecta" . PHP_EOL;
                } catch (Exception $e) {
                    echo "Error general" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Dada incorrecta',
            'explicacio' => 'Els catch s\'avaluen en ordre i catch (Exception) ho captura tot. Cal posar-los del més específic al més general.',
        ],
        [
            'id' => 't4-json-objecte', 'tipus' => 'error', 'conceptes' => ['JSON'],
            'pregunta' => 'Hauria de mostrar «Anna», però peta. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $json = '{"nom": "Anna"}';
                $dades = json_decode($json);
                echo $dades["nom"] . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $json = '{"nom": "Anna"}';
                $dades = json_decode($json, true);
                echo $dades["nom"] . PHP_EOL;
                PHP,
            'esperat' => 'Anna',
            'explicacio' => 'Sense el true, json_decode() retorna un objecte (stdClass), que no es pot fer servir com un array.',
        ],
        [
            'id' => 't4-mode-w', 'tipus' => 'error', 'conceptes' => ['fitxers'],
            'pregunta' => 'Volíem <strong>afegir</strong> la línia 2 al log sense esborrar la 1. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("log.txt", "línia 1\n");
                $f = fopen("log.txt", "w");
                fwrite($f, "línia 2\n");
                fclose($f);
                echo file_get_contents("log.txt");
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                file_put_contents("log.txt", "línia 1\n");
                $f = fopen("log.txt", "a");
                fwrite($f, "línia 2\n");
                fclose($f);
                echo file_get_contents("log.txt");
                PHP,
            'esperat' => "línia 1\nlínia 2",
            'explicacio' => 'El mode "w" esborra el contingut en obrir el fitxer. Per afegir al final cal el mode "a".',
        ],
        [
            'id' => 't4-throw-new', 'tipus' => 'error', 'conceptes' => ['throw'],
            'pregunta' => 'Hauria de mostrar «Edat negativa», però dona un Fatal error. On és l\'error?',
            'codi' => <<<'PHP'
                <?php
                function edat(int $e): int
                {
                    if ($e < 0) {
                        throw InvalidArgumentException("Edat negativa");
                    }
                    return $e;
                }
                try {
                    echo edat(-3);
                } catch (InvalidArgumentException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
                PHP,
            'linia' => 5,
            'corregit' => <<<'PHP'
                <?php
                function edat(int $e): int
                {
                    if ($e < 0) {
                        throw new InvalidArgumentException("Edat negativa");
                    }
                    return $e;
                }
                try {
                    echo edat(-3);
                } catch (InvalidArgumentException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
                PHP,
            'esperat' => 'Edat negativa',
            'explicacio' => 'Falta new: sense, PHP busca una funció anomenada InvalidArgumentException(). Una excepció és un objecte i es crea amb new.',
        ],
        [
            'id' => 't4-ord-fgets', 'tipus' => 'ordenar', 'conceptes' => ['fitxers'],
            'pregunta' => 'Ordena les línies perquè llegeixi el fitxer línia a línia i el mostri en majúscules:',
            'linies' => [
                '<?php',
                'file_put_contents("dades.txt", "un\ndos\n");',
                '$f = fopen("dades.txt", "r");',
                'while (($linia = fgets($f)) !== false) {',
                '    echo strtoupper($linia);',
                '}',
                'fclose($f);',
            ],
            'sortida' => "UN\nDOS",
            'explicacio' => 'Crear el fitxer, obrir-lo, llegir línia a línia fins que fgets retorni false i, al final, tancar-lo.',
        ],
        [
            'id' => 't4-test-display', 'tipus' => 'test', 'conceptes' => ['php.ini'],
            'pregunta' => 'En un servidor de producció, com han d\'estar <code>display_errors</code> i <code>log_errors</code>?',
            'opcions' => ['display On, log Off', 'display Off, log On', 'Tots dos Off', 'Tots dos On'],
            'correcta' => 1,
            'explicacio' => 'Els errors no s\'han de mostrar mai a l\'usuari (revelen informació), però sí que s\'han de registrar al log.',
        ],
    ],
];
