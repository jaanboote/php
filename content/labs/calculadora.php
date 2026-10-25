<?php
/**
 * Laboratori: Calculadora per terminal amb match.
 * Basat en l'exercici «Calculadora per terminal» (Tema 2, pàg. 79) i l'activitat 4 del Tema 3 (prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Calculadora per terminal
// Ús: php calc.php <num1> <operador> <num2>   (operadors: + - x /)

if ($argc !== 4) {
    echo "Ús: php calc.php <num1> <operador> <num2>" . PHP_EOL;
    exit(1);
}

[, $num1, $operador, $num2] = $argv;   // desestructuració: saltem $argv[0]

if (!is_numeric($num1) || !is_numeric($num2)) {
    echo "ERROR: calen dos números." . PHP_EOL;
    exit(2);
}

PHP;

$sol2 = $sol1 . <<<'PHP'

$resultat = match ($operador) {
    '+' => $num1 + $num2,
    '-' => $num1 - $num2,
    'x' => $num1 * $num2,
    '/' => $num1 / $num2,
};

echo "$num1 $operador $num2 = " . round($resultat, 2) . PHP_EOL;

PHP;

$sol3 = str_replace(
    "\$resultat = match (\$operador) {",
    <<<'PHP'
if (!in_array($operador, ['+', '-', 'x', '/'], true)) {
    echo "ERROR: l'operador \"$operador\" no és vàlid. Fes servir + - x /" . PHP_EOL;
    exit(3);
}
if ($operador === '/' && $num2 == 0) {
    echo "ERROR: no es pot dividir per 0." . PHP_EOL;
    exit(4);
}

$resultat = match ($operador) {
PHP,
    $sol2
);

$calc = fn(array $args, string $esperat) => ['nom' => implode(' ', $args), 'args' => $args, 'conte' => [$esperat], 'codi_sortida' => 0];

return [
    'id' => 'calculadora',
    'ordre' => 110,
    'titol' => 'Calculadora per terminal amb match',
    'fitxer' => 'calc.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.1 is_numeric · 3.4 match',
    'origen' => [
        'fitxer' => 'Tema 2 - Fonaments PHP.pdf',
        'descripcio' => 'Exercici «Calculadora per terminal» (Tema 2, pàg. 79) i activitat 4 del Tema 3 (David Porti)',
    ],
    'resum' => 'php calc.php 8 x 5 → 8 x 5 = 40. Validació amb is_numeric(), càlcul amb match, round() i un codi de sortida diferent per a cada error.',
    'xp_final' => 40,
    'paraules_clau' => ['match', 'is_numeric', 'round', 'exit', 'argv'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Calculadora per terminal
        // Ús: php calc.php <num1> <operador> <num2>   (operadors: + - x /)

        PHP,

    'context' => <<<'HTML'
        <p>Una calculadora que rep l'operació com a arguments. Passos: <strong>llegeix</strong> num1, operador i num2 de <code>$argv</code>,
        <strong>valida</strong>, <strong>calcula</strong> amb <code>match</code> i <strong>respon</strong> amb el resultat o un error.</p>
        <pre class="term">$ php calc.php 8 x 5
        8 x 5 = 40</pre>
        <p class="nota">A la terminal, <code>*</code> s'expandeix als noms dels fitxers de la carpeta. Per això multipliquem amb <code>x</code>.</p>
        <p><strong>Repte extra (el farem):</strong> retornar un codi de sortida diferent per a cada tipus d'error. 0 vol dir «tot bé»;
        a bash es consulta amb <code>echo $?</code>.</p>
        HTML,

    'resum_apres' => [
        'Validar el nombre d\'arguments i que siguin números abans de calcular.',
        'Fer servir match com a expressió que retorna el resultat de l\'operació.',
        'Detectar els casos que farien petar el programa (dividir per 0, operador desconegut) abans que passin.',
        'Retornar codis de sortida diferents amb exit(n) perquè un script o un cron sàpiga què ha fallat.',
    ],

    'passos' => [
        [
            'titol' => 'Llegir i validar',
            'conceptes' => ['$argv i $argc', 'is_numeric', 'exit'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Si no hi ha exactament 3 arguments, mostra <code>Ús: php calc.php &lt;num1&gt; &lt;operador&gt; &lt;num2&gt;</code> i acaba amb <code>exit(1)</code>.</li>
                  <li>Si num1 o num2 no són números, mostra <code>ERROR: calen dos números.</code> i acaba amb <code>exit(2)</code>.</li>
                </ul>
                <p class="nota">Les proves també comproven el codi de sortida. Truc (Tema 2): <code>[, $num1, $operador, $num2] = $argv;</code></p>
                HTML,
            'codi' => [
                ['regex' => '/\bis_numeric\s*\(/', 'missatge' => 'Valida els números amb is_numeric().'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php calc.php <num1> <operador> <num2>'], 'codi_sortida' => 1],
                ['nom' => 'Falta un argument', 'args' => ['8', 'x'], 'conte' => ['Ús:'], 'codi_sortida' => 1],
                ['nom' => 'Primer valor no numèric', 'args' => ['a', '+', '2'], 'conte' => ['ERROR: calen dos números.'], 'codi_sortida' => 2],
                ['nom' => 'Segon valor no numèric', 'args' => ['8', '+', 'dos'], 'conte' => ['ERROR: calen dos números.'], 'codi_sortida' => 2],
                ['nom' => 'Correcte: cap error', 'args' => ['8', 'x', '5'], 'no_conte' => ['ERROR', 'Ús:']],
            ],
            'pistes' => [
                'Dues comprovacions en ordre: primer quants arguments, després si són números. Cada error acaba el programa amb un número diferent.',
                '$argc !== 4 (el nom del script + 3). exit(1) i exit(2) acaben el programa amb aquell codi.',
                "if (\$argc !== 4) {\n    echo \"Ús: php calc.php <num1> <operador> <num2>\" . PHP_EOL;\n    exit(1);\n}\nif (!is_numeric(\$num1) || !is_numeric(\$num2)) { ... exit(2); }",
            ],
            'preguntes' => [
                'Amb «php calc.php 8 x 5», quant val $argc?',
                'Per què pot ser útil que cada error tingui un codi de sortida diferent?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Primer es comprova que hi hagi arguments i després que siguin números. Cada error té el seu codi de sortida.',
        ],
        [
            'titol' => 'Calcular amb match',
            'conceptes' => ['match', 'round'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> calcula el resultat amb una expressió <code>match</code> per als operadors <code>+ - x /</code>
                i mostra'l arrodonit a 2 decimals amb <code>round()</code>:</p>
                <pre class="term">$ php calc.php 10 / 3
                10 / 3 = 3.33</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bmatch\s*\(/', 'missatge' => 'Calcula el resultat amb match (requisit de l\'activitat).'],
                ['regex' => '/\bround\s*\(/', 'missatge' => 'Arrodoneix el resultat amb round().'],
            ],
            'proves' => [
                $calc(['8', 'x', '5'], '8 x 5 = 40'),
                $calc(['7', '/', '2'], '7 / 2 = 3.5'),
                $calc(['10', '/', '3'], '10 / 3 = 3.33'),
                $calc(['2.5', '+', '1.25'], '2.5 + 1.25 = 3.75'),
                $calc(['3', '-', '10'], '3 - 10 = -7'),
            ],
            'pistes' => [
                'match retorna un valor: pots fer $resultat = match ($operador) { ... }; amb una branca per a cada operador.',
                'Cada branca és \'+\' => $num1 + $num2, etc. PHP converteix els strings numèrics a números quan fas l\'operació.',
                "\$resultat = match (\$operador) {\n    '+' => \$num1 + \$num2,\n    '-' => \$num1 - \$num2,\n    'x' => \$num1 * \$num2,\n    '/' => \$num1 / \$num2,\n};",
            ],
            'preguntes' => [
                'Què passaria ara mateix amb «php calc.php 8 % 3»? Prova-ho.',
                'Per què round(3.3333, 2) mostra 3.33 però round(40, 2) mostra 40?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'match retorna directament el resultat de la branca que coincideix amb l\'operador.',
        ],
        [
            'titol' => 'Els casos que fan petar el programa',
            'conceptes' => ['validació de dades', 'exit', 'match'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Ara mateix, <code>8 / 0</code> dona un <em>DivisionByZeroError</em> i <code>8 % 3</code> un <em>UnhandledMatchError</em>.
                Un programa ben fet no ha de petar mai.</p>
                <p><strong>Objectiu:</strong> abans de calcular, comprova:</p>
                <ul>
                  <li>Operador desconegut → <code>ERROR: l'operador "%" no és vàlid.</code> (pots afegir quins són vàlids) i <code>exit(3)</code>.</li>
                  <li>Divisió per 0 → <code>ERROR: no es pot dividir per 0.</code> i <code>exit(4)</code>.</li>
                </ul>
                HTML,
            'codi' => [
                ['regex' => '/\bmatch\s*\(/', 'missatge' => 'Continua calculant amb match.'],
            ],
            'proves' => [
                ['nom' => '8 / 0', 'args' => ['8', '/', '0'], 'conte' => ['ERROR: no es pot dividir per 0.'], 'codi_sortida' => 4],
                ['nom' => '8 % 3', 'args' => ['8', '%', '3'], 'conte' => ['ERROR: l\'operador "%" no és vàlid.'], 'codi_sortida' => 3],
                ['nom' => '8 suma 3', 'args' => ['8', 'suma', '3'], 'conte' => ['ERROR: l\'operador "suma" no és vàlid.'], 'codi_sortida' => 3],
                ['nom' => '0 / 5 sí que es pot', 'args' => ['0', '/', '5'], 'conte' => ['0 / 5 = 0'], 'codi_sortida' => 0],
                ['nom' => 'Una operació normal continua funcionant', 'args' => ['8', 'x', '5'], 'conte' => ['8 x 5 = 40'], 'codi_sortida' => 0],
            ],
            'pistes' => [
                'Les dues comprovacions s\'han de fer abans del match. Quina dada dels arguments fa que cada cas sigui un problema?',
                'in_array($operador, [\'+\', \'-\', \'x\', \'/\'], true) diu si l\'operador és vàlid. Per a la divisió: $operador === \'/\' && $num2 == 0.',
                "if (\$operador === '/' && \$num2 == 0) {\n    echo \"ERROR: no es pot dividir per 0.\" . PHP_EOL;\n    exit(4);\n}",
            ],
            'preguntes' => [
                'Quin error dona PHP si un match no troba cap branca i no té default?',
                'És un problema fer 0 / 5? I 5 / 0?',
                'Podries resoldre l\'operador desconegut amb una branca default dins del match? Quina diferència hi hauria?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Comprovant els casos problemàtics abans de calcular, el programa sempre acaba amb un missatge clar i un codi de sortida que explica què ha passat.',
        ],
    ],
];
