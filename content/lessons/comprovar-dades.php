<?php
/**
 * Lliçó: Comprovar dades i funcions numèriques (Tema 3, apartat 3.1, primera part).
 */

$origen = 'Tema 3 - Funcions integrades.pdf';

return [
    'id' => 'comprovar-dades',
    'ordre' => 110,
    'titol' => 'isset, empty, is_* i funcions numèriques',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.1 Tipus, números i dates',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'calculadora',
    'resum' => 'Comprovar que una dada existeix i de quin tipus és abans de fer-la servir, el parany d\'empty("0") i les funcions per arrodonir, formatar i generar números aleatoris.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Per què funcions integrades?',
            'origen' => "$origen · pàg. 4",
            'html' => <<<'HTML'
                <p>PHP porta milers de funcions llestes per fer servir. Estan escrites en C (són més ràpides que el codi equivalent fet a mà),
                provades i documentades: <strong>no reinventis la roda</strong>. Algunes depenen d'extensions (<code>mbstring</code>, <code>json</code>...),
                que pots comprovar amb <code>php -m</code>.</p>
                <p>Truc: escriu <code>php.net/nom_funcio</code> al navegador per veure'n els paràmetres, què retorna (i quan retorna <code>false</code>) i exemples.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'isset(), empty() i is_null()',
            'origen' => "$origen · pàg. 5-8",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Valor de $var</th><th>isset()</th><th>empty()</th><th>is_null()</th></tr>
                  <tr><td>(no definida)</td><td>false</td><td>true</td><td>true + Warning</td></tr>
                  <tr><td>null</td><td>false</td><td>true</td><td>true</td></tr>
                  <tr><td>"" · "0" · 0 · false · []</td><td>true</td><td>true</td><td>false</td></tr>
                  <tr><td>"hola" · [1, 2] · true</td><td>true</td><td>false</td><td>false</td></tr>
                </table>
                <p><code>isset()</code>: existeix i no és null? (accepta diverses variables: totes han de complir).
                <code>empty()</code>: té un valor «buit»? (no avisa si no existeix).
                També: <code>is_numeric</code>, <code>is_int</code>, <code>is_float</code>, <code>is_string</code>, <code>is_bool</code>, <code>is_array</code>,
                <code>unset()</code> i <code>get_debug_type()</code> (PHP 8).</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: comprovar dades',
            'fitxer' => 'comprovar.php',
            'codi' => <<<'PHP'
                <?php
                $num = 42;
                $preu = 3.14;

                var_dump(isset($num));          // true
                var_dump(isset($noExisteix));   // false (sense avís)
                var_dump(empty(""));            // true
                var_dump(empty("0"));           // true  <- compte!
                var_dump(is_numeric("42"));     // true  (cadena numèrica)
                var_dump(is_int("42"));         // false (és un string!)

                unset($num);
                var_dump(isset($num));          // false
                echo get_debug_type($preu) . PHP_EOL;   // float
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['isset i empty'],
            'pregunta' => 'Amb <code>$a = "";</code>, què retornen <code>isset($a)</code> i <code>empty($a)</code>?',
            'opcions' => ['false i true', 'true i true', 'true i false', 'false i false'],
            'correcta' => 1,
            'explicacio' => 'La variable existeix i no és null (isset = true), però el seu valor és buit (empty = true).',
        ],
        [
            'tipus' => 'text',
            'titol' => 'El parany d\'empty() i el "0"',
            'origen' => "$origen · pàg. 9",
            'html' => <<<'HTML'
                <p>Si un usuari demana <strong>0 unitats</strong>, <code>empty($_POST['quantitat'])</code> ho considera camp buit i el rebutja!</p>
                <p>Solució: <code>$q = $_POST['quantitat'] ?? '';</code> i després <code>if ($q === '')</code>. L'operador <code>??</code> dona un valor
                per defecte si la variable no existeix o és null, sense avisos.</p>
                <p>Recorda: les dades d'un formulari (<code>$_GET</code>, <code>$_POST</code>), de <code>readline()</code> o de <code>$argv</code>
                <strong>arriben sempre com a string</strong>.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['isset i empty'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $quantitat = "0";
                echo empty($quantitat) ? "buit" : "ple";
                echo " ";
                echo $quantitat === '' ? "buit" : "ple";
                PHP,
            'resposta' => 'buit ple',
            'explicacio' => 'empty("0") és true (el considera buit). En canvi, "0" === "" és false: hi ha un valor.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Funcions numèriques',
            'origen' => "$origen · pàg. 10-11",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Funció</th><th>Exemple</th><th>Resultat</th></tr>
                  <tr><td>abs</td><td>abs(-10)</td><td>10</td></tr>
                  <tr><td>round</td><td>round(3.14159, 2) · round(2.5)</td><td>3.14 · 3</td></tr>
                  <tr><td>ceil / floor</td><td>ceil(4.2) · floor(4.9)</td><td>5 · 4</td></tr>
                  <tr><td>intdiv / %</td><td>intdiv(17, 5) · 17 % 5</td><td>3 · 2</td></tr>
                  <tr><td>** / sqrt</td><td>2 ** 10 · sqrt(81)</td><td>1024 · 9</td></tr>
                  <tr><td>max / min</td><td>max([6.5, 9, 4.25])</td><td>9</td></tr>
                  <tr><td>number_format</td><td>number_format(1234.567, 2, ',', '.')</td><td>1.234,57</td></tr>
                </table>
                <p><code>round()</code> arrodoneix «allunyant-se del zero»: <code>round(-2.5)</code> és -3.
                Per a contrasenyes, tokens o codis de verificació fes servir <strong><code>random_int()</code></strong> (segur), mai <code>rand()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: números',
            'fitxer' => 'numeros.php',
            'codi' => <<<'PHP'
                <?php
                echo round(3.14159, 2), PHP_EOL;
                echo ceil(4.2), " ", floor(4.9), PHP_EOL;
                echo intdiv(17, 5), " ", 17 % 5, PHP_EOL;
                echo number_format(1234.567, 2, ',', '.'), " €", PHP_EOL;

                $notes = [6.5, 9, 4.25, 8];
                echo "Màxima: " . max($notes) . " · Mínima: " . min($notes) . PHP_EOL;

                echo "Dau: " . random_int(1, 6) . PHP_EOL;   // canvia a cada execució
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q3',
            'conceptes' => ['funcions numèriques'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                echo ceil(7.1) . "-" . floor(7.9) . "-" . round(7.5);
                PHP,
            'resposta' => '8-7-8',
            'explicacio' => 'ceil arrodoneix cap amunt (8), floor cap avall (7) i round(7.5) arrodoneix al més proper, allunyant-se del zero (8).',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['funcions numèriques'],
            'pregunta' => 'Volem mostrar <code>1.234,50</code> (format europeu). Completa el separador decimal:',
            'codi' => "echo number_format(1234.5, 2, ___, '.');",
            'respostes' => ["','", '","'],
            'explicacio' => 'Els paràmetres són: número, decimals, separador decimal (la coma) i separador de milers (el punt).',
        ],
    ],
];
