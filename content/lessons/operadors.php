<?php
/**
 * Lliçó: Operadors (Tema 2, apartat 2.3).
 */

$origen = 'Tema 2 - Fonaments PHP.pdf';

return [
    'id' => 'operadors',
    'ordre' => 20,
    'titol' => 'Operadors: ==, ===, ??, <=> i companyia',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.3 Operadors',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'alerta-meteo',
    'resum' => 'Aritmètics, comparació (i per què === és més segur que ==), lògics, increment i els operadors curts ?:, ??, ??= i <=>.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Aritmètics i d\'assignació',
            'origen' => "$origen · pàg. 33",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Op.</th><th>Operació</th><th>$a = 7, $b = 2</th></tr>
                  <tr><td>+ - *</td><td>Suma, resta, multiplicació</td><td>9, 5, 14</td></tr>
                  <tr><td>/</td><td>Divisió</td><td>3.5</td></tr>
                  <tr><td>%</td><td>Mòdul (residu)</td><td>1</td></tr>
                  <tr><td>**</td><td>Potència</td><td>49</td></tr>
                </table>
                <p>Abreviatures: <code>$a += $b</code> equival a <code>$a = $a + $b</code> (també <code>-=</code>, <code>*=</code>, <code>/=</code>, <code>%=</code>, <code>.=</code>).
                El mòdul és molt útil: <code>$n % 2 === 0</code> vol dir que <code>$n</code> és parell.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: aritmètica',
            'fitxer' => 'aritmetica.php',
            'codi' => <<<'PHP'
                <?php
                $a = 7;
                $b = 2;
                echo $a / $b, PHP_EOL;         // 3.5
                echo intdiv($a, $b), PHP_EOL;  // 3 (divisió entera)
                echo $a % $b, PHP_EOL;         // 1
                echo $a ** $b, PHP_EOL;        // 49
                $a += 3;
                echo $a, PHP_EOL;              // 10
                echo 15 % 2 === 0 ? "parell" : "senar", PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['operadors aritmètics'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho 7 % 3 + 2 ** 3;",
            'resposta' => '9',
            'explicacio' => '7 % 3 és 1 (el residu) i 2 ** 3 és 8. 1 + 8 = 9.',
        ],
        [
            'tipus' => 'text',
            'titol' => '== o ===?',
            'origen' => "$origen · pàg. 34-35",
            'html' => <<<'HTML'
                <p><code>==</code> compara el <strong>valor</strong> convertint tipus; <code>===</code> exigeix el mateix valor <strong>i</strong> el mateix tipus.</p>
                <table class="taula">
                  <tr><th>Comparació</th><th>==</th><th>===</th></tr>
                  <tr><td>5 == "5"</td><td>true</td><td>false</td></tr>
                  <tr><td>"1" == "01"</td><td>true</td><td>false</td></tr>
                  <tr><td>null == false</td><td>true</td><td>false</td></tr>
                  <tr><td>100 == "1e2"</td><td>true</td><td>false</td></tr>
                  <tr><td>"abc" == 0</td><td>false (PHP 8)</td><td>false</td></tr>
                </table>
                <p><strong>Error clàssic:</strong> si el PIN d'un usuari és <code>"0"</code>, <code>$pin == false</code> és cert i el programa creuria que no n'hi ha.
                Fes servir <code>===</code> i <code>!==</code> sempre que puguis.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: comparacions',
            'fitxer' => 'comparar.php',
            'codi' => <<<'PHP'
                <?php
                var_dump(5 == "5");
                var_dump(5 === "5");
                var_dump("1" == "01");
                var_dump(null == false);

                $pin = "0";
                if ($pin == false) {
                    echo "Cap PIN! (error: sí que n'hi ha)" . PHP_EOL;
                }
                if ($pin === "") {
                    echo "Aquest no s'executa" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['comparacions'],
            'pregunta' => 'Quina d\'aquestes comparacions retorna <code>true</code>?',
            'opcions' => ['"1" === "01"', '5 === "5"', 'null == false', '"abc" == 0'],
            'correcta' => 2,
            'explicacio' => 'Amb == null i false es consideren iguals. Les dues primeres són === amb valors o tipus diferents, i "abc" == 0 és false des de PHP 8.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Operadors lògics',
            'origen' => "$origen · pàg. 36",
            'html' => <<<'HTML'
                <p><code>&amp;&amp;</code> (i), <code>||</code> (o), <code>!</code> (no) i <code>xor</code> (només un dels dos).
                <strong>Curtcircuit:</strong> a <code>$a &amp;&amp; $b</code>, si <code>$a</code> és fals, <code>$b</code> ni s'avalua.</p>
                <p>Existeixen <code>and</code> i <code>or</code>, però tenen <strong>menys prioritat que =</strong>:
                <code>$x = true and false;</code> assigna <code>true</code> a <code>$x</code>! Fes servir sempre <code>&amp;&amp;</code> i <code>||</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: and contra &&',
            'fitxer' => 'logics.php',
            'codi' => <<<'PHP'
                <?php
                $x = true and false;   // ($x = true) and false
                var_dump($x);          // bool(true) !
                $y = true && false;    // $y = (true && false)
                var_dump($y);          // bool(false)

                $edat = 20;
                $carnet = true;
                if ($edat >= 18 && $carnet) {
                    echo "Pots conduir" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Increment i decrement',
            'origen' => "$origen · pàg. 37",
            'html' => <<<'HTML'
                <p><code>++$a</code> suma 1 i <strong>després</strong> retorna el valor; <code>$a++</code> retorna el valor i després suma 1.
                Si l'operador va sol en una línia (<code>$i++;</code>) no hi ha diferència: només importa dins d'una expressió.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q3',
            'conceptes' => ['increment'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = 5;\necho \$a++;\necho \$a;\necho ++\$a;",
            'resposta' => '567',
            'explicacio' => '$a++ mostra 5 i després suma (6). echo $a mostra 6. ++$a suma primer (7) i després mostra.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Ternari, ??, ??= i <=>',
            'origen' => "$origen · pàg. 38",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Op.</th><th>Exemple</th><th>Resultat</th></tr>
                  <tr><td>? :</td><td>$nota >= 5 ? "Aprovat" : "Suspès"</td><td>Tria entre dos valors</td></tr>
                  <tr><td>??</td><td>$nom ?? "convidat"</td><td>$nom si existeix i no és null</td></tr>
                  <tr><td>??=</td><td>$nom ??= "convidat";</td><td>Assigna només si és null</td></tr>
                  <tr><td>&lt;=&gt;</td><td>5 &lt;=&gt; 3</td><td>1, 0 o -1</td></tr>
                </table>
                <p>La «nau espacial» <code>&lt;=&gt;</code> retorna -1 si l'esquerra és menor, 0 si són iguals i 1 si és major. És la que fa servir <code>usort()</code> per ordenar.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: operadors curts',
            'fitxer' => 'curts.php',
            'codi' => <<<'PHP'
                <?php
                $nota = 7;
                echo $nota >= 5 ? "Aprovat" : "Suspès", PHP_EOL;

                $usuari = null;
                echo $usuari ?? "convidat", PHP_EOL;

                $config = [];
                $config['tema'] ??= "clar";
                echo $config['tema'], PHP_EOL;

                echo 5 <=> 3, " ", 3 <=> 3, " ", 1 <=> 3, PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['operador ??'],
            'pregunta' => 'Volem que <code>$nom</code> valgui <code>$usuari</code> si existeix, i "convidat" si no, <strong>sense cap avís</strong>. Què hi va a ___ ?',
            'codi' => "\$nom = \$usuari ___ \"convidat\";",
            'respostes' => ['??'],
            'explicacio' => 'L\'operador ?? comprova si existeix i no és null (com isset) i no genera cap Warning.',
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q5',
            'conceptes' => ['operador <=>'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\necho 3 <=> 5;",
            'resposta' => '-1',
            'explicacio' => '3 és menor que 5, per tant <=> retorna -1.',
        ],
    ],
];
