<?php
/**
 * Laboratori: Caixa d'eines matemàtica.
 * Basat en l'Exercici 16 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 * Els passos 1-4 proven cada funció cridant-la directament (append); el pas 5 prova el menú amb l'entrada.
 */

$sol1 = <<<'PHP'
<?php
// Caixa d'eines matemàtica

function esPrimer(int $n): bool
{
    if ($n < 2) {
        return false;
    }
    // N'hi ha prou de provar divisors fins a l'arrel quadrada
    for ($i = 2; $i <= sqrt($n); $i++) {
        if ($n % $i === 0) {
            return false;
        }
    }
    return true;
}

PHP;

$sol2 = $sol1 . <<<'PHP'

function factorial(int $n): int
{
    $resultat = 1;
    for ($i = 2; $i <= $n; $i++) {
        $resultat *= $i;
    }
    return $resultat;
}

PHP;

$sol3 = $sol2 . <<<'PHP'

// Algorisme d'Euclides: mcd(a, b) = mcd(b, a % b) fins que b val 0
function mcd(int $a, int $b): int
{
    $a = abs($a);
    $b = abs($b);
    while ($b !== 0) {
        $residu = $a % $b;
        $a = $b;
        $b = $residu;
    }
    return $a;
}

PHP;

$sol4 = $sol3 . <<<'PHP'

function sumaDigits(int $n): int
{
    $n = abs($n);
    $suma = 0;
    while ($n > 0) {
        $suma += $n % 10;       // l'última xifra
        $n = intdiv($n, 10);    // treu l'última xifra
    }
    return $suma;
}

PHP;

$sol5 = $sol4 . <<<'PHP'

function demanarEnter(string $missatge): int
{
    return (int) readline($missatge);
}

// ---- Programa principal ----
do {
    echo PHP_EOL . "===== CAIXA D'EINES MATEMÀTICA =====" . PHP_EOL;
    echo "1) És primer?" . PHP_EOL;
    echo "2) Factorial" . PHP_EOL;
    echo "3) Màxim comú divisor" . PHP_EOL;
    echo "4) Suma de dígits" . PHP_EOL;
    echo "5) Sortir" . PHP_EOL;
    $opcio = demanarEnter("Opció: ");

    switch ($opcio) {
        case 1:
            $n = demanarEnter("Número: ");
            echo esPrimer($n) ? "$n és primer." : "$n no és primer.";
            echo PHP_EOL;
            break;
        case 2:
            $n = demanarEnter("Número: ");
            if ($n < 0 || $n > 20) {
                echo "Error: el factorial només s'accepta entre 0 i 20." . PHP_EOL;
            } else {
                echo "$n! = " . factorial($n) . PHP_EOL;
            }
            break;
        case 3:
            $a = demanarEnter("Primer número: ");
            $b = demanarEnter("Segon número: ");
            echo "MCD($a, $b) = " . mcd($a, $b) . PHP_EOL;
            break;
        case 4:
            $n = demanarEnter("Número: ");
            echo "La suma dels dígits de $n és " . sumaDigits($n) . "." . PHP_EOL;
            break;
        case 5:
            echo "Fins aviat!" . PHP_EOL;
            break;
        default:
            echo "Opció no vàlida." . PHP_EOL;
    }
} while ($opcio !== 5);

PHP;

$menu = fn(string $nom, string $stdin, array $conte) => ['nom' => $nom, 'stdin' => $stdin, 'conte' => $conte];

return [
    'id' => 'eines-matematiques',
    'ordre' => 40,
    'titol' => 'Caixa d\'eines matemàtica',
    'fitxer' => 'Exercici16_eines_matematiques.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.6 Funcions',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 16 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Quatre funcions tipades (primer, factorial, MCD i suma de dígits) i un menú que les fa servir. Les funcions retornen, no fan echo.',
    'xp_final' => 50,
    'paraules_clau' => ['function', 'return', 'tipus', 'sqrt', 'intdiv'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Caixa d'eines matemàtica

        PHP,

    'context' => <<<'HTML'
        <p>Volem una petita «calculadora» amb operacions que no porta una calculadora normal. Cada operació serà una
        <strong>funció independent</strong> que es pot reutilitzar en altres programes.</p>
        <p><strong>Requisits de l'enunciat:</strong></p>
        <ul>
          <li>Totes les funcions declaren el tipus dels paràmetres i el de retorn.</li>
          <li>Les funcions <strong>no poden fer echo</strong>: només retornen el resultat, i el programa principal el mostra.</li>
        </ul>
        <p class="nota">Als passos 1 a 4, les proves criden les teves funcions directament. No cal programa principal fins al pas 5.</p>
        HTML,

    'resum_apres' => [
        'Declarar funcions amb paràmetres tipats i tipus de retorn (int, bool).',
        'Separar el càlcul (funcions que retornen) de la presentació (el programa principal fa echo).',
        'Fer servir bucles dins de funcions i sortir-ne aviat amb return.',
        'Construir un menú repetitiu amb do...while i switch que crida les funcions.',
    ],

    'passos' => [
        [
            'titol' => 'esPrimer(int $n): bool',
            'conceptes' => ['funcions', 'tipus a les funcions', 'bucle for'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>Un número és primer si és <strong>més gran que 1</strong> i només és divisible per 1 i per ell mateix.</p>
                <p><strong>Objectiu:</strong> crea <code>function esPrimer(int $n): bool</code> que retorni <code>true</code> o <code>false</code>.</p>
                <p class="nota">La prova executa <code>var_dump(esPrimer(97), esPrimer(1), esPrimer(2), esPrimer(91), esPrimer(0));</code>
                i espera exactament aquesta sortida, sense res més.</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+esPrimer\s*\(\s*int\s+\$\w+\s*\)\s*:\s*bool/i', 'missatge' => 'La signatura ha de ser: function esPrimer(int $n): bool'],
            ],
            'proves' => [
                ['nom' => '97, 1, 2, 91 i 0', 'append' => 'var_dump(esPrimer(97), esPrimer(1), esPrimer(2), esPrimer(91), esPrimer(0));',
                 'exacte' => "bool(true)\nbool(false)\nbool(true)\nbool(false)\nbool(false)"],
            ],
            'pistes' => [
                'Primer descarta els casos que segur que no són primers (0 i 1). Després busca algun divisor: si en trobes un, ja pots retornar false.',
                'Recorre els possibles divisors amb un for des de 2. $n % $i === 0 vol dir que $i divideix $n. N\'hi ha prou d\'arribar a sqrt($n).',
                "function esPrimer(int \$n): bool\n{\n    if (\$n < 2) {\n        return false;\n    }\n    for (\$i = 2; \$i <= sqrt(\$n); \$i++) {\n        // si \$i divideix \$n...\n    }\n    return true;\n}",
            ],
            'preguntes' => [
                '91 és primer? Prova de dividir-lo entre 7.',
                'Per què n\'hi ha prou de provar divisors fins a l\'arrel quadrada?',
                'Quan trobes un divisor, cal continuar el bucle?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'return dins del bucle surt de la funció en trobar el primer divisor. Si el bucle acaba sense trobar-ne cap, és primer.',
        ],
        [
            'titol' => 'factorial(int $n): int',
            'conceptes' => ['funcions', 'bucle for'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><code>n! = 1 × 2 × 3 × ... × n</code>, i per definició <code>0! = 1</code>.</p>
                <p><strong>Objectiu:</strong> crea <code>function factorial(int $n): int</code>.</p>
                <p class="nota">La prova executa <code>echo factorial(0), ",", factorial(5), ",", factorial(20);</code> → <code>1,120,2432902008176640000</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+factorial\s*\(\s*int\s+\$\w+\s*\)\s*:\s*int/i', 'missatge' => 'La signatura ha de ser: function factorial(int $n): int'],
            ],
            'proves' => [
                ['nom' => '0!, 5! i 20!', 'append' => 'echo factorial(0), ",", factorial(5), ",", factorial(20);', 'exacte' => '1,120,2432902008176640000'],
            ],
            'pistes' => [
                'Comença amb un resultat que no canviï res en multiplicar (quin número és?) i ves multiplicant per cada número fins a n.',
                'Variable acumuladora $resultat = 1 i un for de 2 a $n amb $resultat *= $i.',
                "function factorial(int \$n): int\n{\n    \$resultat = 1;\n    for (\$i = 2; \$i <= \$n; \$i++) {\n        \$resultat *= \$i;\n    }\n    return \$resultat;\n}",
            ],
            'preguntes' => [
                'Si $resultat comencés a 0, què passaria amb la multiplicació?',
                'Quantes voltes fa el teu bucle per a factorial(0)?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'L\'acumulador comença a 1. Amb $n = 0 o 1 el bucle no fa cap volta i retorna 1.',
        ],
        [
            'titol' => 'mcd(int $a, int $b): int',
            'conceptes' => ['funcions', 'bucle while'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Algorisme d'Euclides:</strong> mentre <code>b</code> no sigui 0, substitueix <code>(a, b)</code> per <code>(b, a % b)</code>.
                Quan <code>b</code> val 0, el resultat és <code>a</code>. Exemple: (48, 18) → (18, 12) → (12, 6) → (6, 0) → <strong>6</strong>.</p>
                <p><strong>Objectiu:</strong> crea <code>function mcd(int $a, int $b): int</code>.</p>
                <p class="nota">La prova executa <code>echo mcd(48, 18), ",", mcd(17, 5), ",", mcd(100, 75);</code> → <code>6,1,25</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+mcd\s*\(\s*int\s+\$\w+\s*,\s*int\s+\$\w+\s*\)\s*:\s*int/i', 'missatge' => 'La signatura ha de ser: function mcd(int $a, int $b): int'],
            ],
            'proves' => [
                ['nom' => 'mcd(48, 18), mcd(17, 5) i mcd(100, 75)', 'append' => 'echo mcd(48, 18), ",", mcd(17, 5), ",", mcd(100, 75);', 'exacte' => '6,1,25'],
            ],
            'pistes' => [
                'No saps quantes voltes caldran: és un bucle que es repeteix mentre es compleixi una condició.',
                'while ($b !== 0) { ... }. A dins necessites una variable auxiliar per guardar $a % $b abans de canviar $a.',
                "while (\$b !== 0) {\n    \$residu = \$a % \$b;\n    \$a = \$b;\n    \$b = \$residu;\n}\nreturn \$a;",
            ],
            'preguntes' => [
                'Per què aquí és millor un while que un for?',
                'Si fas $a = $b; abans de calcular el residu, què perds?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'La variable $residu evita perdre el valor de $a abans de calcular el mòdul.',
        ],
        [
            'titol' => 'sumaDigits(int $n): int',
            'conceptes' => ['funcions', 'intdiv i %'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>Suma totes les xifres d'un número: <code>1234 → 1 + 2 + 3 + 4 = 10</code>.</p>
                <p><strong>Objectiu:</strong> crea <code>function sumaDigits(int $n): int</code> fent servir <code>% 10</code> (l'última xifra) i
                <code>intdiv($n, 10)</code> (treure l'última xifra).</p>
                <p class="nota">La prova executa <code>echo sumaDigits(1234), ",", sumaDigits(0), ",", sumaDigits(9999);</code> → <code>10,0,36</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+sumaDigits\s*\(\s*int\s+\$\w+\s*\)\s*:\s*int/i', 'missatge' => 'La signatura ha de ser: function sumaDigits(int $n): int'],
            ],
            'proves' => [
                ['nom' => '1234, 0 i 9999', 'append' => 'echo sumaDigits(1234), ",", sumaDigits(0), ",", sumaDigits(9999);', 'exacte' => '10,0,36'],
            ],
            'pistes' => [
                'Pensa com ho faries a mà amb 1234: agafes el 4, et queda 123; agafes el 3, et queda 12... fins que no queda res.',
                '$n % 10 dona l\'última xifra i intdiv($n, 10) la treu. Repeteix-ho mentre $n > 0.',
                "\$suma = 0;\nwhile (\$n > 0) {\n    \$suma += \$n % 10;\n    \$n = intdiv(\$n, 10);\n}\nreturn \$suma;",
            ],
            'preguntes' => [
                'Què dona 1234 % 10? I intdiv(1234, 10)?',
                'Quan s\'ha d\'aturar el bucle?',
            ],
            'solucio' => $sol4,
            'solucio_explicacio' => 'Cada volta treu l\'última xifra i la suma. Amb $n = 0 no entra al bucle i retorna 0.',
        ],
        [
            'titol' => 'El menú',
            'conceptes' => ['do...while', 'switch', 'funcions'],
            'xp' => 35,
            'explicacio' => <<<'HTML'
                <p>Crea la funció auxiliar <code>demanarEnter(string $missatge): int</code> que llegeixi un número amb <code>readline()</code>.</p>
                <p><strong>Objectiu:</strong> al programa principal, mostra un menú en bucle (<code>do...while</code> + <code>switch</code>):</p>
                <pre class="term">===== CAIXA D'EINES MATEMÀTICA =====
                1) És primer?
                2) Factorial
                3) Màxim comú divisor
                4) Suma de dígits
                5) Sortir
                Opció: </pre>
                <p>Les respostes han de tenir aquest format:</p>
                <table class="taula">
                  <tr><td>1</td><td><code>97 és primer.</code> / <code>91 no és primer.</code></td></tr>
                  <tr><td>2</td><td><code>5! = 120</code> · fora de 0-20: un error que digui <code>entre 0 i 20</code></td></tr>
                  <tr><td>3</td><td><code>MCD(48, 18) = 6</code></td></tr>
                  <tr><td>4</td><td><code>La suma dels dígits de 1234 és 10.</code></td></tr>
                  <tr><td>altres</td><td><code>Opció no vàlida.</code></td></tr>
                </table>
                <p class="nota">Per provar-lo, escriu a l'entrada una resposta per línia, acabant amb 5. Per exemple: <code>1</code>, <code>97</code>, <code>5</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+demanarEnter\s*\(\s*string\s+\$\w+\s*\)\s*:\s*int/i', 'missatge' => 'Crea function demanarEnter(string $missatge): int'],
                ['regex' => '/\bdo\s*\{/', 'missatge' => 'El menú ha d\'anar dins d\'un do...while.'],
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'Tria l\'opció amb switch.'],
            ],
            'proves' => [
                $menu('És primer: 97', "1\n97\n5\n", ['97 és primer.']),
                $menu('És primer: 91', "1\n91\n5\n", ['91 no és primer.']),
                $menu('Factorial de 5', "2\n5\n5\n", ['5! = 120']),
                $menu('Factorial de 21 (fora de rang)', "2\n21\n5\n", ['entre 0 i 20']),
                $menu('MCD(48, 18)', "3\n48\n18\n5\n", ['MCD(48, 18) = 6']),
                $menu('Suma de dígits de 1234', "4\n1234\n5\n", ['La suma dels dígits de 1234 és 10.']),
                $menu('Opció inexistent i després sortir', "7\n5\n", ['Opció no vàlida.']),
                $menu('Dues operacions seguides', "1\n2\n4\n99\n5\n", ['2 és primer.', 'La suma dels dígits de 99 és 18.']),
            ],
            'pistes' => [
                'El menú s\'ha de mostrar com a mínim una vegada i repetir-se fins que l\'opció sigui 5: per això do...while.',
                'demanarEnter() només fa return (int) readline($missatge);. Al switch, cada case demana les dades, crida la funció i mostra el resultat. La condició del do...while: $opcio !== 5.',
                "do {\n    // mostra el menú\n    \$opcio = demanarEnter(\"Opció: \");\n    switch (\$opcio) {\n        case 1:\n            \$n = demanarEnter(\"Número: \");\n            echo esPrimer(\$n) ? \"\$n és primer.\" : \"\$n no és primer.\";\n            echo PHP_EOL;\n            break;\n        // ...\n    }\n} while (\$opcio !== 5);",
            ],
            'preguntes' => [
                'Per què aquí és millor do...while que while?',
                'On ha d\'anar l\'echo del resultat: dins de la funció o al switch? Per què?',
                'Si el bucle no s\'atura mai, quina és la condició que falla?',
            ],
            'solucio' => $sol5,
            'solucio_explicacio' => 'Les funcions calculen i el switch mostra. El do...while repeteix el menú fins a l\'opció 5.',
        ],
    ],
];
