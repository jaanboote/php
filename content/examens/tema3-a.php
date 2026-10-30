<?php
/**
 * Simulacre A del Tema 3 (Funcions integrades de PHP).
 */

return [
    'id' => 'tema3-a',
    'ordre' => 31,
    'titol' => 'Tema 3 · Simulacre A',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'resum' => 'Comprovar dades, números, cadenes amb accents (mb_*) i funcions d\'arrays, i un formatador de noms.',
    'minuts' => 45,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'sortida', 'conceptes' => ['isset i empty'],
            'pregunta' => 'Què imprimeix? (S = sí, N = no)',
            'codi' => <<<'PHP'
                <?php
                $a = "0";
                $b = null;
                echo isset($a) ? "S" : "N";
                echo empty($a) ? "S" : "N";
                echo isset($b) ? "S" : "N";
                PHP,
            'resposta' => 'SSN',
            'explicacio' => '"0" existeix (isset true) però empty() el considera buit. Una variable a null no passa isset().',
        ],
        [
            'id' => 'q2', 'tipus' => 'sortida', 'conceptes' => ['round i number_format'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                echo round(2.456, 2) . " " . number_format(2.456, 1, ",", ".") . " " . intdiv(17, 3);
                PHP,
            'resposta' => '2.46 2,5 5',
            'explicacio' => 'round() arrodoneix (2,46), number_format() també arrodoneix i hi posa la coma, i intdiv() fa la divisió entera.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $p = "Lleida";
                $q = "Girona·Cat";
                echo strlen($p) . " " . mb_strlen($q) . " " . strlen($q);
                PHP,
            'resposta' => '6 10 11',
            'explicacio' => 'El punt volat (·) ocupa 2 bytes en UTF-8: mb_strlen() compta 10 caràcters, strlen() 11 bytes.',
        ],
        [
            'id' => 'q4', 'tipus' => 'test', 'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Quina funció passa <code>"àvila"</code> a <code>"ÀVILA"</code> correctament?',
            'opcions' => ['strtoupper()', 'mb_strtoupper()', 'ucfirst()', 'ucwords()'],
            'correcta' => 1,
            'explicacio' => 'strtoupper() només entén caràcters d\'un byte i deixa la «à» en minúscula; mb_strtoupper() treballa amb UTF-8.',
        ],
        [
            'id' => 'q5', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $email = "  Anna.Puig@Institut.cat ";
                $net = strtolower(trim($email));
                echo substr($net, 0, strpos($net, "@"));
                PHP,
            'resposta' => 'anna.puig',
            'explicacio' => 'trim() treu els espais, strtolower() ho posa en minúscules i substr() agafa fins a la posició de l\'@.',
        ],
        [
            'id' => 'q6', 'tipus' => 'sortida', 'conceptes' => ['explode i implode'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $data = "2026-06-15";
                $parts = explode("-", $data);
                echo implode("/", array_reverse($parts));
                PHP,
            'resposta' => '15/06/2026',
            'explicacio' => 'explode() parteix pel guió, array_reverse() gira l\'ordre i implode() ho torna a unir amb barres.',
        ],
        [
            'id' => 'q7', 'tipus' => 'sortida', 'conceptes' => ['map, filter i reduce'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $preus = [10, 25, 4, 40];
                $cars = array_filter($preus, fn($p) => $p > 20);
                $iva = array_map(fn($p) => $p * 2, $cars);
                echo implode(",", $iva);
                PHP,
            'resposta' => '50,80',
            'explicacio' => 'array_filter() es queda amb 25 i 40, i array_map() els multiplica per 2.',
        ],
        [
            'id' => 'q8', 'tipus' => 'error', 'conceptes' => ['strpos'],
            'pregunta' => 'Hauria de dir «Conté PHP», però diu el contrari. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $text = "PHP és un llenguatge de servidor";
                if (strpos($text, "PHP") == false) {
                    echo "No conté PHP" . PHP_EOL;
                } else {
                    echo "Conté PHP" . PHP_EOL;
                }
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $text = "PHP és un llenguatge de servidor";
                if (strpos($text, "PHP") === false) {
                    echo "No conté PHP" . PHP_EOL;
                } else {
                    echo "Conté PHP" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Conté PHP',
            'explicacio' => 'strpos() retorna 0 (posició inicial), i 0 == false és cert. Cal === false (o str_contains()).',
        ],
        [
            'id' => 'q9', 'tipus' => 'completa', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Completa el codi perquè ordeni l\'array associatiu per valor, de més gran a més petit, conservant les claus.',
            'codi' => <<<'PHP'
                <?php
                $punts = ["Anna" => 12, "Pau" => 30, "Rita" => 21];
                ___($punts);
                echo implode(",", array_keys($punts));
                PHP,
            'respostes' => ['arsort'],
            'explicacio' => 'arsort() ordena per valor de més gran a més petit i conserva les claus (sort() i rsort() les perdrien).',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Ordena les línies perquè compti les paraules diferents d\'una frase.',
            'linies' => [
                '<?php',
                '$frase = "pa pa vi pa aigua";',
                '$paraules = explode(" ", $frase);',
                '$diferents = array_unique($paraules);',
                'echo count($diferents);',
            ],
            'sortida' => '3',
            'explicacio' => 'Partir la frase, treure els repetits amb array_unique() i comptar: pa, vi i aigua.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Formatador de noms',
            'fitxer' => 'noms.php',
            'punts' => 4,
            'enunciat' => <<<'HTML'
                <p>Els noms d'un formulari arriben escrits de qualsevol manera. Fes un programa que rebi un nom complet (entre cometes,
                és un sol argument) i el mostri ben escrit, amb les inicials i el nombre de lletres.</p>
                <pre class="term">$ php noms.php "  àNNA   puig  SERRA "
                Nom: Ànna Puig Serra
                Inicials: À.P.S.
                Lletres: 13</pre>
                <ul>
                  <li>Sense argument o amb un text buit: <code>Ús: php noms.php "&lt;nom complet&gt;"</code> i <code>exit(1)</code>.</li>
                  <li>Treu els espais de les puntes i els repetits entre paraules (cada paraula, separada per un sol espai).</li>
                  <li>Cada paraula amb la primera lletra en majúscula i la resta en minúscula, <strong>també amb accents</strong>.
                      Pots fer servir <code>mb_convert_case($text, MB_CASE_TITLE)</code> o <code>mb_strtoupper</code>/<code>mb_substr</code>.</li>
                  <li><code>Lletres</code> és el nombre de caràcters sense comptar els espais.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Formatador de noms: php noms.php \"<nom complet>\"\n\n",
            'proves' => [
                ['nom' => 'Desordenat i amb accent', 'args' => ['  àNNA   puig  SERRA '], 'conte' => ['Nom: Ànna Puig Serra', 'Inicials: À.P.S.', 'Lletres: 13']],
                ['nom' => 'Un sol nom', 'args' => ['joan'], 'exacte' => "Nom: Joan\nInicials: J.\nLletres: 4"],
                ['nom' => 'Amb ç i accents', 'args' => ['FRANÇOIS ÉTIENNE'], 'conte' => ['Nom: François Étienne', 'Inicials: F.É.', 'Lletres: 15']],
                ['nom' => 'Sense argument', 'args' => [], 'conte' => ['Ús: php noms.php "<nom complet>"'], 'codi_sortida' => 1],
                ['nom' => 'Només espais', 'args' => ['   '], 'conte' => ['Ús: php noms.php "<nom complet>"'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Formatador de noms: php noms.php "<nom complet>"

                if ($argc < 2 || trim($argv[1]) === "") {
                    echo "Ús: php noms.php \"<nom complet>\"" . PHP_EOL;
                    exit(1);
                }

                // Partir per espais i descartar els trossos buits (espais repetits)
                $paraules = array_filter(explode(" ", trim($argv[1])), fn($p) => $p !== "");
                $paraules = array_map(fn($p) => mb_convert_case($p, MB_CASE_TITLE, "UTF-8"), $paraules);

                $inicials = "";
                $lletres = 0;
                foreach ($paraules as $p) {
                    $inicials .= mb_substr($p, 0, 1) . ".";
                    $lletres += mb_strlen($p);
                }

                echo "Nom: " . implode(" ", $paraules) . PHP_EOL;
                echo "Inicials: $inicials" . PHP_EOL;
                echo "Lletres: $lletres" . PHP_EOL;
                PHP,
            'solucio_explicacio' => 'explode + array_filter treu els espais repetits; mb_convert_case(..., MB_CASE_TITLE) posa la primera lletra en majúscula també amb accents, i mb_substr / mb_strlen compten caràcters i no bytes.',
        ],
    ],
];
