<?php
/**
 * Laboratori: Quant trigarà la còpia de seguretat?
 * Basat en l'Exercici 2 de «Exercicis d'arguments per consola» (Exercicis 02, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Quant trigarà la còpia de seguretat?
// Ús: php transferencia.php <mida> <velocitat>

function mostraAjuda(): void
{
    echo "Ús: php transferencia.php <mida> <velocitat>" . PHP_EOL;
    echo "  mida        Mida de la còpia en GB (> 0)" . PHP_EOL;
    echo "  velocitat   Velocitat de la xarxa en Mbps (> 0)" . PHP_EOL;
}

if ($argc != 3) {
    echo "ERROR: s'esperaven 2 arguments i se n'han rebut " . ($argc - 1) . "." . PHP_EOL . PHP_EOL;
    mostraAjuda();
    exit(1);
}

PHP;

$sol2 = $sol1 . <<<'PHP'

$mida = $argv[1];
$velocitat = $argv[2];

if (!is_numeric($mida) || $mida <= 0) {
    echo "ERROR: la mida \"$mida\" ha de ser un número més gran que 0." . PHP_EOL;
    exit(1);
}
if (!is_numeric($velocitat) || $velocitat <= 0) {
    echo "ERROR: la velocitat \"$velocitat\" ha de ser un número més gran que 0." . PHP_EOL;
    exit(1);
}

PHP;

$sol3 = str_replace(
    "function mostraAjuda",
    "const MB_PER_GB = 1000;\nconst BITS_PER_BYTE = 8;\n\nfunction mostraAjuda",
    $sol2
) . <<<'PHP'

$megabits = $mida * MB_PER_GB * BITS_PER_BYTE;
$segons = (int) round($megabits / $velocitat);

$hores = intdiv($segons, 3600);
$minuts = intdiv($segons % 3600, 60);
$segonsRestants = $segons % 60;

echo "Mida:      $mida GB" . PHP_EOL;
echo "Velocitat: $velocitat Mbps" . PHP_EOL;
echo "Temps:     $hores h $minuts min $segonsRestants s" . PHP_EOL;

PHP;

return [
    'id' => 'transferencia',
    'ordre' => 60,
    'titol' => 'Quant trigarà la còpia de seguretat?',
    'fitxer' => 'transferencia.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.7 Arguments per consola ($argv, $argc)',
    'origen' => [
        'fitxer' => 'Exercicis_02.Arguments_per_consola_Enunciat.pdf',
        'descripcio' => 'Exercici 2 de «Exercicis d\'arguments per consola» (David Porti)',
    ],
    'resum' => 'Calcula quant trigarà una còpia de seguretat per la xarxa. Practicaràs $argv, is_numeric(), constants, intdiv() i %.',
    'xp_final' => 40,
    'paraules_clau' => ['argv', 'argc', 'is_numeric', 'intdiv', 'transferencia'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Quant trigarà la còpia de seguretat?
        // Ús: php transferencia.php <mida> <velocitat>

        PHP,

    'context' => <<<'HTML'
        <p>Cada nit cal copiar les còpies de seguretat d'un servidor a un altre per la xarxa. Abans de programar la tasca,
        l'administrador vol saber quant trigarà, per assegurar-se que acaba abans que arribin els usuaris al matí.</p>
        <pre class="term">$ php transferencia.php 250 100
        Mida:      250 GB
        Velocitat: 100 Mbps
        Temps:     5 h 33 min 20 s</pre>
        HTML,

    'resum_apres' => [
        'Validar el nombre d\'arguments amb $argc i reutilitzar l\'ajuda en una funció.',
        'Comprovar que els arguments són números positius amb is_numeric() abans de fer cap càlcul.',
        'Guardar els valors fixos (1000, 8) en constants.',
        'Passar segons a hores, minuts i segons amb intdiv() i %.',
        'La mida es mesura en bytes i la velocitat de la xarxa en bits: cal la mateixa unitat.',
    ],

    'passos' => [
        [
            'titol' => 'Exactament 2 arguments',
            'conceptes' => ['$argv i $argc', 'funcions'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>El programa rep la <strong>mida</strong> (en GB) i la <strong>velocitat</strong> (en Mbps) com a arguments.</p>
                <p><strong>Objectiu:</strong> crea la funció <code>mostraAjuda()</code> i, si no hi ha exactament 2 arguments, mostra:</p>
                <pre class="term">ERROR: s'esperaven 2 arguments i se n'han rebut 1.

                Ús: php transferencia.php &lt;mida&gt; &lt;velocitat&gt;
                  mida        Mida de la còpia en GB (&gt; 0)
                  velocitat   Velocitat de la xarxa en Mbps (&gt; 0)</pre>
                HTML,
            'codi' => [
                ['regex' => '/\$argc\b/', 'missatge' => 'Comprova el nombre d\'arguments amb $argc.'],
                ['regex' => '/function\s+mostraAjuda\s*\(/i', 'missatge' => 'Posa l\'ajuda dins de la funció mostraAjuda().'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ["ERROR: s'esperaven 2 arguments i se n'han rebut 0.", 'Ús: php transferencia.php <mida> <velocitat>']],
                ['nom' => 'Falta un argument', 'args' => ['250'], 'conte' => ["se n'han rebut 1.", "mida Mida de la còpia en GB (> 0)\nvelocitat Velocitat de la xarxa en Mbps (> 0)"]],
                ['nom' => 'Dos arguments: cap error', 'args' => ['250', '100'], 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'És el mateix patró que al traductor de permisos, però amb 2 arguments. Quant ha de valer $argc?',
                '$argc != 3 vol dir que no hi ha exactament 2 arguments (el primer element és el nom del script). Mostra l\'error, una línia en blanc, l\'ajuda i exit.',
                "if (\$argc != 3) {\n    echo \"ERROR: s'esperaven 2 arguments i se n'han rebut \" . (\$argc - 1) . \".\" . PHP_EOL . PHP_EOL;\n    mostraAjuda();\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Amb «php transferencia.php 250 100», quant val $argc?',
                'Què ha de passar després de mostrar l\'ajuda?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Amb 2 arguments, $argc val 3. Si no, error, ajuda i exit.',
        ],
        [
            'titol' => 'Números més grans que 0',
            'conceptes' => ['is_numeric', 'validació de dades'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> valida que tots dos arguments són números més grans que 0. El missatge ha d'indicar
                <strong>quin argument falla</strong>:</p>
                <pre class="term">$ php transferencia.php 250 cent
                ERROR: la velocitat "cent" ha de ser un número més gran que 0.

                $ php transferencia.php -5 100
                ERROR: la mida "-5" ha de ser un número més gran que 0.</pre>
                <p class="nota">La mida pot tenir decimals amb punt (<code>1.5</code>).</p>
                HTML,
            'codi' => [
                ['regex' => '/\bis_numeric\s*\(/', 'missatge' => 'Valida els números amb is_numeric().'],
            ],
            'proves' => [
                ['nom' => 'Velocitat no numèrica', 'args' => ['250', 'cent'], 'conte' => ['ERROR: la velocitat "cent" ha de ser un número més gran que 0.']],
                ['nom' => 'Mida negativa', 'args' => ['-5', '100'], 'conte' => ['ERROR: la mida "-5" ha de ser un número més gran que 0.']],
                ['nom' => 'Mida 0', 'args' => ['0', '100'], 'conte' => ['ERROR: la mida "0"']],
                ['nom' => 'Velocitat 0', 'args' => ['250', '0'], 'conte' => ['ERROR: la velocitat "0"']],
                ['nom' => 'Mida amb decimals: cap error', 'args' => ['1.5', '100'], 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'Cal comprovar dues coses de cada argument: que és un número i que és més gran que 0. Si alguna falla, error i exit.',
                'if (!is_numeric($mida) || $mida <= 0) { ... }. Gràcies al curtcircuit, si no és numèric ja no es compara amb 0.',
                "if (!is_numeric(\$mida) || \$mida <= 0) {\n    echo \"ERROR: la mida \\\"\$mida\\\" ha de ser un número més gran que 0.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Per què cal comprovar is_numeric() abans de comparar amb 0?',
                'Quin operador lògic fas servir per unir les dues condicions d\'error?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Amb || n\'hi ha prou que una condició falli per mostrar l\'error. El curtcircuit evita comparar un text amb 0.',
        ],
        [
            'titol' => 'El càlcul',
            'conceptes' => ['constants', 'intdiv i %', 'operadors aritmètics'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><strong>Regles:</strong> megabytes = GB × 1000 · megabits = megabytes × 8 · segons = megabits ÷ velocitat (arrodonit a enter).
                Després, passa els segons a hores, minuts i segons.</p>
                <p><strong>Requisits:</strong> 1000 i 8 han de ser <strong>constants</strong>, i fes servir <code>intdiv()</code> i <code>%</code>.</p>
                <pre class="term">Mida:      250 GB
                Velocitat: 100 Mbps
                Temps:     5 h 33 min 20 s</pre>
                <table class="taula">
                  <tr><th>Ordre</th><th>Megabits</th><th>Temps</th></tr>
                  <tr><td>250 100</td><td>2.000.000</td><td>5 h 33 min 20 s</td></tr>
                  <tr><td>50 1000</td><td>400.000</td><td>0 h 6 min 40 s</td></tr>
                  <tr><td>2000 1000</td><td>16.000.000</td><td>4 h 26 min 40 s</td></tr>
                </table>
                HTML,
            'codi' => [
                ['regex' => '/\b(const\s+\w+\s*=\s*1000|define\s*\(\s*[\'"]\w+[\'"]\s*,\s*1000)/', 'missatge' => 'Guarda el 1000 (MB per GB) en una constant.'],
                ['regex' => '/\b(const\s+\w+\s*=\s*8\b|define\s*\(\s*[\'"]\w+[\'"]\s*,\s*8\s*\))/', 'missatge' => 'Guarda el 8 (bits per byte) en una constant.'],
                ['regex' => '/\bintdiv\s*\(/', 'missatge' => 'Calcula les hores i els minuts amb intdiv().'],
            ],
            'proves' => [
                ['nom' => '250 GB a 100 Mbps', 'args' => ['250', '100'], 'exacte' => "Mida: 250 GB\nVelocitat: 100 Mbps\nTemps: 5 h 33 min 20 s"],
                ['nom' => '50 GB a 1000 Mbps', 'args' => ['50', '1000'], 'conte' => ['Temps: 0 h 6 min 40 s']],
                ['nom' => '2000 GB a 1000 Mbps', 'args' => ['2000', '1000'], 'conte' => ['Temps: 4 h 26 min 40 s']],
                ['nom' => '1.5 GB a 100 Mbps', 'args' => ['1.5', '100'], 'conte' => ['Mida: 1.5 GB', 'Temps: 0 h 2 min 0 s']],
            ],
            'pistes' => [
                'Primer passa-ho tot a megabits i calcula els segons totals. Després, quantes hores completes caben en aquests segons? I amb el que sobra, quants minuts?',
                'Una hora té 3600 segons: hores = intdiv($segons, 3600). Els minuts surten del que sobra: intdiv($segons % 3600, 60). Els segons finals: $segons % 60.',
                "\$megabits = \$mida * MB_PER_GB * BITS_PER_BYTE;\n\$segons = (int) round(\$megabits / \$velocitat);\n\$hores = intdiv(\$segons, 3600);\n\$minuts = intdiv(\$segons % 3600, 60);\n\$segonsRestants = \$segons % 60;",
            ],
            'preguntes' => [
                'Quants megabits són 250 GB?',
                'Si tens 20.000 segons, quantes hores completes són? Quants segons sobren?',
                'Per què cal multiplicar per 8?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Tot es passa a la mateixa unitat (megabits). intdiv dona les unitats completes i % el que sobra.',
        ],
    ],
];
