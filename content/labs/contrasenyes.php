<?php
/**
 * Laboratori: Validador de contrasenyes.
 * Basat en l'Exercici 10 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Validador de contrasenyes

$contrasenya = (string) readline("Introdueix una contrasenya: ");

$teMajuscula = false;
$teMinuscula = false;
$teDigit = false;
$teSimbol = false;
for ($i = 0; $i < strlen($contrasenya); $i++) {
    $c = $contrasenya[$i];
    if (ctype_upper($c)) {
        $teMajuscula = true;
    } elseif (ctype_lower($c)) {
        $teMinuscula = true;
    } elseif (ctype_digit($c)) {
        $teDigit = true;
    } else {
        $teSimbol = true;
    }
}

// Es mostren tots els requisits que falten, no només el primer
$esValida = true;
if (strlen($contrasenya) < 8) {
    echo " - Ha de tenir almenys 8 caràcters." . PHP_EOL;
    $esValida = false;
}
if (!$teMajuscula) {
    echo " - Falta una lletra majúscula." . PHP_EOL;
    $esValida = false;
}
if (!$teMinuscula) {
    echo " - Falta una lletra minúscula." . PHP_EOL;
    $esValida = false;
}
if (!$teDigit) {
    echo " - Falta un dígit." . PHP_EOL;
    $esValida = false;
}
if (!$teSimbol) {
    echo " - Falta un símbol (p. ex. ! @ # $ %)." . PHP_EOL;
    $esValida = false;
}
if ($esValida) {
    echo "Contrasenya vàlida. Desada correctament!" . PHP_EOL;
}

PHP;

$sol2 = <<<'PHP'
<?php
// Validador de contrasenyes

do {
    $contrasenya = (string) readline("Introdueix una contrasenya: ");

    $teMajuscula = false;
    $teMinuscula = false;
    $teDigit = false;
    $teSimbol = false;
    for ($i = 0; $i < strlen($contrasenya); $i++) {
        $c = $contrasenya[$i];
        if (ctype_upper($c)) {
            $teMajuscula = true;
        } elseif (ctype_lower($c)) {
            $teMinuscula = true;
        } elseif (ctype_digit($c)) {
            $teDigit = true;
        } else {
            $teSimbol = true;
        }
    }

    $esValida = true;
    if (strlen($contrasenya) < 8) {
        echo " - Ha de tenir almenys 8 caràcters." . PHP_EOL;
        $esValida = false;
    }
    if (!$teMajuscula) {
        echo " - Falta una lletra majúscula." . PHP_EOL;
        $esValida = false;
    }
    if (!$teMinuscula) {
        echo " - Falta una lletra minúscula." . PHP_EOL;
        $esValida = false;
    }
    if (!$teDigit) {
        echo " - Falta un dígit." . PHP_EOL;
        $esValida = false;
    }
    if (!$teSimbol) {
        echo " - Falta un símbol (p. ex. ! @ # $ %)." . PHP_EOL;
        $esValida = false;
    }
} while (!$esValida);

echo "Contrasenya vàlida. Desada correctament!" . PHP_EOL;

PHP;

$sol3 = str_replace(
    ["do {\n", "} while (!\$esValida);\n\necho \"Contrasenya vàlida. Desada correctament!\" . PHP_EOL;\n"],
    ["\$intents = 0;\ndo {\n    \$intents++;\n", "} while (!\$esValida);\n\necho \"Contrasenya vàlida. Desada correctament!\" . PHP_EOL;\necho \"Has necessitat \$intents \" . (\$intents == 1 ? \"intent\" : \"intents\") . \".\" . PHP_EOL;\n"],
    $sol2
);

$falta = [
    'llarg' => ' - Ha de tenir almenys 8 caràcters.',
    'maj' => ' - Falta una lletra majúscula.',
    'min' => ' - Falta una lletra minúscula.',
    'dig' => ' - Falta un dígit.',
    'sim' => ' - Falta un símbol (p. ex. ! @ # $ %).',
];

return [
    'id' => 'contrasenyes',
    'ordre' => 30,
    'titol' => 'Validador de contrasenyes',
    'fitxer' => 'Exercici10_validador_contrasenyes.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.4 Estructures de control',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 10 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Recorre una contrasenya caràcter a caràcter amb un for i uns flags booleans, i torna-la a demanar amb do...while fins que sigui segura.',
    'xp_final' => 40,
    'paraules_clau' => ['for', 'do...while', 'strlen', 'ctype_upper', 'ctype_digit', 'flag'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Validador de contrasenyes

        PHP,

    'context' => <<<'HTML'
        <p>Una aplicació web obliga els usuaris a triar una contrasenya segura: almenys <strong>8 caràcters</strong>, una
        <strong>majúscula</strong>, una <strong>minúscula</strong>, un <strong>dígit</strong> i un <strong>símbol</strong>.
        El programa la demana tantes vegades com calgui fins que compleixi tots els requisits.</p>
        <p><strong>Com es prova:</strong> escriu cada intent en una línia de la caixa «Entrada per a readline()».</p>
        <pre class="term">Introdueix una contrasenya: hola
         - Ha de tenir almenys 8 caràcters.
         - Falta una lletra majúscula.
         - Falta un dígit.
         - Falta un símbol (p. ex. ! @ # $ %).
        Introdueix una contrasenya: Hola1234!
        Contrasenya vàlida. Desada correctament!</pre>
        HTML,

    'resum_apres' => [
        'Un string es pot recórrer com un array: $text[$i] és el caràcter de la posició $i.',
        'Els flags booleans ($teDigit...) comencen a false i es posen a true quan es troba el que busquem.',
        'ctype_upper(), ctype_lower() i ctype_digit() diuen de quin tipus és un caràcter.',
        'do...while executa el bloc almenys una vegada i el repeteix mentre la condició sigui certa.',
    ],

    'passos' => [
        [
            'titol' => 'Comprovar una contrasenya',
            'conceptes' => ['bucle for', 'booleans', 'cadenes de text'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> demana una contrasenya amb <code>readline("Introdueix una contrasenya: ")</code> i:</p>
                <ol>
                  <li>Inicialitza a <code>false</code> quatre flags: <code>$teMajuscula</code>, <code>$teMinuscula</code>, <code>$teDigit</code> i <code>$teSimbol</code>.</li>
                  <li>Recorre-la amb un <code>for</code> de 0 fins a <code>strlen() - 1</code>. Cada caràcter és <code>$contrasenya[$i]</code>.</li>
                  <li>Per a cada caràcter, mira si és majúscula (<code>ctype_upper</code>), minúscula (<code>ctype_lower</code>) o dígit (<code>ctype_digit</code>). Si no és cap d'aquests, és un símbol.</li>
                  <li>Després del bucle, mostra <strong>tots</strong> els requisits que falten, cada un en una línia i en aquest ordre:</li>
                </ol>
                <pre class="term"> - Ha de tenir almenys 8 caràcters.
                 - Falta una lletra majúscula.
                 - Falta una lletra minúscula.
                 - Falta un dígit.
                 - Falta un símbol (p. ex. ! @ # $ %).</pre>
                <p>Si no en falta cap, mostra <code>Contrasenya vàlida. Desada correctament!</code></p>
                HTML,
            'codi' => [
                ['regex' => '/\bfor\s*\(/', 'missatge' => 'Recorre la contrasenya amb un for.'],
                ['regex' => '/\bstrlen\s*\(/', 'missatge' => 'Fes servir strlen() per saber quants caràcters té.'],
                ['regex' => '/\bctype_upper\s*\(/', 'missatge' => 'Comprova les majúscules amb ctype_upper().'],
                ['regex' => '/\bctype_digit\s*\(/', 'missatge' => 'Comprova els dígits amb ctype_digit().'],
            ],
            'proves' => [
                ['nom' => '«hola»: en falten quatre', 'stdin' => "hola\n", 'conte' => [$falta['llarg'], $falta['maj'], $falta['dig'], $falta['sim']], 'no_conte' => [$falta['min'], 'Contrasenya vàlida']],
                ['nom' => '«HOLAMUNDO1»: falten minúscula i símbol', 'stdin' => "HOLAMUNDO1\n", 'conte' => [$falta['min'], $falta['sim']], 'no_conte' => [$falta['llarg'], $falta['maj'], $falta['dig']]],
                ['nom' => '«Hola1234!»: vàlida', 'stdin' => "Hola1234!\n", 'conte' => ['Contrasenya vàlida. Desada correctament!'], 'no_conte' => ['Falta', 'almenys']],
                ['nom' => '«Ab1#»: només és curta', 'stdin' => "Ab1#\n", 'conte' => [$falta['llarg']], 'no_conte' => ['Falta', 'Contrasenya vàlida']],
            ],
            'pistes' => [
                'Fes primer el recorregut (que només encén flags) i, quan s\'acabi, fes les comprovacions una a una. Cada requisit que falta és un if independent, no un elseif.',
                'Dins del for: if (ctype_upper($c)) ... elseif (ctype_lower($c)) ... elseif (ctype_digit($c)) ... else $teSimbol = true. Una variable $esValida que comença a true i es posa a false a cada error t\'ajudarà al final.',
                "for (\$i = 0; \$i < strlen(\$contrasenya); \$i++) {\n    \$c = \$contrasenya[\$i];\n    if (ctype_upper(\$c)) {\n        \$teMajuscula = true;\n    } elseif (ctype_lower(\$c)) {\n        // ...\n    }\n}\n\$esValida = true;\nif (strlen(\$contrasenya) < 8) {\n    echo \" - Ha de tenir almenys 8 caràcters.\" . PHP_EOL;\n    \$esValida = false;\n}",
            ],
            'preguntes' => [
                'Per què les comprovacions del final són if separats i no if / elseif?',
                'Què val strlen("Hola1234!")? Quina és l\'última posició vàlida?',
                'Per què els flags han de començar a false?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'El for només encén flags. Després, cada requisit és un if independent perquè surtin tots els que falten.',
        ],
        [
            'titol' => 'Tornar-la a demanar amb do...while',
            'conceptes' => ['bucle do...while'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> posa tot el que has fet dins d'un <code>do { ... } while (...)</code> perquè el programa
                torni a demanar la contrasenya <strong>mentre no sigui vàlida</strong>. Quan ho sigui, surt del bucle i mostra
                <code>Contrasenya vàlida. Desada correctament!</code></p>
                <pre class="term">Introdueix una contrasenya: hola
                 - Ha de tenir almenys 8 caràcters.
                 ...
                Introdueix una contrasenya: Hola1234!
                Contrasenya vàlida. Desada correctament!</pre>
                <p class="nota">Recorda inicialitzar els flags <strong>dins</strong> del bucle: cada intent comença de zero.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bdo\s*\{/', 'missatge' => 'Fes servir un bucle do { ... } while (...).'],
                ['regex' => '/\}\s*while\s*\(/', 'missatge' => 'La condició va al final: } while (...);'],
            ],
            'proves' => [
                ['nom' => 'Dos intents', 'stdin' => "hola\nHola1234!\n", 'conte' => [$falta['llarg'], 'Contrasenya vàlida. Desada correctament!']],
                ['nom' => 'Tres intents', 'stdin' => "hola\nholaholahola\nHola1234!\n", 'conte' => [$falta['maj'], 'Contrasenya vàlida. Desada correctament!']],
                ['nom' => 'A la primera', 'stdin' => "Segura#2026\n", 'conte' => ['Contrasenya vàlida. Desada correctament!'], 'no_conte' => ['Falta']],
            ],
            'pistes' => [
                'El que vols repetir és tot el procés: llegir, recórrer i comprovar. La condició per repetir és que la contrasenya no sigui vàlida.',
                'do { ... tot el codi ... } while (!$esValida); El missatge de «Contrasenya vàlida» pot anar després del bucle.',
                "do {\n    \$contrasenya = (string) readline(\"Introdueix una contrasenya: \");\n    \$teMajuscula = false;\n    // ... el recorregut i les comprovacions ...\n} while (!\$esValida);\necho \"Contrasenya vàlida. Desada correctament!\" . PHP_EOL;",
            ],
            'preguntes' => [
                'Quina diferència hi ha entre while (...) { } i do { } while (...)? Per què aquí va millor el segon?',
                'Què passaria si inicialitzessis els flags abans del do?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'do...while executa el bloc almenys un cop (cal demanar la contrasenya de totes maneres) i el repeteix mentre $esValida sigui false.',
        ],
        [
            'titol' => 'Comptar els intents',
            'conceptes' => ['comptadors', 'operador ternari'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Ampliació:</strong> l'enunciat no ho demana, però és útil saber quants intents ha calgut.</p>
                <p><strong>Objectiu:</strong> compta els intents amb una variable <code>$intents</code> i, després del missatge de
                contrasenya vàlida, mostra-ho (fixa't en el singular):</p>
                <pre class="term">Has necessitat 1 intent.
                Has necessitat 3 intents.</pre>
                HTML,
            'codi' => [
                ['regex' => '/\$intents\s*\+\+|\+\+\s*\$intents|\$intents\s*\+=\s*1/', 'missatge' => 'Suma 1 a $intents a cada volta del bucle.'],
            ],
            'proves' => [
                ['nom' => 'A la primera', 'stdin' => "Hola1234!\n", 'conte' => ['Has necessitat 1 intent.']],
                ['nom' => 'Tres intents', 'stdin' => "hola\nholaholahola\nHola1234!\n", 'conte' => ['Has necessitat 3 intents.']],
            ],
            'pistes' => [
                'Un comptador comença a 0 abans del bucle i augmenta d\'1 cada vegada que es demana una contrasenya.',
                '$intents = 0; abans del do, i $intents++; com a primera línia dins del bucle. Per al singular, un ternari: $intents == 1 ? "intent" : "intents".',
                "\$intents = 0;\ndo {\n    \$intents++;\n    // ...\n} while (!\$esValida);",
            ],
            'preguntes' => [
                'Per què el comptador s\'inicialitza fora del bucle i els flags, a dins?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'El comptador viu fora del bucle (si no, tornaria a 0 a cada volta) i augmenta a cada intent.',
        ],
    ],
];
