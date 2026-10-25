<?php
/**
 * Lliçó: Estructures de control (Tema 2, apartat 2.4).
 */

$origen = 'Tema 2 - Fonaments PHP.pdf';

return [
    'id' => 'estructures-control',
    'ordre' => 30,
    'titol' => 'Condicionals i bucles',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.4 Estructures de control',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'horoscop',
    'resum' => 'if, switch i match per decidir; for, while, do...while i foreach per repetir; break, continue, include i require.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'if / elseif / else',
            'origen' => "$origen · pàg. 40-41",
            'html' => <<<'HTML'
                <p>Només s'executa el primer bloc la condició del qual sigui certa. Posa sempre les claus <code>{ }</code>, encara que hi hagi una sola instrucció,
                i compara textos amb <code>===</code>.</p>
                <p><strong>Truc:</strong> si les condicions van de menor a major, a cada <code>elseif</code> només cal comprovar el límit superior,
                perquè l'inferior ja està garantit per la condició anterior.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: trams amb if/elseif',
            'fitxer' => 'trams.php',
            'args' => '7.5',
            'html' => '<p>Canvia la nota a la caixa d\'arguments (prova 4, 5, 9.2...).</p>',
            'codi' => <<<'PHP'
                <?php
                $nota = (float) ($argv[1] ?? 0);

                if ($nota < 5) {
                    echo "Insuficient";
                } elseif ($nota < 7) {      // aquí ja sabem que $nota >= 5
                    echo "Aprovat";
                } elseif ($nota < 9) {
                    echo "Notable";
                } else {
                    echo "Excel·lent";
                }
                echo PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'switch: no oblidis el break',
            'origen' => "$origen · pàg. 42",
            'html' => <<<'HTML'
                <p><code>switch</code> compara una variable amb diversos valors (<code>case</code>). <strong>Sense <code>break</code></strong>, també s'executen
                els <code>case</code> següents (<em>fall-through</em>). Es poden agrupar diversos <code>case</code> per a la mateixa acció, i
                <code>default</code> s'executa si no coincideix cap. Compte: <code>switch</code> compara amb <code>==</code> (no estricte).</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['switch'],
            'pregunta' => 'Què imprimeix? (fixa\'t en els break)',
            'codi' => "<?php\n\$x = 1;\nswitch (\$x) {\n    case 1:\n        echo \"A\";\n    case 2:\n        echo \"B\";\n        break;\n    case 3:\n        echo \"C\";\n}",
            'resposta' => 'AB',
            'explicacio' => 'El case 1 no té break, així que continua executant el case 2 fins al primer break.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'match (PHP 8)',
            'origen' => "$origen · pàg. 43",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th></th><th>switch</th><th>match</th></tr>
                  <tr><td>Comparació</td><td>== (no estricta)</td><td>=== (estricta)</td></tr>
                  <tr><td>break</td><td>Obligatori a cada case</td><td>No cal</td></tr>
                  <tr><td>Retorna un valor</td><td>No</td><td>Sí</td></tr>
                  <tr><td>Sense coincidència</td><td>No fa res</td><td>UnhandledMatchError</td></tr>
                </table>
                <p>Amb <code>match (true)</code> pots fer trams: la primera condició certa guanya.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: match',
            'fitxer' => 'match.php',
            'args' => 'dissabte',
            'codi' => <<<'PHP'
                <?php
                $dia = $argv[1] ?? "dilluns";

                $tipus = match ($dia) {
                    "dilluns", "dimarts", "dimecres", "dijous", "divendres" => "Laborable",
                    "dissabte", "diumenge" => "Cap de setmana",
                    default => "Dia desconegut",
                };
                echo $tipus . PHP_EOL;

                $nota = 7.5;
                echo match (true) {
                    $nota >= 9 => "Excel·lent",
                    $nota >= 7 => "Notable",
                    $nota >= 5 => "Aprovat",
                    default    => "Suspès",
                } . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['match'],
            'pregunta' => 'Què passa si cap branca d\'un <code>match</code> coincideix i no hi ha <code>default</code>?',
            'opcions' => ['Retorna null', 'No fa res, com switch', 'Llança un UnhandledMatchError', 'Executa la primera branca'],
            'correcta' => 2,
            'explicacio' => 'match és estricte: si cap valor coincideix i no hi ha default, el programa s\'atura amb UnhandledMatchError.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Bucles: quin triar?',
            'origen' => "$origen · pàg. 44-47",
            'html' => <<<'HTML'
                <ul>
                  <li><code>for</code>: saps quantes vegades cal repetir.</li>
                  <li><code>while</code>: repeteixes mentre es compleixi una condició (pot no executar-se mai).</li>
                  <li><code>do...while</code>: com while, però s'executa <strong>com a mínim una vegada</strong> (menús, demanar dades fins que siguin vàlides).</li>
                  <li><code>foreach</code>: recorres tots els elements d'un array.</li>
                </ul>
                <p>Error típic: una condició que no es fa mai falsa = <strong>bucle infinit</strong>. Aquí el sandbox l'atura als 5 segons.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: els quatre bucles',
            'fitxer' => 'bucles.php',
            'codi' => <<<'PHP'
                <?php
                for ($i = 10; $i > 0; $i -= 2) {
                    echo "$i ";
                }
                echo PHP_EOL;

                $i = 0;
                while ($i < 5) {
                    echo $i++ . " ";
                }
                echo PHP_EOL;

                $i = 10;
                do {
                    echo $i++ . " ";     // s'executa 1 vegada encara que la condició és falsa
                } while ($i < 5);
                echo PHP_EOL;

                $hores = ["Dissabte" => 11, "Diumenge" => 22];
                foreach ($hores as $dia => $quantitat) {
                    echo "$dia: $quantitat h" . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q3',
            'conceptes' => ['bucles'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$i = 10;\ndo {\n    echo \$i;\n    \$i++;\n} while (\$i < 5);",
            'resposta' => '10',
            'explicacio' => 'do...while executa el bloc abans de comprovar la condició. Mostra 10, i com que 11 < 5 és fals, s\'acaba.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'break i continue',
            'origen' => "$origen · pàg. 48-49",
            'html' => <<<'HTML'
                <p><code>break</code> surt del bucle (o del switch); <code>break 2</code> surt de dos bucles niats alhora.
                <code>continue</code> salta a la iteració següent sense executar la resta del bloc.</p>
                HTML,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['break i continue'],
            'pregunta' => 'Volem mostrar només els números senars (1 3 5 7 9). Què hi va a ___ ?',
            'codi' => "for (\$i = 0; \$i < 10; \$i++) {\n    if (\$i % 2 === 0) {\n        ___;\n    }\n    echo \"\$i \";\n}",
            'respostes' => ['continue'],
            'explicacio' => 'continue salta l\'echo quan el número és parell i passa al següent. Amb break, el bucle s\'acabaria a la primera volta.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'include i require',
            'origen' => "$origen · pàg. 50-53",
            'html' => <<<'HTML'
                <p>Serveixen per reutilitzar codi (capçaleres, funcions, configuració). El fitxer inclòs s'executa com si estigués escrit en aquell punt
                i <strong>comparteix les variables</strong>.</p>
                <table class="taula">
                  <tr><th>Instrucció</th><th>Si el fitxer no existeix</th><th>Quan</th></tr>
                  <tr><td>include</td><td>Warning: l'script continua</td><td>Parts opcionals</td></tr>
                  <tr><td>require</td><td>Error fatal: s'atura</td><td>Imprescindibles (config, BD)</td></tr>
                </table>
                <p><code>require_once</code> no torna a incloure un fitxer ja inclòs (evita «Cannot redeclare»). Fes servir rutes amb
                <code>__DIR__</code>: <code>require_once __DIR__ . "/lib/funcions.php";</code>. Un fitxer inclòs pot fer <code>return [ ... ];</code>:
                és el patró dels fitxers de configuració (com el <code>config.php</code> d'aquesta app!).</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q5',
            'conceptes' => ['include i require'],
            'pregunta' => 'Fem <code>require "config.php";</code> però el fitxer no existeix. Què passa?',
            'opcions' => ['Un Warning i l\'script continua', 'Un error fatal i l\'script s\'atura', 'No passa res', 'PHP crea el fitxer buit'],
            'correcta' => 1,
            'explicacio' => 'require és per a fitxers imprescindibles: si no hi és, Fatal error. include només donaria un Warning.',
        ],
    ],
];
