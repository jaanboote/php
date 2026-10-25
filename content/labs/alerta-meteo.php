<?php
/**
 * Laboratori: Alerta meteorològica.
 * Basat en l'Exercici 4 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 * Format dels passos: mira content/labs/permisos.php.
 */

$sol1 = <<<'PHP'
<?php
// Alerta meteorològica

$entrada = readline("Introdueix la temperatura (°C): ");

// is_numeric() accepta "20", "-3" o "36.5", però no "calor"
if (!is_numeric($entrada)) {
    echo "Error: '$entrada' no és un número vàlid." . PHP_EOL;
    exit(1);
}
$celsius = (float) $entrada;

PHP;

$sol2 = $sol1 . <<<'PHP'

$fahrenheit = $celsius * 9 / 5 + 32;
echo PHP_EOL . "Temperatura: $celsius °C (" . round($fahrenheit, 1) . " °F)" . PHP_EOL;

PHP;

$sol3 = $sol2 . <<<'PHP'

// De menor a major: a cada elseif només cal mirar el límit superior
if ($celsius < 0) {
    $nivell = "GELADA - Compte amb el gel a la carretera!";
} elseif ($celsius <= 10) {
    $nivell = "FRED - Agafa l'abric.";
} elseif ($celsius <= 24) {
    $nivell = "AGRADABLE - Bon dia per sortir.";
} elseif ($celsius <= 34) {
    $nivell = "CALOR - Beu aigua sovint.";
} else {
    $nivell = "ALERTA VERMELLA - Evita el sol a les hores centrals!";
}
echo "Nivell: $nivell" . PHP_EOL;

PHP;

$niv = fn(string $t, string $n) => ['nom' => "$t °C → $n", 'stdin' => "$t\n", 'conte' => ["Nivell: $n"]];

return [
    'id' => 'alerta-meteo',
    'ordre' => 10,
    'titol' => 'Alerta meteorològica',
    'fitxer' => 'Exercici04_alerta_meteorologica.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.2 Tipus · 2.4 Condicionals',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 4 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Llegeix una temperatura amb readline(), valida-la, passa-la a Fahrenheit i decideix el nivell d\'alerta amb if/elseif/else.',
    'xp_final' => 40,
    'paraules_clau' => ['readline', 'is_numeric', 'elseif', 'temperatura', 'round'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Alerta meteorològica

        PHP,

    'context' => <<<'HTML'
        <p>El servei de meteorologia de la comarca vol un programa que, a partir d'una temperatura, indiqui el nivell d'alerta
        i una recomanació per a la població.</p>
        <p><strong>Com es prova:</strong> el programa llegeix amb <code>readline()</code>. Escriu la resposta a la caixa
        «Entrada per a readline()» (sota l'editor) abans de prémer Executa.</p>
        <pre class="term">Introdueix la temperatura (°C): 38

        Temperatura: 38 °C (100.4 °F)
        Nivell: ALERTA VERMELLA - Evita el sol a les hores centrals!</pre>
        HTML,

    'resum_apres' => [
        'readline() sempre retorna un string: cal validar-lo (is_numeric) i convertir-lo ((float)).',
        'Aturar el programa amb exit quan les dades no són vàlides.',
        'Fer càlculs amb operadors aritmètics i arrodonir amb round().',
        'Escriure trams amb una sola estructura if / elseif / else, de menor a major.',
    ],

    'passos' => [
        [
            'titol' => 'Llegir i validar la temperatura',
            'conceptes' => ['readline', 'is_numeric', 'exit'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>Demana la temperatura amb <code>readline("Introdueix la temperatura (°C): ")</code>.
                Recorda que <code>readline()</code> <strong>sempre retorna un text</strong>.</p>
                <p><strong>Objectiu:</strong> comprova amb <code>is_numeric()</code> que és un número. Si no ho és, mostra exactament
                aquest error i atura el programa amb <code>exit</code>:</p>
                <pre class="term">Introdueix la temperatura (°C): calor
                Error: 'calor' no és un número vàlid.</pre>
                <p>Si és vàlid, converteix-lo a número (<code>(float)</code>) i desa'l en una variable.</p>
                <p class="nota">Escriu <code>calor</code> o <code>20</code> a la caixa «Entrada per a readline()» per provar-ho.</p>
                HTML,
            'codi' => [
                ['regex' => '/\breadline\s*\(/', 'missatge' => 'Llegeix la temperatura amb readline().'],
                ['regex' => '/\bis_numeric\s*\(/', 'missatge' => 'Valida el valor amb is_numeric().'],
            ],
            'proves' => [
                ['nom' => 'Text que no és un número', 'stdin' => "calor\n", 'conte' => ["Error: 'calor' no és un número vàlid."]],
                ['nom' => 'Número vàlid: cap error', 'stdin' => "20\n", 'no_conte' => ['Error']],
                ['nom' => 'Número negatiu amb decimals: cap error', 'stdin' => "-3.5\n", 'no_conte' => ['Error']],
            ],
            'pistes' => [
                'Primer llegeix, després comprova. Si la comprovació falla, el programa no ha de continuar: hi ha una instrucció que l\'atura.',
                'is_numeric($text) retorna true si el text és un número. Fes servir if (!is_numeric(...)) per detectar l\'error i exit; per aturar.',
                "\$entrada = readline(\"Introdueix la temperatura (°C): \");\nif (!is_numeric(\$entrada)) {\n    echo \"Error: '\$entrada' no és un número vàlid.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'De quin tipus és el que retorna readline(), encara que l\'usuari escrigui 20?',
                'Què passaria amb els càlculs si l\'usuari escriu «calor» i no ho comproves?',
                'Com pots posar unes cometes simples dins d\'un text entre cometes dobles?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Llegim, validem i, si tot és correcte, convertim a float. Si no, exit atura el programa abans de fer cap càlcul.',
        ],
        [
            'titol' => 'Passar a Fahrenheit',
            'conceptes' => ['operadors aritmètics', 'round'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>La fórmula és <code>F = C × 9 / 5 + 32</code>.</p>
                <p><strong>Objectiu:</strong> calcula els graus Fahrenheit i mostra (després d'una línia en blanc) la temperatura en les dues unitats,
                amb els Fahrenheit <strong>arrodonits a 1 decimal</strong>:</p>
                <pre class="term">Temperatura: 38 °C (100.4 °F)</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bround\s*\(/', 'missatge' => 'Arrodoneix els Fahrenheit amb round(..., 1).'],
            ],
            'proves' => [
                ['nom' => '38 °C', 'stdin' => "38\n", 'conte' => ['Temperatura: 38 °C (100.4 °F)']],
                ['nom' => '0 °C', 'stdin' => "0\n", 'conte' => ['Temperatura: 0 °C (32 °F)']],
                ['nom' => '-5.5 °C', 'stdin' => "-5.5\n", 'conte' => ['Temperatura: -5.5 °C (22.1 °F)']],
            ],
            'pistes' => [
                'Calcula primer el resultat en una variable nova i després munta la frase. L\'arrodoniment només cal per mostrar-lo.',
                'La multiplicació i la divisió es fan abans que la suma, així que $celsius * 9 / 5 + 32 ja funciona sense parèntesis. round($valor, 1) deixa 1 decimal.',
                "\$fahrenheit = \$celsius * 9 / 5 + 32;\necho PHP_EOL . \"Temperatura: \$celsius °C (\" . round(\$fahrenheit, 1) . \" °F)\" . PHP_EOL;",
            ],
            'preguntes' => [
                'En quin ordre fa PHP les operacions de $celsius * 9 / 5 + 32?',
                'Per què 0 °C es mostra com a «0» i no com a «0.0»?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'round() només s\'aplica en mostrar el valor. PHP mostra els floats sense decimals sobrants: 32.0 surt com 32.',
        ],
        [
            'titol' => 'El nivell d\'alerta',
            'conceptes' => ['if / elseif / else', 'operadors de comparació'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <table class="taula">
                  <tr><th>Temperatura</th><th>Nivell</th><th>Recomanació</th></tr>
                  <tr><td>Menys de 0 °C</td><td>GELADA</td><td>Compte amb el gel a la carretera!</td></tr>
                  <tr><td>De 0 a 10 °C</td><td>FRED</td><td>Agafa l'abric.</td></tr>
                  <tr><td>De 11 a 24 °C</td><td>AGRADABLE</td><td>Bon dia per sortir.</td></tr>
                  <tr><td>De 25 a 34 °C</td><td>CALOR</td><td>Beu aigua sovint.</td></tr>
                  <tr><td>35 °C o més</td><td>ALERTA VERMELLA</td><td>Evita el sol a les hores centrals!</td></tr>
                </table>
                <p><strong>Objectiu:</strong> amb <strong>una única</strong> estructura <code>if / elseif / else</code>, mostra:</p>
                <pre class="term">Nivell: ALERTA VERMELLA - Evita el sol a les hores centrals!</pre>
                <p class="nota">Pensa bé l'ordre de les condicions perquè cada temperatura caigui en un sol tram. Les proves miren els límits (0, 10, 11, 24, 25, 34, 35).</p>
                HTML,
            'codi' => [
                ['regex' => '/\belseif\b|\belse\s+if\b/', 'missatge' => 'Fes servir una cadena if / elseif / else.'],
            ],
            'proves' => [
                $niv('-1', 'GELADA - Compte amb el gel a la carretera!'),
                $niv('0', "FRED - Agafa l'abric."),
                $niv('10', "FRED - Agafa l'abric."),
                $niv('11', 'AGRADABLE - Bon dia per sortir.'),
                $niv('24', 'AGRADABLE - Bon dia per sortir.'),
                $niv('25', 'CALOR - Beu aigua sovint.'),
                $niv('34', 'CALOR - Beu aigua sovint.'),
                $niv('35', 'ALERTA VERMELLA - Evita el sol a les hores centrals!'),
            ],
            'pistes' => [
                'Ordena els trams de més fred a més calor. Quan una condició és certa, PHP ja no mira les següents.',
                'Si la primera condició és $celsius < 0, al primer elseif ja saps que és >= 0: només cal comprovar <= 10. I així successivament.',
                "if (\$celsius < 0) {\n    \$nivell = \"GELADA - Compte amb el gel a la carretera!\";\n} elseif (\$celsius <= 10) {\n    \$nivell = \"FRED - Agafa l'abric.\";\n} elseif (...) {\n    ...\n} else {\n    \$nivell = \"ALERTA VERMELLA - ...\";\n}",
            ],
            'preguntes' => [
                'Si la temperatura és 10, quina condició és la primera que es compleix al teu codi?',
                'Què passaria si posessis la condició de CALOR abans que la de FRED?',
                'Per què és millor un sol if/elseif que cinc if independents?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Amb els trams ordenats de menor a major, cada elseif només comprova el límit superior.',
        ],
    ],
];
