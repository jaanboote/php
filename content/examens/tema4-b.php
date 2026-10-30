<?php
/**
 * Simulacre B del Tema 4 (Errors, excepcions i fitxers).
 */

$json = <<<'JSON'
[
    {"nom": "Pau", "dia": "11-03"},
    {"nom": "Anna", "dia": "05-14"}
]
JSON;

return [
    'id' => 'tema4-b',
    'ordre' => 42,
    'titol' => 'Tema 4 · Simulacre B',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'resum' => 'Jerarquia d\'excepcions, fopen i fgets, JSON amb JSON_THROW_ON_ERROR, i una agenda d\'aniversaris en JSON.',
    'minuts' => 50,
    'xp' => 50,
    'punts_teoria' => 6,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'test', 'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Quin <code>catch</code> captura <strong>tant</strong> les excepcions (Exception) com els errors interns (Error, TypeError...)?',
            'opcions' => ['catch (Exception $e)', 'catch (Error $e)', 'catch (Throwable $e)', 'catch (RuntimeException $e)'],
            'correcta' => 2,
            'explicacio' => 'Throwable és la interfície comuna d\'Exception i d\'Error.',
        ],
        [
            'id' => 'q2', 'tipus' => 'sortida', 'conceptes' => ['JSON'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $alumne = ["nom" => "Anna", "notes" => [7, 9]];
                echo json_encode($alumne);
                PHP,
            'resposta' => '{"nom":"Anna","notes":[7,9]}',
            'explicacio' => 'Un array associatiu es converteix en un objecte JSON i un d\'indexat, en una llista.',
        ],
        [
            'id' => 'q3', 'tipus' => 'sortida', 'conceptes' => ['JSON'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $d = json_decode('[{"n":"pa","q":2},{"n":"ous","q":12}]', true);
                echo count($d) . " " . $d[1]["n"] . " " . array_sum(array_column($d, "q"));
                PHP,
            'resposta' => '2 ous 14',
            'explicacio' => 'És una llista de 2 objectes. array_column() treu totes les quantitats i array_sum() les suma.',
        ],
        [
            'id' => 'q4', 'tipus' => 'sortida', 'conceptes' => ['JSON', 'excepcions'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                try {
                    json_decode("{trencat", true, 512, JSON_THROW_ON_ERROR);
                    echo "OK";
                } catch (JsonException $e) {
                    echo "JSON malmès";
                }
                PHP,
            'resposta' => 'JSON malmès',
            'explicacio' => 'Amb JSON_THROW_ON_ERROR, un JSON incorrecte llança una JsonException en lloc de retornar null.',
        ],
        [
            'id' => 'q5', 'tipus' => 'sortida', 'conceptes' => ['fitxers'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("llista.txt", "pa\nllet\nous\n");
                $f = fopen("llista.txt", "r");
                $n = 0;
                while (($linia = fgets($f)) !== false) {
                    $n++;
                    echo $n . trim($linia) . " ";
                }
                fclose($f);
                PHP,
            'resposta' => '1pa 2llet 3ous',
            'explicacio' => 'fgets() llegeix una línia cada vegada (amb el salt de línia, que trim() treu) i retorna false en arribar al final.',
        ],
        [
            'id' => 'q6', 'tipus' => 'test', 'conceptes' => ['fitxers'],
            'pregunta' => 'Què passa amb <code>file_put_contents("dades.txt", "hola")</code> si <code>dades.txt</code> ja existeix?',
            'opcions' => ['Afegeix «hola» al final', 'Substitueix tot el contingut per «hola»', 'Dona un error', 'No fa res'],
            'correcta' => 1,
            'explicacio' => 'Sense FILE_APPEND, file_put_contents() sobreescriu el fitxer sencer.',
        ],
        [
            'id' => 'q7', 'tipus' => 'error', 'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Volíem «Falta el fitxer» però surt «Error inesperat». A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                try {
                    throw new RuntimeException("Falta el fitxer");
                } catch (Throwable $e) {
                    echo "Error inesperat" . PHP_EOL;
                } catch (RuntimeException $e) {
                    echo $e->getMessage() . PHP_EOL;
                }
                PHP,
            'linia' => 4,
            'corregit' => <<<'PHP'
                <?php
                try {
                    throw new RuntimeException("Falta el fitxer");
                } catch (RuntimeException $e) {
                    echo $e->getMessage() . PHP_EOL;
                } catch (Throwable $e) {
                    echo "Error inesperat" . PHP_EOL;
                }
                PHP,
            'esperat' => 'Falta el fitxer',
            'explicacio' => 'Els catch es miren en ordre i Throwable ho captura tot. El més específic (RuntimeException) ha d\'anar primer.',
        ],
        [
            'id' => 'q8', 'tipus' => 'completa', 'conceptes' => ['JSON'],
            'pregunta' => 'Completa el codi perquè <code>json_decode()</code> retorni un <strong>array associatiu</strong> i no un objecte.',
            'codi' => <<<'PHP'
                <?php
                $d = json_decode('{"nom":"Pau"}', ___);
                echo $d["nom"];
                PHP,
            'respostes' => ['true'],
            'explicacio' => 'El segon paràmetre a true fa que els objectes JSON es converteixin en arrays associatius.',
        ],
        [
            'id' => 'q9', 'tipus' => 'sortida', 'conceptes' => ['try / catch / finally'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                function prova(int $n): string
                {
                    try {
                        if ($n < 0) {
                            throw new InvalidArgumentException("negatiu");
                        }
                        return "ok";
                    } catch (InvalidArgumentException $e) {
                        return $e->getMessage();
                    } finally {
                        echo "[f]";
                    }
                }
                echo prova(1) . prova(-1);
                PHP,
            'resposta' => '[f][f]oknegatiu',
            'explicacio' => 'El finally s\'executa abans que la funció acabi de retornar, fins i tot amb return. Les dues crides es fan abans de l\'echo final, per això els dos [f] surten primer.',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['JSON', 'fitxers'],
            'pregunta' => 'Ordena les línies perquè afegeixi un contacte a un fitxer JSON i en mostri el total.',
            'linies' => [
                '<?php',
                'file_put_contents("c.json", \'[{"nom":"Anna"}]\');',
                '$contactes = json_decode(file_get_contents("c.json"), true);',
                '$contactes[] = ["nom" => "Pau"];',
                'file_put_contents("c.json", json_encode($contactes));',
                'echo count(json_decode(file_get_contents("c.json"), true));',
            ],
            'sortida' => '2',
            'explicacio' => 'Llegir el JSON, convertir-lo en array, afegir-hi l\'element i tornar a desar el fitxer sencer.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Agenda d\'aniversaris',
            'fitxer' => 'aniversaris.php',
            'punts' => 4,
            'fitxers' => ['aniversaris.json' => $json],
            'enunciat' => <<<'HTML'
                <p>Inspirat en l'activitat <strong>Agenda en JSON</strong> del Tema 4. Els aniversaris es desen a
                <code>aniversaris.json</code>, una llista d'objectes amb <code>nom</code> i <code>dia</code> (format <code>mm-dd</code>).</p>
                <pre class="term">$ php aniversaris.php llista
                Aniversaris (2):
                - Anna: 14/05
                - Pau: 03/11
                $ php aniversaris.php afegeix Biel 01-30
                Afegit: Biel (30/01)</pre>
                <ul>
                  <li><code>llista</code>: ordenats per data (mes i dia), en format <code>dd/mm</code>. Si no n'hi ha cap (o el fitxer no existeix): <code>No hi ha cap aniversari.</code></li>
                  <li><code>afegeix &lt;nom&gt; &lt;mm-dd&gt;</code>: afegeix l'aniversari i desa el fitxer amb <code>json_encode()</code>.</li>
                  <li>Llegeix el JSON amb <code>JSON_THROW_ON_ERROR</code>. Si està malmès: <code>ERROR: el fitxer aniversaris.json està malmès.</code> i <code>exit(2)</code>.</li>
                  <li>Errors d'ús (<code>exit(1)</code>): comanda que no existeix → <code>ERROR: comanda desconeguda.</code>; a <code>afegeix</code>, si
                      falten dades o el dia no té el format <code>mm-dd</code> → <code>ERROR: cal un nom i un dia (mm-dd).</code></li>
                </ul>
                <p class="nota">Per ordenar per data n'hi ha prou amb comparar els textos <code>mm-dd</code> amb <code>usort()</code> i <code>&lt;=&gt;</code>.</p>
                HTML,
            'codi_inicial' => "<?php\n// Agenda d'aniversaris: php aniversaris.php <llista|afegeix> [nom] [mm-dd]\n\n",
            'proves' => [
                ['nom' => 'Llistar', 'args' => ['llista'], 'fitxers' => ['aniversaris.json' => $json], 'exacte' => "Aniversaris (2):\n- Anna: 14/05\n- Pau: 03/11"],
                ['nom' => 'Afegir', 'args' => ['afegeix', 'Biel', '01-30'], 'fitxers' => ['aniversaris.json' => $json],
                 'conte' => ['Afegit: Biel (30/01)'], 'fitxer_conte' => ['aniversaris.json' => ['Biel', '01-30', 'Anna', 'Pau']]],
                ['nom' => 'Sense fitxer', 'args' => ['llista'], 'conte' => ['No hi ha cap aniversari.']],
                ['nom' => 'Afegir sense fitxer', 'args' => ['afegeix', 'Rita', '12-24'], 'conte' => ['Afegit: Rita (24/12)'], 'fitxer_conte' => ['aniversaris.json' => ['Rita']]],
                ['nom' => 'JSON malmès', 'args' => ['llista'], 'fitxers' => ['aniversaris.json' => '[{"nom": "Anna",'], 'conte' => ['ERROR: el fitxer aniversaris.json està malmès.'], 'codi_sortida' => 2],
                ['nom' => 'Dia mal escrit', 'args' => ['afegeix', 'Biel', '30/01'], 'fitxers' => ['aniversaris.json' => $json], 'conte' => ['ERROR: cal un nom i un dia (mm-dd).'], 'codi_sortida' => 1],
                ['nom' => 'Comanda desconeguda', 'args' => ['esborra'], 'conte' => ['ERROR: comanda desconeguda.'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Agenda d'aniversaris: php aniversaris.php <llista|afegeix> [nom] [mm-dd]

                $fitxer = "aniversaris.json";

                function llegeix(string $fitxer): array
                {
                    if (!file_exists($fitxer)) {
                        return [];
                    }
                    return json_decode((string) file_get_contents($fitxer), true, 512, JSON_THROW_ON_ERROR);
                }

                // "05-14" → "14/05"
                function format(string $dia): string
                {
                    [$mes, $d] = explode("-", $dia);
                    return "$d/$mes";
                }

                try {
                    $comanda = $argv[1] ?? "";
                    if ($comanda === "llista") {
                        $llista = llegeix($fitxer);
                        if (count($llista) === 0) {
                            echo "No hi ha cap aniversari." . PHP_EOL;
                            exit(0);
                        }
                        usort($llista, fn($a, $b) => $a["dia"] <=> $b["dia"]);
                        echo "Aniversaris (" . count($llista) . "):" . PHP_EOL;
                        foreach ($llista as $a) {
                            echo "- {$a['nom']}: " . format($a["dia"]) . PHP_EOL;
                        }
                    } elseif ($comanda === "afegeix") {
                        if ($argc != 4 || !preg_match('/^\d{2}-\d{2}$/', $argv[3])) {
                            throw new InvalidArgumentException("cal un nom i un dia (mm-dd).");
                        }
                        $llista = llegeix($fitxer);
                        $llista[] = ["nom" => $argv[2], "dia" => $argv[3]];
                        file_put_contents($fitxer, json_encode($llista, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        echo "Afegit: {$argv[2]} (" . format($argv[3]) . ")" . PHP_EOL;
                    } else {
                        throw new InvalidArgumentException("comanda desconeguda.");
                    }
                } catch (JsonException $e) {
                    echo "ERROR: el fitxer $fitxer està malmès." . PHP_EOL;
                    exit(2);
                } catch (InvalidArgumentException $e) {
                    echo "ERROR: " . $e->getMessage() . PHP_EOL;
                    exit(1);
                }
                PHP,
            'solucio_explicacio' => 'llegeix() fa servir JSON_THROW_ON_ERROR, i un JSON malmès acaba al catch de JsonException. Els textos mm-dd s\'ordenen bé comparant-los com a text.',
        ],
    ],
];
