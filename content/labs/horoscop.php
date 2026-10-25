<?php
/**
 * Laboratori: Horòscop xinès.
 * Basat en l'Exercici 7 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Horòscop xinès

$any = (int) readline("En quin any vas néixer? ");

// El cicle de 12 animals va començar l'any 4 amb la Rata: restant 4, la Rata queda a la posició 0
switch (($any - 4) % 12) {
    case 0:
        $animal = "Rata";
        break;
    case 1:
        $animal = "Bou";
        break;
    case 2:
        $animal = "Tigre";
        break;
    case 3:
        $animal = "Conill";
        break;
    case 4:
        $animal = "Drac";
        break;
    case 5:
        $animal = "Serp";
        break;
    case 6:
        $animal = "Cavall";
        break;
    case 7:
        $animal = "Cabra";
        break;
    case 8:
        $animal = "Mico";
        break;
    case 9:
        $animal = "Gall";
        break;
    case 10:
        $animal = "Gos";
        break;
    default:
        $animal = "Porc";
}
echo PHP_EOL . "Any $any: ets $animal" . PHP_EOL;

PHP;

$sol2 = str_replace(
    'echo PHP_EOL . "Any $any: ets $animal" . PHP_EOL;' . "\n",
    <<<'PHP'
// match compara amb === i retorna el valor directament
$element = match ($any % 10) {
    0, 1 => "Metall",
    2, 3 => "Aigua",
    4, 5 => "Fusta",
    6, 7 => "Foc",
    8, 9 => "Terra",
};
echo PHP_EOL . "Any $any: ets $animal de $element." . PHP_EOL;

PHP,
    $sol1
);

$anim = fn(string $any, string $animal) => ['nom' => "$any → $animal", 'stdin' => "$any\n", 'conte' => ["Any $any: ets $animal"]];
$frase = fn(string $any, string $text) => ['nom' => "Any $any", 'stdin' => "$any\n", 'conte' => [$text]];

return [
    'id' => 'horoscop',
    'ordre' => 20,
    'titol' => 'Horòscop xinès',
    'fitxer' => 'Exercici07_horoscop_xines.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.4 switch i match',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 7 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Calcula l\'animal i l\'element d\'un any de naixement. Compararàs switch i match i faràs servir el mòdul %.',
    'xp_final' => 40,
    'paraules_clau' => ['switch', 'match', 'mòdul', 'case', 'break'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Horòscop xinès

        PHP,

    'context' => <<<'HTML'
        <p>L'horòscop xinès assigna un animal a cada any en un cicle de 12 i un element segons l'última xifra de l'any.</p>
        <pre class="term">En quin any vas néixer? 2024

        Any 2024: ets Drac de Fusta.</pre>
        <p class="nota">L'any s'escriu a la caixa «Entrada per a readline()».</p>
        HTML,

    'resum_apres' => [
        'Fer servir el mòdul % per obtenir una posició dins d\'un cicle (de 0 a 11) i l\'última xifra (any % 10).',
        'Escriure un switch amb molts case, cadascun amb el seu break.',
        'Fer servir match per agrupar valors (0, 1 => ...) i retornar directament un resultat.',
        'Diferències entre switch (==, break, no retorna) i match (===, sense break, retorna un valor).',
    ],

    'passos' => [
        [
            'titol' => 'L\'animal amb switch',
            'conceptes' => ['switch', 'mòdul %'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Demana l'any amb <code>readline("En quin any vas néixer? ")</code> i converteix-lo a enter.</p>
                <p>Calcula <code>($any - 4) % 12</code>: el resultat (de 0 a 11) indica l'animal. Amb un <strong>switch</strong>, assigna'l:</p>
                <table class="taula">
                  <tr><td>0 Rata</td><td>1 Bou</td><td>2 Tigre</td><td>3 Conill</td><td>4 Drac</td><td>5 Serp</td></tr>
                  <tr><td>6 Cavall</td><td>7 Cabra</td><td>8 Mico</td><td>9 Gall</td><td>10 Gos</td><td>11 Porc</td></tr>
                </table>
                <p><strong>Objectiu:</strong> de moment, mostra (després d'una línia en blanc) <code>Any 2024: ets Drac</code>.
                Afegeix un comentari que expliqui per què es resta 4.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'L\'animal s\'ha de calcular amb switch (requisit de l\'enunciat).'],
                ['regex' => '/%\s*12/', 'missatge' => 'Fes servir el mòdul: ($any - 4) % 12.'],
            ],
            'proves' => [
                $anim('2024', 'Drac'),
                $anim('1990', 'Cavall'),
                $anim('2020', 'Rata'),
                $anim('2019', 'Porc'),
                $anim('2006', 'Gos'),
            ],
            'pistes' => [
                'El residu d\'una divisió entre 12 sempre és un número del 0 a l\'11: és la posició dins del cicle dels animals.',
                'switch (($any - 4) % 12) { case 0: $animal = "Rata"; break; case 1: ... }. Cada case acaba amb break. El cas 11 pot ser el default.',
                "switch ((\$any - 4) % 12) {\n    case 0:\n        \$animal = \"Rata\";\n        break;\n    case 1:\n        \$animal = \"Bou\";\n        break;\n    // ... fins al 10\n    default:\n        \$animal = \"Porc\";\n}",
            ],
            'preguntes' => [
                'Quant dona (2024 - 4) % 12? A quin animal correspon?',
                'Què passa si oblides un break en un dels case?',
                'Has convertit l\'any a enter? Què retorna readline()?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'El mòdul torna la posició dins del cicle. Cada case assigna l\'animal i surt amb break.',
        ],
        [
            'titol' => 'L\'element amb match',
            'conceptes' => ['match', 'mòdul %'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>L'element depèn de l'última xifra de l'any (<code>$any % 10</code>):</p>
                <table class="taula">
                  <tr><td>0 i 1: Metall</td><td>2 i 3: Aigua</td><td>4 i 5: Fusta</td><td>6 i 7: Foc</td><td>8 i 9: Terra</td></tr>
                </table>
                <p><strong>Objectiu:</strong> calcula l'element amb una expressió <strong>match</strong> i mostra la frase completa:</p>
                <pre class="term">Any 2024: ets Drac de Fusta.</pre>
                <p class="nota">match permet agrupar valors separant-los amb comes: <code>0, 1 => "Metall"</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bmatch\s*\(/', 'missatge' => 'L\'element s\'ha de calcular amb match (requisit de l\'enunciat).'],
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'L\'animal s\'ha de continuar calculant amb switch.'],
            ],
            'proves' => [
                $frase('2024', 'Any 2024: ets Drac de Fusta.'),
                $frase('1990', 'Any 1990: ets Cavall de Metall.'),
                $frase('1987', 'Any 1987: ets Conill de Foc.'),
                $frase('2008', 'Any 2008: ets Rata de Terra.'),
                $frase('1996', 'Any 1996: ets Rata de Foc.'),
            ],
            'pistes' => [
                'L\'última xifra d\'un número és el residu de dividir-lo entre 10. Amb aquesta xifra, match tria l\'element.',
                '$element = match ($any % 10) { 0, 1 => "Metall", ... }; Recorda el punt i coma final: match és una expressió, com una assignació.',
                "\$element = match (\$any % 10) {\n    0, 1 => \"Metall\",\n    2, 3 => \"Aigua\",\n    // ...\n};",
            ],
            'preguntes' => [
                'Quina és l\'última xifra de 1987? Com l\'obtens amb un operador?',
                'Què passaria si a match li falta un valor i no hi ha default?',
                'Quina diferència hi ha entre com retorna el valor switch i com ho fa match?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'match retorna directament l\'element i compara amb ===. Com que $any % 10 sempre és de 0 a 9, no cal default.',
        ],
    ],
];
