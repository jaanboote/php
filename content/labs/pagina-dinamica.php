<?php
/**
 * Laboratori: Pàgina dinàmica (Bon dia / Bona tarda / Bona nit).
 * Basat en l'exemple d'integració PHP + HTML del Tema 1 (diapositiva 13, prof. David Porti).
 * Adaptació: l'hora es pot passar com a argument perquè el resultat es pugui comprovar.
 */

$sol1 = <<<'PHP'
<?php
// Pàgina dinàmica: salutació segons l'hora
// Ús: php salutacio.php [hora 0-23]

$hora = $argv[1] ?? date('H');   // sense argument, l'hora actual

if (!is_numeric($hora) || $hora < 0 || $hora > 23) {
    echo "ERROR: l'hora ha de ser un número entre 0 i 23." . PHP_EOL;
    exit(1);
}
$hora = (int) $hora;
echo "Hora: $hora" . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

if ($hora < 12) {
    $salutacio = "Bon dia!";
} elseif ($hora < 20) {
    $salutacio = "Bona tarda!";
} else {
    $salutacio = "Bona nit!";
}
echo $salutacio . PHP_EOL;

PHP;

$sol3 = <<<'PHP'
<?php
// Pàgina dinàmica: salutació segons l'hora
// Ús: php salutacio.php [hora 0-23]

$hora = $argv[1] ?? date('H');   // sense argument, l'hora actual

if (!is_numeric($hora) || $hora < 0 || $hora > 23) {
    echo "ERROR: l'hora ha de ser un número entre 0 i 23." . PHP_EOL;
    exit(1);
}
$hora = (int) $hora;

if ($hora < 12) {
    $salutacio = "Bon dia!";
} elseif ($hora < 20) {
    $salutacio = "Bona tarda!";
} else {
    $salutacio = "Bona nit!";
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Salutació</title>
</head>
<body>
    <h1><?= $salutacio ?></h1>
    <p>Són les <?= $hora ?> h.</p>
</body>
</html>
PHP;

$hola = fn(string $h, string $text) => ['nom' => "A les $h h", 'args' => [$h], 'conte' => [$text]];

return [
    'id' => 'pagina-dinamica',
    'ordre' => 3,
    'titol' => 'Pàgina dinàmica: Bon dia, bona tarda, bona nit',
    'fitxer' => 'salutacio.php',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.1 Llocs estàtics i dinàmics',
    'origen' => [
        'fitxer' => 'Tema 1 - Introducció al desenvolupament web.pdf',
        'descripcio' => 'Exemple «Integració de llenguatges de marcatge» del Tema 1, diapositiva 13 (David Porti)',
    ],
    'resum' => 'Una pàgina que saluda segons l\'hora: el primer contacte amb com PHP genera HTML. Practicaràs variables, if/elseif i barrejar PHP amb HTML.',
    'xp_final' => 30,
    'paraules_clau' => ['dinàmic', 'html', 'if', 'date', 'echo'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Pàgina dinàmica: salutació segons l'hora
        // Ús: php salutacio.php [hora 0-23]

        PHP,

    'context' => <<<'HTML'
        <p>Una pàgina <strong>estàtica</strong> mostra sempre el mateix. Una de <strong>dinàmica</strong> canvia segons les dades: aquí, segons l'hora.</p>
        <p><strong>Adaptació per poder-ho provar:</strong> l'hora es pot passar com a argument (<code>php salutacio.php 9</code>). Si no se'n passa cap,
        s'agafa l'hora real amb <code>date('H')</code>.</p>
        <pre class="term">$ php salutacio.php 9
        &lt;!DOCTYPE html&gt;
        &lt;html lang="ca"&gt;
        ...
            &lt;h1&gt;Bon dia!&lt;/h1&gt;
            &lt;p&gt;Són les 9 h.&lt;/p&gt;
        ...</pre>
        HTML,

    'resum_apres' => [
        'Agafar una dada (argument o date(\'H\')) i validar-la abans de fer-la servir.',
        'Triar entre tres casos amb if / elseif / else.',
        'Barrejar PHP i HTML: la part fixa és HTML i només els trossos que canvien són PHP (<?= ?>).',
        'El navegador només rep l\'HTML resultant: el PHP no surt mai del servidor.',
    ],

    'passos' => [
        [
            'titol' => 'L\'hora',
            'conceptes' => ['variables', 'validació de dades'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> agafa l'hora de l'argument o, si no n'hi ha, de <code>date('H')</code>
                (truc: <code>$argv[1] ?? date('H')</code>). Si no és un número entre 0 i 23, mostra
                <code>ERROR: l'hora ha de ser un número entre 0 i 23.</code> i <code>exit(1)</code>. Si és correcta, mostra <code>Hora: 9</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bdate\s*\(\s*[\'"]H[\'"]\s*\)/', 'missatge' => 'Sense argument, agafa l\'hora actual amb date(\'H\').'],
            ],
            'proves' => [
                ['nom' => 'Hora 9', 'args' => ['9'], 'conte' => ['Hora: 9'], 'no_conte' => ['ERROR']],
                ['nom' => 'Hora 25', 'args' => ['25'], 'conte' => ["ERROR: l'hora ha de ser un número entre 0 i 23."], 'codi_sortida' => 1],
                ['nom' => 'Hora -1', 'args' => ['-1'], 'conte' => ['ERROR'], 'codi_sortida' => 1],
                ['nom' => 'Un text', 'args' => ['hola'], 'conte' => ['ERROR'], 'codi_sortida' => 1],
                ['nom' => 'Sense argument: l\'hora actual', 'args' => [], 'conte_regex' => ['/Hora: \d{1,2}/' => '«Hora: » i l\'hora actual']],
            ],
            'pistes' => [
                'Primer decideix d\'on surt l\'hora (argument o rellotge) i després comprova que sigui vàlida.',
                'L\'operador ?? dona un valor per defecte si $argv[1] no existeix. Per validar: is_numeric() i que estigui entre 0 i 23.',
                "\$hora = \$argv[1] ?? date('H');\nif (!is_numeric(\$hora) || \$hora < 0 || \$hora > 23) {\n    echo \"ERROR: l'hora ha de ser un número entre 0 i 23.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Què retorna date(\'H\') a les 9 del matí? I a les 21 h?',
                'Què fa $argv[1] ?? date(\'H\') si no hi ha cap argument?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'L\'operador ?? evita l\'avís si no hi ha argument. La validació evita hores impossibles.',
        ],
        [
            'titol' => 'La salutació',
            'conceptes' => ['if / elseif / else'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> com a l'exemple del temari, tria la salutació i mostra-la:</p>
                <table class="taula">
                  <tr><td>Abans de les 12</td><td><code>Bon dia!</code></td></tr>
                  <tr><td>De les 12 a abans de les 20</td><td><code>Bona tarda!</code></td></tr>
                  <tr><td>Des de les 20</td><td><code>Bona nit!</code></td></tr>
                </table>
                HTML,
            'codi' => [
                ['regex' => '/\belseif\b|\belse\s+if\b/', 'missatge' => 'Fes servir if / elseif / else.'],
            ],
            'proves' => [
                $hola('0', 'Bon dia!'),
                $hola('11', 'Bon dia!'),
                $hola('12', 'Bona tarda!'),
                $hola('19', 'Bona tarda!'),
                $hola('20', 'Bona nit!'),
                $hola('23', 'Bona nit!'),
            ],
            'pistes' => [
                'Tres casos, ordenats per hora: el primer que es compleixi guanya.',
                'if ($hora < 12) ... elseif ($hora < 20) ... else ... Guarda la salutació en una variable: et servirà al pas següent.',
                "if (\$hora < 12) {\n    \$salutacio = \"Bon dia!\";\n} elseif (\$hora < 20) {\n    \$salutacio = \"Bona tarda!\";\n} else {\n    \$salutacio = \"Bona nit!\";\n}",
            ],
            'preguntes' => [
                'A les 12 en punt, quina condició es compleix primer?',
                'Per què és útil guardar la salutació en una variable en lloc de fer-ne echo directament?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Guardar el resultat en una variable separa el càlcul de la presentació (la idea de l\'MVC!).',
        ],
        [
            'titol' => 'La pàgina HTML',
            'conceptes' => ['PHP i HTML', 'estàtic i dinàmic'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> en lloc de les línies de text, el programa ha de generar una pàgina HTML completa.
                Tanca el bloc PHP (<code>?&gt;</code>) després dels càlculs i escriu l'HTML directament. Només les parts que canvien van amb <code>&lt;?= ?&gt;</code>:</p>
                <pre class="code">&lt;!DOCTYPE html&gt;
                &lt;html lang="ca"&gt;
                &lt;head&gt;
                    &lt;meta charset="UTF-8"&gt;
                    &lt;title&gt;Salutació&lt;/title&gt;
                &lt;/head&gt;
                &lt;body&gt;
                    &lt;h1&gt;Bon dia!&lt;/h1&gt;
                    &lt;p&gt;Són les 9 h.&lt;/p&gt;
                &lt;/body&gt;
                &lt;/html&gt;</pre>
                <p class="nota">Si l'hora no és vàlida, el missatge d'error es continua mostrant tal com estava (sense HTML).</p>
                HTML,
            'codi' => [
                ['regex' => '/^<!DOCTYPE html>/mi', 'missatge' => 'Escriu l\'HTML fora del bloc PHP, començant per <!DOCTYPE html>.'],
                ['regex' => '/<\?=/', 'missatge' => 'Mostra les parts dinàmiques amb <?= ... ?>.'],
            ],
            'proves' => [
                ['nom' => 'A les 9 h', 'args' => ['9'], 'conte' => ['<!DOCTYPE html>', '<h1>Bon dia!</h1>', '<p>Són les 9 h.</p>', '</html>'], 'no_conte' => ['Hora:']],
                ['nom' => 'A les 21 h', 'args' => ['21'], 'conte' => ['<h1>Bona nit!</h1>', '<p>Són les 21 h.</p>']],
                ['nom' => 'Hora no vàlida: sense HTML', 'args' => ['30'], 'conte' => ['ERROR'], 'no_conte' => ['<!DOCTYPE'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Un fitxer .php pot tenir HTML normal fora de les etiquetes <?php ?>: PHP l\'envia tal qual i només executa el que hi ha dins.',
                'Després dels càlculs, tanca amb ?> i escriu la pàgina. Dins de l\'HTML, <?= $salutacio ?> equival a <?php echo $salutacio; ?>.',
                "?>\n<!DOCTYPE html>\n<html lang=\"ca\">\n...\n<body>\n    <h1><?= \$salutacio ?></h1>\n    <p>Són les <?= \$hora ?> h.</p>\n</body>\n</html>",
            ],
            'preguntes' => [
                'Què fa PHP amb el text que hi ha fora de les etiquetes <?php ?>?',
                'Si obrissis aquesta pàgina amb el navegador i fessis «Veure el codi font», veuries $salutacio?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'L\'HTML fix va fora del PHP i només les dades que canvien s\'insereixen amb <?= ?>. És el que fan les vistes d\'una aplicació MVC.',
        ],
    ],
];
