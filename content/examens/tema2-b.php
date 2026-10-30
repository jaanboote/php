<?php
/**
 * Simulacre B del Tema 2 (Fonaments de PHP).
 */

return [
    'id' => 'tema2-b',
    'ordre' => 22,
    'titol' => 'Tema 2 · Simulacre B',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'resum' => 'Constants, operadors lògics, match, bucles, arrays i funcions, i la taula de multiplicar a mida de l\'Exercici 8.',
    'minuts' => 45,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'sortida', 'conceptes' => ['constants'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                const IVA = 21;
                define("BOTIGA", "La Cantonada");
                echo BOTIGA . ": " . 100 * (1 + IVA / 100);
                PHP,
            'resposta' => 'La Cantonada: 121',
            'explicacio' => 'const i define() creen constants, que es fan servir sense $. 100 × 1,21 = 121.',
        ],
        [
            'id' => 'q2', 'tipus' => 'sortida', 'conceptes' => ['operadors lògics'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $edat = 16;
                $permis = true;
                echo ($edat >= 18 || $permis) ? "Entra" : "No entra";
                echo " ";
                echo ($edat >= 18 && $permis) ? "Entra" : "No entra";
                PHP,
            'resposta' => 'Entra No entra',
            'explicacio' => 'Amb || n\'hi ha prou que una sigui certa ($permis). Amb && cal que ho siguin totes dues, i 16 >= 18 és fals.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['match'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $nota = 7;
                echo match (true) {
                    $nota >= 9 => "Excel·lent",
                    $nota >= 7 => "Notable",
                    $nota >= 5 => "Aprovat",
                    default => "Suspès",
                };
                PHP,
            'resposta' => 'Notable',
            'explicacio' => 'match (true) tria la primera branca que és certa: 7 >= 9 és fals, 7 >= 7 és cert.',
        ],
        [
            'id' => 'q4', 'tipus' => 'test', 'conceptes' => ['bucles'],
            'pregunta' => 'Quin bucle executa el codi <strong>almenys una vegada</strong>, encara que la condició sigui falsa d\'entrada?',
            'opcions' => ['for', 'while', 'do...while', 'foreach'],
            'correcta' => 2,
            'explicacio' => 'do...while comprova la condició al final, després d\'haver executat el bloc un cop.',
        ],
        [
            'id' => 'q5', 'tipus' => 'sortida', 'conceptes' => ['break i continue'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                for ($i = 1; $i <= 10; $i++) {
                    if ($i % 2 == 0) {
                        continue;
                    }
                    if ($i > 7) {
                        break;
                    }
                    echo $i;
                }
                PHP,
            'resposta' => '1357',
            'explicacio' => 'continue salta els parells; quan $i val 9 (> 7), break atura el bucle.',
        ],
        [
            'id' => 'q6', 'tipus' => 'completa', 'conceptes' => ['arrays indexats'],
            'pregunta' => 'Completa el codi perquè mostri quants elements té l\'array (3).',
            'codi' => <<<'PHP'
                <?php
                $colors = ["vermell", "verd", "blau"];
                echo ___($colors);
                PHP,
            'respostes' => ['count', 'sizeof'],
            'explicacio' => 'count() retorna el nombre d\'elements d\'un array (sizeof() n\'és un àlies).',
        ],
        [
            'id' => 'q7', 'tipus' => 'error', 'conceptes' => ['arrays indexats'],
            'pregunta' => 'Avís «Undefined array key 3». A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $notes = [6, 8, 7];
                $suma = 0;
                for ($i = 0; $i <= count($notes); $i++) {
                    $suma += $notes[$i];
                }
                echo $suma . PHP_EOL;
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                $notes = [6, 8, 7];
                $suma = 0;
                for ($i = 0; $i < count($notes); $i++) {
                    $suma += $notes[$i];
                }
                echo $suma . PHP_EOL;
                PHP,
            'esperat' => '21',
            'explicacio' => 'Els índexs van de 0 a count - 1. Amb <= el bucle intenta llegir $notes[3], que no existeix.',
        ],
        [
            'id' => 'q8', 'tipus' => 'sortida', 'conceptes' => ['funcions', 'àmbit de variables'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $total = 10;
                function suma(int $total): int
                {
                    $total = $total + 5;
                    return $total;
                }
                echo suma(1) . " " . $total;
                PHP,
            'resposta' => '6 10',
            'explicacio' => 'El paràmetre $total és una variable local de la funció: canviar-la no toca la $total de fora.',
        ],
        [
            'id' => 'q9', 'tipus' => 'test', 'conceptes' => ['tipus i casting'],
            'pregunta' => 'Què retorna <code>readline()</code> si l\'usuari escriu <code>25</code>?',
            'opcions' => ['L\'enter 25', 'El text "25"', 'El float 25.0', 'true'],
            'correcta' => 1,
            'explicacio' => 'readline() sempre retorna un string. Per fer càlculs, converteix-lo amb (int) o (float).',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['arrays associatius', 'foreach'],
            'pregunta' => 'Ordena les línies perquè mostri el preu total de la cistella (5.5).',
            'linies' => [
                '<?php',
                '$cistella = ["pa" => 1.2, "llet" => 0.9, "ous" => 3.4];',
                '$total = 0;',
                'foreach ($cistella as $producte => $preu) {',
                '    $total += $preu;',
                '}',
                'echo $total;',
            ],
            'sortida' => '5.5',
            'explicacio' => 'L\'acumulador s\'inicialitza abans del bucle, se suma a dins i es mostra al final.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Taula de multiplicar a mida',
            'fitxer' => 'taula.php',
            'punts' => 4,
            'enunciat' => <<<'HTML'
                <p>Basat en l'<strong>Exercici 8</strong> d'Exercicis 01. El programa demana el número de la taula i fins a quin
                multiplicador arribar, mostra la taula i pregunta si en vol fer una altra.</p>
                <pre class="term">De quin número vols la taula? 7
                Fins a quin multiplicador? 3
                --- TAULA DEL 7 ---
                    7 x   1 =      7
                    7 x   2 =     14
                    7 x   3 =     21
                Vols fer una altra taula? (s/n): n
                Fins aviat!</pre>
                <ul>
                  <li>Llegeix les dades amb <code>readline()</code> amb exactament aquests textos.</li>
                  <li>Mostra cada línia amb un <code>for</code>. Alinea les columnes amb <code>str_pad(..., STR_PAD_LEFT)</code>
                      (5, 3 i 6 caràcters).</li>
                  <li>Tot ha d'estar dins d'un <code>do...while</code> que es repeteix mentre la resposta sigui «s». Neteja-la amb
                      <code>trim()</code> i <code>strtolower()</code> perquè també valgui « S ».</li>
                  <li>En acabar, <code>Fins aviat!</code></li>
                </ul>
                <p class="nota">Per provar-ho, escriu les respostes en línies a «Entrada per a readline()», per exemple
                <code>7</code>, <code>3</code> i <code>n</code>.</p>
                HTML,
            'codi_inicial' => "<?php\n// Taula de multiplicar a mida\n\n",
            'proves' => [
                ['nom' => 'Taula del 7 fins al 3', 'stdin' => "7\n3\nn\n", 'conte' => ['--- TAULA DEL 7 ---', '7 x 1 = 7', '7 x 2 = 14', '7 x 3 = 21', 'Fins aviat!'], 'no_conte' => ['7 x 4']],
                ['nom' => 'Taula del 9 fins al 10', 'stdin' => "9\n10\nn\n", 'conte' => ['9 x 10 = 90'], 'no_conte' => ['9 x 11']],
                ['nom' => 'Dues taules', 'stdin' => "2\n2\ns\n3\n2\nn\n", 'conte' => ['--- TAULA DEL 2 ---', '2 x 2 = 4', '--- TAULA DEL 3 ---', '3 x 2 = 6', 'Fins aviat!']],
                ['nom' => '« S » amb espais i majúscula', 'stdin' => "5\n1\n S \n6\n1\nn\n", 'conte' => ['5 x 1 = 5', '--- TAULA DEL 6 ---', '6 x 1 = 6']],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Taula de multiplicar a mida

                // do...while: la primera taula s'ha de fer sempre, i la pregunta va al final
                do {
                    $numero = (int) readline("De quin número vols la taula? ");
                    $limit = (int) readline("Fins a quin multiplicador? ");

                    echo "--- TAULA DEL $numero ---" . PHP_EOL;
                    for ($i = 1; $i <= $limit; $i++) {
                        echo str_pad((string) $numero, 5, " ", STR_PAD_LEFT) . " x " . str_pad((string) $i, 3, " ", STR_PAD_LEFT)
                            . " = " . str_pad((string) ($numero * $i), 6, " ", STR_PAD_LEFT) . PHP_EOL;
                    }

                    $resposta = strtolower(trim((string) readline("Vols fer una altra taula? (s/n): ")));
                } while ($resposta == "s");

                echo "Fins aviat!" . PHP_EOL;
                PHP,
            'solucio_explicacio' => 'El do...while fa la primera taula sense preguntar i repeteix mentre la resposta neta sigui «s». str_pad omple per l\'esquerra per alinear els números.',
        ],
    ],
];
