<?php
/**
 * Laboratori: Calculadora segura (CLI).
 * Basat en l'activitat 2 del Tema 4 (prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Calculadora segura

function calcular(float $a, float $b, string $operacio): float
{
    if ($operacio === '/' && $b == 0) {
        throw new InvalidArgumentException("No es pot dividir per zero.");
    }
    return match ($operacio) {
        '+' => $a + $b,
        '-' => $a - $b,
        '*' => $a * $b,
        '/' => $a / $b,
        default => throw new InvalidArgumentException("Operació no vàlida: $operacio"),
    };
}

PHP;

$sol2 = $sol1 . <<<'PHP'

function demanarNumero(string $missatge): float
{
    $text = trim((string) readline($missatge));
    if (!is_numeric($text)) {
        throw new InvalidArgumentException("'$text' no és un número.");
    }
    return (float) $text;
}

PHP;

$sol3 = $sol2 . <<<'PHP'

// ---- Programa principal ----
try {
    $a = demanarNumero("Primer número: ");
    $operacio = trim((string) readline("Operació (+ - * /): "));
    $b = demanarNumero("Segon número: ");

    $resultat = calcular($a, $b, $operacio);
    echo "Resultat: $a $operacio $b = " . round($resultat, 2) . PHP_EOL;
} catch (InvalidArgumentException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
} finally {
    echo "Gràcies per fer servir la calculadora." . PHP_EOL;
}

PHP;

$captura = fn(string $crida) => "try { $crida; echo \"NO HA LLANÇAT CAP EXCEPCIÓ\"; } catch (InvalidArgumentException \$e) { echo \"Capturada: \", \$e->getMessage(); }";

return [
    'id' => 'calculadora-segura',
    'ordre' => 210,
    'titol' => 'Calculadora segura',
    'fitxer' => 'calculadora_segura.php',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.1 Errors i excepcions',
    'origen' => [
        'fitxer' => 'Tema 4 - Gestor d\'exepcions fitxers.pdf',
        'descripcio' => 'Activitat 2 del Tema 4, «Calculadora segura (CLI)» (David Porti)',
    ],
    'resum' => 'Una calculadora per consola que llança InvalidArgumentException quan les dades no són vàlides o es divideix per zero, i la captura amb try / catch / finally.',
    'xp_final' => 40,
    'paraules_clau' => ['throw', 'try', 'catch', 'finally', 'InvalidArgumentException'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Calculadora segura

        PHP,

    'context' => <<<'HTML'
        <p>Demana dos números i una operació per consola. Les funcions <strong>llancen</strong> una <code>InvalidArgumentException</code>
        si les dades no són vàlides o es divideix per zero, i el programa principal la <strong>captura</strong> amb <code>try / catch</code>.</p>
        <pre class="term">Primer número: 8
        Operació (+ - * /): /
        Segon número: 0
        Error: No es pot dividir per zero.
        Gràcies per fer servir la calculadora.</pre>
        <p class="nota">Als passos 1 i 2, les proves criden les teves funcions directament. El programa principal es fa al pas 3.</p>
        HTML,

    'resum_apres' => [
        'Llançar una excepció amb throw quan una funció no pot fer la seva feina.',
        'throw atura la funció a l\'instant: el return ja no s\'executa.',
        'Capturar l\'excepció al programa principal i mostrar un missatge clar amb getMessage().',
        'finally s\'executa sempre, hi hagi error o no.',
    ],

    'passos' => [
        [
            'titol' => 'calcular() llança excepcions',
            'conceptes' => ['throw', 'match'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> crea <code>function calcular(float $a, float $b, string $operacio): float</code> per a <code>+ - * /</code>.</p>
                <ul>
                  <li>Si es divideix per 0: <code>throw new InvalidArgumentException("No es pot dividir per zero.");</code></li>
                  <li>Si l'operació no existeix: <code>InvalidArgumentException</code> amb el missatge <code>Operació no vàlida: %</code> (amb l'operació rebuda).</li>
                </ul>
                <p class="nota">Les proves criden <code>calcular()</code> dins d'un try / catch i comproven el missatge de l'excepció.</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+calcular\s*\(\s*float\s+\$\w+\s*,\s*float\s+\$\w+\s*,\s*string\s+\$\w+\s*\)\s*:\s*float/i', 'missatge' => 'La signatura ha de ser: function calcular(float $a, float $b, string $operacio): float'],
                ['regex' => '/throw\s+new\s+InvalidArgumentException/', 'missatge' => 'Llança les excepcions amb throw new InvalidArgumentException(...).'],
            ],
            'proves' => [
                ['nom' => 'Operacions correctes', 'append' => 'echo calcular(8, 5, "*"), ",", calcular(7, 2, "/"), ",", calcular(3, 10, "-"), ",", calcular(2.5, 1.25, "+");', 'exacte' => '40,3.5,-7,3.75'],
                ['nom' => 'Divisió per zero', 'append' => $captura('calcular(8, 0, "/")'), 'exacte' => 'Capturada: No es pot dividir per zero.'],
                ['nom' => 'Operació desconeguda', 'append' => $captura('calcular(8, 3, "%")'), 'exacte' => 'Capturada: Operació no vàlida: %'],
                ['nom' => '0 / 5 sí que es pot', 'append' => 'echo calcular(0, 5, "/");', 'exacte' => '0'],
            ],
            'pistes' => [
                'Primer descarta els casos impossibles llançant una excepció. Si arribes al final, fes el càlcul i retorna\'l.',
                'Pots fer el càlcul amb un match. A PHP 8, una branca default també pot fer throw: default => throw new InvalidArgumentException(...).',
                "if (\$operacio === '/' && \$b == 0) {\n    throw new InvalidArgumentException(\"No es pot dividir per zero.\");\n}\nreturn match (\$operacio) {\n    '+' => \$a + \$b,\n    // ...\n    default => throw new InvalidArgumentException(\"Operació no vàlida: \$operacio\"),\n};",
            ],
            'preguntes' => [
                'Què passa amb les instruccions que hi ha després d\'un throw dins de la funció?',
                'Per què és millor llançar una excepció que fer echo d\'un error dins de calcular()?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'La funció no mostra res: o retorna el resultat o llança una excepció que explica què ha anat malament.',
        ],
        [
            'titol' => 'demanarNumero() valida l\'entrada',
            'conceptes' => ['throw', 'is_numeric', 'readline'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> crea <code>function demanarNumero(string $missatge): float</code> que llegeixi amb <code>readline($missatge)</code>.
                Si el text no és un número, llança <code>InvalidArgumentException</code> amb el missatge <code>'abc' no és un número.</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+demanarNumero\s*\(\s*string\s+\$\w+\s*\)\s*:\s*float/i', 'missatge' => 'La signatura ha de ser: function demanarNumero(string $missatge): float'],
            ],
            'proves' => [
                ['nom' => 'Un número', 'stdin' => "4.5\n", 'append' => 'echo PHP_EOL, demanarNumero("N: ") * 2;', 'conte' => ['9']],
                ['nom' => 'Un text', 'stdin' => "abc\n", 'append' => 'echo PHP_EOL; ' . $captura('demanarNumero("N: ")'), 'conte' => ["Capturada: 'abc' no és un número."]],
            ],
            'pistes' => [
                'Llegeix, comprova i, si no és vàlid, llança l\'excepció. Si és vàlid, retorna\'l convertit a float.',
                'is_numeric() et diu si el text és un número. Recorda que readline() retorna un string (o false si no hi ha més entrada).',
                "\$text = trim((string) readline(\$missatge));\nif (!is_numeric(\$text)) {\n    throw new InvalidArgumentException(\"'\$text' no és un número.\");\n}\nreturn (float) \$text;",
            ],
            'preguntes' => [
                'Què retorna readline() si l\'usuari escriu «abc»? I què ha de fer la funció?',
                'Per què la funció no captura ella mateixa l\'excepció?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Qui sap què cal fer amb l\'error és el programa principal; la funció només avisa llançant l\'excepció.',
        ],
        [
            'titol' => 'try / catch / finally',
            'conceptes' => ['try / catch / finally'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> al programa principal, dins d'un <code>try</code>:</p>
                <ol>
                  <li>Demana el primer número (<code>Primer número: </code>), l'operació (<code>Operació (+ - * /): </code>) i el segon número (<code>Segon número: </code>).</li>
                  <li>Mostra <code>Resultat: 8 * 5 = 40</code> (arrodonit a 2 decimals).</li>
                </ol>
                <p>Al <code>catch (InvalidArgumentException $e)</code>, mostra <code>Error: </code> + el missatge. Al <code>finally</code>, mostra sempre
                <code>Gràcies per fer servir la calculadora.</code></p>
                HTML,
            'codi' => [
                ['regex' => '/catch\s*\(\s*\\\\?InvalidArgumentException\s+\$\w+\s*\)/', 'missatge' => 'Captura l\'excepció amb catch (InvalidArgumentException $e).'],
                ['regex' => '/\bfinally\s*\{/', 'missatge' => 'Afegeix un bloc finally.'],
            ],
            'proves' => [
                ['nom' => '8 * 5', 'stdin' => "8\n*\n5\n", 'conte' => ['Resultat: 8 * 5 = 40', 'Gràcies per fer servir la calculadora.']],
                ['nom' => '10 / 3', 'stdin' => "10\n/\n3\n", 'conte' => ['Resultat: 10 / 3 = 3.33']],
                ['nom' => 'Divisió per zero', 'stdin' => "8\n/\n0\n", 'conte' => ['Error: No es pot dividir per zero.', 'Gràcies per fer servir la calculadora.'], 'no_conte' => ['Resultat']],
                ['nom' => 'Primer número incorrecte', 'stdin' => "abc\n", 'conte' => ["Error: 'abc' no és un número.", 'Gràcies per fer servir la calculadora.']],
                ['nom' => 'Operació desconeguda', 'stdin' => "8\n%\n3\n", 'conte' => ['Error: Operació no vàlida: %']],
            ],
            'pistes' => [
                'Tot el que pot fallar va dins del try. Si qualsevol funció llança l\'excepció, PHP salta directament al catch.',
                'El catch rep l\'excepció a $e; $e->getMessage() en dona el text. El finally va després del catch.',
                "try {\n    \$a = demanarNumero(\"Primer número: \");\n    // ...\n} catch (InvalidArgumentException \$e) {\n    echo \"Error: \" . \$e->getMessage() . PHP_EOL;\n} finally {\n    echo \"Gràcies per fer servir la calculadora.\" . PHP_EOL;\n}",
            ],
            'preguntes' => [
                'Si el primer número és «abc», es demana l\'operació? Per què?',
                'Quan s\'executa el bloc finally?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Un sol try / catch gestiona tots els errors de les funcions. finally garanteix el missatge final passi el que passi.',
        ],
    ],
];
