<?php
/**
 * Simulacre B del Tema 3 (Funcions integrades de PHP).
 */

return [
    'id' => 'tema3-b',
    'ordre' => 32,
    'titol' => 'Tema 3 · Simulacre B',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'resum' => 'Dates amb date() i DateTime, cadenes, ordenar arrays i novetats de PHP 8, i un comptador de dies entre dues dates.',
    'minuts' => 45,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'sortida', 'conceptes' => ['date'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $t = mktime(9, 5, 0, 3, 7, 2026);
                echo date("d/m/Y H:i", $t);
                PHP,
            'resposta' => '07/03/2026 09:05',
            'explicacio' => 'd i m posen el zero davant; H és l\'hora en format 24 h i i, els minuts.',
        ],
        [
            'id' => 'q2', 'tipus' => 'sortida', 'conceptes' => ['DateTime'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $inici = new DateTime("2026-01-28");
                $inici->modify("+5 days");
                echo $inici->format("d/m");
                PHP,
            'resposta' => '02/02',
            'explicacio' => 'DateTime sap quants dies té cada mes: 28 de gener + 5 dies = 2 de febrer.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['DateTime'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $a = new DateTime("2026-06-01");
                $b = new DateTime("2026-06-20");
                echo $a->diff($b)->days;
                PHP,
            'resposta' => '19',
            'explicacio' => 'diff() retorna un DateInterval; days és el nombre total de dies entre les dues dates.',
        ],
        [
            'id' => 'q4', 'tipus' => 'test', 'conceptes' => ['date'],
            'pregunta' => 'Quin format de <code>date()</code> dona el número del dia de la setmana (1 = dilluns, 7 = diumenge)?',
            'opcions' => ['"d"', '"N"', '"D"', '"w"'],
            'correcta' => 1,
            'explicacio' => '"N" va de 1 (dilluns) a 7 (diumenge). "w" va de 0 (diumenge) a 6, i "D" dona el nom abreujat en anglès.',
        ],
        [
            'id' => 'q5', 'tipus' => 'sortida', 'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $codi = "7";
                echo str_pad($codi, 4, "0", STR_PAD_LEFT) . "|" . str_repeat("-", 3) . "|" . ucfirst("php");
                PHP,
            'resposta' => '0007|---|Php',
            'explicacio' => 'str_pad omple amb zeros per l\'esquerra fins a 4 caràcters, str_repeat repeteix el guió i ucfirst posa la primera en majúscula.',
        ],
        [
            'id' => 'q6', 'tipus' => 'sortida', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $edats = ["Pau" => 20, "Anna" => 17, "Biel" => 18];
                ksort($edats);
                echo implode(",", array_keys($edats)) . " " . implode(",", $edats);
                PHP,
            'resposta' => 'Anna,Biel,Pau 17,18,20',
            'explicacio' => 'ksort() ordena per la clau (alfabèticament) i conserva cada valor amb la seva clau.',
        ],
        [
            'id' => 'q7', 'tipus' => 'sortida', 'conceptes' => ['novetats de PHP 8'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $usuari = ["nom" => "Anna"];
                echo $usuari["cognom"] ?? "sense cognom";
                echo " " . str_starts_with("index.php", "index") . str_ends_with("foto.png", ".jpg");
                PHP,
            'resposta' => 'sense cognom 1',
            'explicacio' => '?? dona el valor de la dreta si la clau no existeix. true es mostra com a «1» i false com a res.',
        ],
        [
            'id' => 'q8', 'tipus' => 'error', 'conceptes' => ['DateTime'],
            'pregunta' => 'Fatal error. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $data = DateTime::createFromFormat("!d/m/Y", "15/06/2026");
                echo $data->format("Y-m-d") . PHP_EOL;
                echo $data->diff(new DateTime("2026-06-20"))->day . PHP_EOL;
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                $data = DateTime::createFromFormat("!d/m/Y", "15/06/2026");
                echo $data->format("Y-m-d") . PHP_EOL;
                echo $data->diff(new DateTime("2026-06-20"))->days . PHP_EOL;
                PHP,
            'esperat' => "2026-06-15\n5",
            'explicacio' => 'L\'interval té la propietat days (amb s). «day» no existeix i PHP avisa d\'una propietat no definida.',
        ],
        [
            'id' => 'q9', 'tipus' => 'test', 'conceptes' => ['comprovar dades'],
            'pregunta' => 'Quina d\'aquestes crides retorna <code>true</code>?',
            'opcions' => ['is_numeric("12abc")', 'is_numeric("12.5")', 'is_int("12")', 'ctype_digit("-12")'],
            'correcta' => 1,
            'explicacio' => '"12.5" és un text numèric. "12abc" no ho és, is_int("12") és fals perquè és un string, i ctype_digit no accepta el signe.',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Ordena les línies perquè mostri els productes del més barat al més car: «pa,llet,oli».',
            'linies' => [
                '<?php',
                '$preus = ["oli" => 6.5, "pa" => 1.2, "llet" => 1.45];',
                'asort($preus);',
                'echo implode(",", array_keys($preus));',
            ],
            'sortida' => 'pa,llet,oli',
            'explicacio' => 'asort() ordena per valor de petit a gran conservant les claus; després es mostren les claus en aquest ordre.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Quants dies falten?',
            'fitxer' => 'dies.php',
            'punts' => 4,
            'enunciat' => <<<'HTML'
                <p>Inspirat en l'activitat <strong>Calendari de lliuraments</strong> del Tema 3. El programa rep dues dates en format
                <code>dd/mm/aaaa</code>: avui i el dia d'un lliurament.</p>
                <pre class="term">$ php dies.php 01/06/2026 15/06/2026
                Lliurament: dilluns 15/06/2026
                Falten 14 dies.</pre>
                <ul>
                  <li>Si no hi ha 2 arguments: <code>Ús: php dies.php &lt;avui&gt; &lt;lliurament&gt;</code> i <code>exit(1)</code>.</li>
                  <li>Llegeix-les amb <code>DateTime::createFromFormat("d/m/Y", ...)</code>. Si alguna no és vàlida (retorna <code>false</code>):
                      <code>ERROR: «32/13/2026» no és una data vàlida.</code> i <code>exit(1)</code>.</li>
                  <li>El dia de la setmana en català (dilluns, dimarts, dimecres, dijous, divendres, dissabte, diumenge): fes servir
                      <code>format("N")</code> i un array.</li>
                  <li>Si queda 1 dia: <code>Falta 1 dia.</code> Si és avui: <code>És avui!</code> Si ja ha passat: <code>Ja ha passat (fa 3 dies).</code></li>
                </ul>
                <p class="nota">Compte: <code>createFromFormat()</code> accepta el 32/13/2026 i el converteix en una altra data. Per detectar-ho,
                comprova que, tornant-la a formatar amb <code>"d/m/Y"</code>, dona el mateix text.</p>
                HTML,
            'codi_inicial' => "<?php\n// Quants dies falten? php dies.php <avui> <lliurament>\n\n",
            'proves' => [
                ['nom' => 'Falten 14 dies', 'args' => ['01/06/2026', '15/06/2026'], 'conte' => ['Lliurament: dilluns 15/06/2026', 'Falten 14 dies.']],
                ['nom' => 'Canvi de mes', 'args' => ['28/02/2026', '02/03/2026'], 'conte' => ['Lliurament: dilluns 02/03/2026', 'Falten 2 dies.']],
                ['nom' => 'Falta 1 dia', 'args' => ['18/06/2026', '19/06/2026'], 'conte' => ['Lliurament: divendres 19/06/2026', 'Falta 1 dia.']],
                ['nom' => 'És avui', 'args' => ['21/06/2026', '21/06/2026'], 'conte' => ['diumenge 21/06/2026', 'És avui!']],
                ['nom' => 'Ja ha passat', 'args' => ['10/06/2026', '07/06/2026'], 'conte' => ['Ja ha passat (fa 3 dies).']],
                ['nom' => 'Data impossible', 'args' => ['01/06/2026', '32/13/2026'], 'conte' => ['ERROR: «32/13/2026» no és una data vàlida.'], 'codi_sortida' => 1],
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php dies.php <avui> <lliurament>'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Quants dies falten? php dies.php <avui> <lliurament>

                if ($argc != 3) {
                    echo "Ús: php dies.php <avui> <lliurament>" . PHP_EOL;
                    exit(1);
                }

                // createFromFormat() «arregla» dates impossibles (32/13 → una altra data): si en tornar-la a formatar no
                // coincideix amb el text, és que no era vàlida
                function llegeixData(string $text): DateTime
                {
                    $d = DateTime::createFromFormat("!d/m/Y", $text);
                    if ($d === false || $d->format("d/m/Y") !== $text) {
                        echo "ERROR: «{$text}» no és una data vàlida." . PHP_EOL;
                        exit(1);
                    }
                    return $d;
                }

                $avui = llegeixData($argv[1]);
                $lliurament = llegeixData($argv[2]);

                $dies = ["", "dilluns", "dimarts", "dimecres", "dijous", "divendres", "dissabte", "diumenge"];
                echo "Lliurament: " . $dies[(int) $lliurament->format("N")] . " " . $lliurament->format("d/m/Y") . PHP_EOL;

                $diff = $avui->diff($lliurament);
                if ($diff->days === 0) {
                    echo "És avui!" . PHP_EOL;
                } elseif ($diff->invert) {
                    echo "Ja ha passat (fa {$diff->days} " . ($diff->days === 1 ? "dia" : "dies") . ")." . PHP_EOL;
                } elseif ($diff->days === 1) {
                    echo "Falta 1 dia." . PHP_EOL;
                } else {
                    echo "Falten {$diff->days} dies." . PHP_EOL;
                }
                PHP,
            'solucio_explicacio' => 'Amb «!» al format, l\'hora queda a 00:00 i la diferència és de dies sencers. diff() dona els dies (days) i si la segona data és anterior (invert).',
        ],
    ],
];
