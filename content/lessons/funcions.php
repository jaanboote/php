<?php
/**
 * Lliçó: Funcions (Tema 2, apartat 2.6).
 */

$origen = 'Tema 2 - Fonaments PHP.pdf';

return [
    'id' => 'funcions',
    'ordre' => 50,
    'titol' => 'Funcions',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.6 Funcions',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'eines-matematiques',
    'resum' => 'Definir funcions, tipus i valors per defecte, arguments amb nom, pas per referència, paràmetres variables, callbacks i funcions fletxa.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Definir i cridar funcions',
            'origen' => "$origen · pàg. 64-65",
            'html' => <<<'HTML'
                <p>Una funció agrupa instruccions amb un nom per reutilitzar-les. Pot rebre <strong>paràmetres</strong> (que són variables locals)
                i retornar un valor amb <code>return</code>. Nom: un verb en camelCase que digui què fa (<code>calcularIva</code>, <code>mostrarMenu</code>).</p>
                <p>Una funció que <strong>retorna</strong> un valor és més reutilitzable que una que fa <code>echo</code>: el resultat es pot guardar, mostrar o
                fer servir en un altre càlcul. Els paràmetres amb valor per defecte van sempre al final.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: les primeres funcions',
            'fitxer' => 'funcions.php',
            'codi' => <<<'PHP'
                <?php
                function calcularIva($preu) {
                    return $preu * 0.21;
                }

                function saludar($nom = "usuari") {
                    echo "Hola $nom" . PHP_EOL;
                }

                echo calcularIva(100) . PHP_EOL;   // 21
                saludar();                         // Hola usuari
                saludar("Quim");                   // Hola Quim
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['funcions'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfunction suma(\$a, \$b) {\n    return \$a + \$b;\n}\necho suma(suma(1, 2), 3);",
            'resposta' => '6',
            'explicacio' => 'Primer es calcula suma(1, 2) = 3, i aquest resultat es passa a suma(3, 3) = 6.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Tipus, valors per defecte i arguments amb nom',
            'origen' => "$origen · pàg. 66",
            'html' => <<<'HTML'
                <p><code>function calcularPreu(float $preu, int $quantitat = 1, float $descompte = 0): float</code></p>
                <ul>
                  <li><code>float $preu</code>: tipus del paràmetre. <code>): float</code>: tipus del valor retornat.</li>
                  <li><code>?int</code> = int o null · <code>int|string</code> = un o l'altre · <code>void</code> = no retorna res.</li>
                  <li>Arguments amb nom (PHP 8): <code>calcularPreu(10.0, descompte: 0.2)</code> salta els opcionals.</li>
                  <li><code>declare(strict_types=1);</code> (primera línia del fitxer): sense ella, PHP convertiria "10" a float sense avisar; amb ella, dona TypeError.</li>
                </ul>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: tipus i arguments amb nom',
            'fitxer' => 'preu.php',
            'html' => '<p>Descomenta l\'última línia per veure el TypeError amb strict_types.</p>',
            'codi' => <<<'PHP'
                <?php
                declare(strict_types=1);

                function calcularPreu(float $preu, int $quantitat = 1, float $descompte = 0): float {
                    return $preu * $quantitat * (1 - $descompte);
                }

                echo calcularPreu(10.0) . PHP_EOL;                  // 10
                echo calcularPreu(10.0, 3) . PHP_EOL;               // 30
                echo calcularPreu(10.0, descompte: 0.2) . PHP_EOL;  // 8
                // echo calcularPreu("10");
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['tipus a les funcions'],
            'pregunta' => 'Amb <code>declare(strict_types=1);</code>, què passa si cridem <code>calcularPreu("10")</code> (un string on s\'espera float)?',
            'opcions' => ['Retorna 10.0', 'Retorna 0', 'TypeError', 'Un Warning i continua'],
            'correcta' => 2,
            'explicacio' => 'Amb tipus estrictes PHP no converteix automàticament: llança un TypeError.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Per valor i per referència',
            'origen' => "$origen · pàg. 67",
            'html' => <<<'HTML'
                <p>Per defecte, la funció rep una <strong>còpia</strong>: la variable original no canvia. Amb <code>&amp;</code> a la definició
                (<code>function duplicar(&amp;$val)</code>), la funció modifica l'<strong>original</strong>. Normalment és més clar retornar el resultat.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q3',
            'conceptes' => ['pas per referència'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\nfunction f(\$v) {\n    \$v *= 2;\n}\nfunction g(&\$v) {\n    \$v *= 2;\n}\n\$n = 5;\nf(\$n);\ng(\$n);\necho \$n;",
            'resposta' => '10',
            'explicacio' => 'f() treballa amb una còpia i no canvia $n. g() rep una referència i la duplica: 5 × 2 = 10.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Nombre variable de paràmetres',
            'origen' => "$origen · pàg. 68",
            'html' => <<<'HTML'
                <p><code>...$nums</code> recull tots els arguments en un array (ha de ser l'últim paràmetre). En la crida, <code>...$array</code> fa l'invers:
                expandeix l'array en arguments. Moltes funcions de PHP ja funcionen així: <code>max(3, 9, 4)</code>, <code>printf()</code>, <code>array_merge()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: variadic i spread',
            'fitxer' => 'variadic.php',
            'codi' => <<<'PHP'
                <?php
                function sumaTot(int ...$nums): int {
                    return array_sum($nums);
                }

                echo sumaTot(1, 2) . PHP_EOL;          // 3
                echo sumaTot(1, 2, 3, 4) . PHP_EOL;    // 10

                $valors = [5, 10, 15];
                echo sumaTot(...$valors) . PHP_EOL;    // 30
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Callbacks, funcions anònimes i fletxa',
            'origen' => "$origen · pàg. 69-70",
            'html' => <<<'HTML'
                <p>Una funció es pot passar com a argument a una altra (tipus <code>callable</code>): pel nom com a string (<code>"sumar"</code>) o com a funció anònima.</p>
                <table class="taula">
                  <tr><th></th><th>function () use ()</th><th>fn () =></th></tr>
                  <tr><td>Variables de fora</td><td>Cal indicar-les amb use</td><td>Les captura automàticament</td></tr>
                  <tr><td>Cos</td><td>Diverses instruccions + return</td><td>Una sola expressió</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: callbacks',
            'fitxer' => 'callbacks.php',
            'codi' => <<<'PHP'
                <?php
                function calcular(callable $operacio, $a, $b) {
                    return $operacio($a, $b);
                }
                function sumar($a, $b) {
                    return $a + $b;
                }

                echo calcular("sumar", 10, 5) . PHP_EOL;                  // 15
                echo calcular(fn($a, $b) => $a - $b, 10, 5) . PHP_EOL;    // 5

                $salutacio = "Bon dia";
                $saludar = function ($nom) use ($salutacio) {
                    return "$salutacio, $nom!";
                };
                echo $saludar("Anna") . PHP_EOL;

                print_r(array_map(fn($x) => $x * 2, [1, 2, 3]));
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['funcions fletxa'],
            'pregunta' => 'Completa la funció fletxa perquè <code>$dobles</code> sigui [2, 4, 6]:',
            'codi' => "\$dobles = array_map(___(\$x) => \$x * 2, [1, 2, 3]);",
            'respostes' => ['fn'],
            'explicacio' => 'Les funcions fletxa s\'escriuen fn($x) => expressió. Són perfectes per a array_map, array_filter o usort.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Funcions predefinides útils',
            'origen' => "$origen · pàg. 71",
            'html' => <<<'HTML'
                <p><code>abs</code>, <code>round</code>, <code>floor</code>, <code>ceil</code>, <code>max</code>, <code>min</code>, <code>random_int(1, 6)</code>,
                <code>sqrt</code>, <code>pow</code>, <code>date("d/m/Y H:i")</code>, <code>time()</code>, <code>number_format(1234.5, 2, ",", ".")</code>,
                <code>isset</code>, <code>empty</code>, <code>is_numeric</code>... Abans d'escriure una funció, busca si ja existeix a php.net.</p>
                HTML,
        ],
    ],
];
