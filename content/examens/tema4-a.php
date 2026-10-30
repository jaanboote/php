<?php
/**
 * Simulacre A del Tema 4 (Errors, excepcions i fitxers).
 */

return [
    'id' => 'tema4-a',
    'ordre' => 41,
    'titol' => 'Tema 4 · Simulacre A',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'resum' => 'Tipus d\'errors i php.ini, try / catch / finally, llegir i escriure fitxers, i un registre de temperatures.',
    'minuts' => 50,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'test', 'conceptes' => ['tipus d\'errors'],
            'pregunta' => 'Quin tipus d\'error <strong>atura</strong> l\'execució del programa?',
            'opcions' => ['Notice', 'Warning', 'Deprecated', 'Fatal error'],
            'correcta' => 3,
            'explicacio' => 'Notice, Warning i Deprecated avisen però el programa continua. Un Fatal error (o un Parse error) l\'atura.',
        ],
        [
            'id' => 'q2', 'tipus' => 'test', 'conceptes' => ['php.ini'],
            'pregunta' => 'En un servidor de producció, quina configuració és correcta?',
            'opcions' => ['display_errors = On', 'display_errors = Off i log_errors = On', 'error_reporting = 0 i log_errors = Off', 'display_errors = On i log_errors = Off'],
            'correcta' => 1,
            'explicacio' => 'A producció els errors no s\'han de mostrar (revelen informació), però s\'han de registrar al log.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['try / catch / finally'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                try {
                    echo "A";
                    throw new RuntimeException("B");
                    echo "C";
                } catch (RuntimeException $e) {
                    echo $e->getMessage();
                } finally {
                    echo "D";
                }
                PHP,
            'resposta' => 'ABD',
            'explicacio' => 'Després del throw, la resta del try ja no s\'executa (la C no surt). El catch mostra el missatge i el finally sempre s\'executa.',
        ],
        [
            'id' => 'q4', 'tipus' => 'sortida', 'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                try {
                    echo intdiv(10, 0);
                } catch (DivisionByZeroError $e) {
                    echo "Divisió per zero";
                } catch (Exception $e) {
                    echo "Excepció";
                }
                PHP,
            'resposta' => 'Divisió per zero',
            'explicacio' => 'intdiv() per zero llança un DivisionByZeroError (un Error, no una Exception), i el primer catch el captura.',
        ],
        [
            'id' => 'q5', 'tipus' => 'completa', 'conceptes' => ['throw'],
            'pregunta' => 'Completa el codi perquè la funció llanci una excepció quan el divisor és 0.',
            'codi' => <<<'PHP'
                <?php
                function divideix(float $a, float $b): float
                {
                    if ($b == 0) {
                        ___ new InvalidArgumentException("No es pot dividir per zero");
                    }
                    return $a / $b;
                }
                echo divideix(10, 4);
                PHP,
            'respostes' => ['throw'],
            'explicacio' => 'throw llança l\'excepció; qui crida la funció la pot capturar amb try / catch.',
        ],
        [
            'id' => 'q6', 'tipus' => 'sortida', 'conceptes' => ['fitxers'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("notes.txt", "7\n");
                file_put_contents("notes.txt", "9\n", FILE_APPEND);
                file_put_contents("notes.txt", "5\n", FILE_APPEND);
                $notes = file("notes.txt", FILE_IGNORE_NEW_LINES);
                echo count($notes) . " " . array_sum($notes);
                PHP,
            'resposta' => '3 21',
            'explicacio' => 'La primera escriptura crea el fitxer i les altres dues hi afegeixen línies: 3 notes que sumen 21.',
        ],
        [
            'id' => 'q7', 'tipus' => 'test', 'conceptes' => ['fitxers'],
            'pregunta' => 'Amb quin mode de <code>fopen()</code> s\'afegeix al final d\'un fitxer sense esborrar el que hi ha?',
            'opcions' => ['"r"', '"w"', '"a"', '"x"'],
            'correcta' => 2,
            'explicacio' => '"a" (append) escriu al final. "w" buida el fitxer, "r" només llegeix i "x" falla si ja existeix.',
        ],
        [
            'id' => 'q8', 'tipus' => 'error', 'conceptes' => ['fitxers'],
            'pregunta' => 'Hauria de mostrar les dues línies, però surt un error. A quina línia és?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("dades.txt", "un\ndos\n");
                $f = fopen("dades.txt", "r");
                while (($linia = fgets($f)) !== false) {
                    echo strtoupper($linia);
                }
                fclose($linia);
                PHP,
            'linia' => 7,
            'corregit' => <<<'PHP'
                <?php
                file_put_contents("dades.txt", "un\ndos\n");
                $f = fopen("dades.txt", "r");
                while (($linia = fgets($f)) !== false) {
                    echo strtoupper($linia);
                }
                fclose($f);
                PHP,
            'esperat' => "UN\nDOS",
            'explicacio' => 'fclose() ha de rebre el recurs del fitxer ($f), no l\'última línia llegida (que, al final, val false).',
        ],
        [
            'id' => 'q9', 'tipus' => 'sortida', 'conceptes' => ['excepcions'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                function llegeix(string $f): string
                {
                    if (!file_exists($f)) {
                        throw new RuntimeException("Falta $f");
                    }
                    return file_get_contents($f);
                }
                try {
                    echo llegeix("config.ini");
                } catch (RuntimeException $e) {
                    echo "Error: " . $e->getMessage();
                }
                PHP,
            'resposta' => 'Error: Falta config.ini',
            'explicacio' => 'L\'excepció es llança dins de la funció i es captura al try de qui la crida.',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['fitxers', 'excepcions'],
            'pregunta' => 'Ordena les línies perquè llegeixi un fitxer que no existeix i mostri l\'error sense cap avís de PHP.',
            'linies' => [
                '<?php',
                'try {',
                '    $text = @file_get_contents("no-hi-es.txt");',
                '    if ($text === false) {',
                '        throw new RuntimeException("No es pot llegir");',
                '    }',
                '} catch (RuntimeException $e) {',
                '    echo $e->getMessage();',
                '}',
            ],
            'sortida' => 'No es pot llegir',
            'explicacio' => 'L\'@ silencia l\'avís; es comprova el false i es converteix en una excepció que es captura.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Registre de temperatures',
            'fitxer' => 'temperatures.php',
            'punts' => 4,
            'fitxers' => ['temperatures.txt' => "12.5\n14\n9.5\n16\n"],
            'enunciat' => <<<'HTML'
                <p>Una estació meteorològica desa les temperatures a <code>temperatures.txt</code>, una per línia. Fes un programa que
                llegeixi el fitxer i en mostri el resum.</p>
                <pre class="term">$ php temperatures.php
                Lectures: 4
                Mitjana: 13 °C
                Màxima: 16 °C
                Mínima: 9.5 °C</pre>
                <ul>
                  <li>Llegeix el fitxer amb <code>file()</code> (sense salts de línia i sense línies buides).</li>
                  <li>Si el fitxer no existeix o no es pot llegir, llança una <code>RuntimeException</code> i, en capturar-la, mostra
                      <code>ERROR: no es pot llegir temperatures.txt</code> i <code>exit(1)</code>.</li>
                  <li>Si alguna línia no és un número, llança una <code>InvalidArgumentException</code>: <code>ERROR: la línia 3 («calor») no és un número.</code> i <code>exit(2)</code>.</li>
                  <li>Si el fitxer és buit: <code>No hi ha cap lectura.</code></li>
                  <li>La mitjana, arrodonida a 1 decimal amb <code>round()</code>.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Registre de temperatures: llegeix temperatures.txt\n\n",
            'proves' => [
                ['nom' => 'Quatre lectures', 'fitxers' => ['temperatures.txt' => "12.5\n14\n9.5\n16\n"], 'conte' => ['Lectures: 4', 'Mitjana: 13 °C', 'Màxima: 16 °C', 'Mínima: 9.5 °C']],
                ['nom' => 'Negatives i línies buides', 'fitxers' => ['temperatures.txt' => "-2\n\n3.5\n-4.5\n"], 'conte' => ['Lectures: 3', 'Mitjana: -1 °C', 'Màxima: 3.5 °C', 'Mínima: -4.5 °C']],
                ['nom' => 'Fitxer buit', 'fitxers' => ['temperatures.txt' => ''], 'conte' => ['No hi ha cap lectura.'], 'no_conte' => ['Mitjana']],
                ['nom' => 'Sense fitxer', 'conte' => ['ERROR: no es pot llegir temperatures.txt'], 'codi_sortida' => 1],
                ['nom' => 'Una línia que no és un número', 'fitxers' => ['temperatures.txt' => "12\n13\ncalor\n"], 'conte' => ['ERROR: la línia 3 («calor») no és un número.'], 'codi_sortida' => 2],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Registre de temperatures: llegeix temperatures.txt

                $fitxer = "temperatures.txt";

                function llegeixTemperatures(string $fitxer): array
                {
                    if (!is_file($fitxer)) {
                        throw new RuntimeException("no es pot llegir $fitxer");
                    }
                    $linies = @file($fitxer, FILE_IGNORE_NEW_LINES);
                    if ($linies === false) {
                        throw new RuntimeException("no es pot llegir $fitxer");
                    }
                    $temperatures = [];
                    foreach ($linies as $n => $linia) {
                        $linia = trim($linia);
                        if ($linia === "") {
                            continue;
                        }
                        if (!is_numeric($linia)) {
                            throw new InvalidArgumentException("la línia " . ($n + 1) . " («{$linia}») no és un número.");
                        }
                        $temperatures[] = (float) $linia;
                    }
                    return $temperatures;
                }

                try {
                    $t = llegeixTemperatures($fitxer);
                    if (count($t) === 0) {
                        echo "No hi ha cap lectura." . PHP_EOL;
                        exit(0);
                    }
                    echo "Lectures: " . count($t) . PHP_EOL;
                    echo "Mitjana: " . round(array_sum($t) / count($t), 1) . " °C" . PHP_EOL;
                    echo "Màxima: " . max($t) . " °C" . PHP_EOL;
                    echo "Mínima: " . min($t) . " °C" . PHP_EOL;
                } catch (RuntimeException $e) {
                    echo "ERROR: " . $e->getMessage() . PHP_EOL;
                    exit(1);
                } catch (InvalidArgumentException $e) {
                    echo "ERROR: " . $e->getMessage() . PHP_EOL;
                    exit(2);
                }
                PHP,
            'solucio_explicacio' => 'La funció només llança excepcions; el programa principal les captura i tria el missatge i el codi de sortida. Les línies buides se salten amb continue.',
        ],
    ],
];
