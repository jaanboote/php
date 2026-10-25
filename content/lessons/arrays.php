<?php
/**
 * Lliçó: Arrays (Tema 2, apartat 2.5).
 */

$origen = 'Tema 2 - Fonaments PHP.pdf';

return [
    'id' => 'arrays',
    'ordre' => 40,
    'titol' => 'Arrays indexats, associatius i multidimensionals',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.5 Arrays',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'caixer',
    'resum' => 'Crear i recórrer arrays, claus automàtiques, arrays de registres, funcions útils, ordenació, desestructuració i spread.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Què és un array',
            'origen' => "$origen · pàg. 55",
            'html' => <<<'HTML'
                <p>Un array guarda diversos valors en una sola variable. Cada valor té una <strong>clau</strong> (índex) per accedir-hi.</p>
                <table class="taula">
                  <tr><th>Tipus</th><th>Claus</th></tr>
                  <tr><td>Indexat</td><td>Números 0, 1, 2... automàtics</td></tr>
                  <tr><td>Associatiu</td><td>Strings triats per nosaltres: "nom", "edat"...</td></tr>
                  <tr><td>Multidimensional</td><td>Els valors són, al seu torn, arrays (taules)</td></tr>
                </table>
                <p>Sintaxi recomanada: <code>[ ]</code> (<code>array()</code> és equivalent però més antiga). Llegir una posició que no existeix dona
                <code>Warning: Undefined array key</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: array indexat',
            'fitxer' => 'compra.php',
            'codi' => <<<'PHP'
                <?php
                $compra = ["pa", "llet", "ous"];
                $compra[] = "fruita";        // afegeix al final
                echo $compra[0] . PHP_EOL;   // pa
                echo count($compra) . PHP_EOL;

                for ($i = 0; $i < count($compra); $i++) {
                    echo $compra[$i] . " ";
                }
                echo PHP_EOL;

                foreach ($compra as $pos => $producte) {
                    echo "[$pos] => $producte" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Claus automàtiques',
            'origen' => "$origen · pàg. 58",
            'html' => <<<'HTML'
                <p>Sense claus, PHP assigna enters consecutius des de 0. <code>$arr[] = valor</code> afegeix al final amb
                <strong>la clau entera més gran + 1</strong>. Per unir arrays indexats, fes servir <code>array_merge()</code>, no <code>+</code>
                (el <code>+</code> només afegeix les claus que encara no hi són).</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['arrays indexats'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$b = [5 => \"a\", \"b\"];\n\$b[] = \"c\";\necho implode(\",\", array_keys(\$b));",
            'resposta' => '5,6,7',
            'explicacio' => '"a" té la clau 5. Les següents sense clau agafen la més gran + 1: 6 i 7.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Arrays associatius',
            'origen' => "$origen · pàg. 57",
            'html' => <<<'HTML'
                <p>Cada element s'identifica amb una clau de tipus string. Són ideals per representar un <strong>registre</strong>: un alumne, un producte...
                Abans de llegir una clau dubtosa, comprova-la amb <code>array_key_exists()</code> o <code>isset()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: array associatiu',
            'fitxer' => 'naixement.php',
            'codi' => <<<'PHP'
                <?php
                $naixement = [
                    "Joan" => "01/02/2000",
                    "Marc" => "23/06/1998",
                ];
                $naixement["Laia"] = "14/03/2001";   // afegeix

                foreach ($naixement as $nom => $data) {
                    echo "$nom va néixer el $data" . PHP_EOL;
                }

                if (array_key_exists("Pau", $naixement)) {
                    echo "Pau hi és" . PHP_EOL;
                } else {
                    echo "Pau no hi és" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Arrays multidimensionals',
            'origen' => "$origen · pàg. 59",
            'html' => <<<'HTML'
                <p>Un array d'arrays associatius és com una taula: <code>$alumnes[1]["nom"]</code> vol dir «fila 1, columna nom».
                És l'estructura típica dels resultats d'una base de dades: cada fila és un array associatiu.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: taula d\'alumnes',
            'fitxer' => 'alumnes.php',
            'codi' => <<<'PHP'
                <?php
                $alumnes = [
                    ["nom" => "Anna", "nota" => 8.5],
                    ["nom" => "Pau",  "nota" => 6.0],
                    ["nom" => "Laia", "nota" => 9.2],
                ];
                echo $alumnes[1]["nom"] . PHP_EOL;

                foreach ($alumnes as $alumne) {
                    echo "{$alumne['nom']}: {$alumne['nota']}" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q2',
            'conceptes' => ['arrays multidimensionals'],
            'pregunta' => 'Amb l\'array <code>$alumnes</code> de l\'exemple, volem mostrar la nota de la Laia (9.2). Què hi va a ___ ?',
            'codi' => "echo \$alumnes[___][\"nota\"];",
            'respostes' => ['2'],
            'explicacio' => 'Laia és el tercer element, i els índexs comencen per 0: posició 2.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Funcions útils',
            'origen' => "$origen · pàg. 60",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Funció</th><th>Exemple</th><th>Resultat</th></tr>
                  <tr><td>count</td><td>count([4, 5, 6])</td><td>3</td></tr>
                  <tr><td>in_array</td><td>in_array(5, [4, 5, 6])</td><td>true</td></tr>
                  <tr><td>array_search</td><td>array_search("b", ["a", "b"])</td><td>1 (la clau)</td></tr>
                  <tr><td>array_keys / array_values</td><td>array_keys(["a" => 1, "b" => 2])</td><td>["a", "b"]</td></tr>
                  <tr><td>array_push / array_pop</td><td>array_pop($pila)</td><td>Treu l'últim</td></tr>
                  <tr><td>array_sum / max / min</td><td>array_sum([1, 2, 3])</td><td>6</td></tr>
                  <tr><td>array_merge</td><td>array_merge([1, 2], [3])</td><td>[1, 2, 3]</td></tr>
                  <tr><td>implode / explode</td><td>implode(", ", ["a", "b"])</td><td>"a, b"</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: funcions d\'arrays',
            'fitxer' => 'funcions_arrays.php',
            'codi' => <<<'PHP'
                <?php
                $temperatures = [18, 20, 22, 19, 25, 24, 17];
                $dies = ["Dl", "Dt", "Dc", "Dj", "Dv", "Ds", "Dg"];

                $mitjana = array_sum($temperatures) / count($temperatures);
                echo "Mitjana: " . round($mitjana, 1) . PHP_EOL;

                $max = max($temperatures);
                $pos = array_search($max, $temperatures);
                echo "Màxima: $max ({$dies[$pos]})" . PHP_EOL;

                var_dump(in_array(30, $temperatures));
                echo implode(" - ", $dies) . PHP_EOL;
                print_r(explode("-", "2026-09-29"));
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Ordenar',
            'origen' => "$origen · pàg. 61",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Funció</th><th>Ordena per</th><th>Manté clau => valor?</th></tr>
                  <tr><td>sort / rsort</td><td>valor (asc. / desc.)</td><td>No: reindexa 0, 1, 2...</td></tr>
                  <tr><td>asort / arsort</td><td>valor (asc. / desc.)</td><td>Sí</td></tr>
                  <tr><td>ksort / krsort</td><td>clau (asc. / desc.)</td><td>Sí</td></tr>
                  <tr><td>usort</td><td>criteri propi (funció)</td><td>No</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: arsort contra rsort',
            'fitxer' => 'ordenar.php',
            'codi' => <<<'PHP'
                <?php
                $notes = ["Anna" => 8, "Pau" => 6, "Laia" => 9];

                $copia = $notes;
                arsort($copia);     // conserva els noms
                print_r($copia);

                $copia = $notes;
                rsort($copia);      // perd els noms!
                print_r($copia);
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q3',
            'conceptes' => ['ordenar arrays'],
            'pregunta' => 'Tens <code>$notes = ["Anna" => 8, "Pau" => 6]</code> i vols ordenar-lo de més a menys nota <strong>sense perdre els noms</strong>. Quina funció?',
            'opcions' => ['rsort', 'arsort', 'krsort', 'sort'],
            'correcta' => 1,
            'explicacio' => 'arsort ordena per valor de major a menor i conserva la relació clau => valor. rsort perdria els noms i krsort ordenaria pels noms.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Desestructuració i spread',
            'origen' => "$origen · pàg. 62",
            'html' => <<<'HTML'
                <p><code>[$nom, $edat] = ["Anna", 25];</code> assigna cada posició a una variable (també funciona amb claus:
                <code>["ciutat" => $ciutat] = $dades;</code>). L'operador <strong>spread</strong> <code>...</code> expandeix un array dins d'un altre:
                <code>["kiwi", ...$fruites]</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q4',
            'conceptes' => ['desestructuració'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = 1;\n\$b = 2;\n[\$a, \$b] = [\$b, \$a];\necho \$a . \$b;",
            'resposta' => '21',
            'explicacio' => 'És el truc per intercanviar dues variables: ara $a val 2 i $b val 1.',
        ],
    ],
];
