<?php
/**
 * Examen final dels temes 1 a 4: preguntes de tots quatre temes i dos programes.
 */

return [
    'id' => 'final-1-4',
    'ordre' => 90,
    'titol' => 'Examen final · Temes 1 a 4',
    'temes' => [
        'Tema 1 – Introducció al desenvolupament web',
        'Tema 2 – Fonaments de PHP',
        'Tema 3 – Funcions integrades de PHP',
        'Tema 4 – Errors, excepcions i fitxers',
    ],
    'resum' => 'Un examen complet com el de veritat: 16 preguntes de tots quatre temes i dos programes, un de dades per consola i un de fitxers amb excepcions.',
    'minuts' => 90,
    'xp' => 100,
    'punts_teoria' => 6,

    'teoria' => [
        // ---- Tema 1 ----
        [
            'id' => 'q1', 'tema' => 'Tema 1 – Introducció al desenvolupament web', 'tipus' => 'test', 'conceptes' => ['client i servidor'],
            'pregunta' => 'Què rep el navegador quan demana una pàgina <code>.php</code>?',
            'opcions' => ['El codi PHP, que executa ell mateix', 'L\'HTML que ha generat el servidor en executar el PHP', 'Un fitxer binari', 'Res fins que l\'usuari fa clic'],
            'correcta' => 1,
            'explicacio' => 'El PHP s\'executa al servidor; el navegador només rep el resultat (HTML, CSS, JS).',
        ],
        [
            'id' => 'q2', 'tema' => 'Tema 1 – Introducció al desenvolupament web', 'tipus' => 'test', 'conceptes' => ['codis d\'estat'],
            'pregunta' => 'Quina família de codis d\'estat indica un error <strong>del servidor</strong>?',
            'opcions' => ['2xx', '3xx', '4xx', '5xx'],
            'correcta' => 3,
            'explicacio' => '5xx són errors del servidor (500 Internal Server Error); 4xx, del client (404 Not Found).',
        ],
        [
            'id' => 'q3', 'tema' => 'Tema 1 – Introducció al desenvolupament web', 'tipus' => 'test', 'conceptes' => ['MVC'],
            'pregunta' => 'En MVC, si canvies el disseny d\'una pàgina sense tocar les dades, quina part modifiques?',
            'opcions' => ['El model', 'La vista', 'El controlador', 'La base de dades'],
            'correcta' => 1,
            'explicacio' => 'La vista és la presentació. Separar-la del model permet canviar el disseny sense tocar les dades.',
        ],
        [
            'id' => 'q4', 'tema' => 'Tema 1 – Introducció al desenvolupament web', 'tipus' => 'ordenar', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Ordena el que passa des que escrius una URL fins que veus la pàgina:',
            'elements' => [
                'Resolució DNS',
                'Connexió TCP/IP (port 80 o 443)',
                'Petició HTTP',
                'El servidor executa el PHP',
                'Resposta HTTP amb codi d\'estat',
                'El navegador renderitza la pàgina',
            ],
            'explicacio' => 'IP, connexió, petició, processament, resposta i renderitzat.',
        ],
        // ---- Tema 2 ----
        [
            'id' => 'q5', 'tema' => 'Tema 2 – Fonaments de PHP', 'tipus' => 'sortida', 'conceptes' => ['tipus i casting'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $a = "3";
                $b = 4;
                var_dump($a + $b, $a . $b, $a == 3, $a === 3);
                PHP,
            'resposta' => "int(7)\nstring(2) \"34\"\nbool(true)\nbool(false)",
            'explicacio' => '+ converteix a número, . concatena, == compara el valor i === també el tipus.',
        ],
        [
            'id' => 'q6', 'tema' => 'Tema 2 – Fonaments de PHP', 'tipus' => 'sortida', 'conceptes' => ['bucles', 'arrays'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $notes = ["Anna" => 8, "Pau" => 4, "Rita" => 6];
                $aprovats = 0;
                foreach ($notes as $nom => $nota) {
                    if ($nota >= 5) {
                        $aprovats++;
                        echo $nom[0];
                    }
                }
                echo $aprovats;
                PHP,
            'resposta' => 'AR2',
            'explicacio' => 'Aproven Anna i Rita: es mostra la primera lletra de cada nom i, al final, el comptador.',
        ],
        [
            'id' => 'q7', 'tema' => 'Tema 2 – Fonaments de PHP', 'tipus' => 'error', 'conceptes' => ['funcions'],
            'pregunta' => 'Fatal error: «Too few arguments». A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                function area(float $base, float $altura): float
                {
                    return $base * $altura / 2;
                }
                echo area(6) . PHP_EOL;
                PHP,
            'linia' => 6,
            'corregit' => <<<'PHP'
                <?php
                function area(float $base, float $altura): float
                {
                    return $base * $altura / 2;
                }
                echo area(6, 4) . PHP_EOL;
                PHP,
            'esperat' => '12',
            'explicacio' => 'La funció demana dos arguments sense valor per defecte i només se n\'hi passa un.',
        ],
        [
            'id' => 'q8', 'tema' => 'Tema 2 – Fonaments de PHP', 'tipus' => 'sortida', 'conceptes' => ['arguments per consola'],
            'pregunta' => 'S\'executa amb <code>php calc.php 8 x 5</code>. Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $op = $argv[2];
                $r = match ($op) {
                    "+" => $argv[1] + $argv[3],
                    "x" => $argv[1] * $argv[3],
                    default => "?",
                };
                echo "$argc: $r";
                PHP,
            'args' => '8 x 5',
            'resposta' => '4: 40',
            'explicacio' => '$argv té 4 elements (el programa i els 3 arguments) i match tria la branca de la «x».',
        ],
        // ---- Tema 3 ----
        [
            'id' => 'q9', 'tema' => 'Tema 3 – Funcions integrades de PHP', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $t = "Programació en PHP";
                echo mb_strlen($t) . " " . mb_strtoupper(mb_substr($t, 0, 4)) . " " . str_word_count("PHP en el servidor");
                PHP,
            'resposta' => '18 PROG 4',
            'explicacio' => 'mb_strlen compta 18 caràcters (la ó és un de sol), mb_substr agafa els 4 primers i str_word_count compta 4 paraules.',
        ],
        [
            'id' => 'q10', 'tema' => 'Tema 3 – Funcions integrades de PHP', 'tipus' => 'sortida', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $p = [["n" => "Pau", "e" => 20], ["n" => "Anna", "e" => 17], ["n" => "Biel", "e" => 19]];
                usort($p, fn($a, $b) => $a["e"] <=> $b["e"]);
                echo implode(",", array_column($p, "n"));
                PHP,
            'resposta' => 'Anna,Biel,Pau',
            'explicacio' => 'usort() ordena per edat de menor a major i array_column() treu els noms en aquest ordre.',
        ],
        [
            'id' => 'q11', 'tema' => 'Tema 3 – Funcions integrades de PHP', 'tipus' => 'test', 'conceptes' => ['comprovar dades'],
            'pregunta' => 'L\'usuari escriu <code>"3.5"</code>. Quina comprovació et diu si és un número vàlid?',
            'opcions' => ['is_int($x)', 'is_numeric($x)', 'ctype_digit($x)', 'isset($x)'],
            'correcta' => 1,
            'explicacio' => 'is_numeric() accepta enters i decimals escrits com a text. is_int() mira el tipus i ctype_digit() no accepta el punt.',
        ],
        [
            'id' => 'q12', 'tema' => 'Tema 3 – Funcions integrades de PHP', 'tipus' => 'completa', 'conceptes' => ['number_format'],
            'pregunta' => 'Completa el codi perquè mostri <code>1.500,00 €</code>.',
            'codi' => <<<'PHP'
                <?php
                $preu = 1500;
                echo ___ . " €";
                PHP,
            'respostes' => ['number_format($preu, 2, ",", ".")', "number_format(\$preu, 2, ',', '.')"],
            'explicacio' => 'number_format(número, decimals, separador decimal, separador de milers).',
        ],
        // ---- Tema 4 ----
        [
            'id' => 'q13', 'tema' => 'Tema 4 – Errors, excepcions i fitxers', 'tipus' => 'sortida', 'conceptes' => ['try / catch / finally'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                function comprova(int $edat): string
                {
                    if ($edat < 0) {
                        throw new InvalidArgumentException("edat negativa");
                    }
                    return "ok";
                }
                foreach ([20, -1] as $e) {
                    try {
                        echo comprova($e) . " ";
                    } catch (InvalidArgumentException $ex) {
                        echo "error: " . $ex->getMessage() . " ";
                    } finally {
                        echo "| ";
                    }
                }
                PHP,
            'resposta' => 'ok | error: edat negativa |',
            'explicacio' => 'A cada volta, el try o el catch i després, sempre, el finally.',
        ],
        [
            'id' => 'q14', 'tema' => 'Tema 4 – Errors, excepcions i fitxers', 'tipus' => 'error', 'conceptes' => ['JSON'],
            'pregunta' => 'Volíem «Anna», però surt un avís i res més. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("u.json", json_encode(["nom" => "Anna"]));
                $u = json_decode(file_get_contents("u.json"));
                echo $u["nom"] . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                file_put_contents("u.json", json_encode(["nom" => "Anna"]));
                $u = json_decode(file_get_contents("u.json"), true);
                echo $u["nom"] . PHP_EOL;
                PHP,
            'esperat' => 'Anna',
            'explicacio' => 'Sense true, json_decode() retorna un objecte i no s\'hi pot accedir amb ["nom"]. Amb true retorna un array associatiu.',
        ],
        [
            'id' => 'q15', 'tema' => 'Tema 4 – Errors, excepcions i fitxers', 'tipus' => 'test', 'conceptes' => ['fitxers'],
            'pregunta' => 'Vols llegir un fitxer gran línia a línia, sense carregar-lo tot a memòria. Què fas servir?',
            'opcions' => ['file_get_contents()', 'file()', 'fopen() i fgets() dins d\'un bucle', 'json_decode()'],
            'correcta' => 2,
            'explicacio' => 'file_get_contents() i file() carreguen tot el fitxer; fgets() en llegeix una línia cada vegada.',
        ],
        [
            'id' => 'q16', 'tema' => 'Tema 4 – Errors, excepcions i fitxers', 'tipus' => 'ordenar', 'conceptes' => ['fitxers', 'excepcions'],
            'pregunta' => 'Ordena les línies perquè compti les línies d\'un fitxer i mostri un error si no existeix.',
            'linies' => [
                '<?php',
                'try {',
                '    if (!file_exists("dades.txt")) {',
                '        throw new RuntimeException("Falta dades.txt");',
                '    }',
                '    echo count(file("dades.txt"));',
                '} catch (RuntimeException $e) {',
                '    echo "Error: " . $e->getMessage();',
                '}',
            ],
            'sortida' => 'Error: Falta dades.txt',
            'explicacio' => 'Primer es comprova que existeix (i si no, es llança l\'excepció); només després es llegeix.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Conversor de temps',
            'fitxer' => 'temps.php',
            'punts' => 2,
            'enunciat' => <<<'HTML'
                <p>Un cronòmetre dona els temps en segons. Fes un programa que els passi a hores, minuts i segons.</p>
                <pre class="term">$ php temps.php 3725
                1 h 2 min 5 s
                $ php temps.php 3725 curt
                01:02:05</pre>
                <ul>
                  <li>Sense arguments: <code>Ús: php temps.php &lt;segons&gt; [curt]</code> i <code>exit(1)</code>.</li>
                  <li>Si el primer argument no és un enter positiu o zero (<code>ctype_digit</code>): <code>ERROR: «abc» no és un nombre de segons vàlid.</code> i <code>exit(1)</code>.</li>
                  <li>Format normal: només les parts que no són zero (<code>2 min 5 s</code>, <code>1 h</code>). Si són 0 segons, <code>0 s</code>.</li>
                  <li>Amb <code>curt</code> com a segon argument: sempre <code>hh:mm:ss</code>, amb zeros davant (<code>str_pad</code>).</li>
                  <li>Fes servir <code>intdiv()</code> i <code>%</code>.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Conversor de temps: php temps.php <segons> [curt]\n\n",
            'proves' => [
                ['nom' => '3725 segons', 'args' => ['3725'], 'exacte' => '1 h 2 min 5 s'],
                ['nom' => '125 segons', 'args' => ['125'], 'exacte' => '2 min 5 s'],
                ['nom' => 'Una hora justa', 'args' => ['3600'], 'exacte' => '1 h'],
                ['nom' => 'Zero', 'args' => ['0'], 'exacte' => '0 s'],
                ['nom' => 'Format curt', 'args' => ['3725', 'curt'], 'exacte' => '01:02:05'],
                ['nom' => 'Curt de més de 10 hores', 'args' => ['40000', 'curt'], 'exacte' => '11:06:40'],
                ['nom' => 'Un text', 'args' => ['abc'], 'conte' => ['ERROR: «abc» no és un nombre de segons vàlid.'], 'codi_sortida' => 1],
                ['nom' => 'Negatiu', 'args' => ['-5'], 'conte' => ['ERROR: «-5» no és un nombre de segons vàlid.'], 'codi_sortida' => 1],
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php temps.php <segons> [curt]'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Conversor de temps: php temps.php <segons> [curt]

                if ($argc < 2) {
                    echo "Ús: php temps.php <segons> [curt]" . PHP_EOL;
                    exit(1);
                }
                if (!ctype_digit($argv[1])) {
                    echo "ERROR: «{$argv[1]}» no és un nombre de segons vàlid." . PHP_EOL;
                    exit(1);
                }

                $total = (int) $argv[1];
                $h = intdiv($total, 3600);
                $min = intdiv($total % 3600, 60);
                $s = $total % 60;

                if (($argv[2] ?? "") === "curt") {
                    $dos = fn(int $n) => str_pad((string) $n, 2, "0", STR_PAD_LEFT);
                    echo $dos($h) . ":" . $dos($min) . ":" . $dos($s) . PHP_EOL;
                    exit(0);
                }

                $parts = [];
                if ($h > 0) {
                    $parts[] = "$h h";
                }
                if ($min > 0) {
                    $parts[] = "$min min";
                }
                if ($s > 0 || $parts === []) {
                    $parts[] = "$s s";
                }
                echo implode(" ", $parts) . PHP_EOL;
                PHP,
            'solucio_explicacio' => 'intdiv() dona les hores; el residu (%) de 3600 són els segons que sobren, que es tornen a dividir per 60. Les parts a zero no s\'afegeixen, tret que no n\'hi hagi cap.',
        ],
        [
            'id' => 'p2',
            'titol' => 'Paraules més repetides d\'un fitxer',
            'fitxer' => 'paraules.php',
            'punts' => 2,
            'fitxers' => ['text.txt' => "El pa i el vi.\nEl pa és bo, i el vi també!\n"],
            'enunciat' => <<<'HTML'
                <p>Inspirat en l'<strong>Exercici 15</strong> (Comptador de paraules) i en els fitxers del Tema 4. El programa llegeix
                <code>text.txt</code> i mostra quantes paraules té i les 3 més repetides.</p>
                <pre class="term">$ php paraules.php
                Paraules: 13
                1. el (4)
                2. pa (2)
                3. i (2)</pre>
                <ul>
                  <li>Passa el text a minúscules amb <code>mb_strtolower()</code>, parteix-lo per espais i salts de línia, i treu la puntuació
                      del principi i del final de cada paraula (<code>. , ; : ! ?</code>). Les paraules buides no compten.</li>
                  <li>Compta-les amb <code>array_count_values()</code> i ordena de més a menys amb <code>arsort()</code> (si empaten, queda
                      primer la que ha sortit abans al text).</li>
                  <li>Si el fitxer no existeix, llança una <code>RuntimeException</code> i, en capturar-la, mostra
                      <code>ERROR: no es pot llegir text.txt</code> i <code>exit(1)</code>.</li>
                  <li>Si no hi ha cap paraula: <code>El fitxer és buit.</code></li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Paraules més repetides de text.txt\n\n",
            'proves' => [
                ['nom' => 'Text de l\'exemple', 'fitxers' => ['text.txt' => "El pa i el vi.\nEl pa és bo, i el vi també!\n"],
                 'exacte' => "Paraules: 13\n1. el (4)\n2. pa (2)\n3. i (2)"],
                ['nom' => 'Majúscules i accents', 'fitxers' => ['text.txt' => "Àvila ÀVILA àvila? Girona, girona.\n"], 'conte' => ['Paraules: 5', '1. àvila (3)', '2. girona (2)']],
                ['nom' => 'Fitxer buit', 'fitxers' => ['text.txt' => "\n"], 'conte' => ['El fitxer és buit.']],
                ['nom' => 'Sense fitxer', 'conte' => ['ERROR: no es pot llegir text.txt'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Paraules més repetides de text.txt

                $fitxer = "text.txt";

                try {
                    if (!is_file($fitxer)) {
                        throw new RuntimeException("no es pot llegir $fitxer");
                    }
                    $text = mb_strtolower((string) file_get_contents($fitxer));

                    // Espais i salts de línia separen paraules; la puntuació de les puntes no en forma part
                    $trossos = explode(" ", str_replace(["\r\n", "\n", "\t"], " ", $text));
                    $paraules = [];
                    foreach ($trossos as $t) {
                        $t = trim($t, ".,;:!?");
                        if ($t !== "") {
                            $paraules[] = $t;
                        }
                    }
                    if ($paraules === []) {
                        echo "El fitxer és buit." . PHP_EOL;
                        exit(0);
                    }

                    $comptes = array_count_values($paraules);
                    arsort($comptes);
                    echo "Paraules: " . count($paraules) . PHP_EOL;
                    $pos = 1;
                    foreach (array_slice($comptes, 0, 3, true) as $paraula => $n) {
                        echo "$pos. $paraula ($n)" . PHP_EOL;
                        $pos++;
                    }
                } catch (RuntimeException $e) {
                    echo "ERROR: " . $e->getMessage() . PHP_EOL;
                    exit(1);
                }
                PHP,
            'solucio_explicacio' => 'array_count_values() fa el recompte; arsort() ordena de més a menys conservant les paraules com a claus, i array_slice(..., true) en deixa les 3 primeres.',
        ],
    ],
];
