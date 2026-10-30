<?php
/**
 * Laboratori: Analitzador de text.
 * Basat en l'activitat proposada 2 del Tema 3 (prof. David Porti).
 */

$sol1 = <<<'PHP'
<?php
// Analitzador de text: php analitzador.php "<text>" [paraula clau]

if ($argc < 2 || trim($argv[1]) === "") {
    echo "Ús: php analitzador.php \"<text>\" [paraula clau]" . PHP_EOL;
    exit(1);
}
$text = trim($argv[1]);

// mb_strlen compta lletres; strlen comptaria bytes (una «à» en són 2)
echo "Caràcters: " . mb_strlen($text) . PHP_EOL;

// Els espais seguits deixen trossos buits: no són paraules
$paraules = array_filter(explode(" ", $text), fn($p) => $p !== "");
echo "Paraules: " . count($paraules) . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

// La més llarga, sense els signes de puntuació enganxats («text,» → «text»)
$mesLlarga = "";
foreach ($paraules as $paraula) {
    $neta = trim($paraula, ".,;:!?¡¿«»\"()");
    if (mb_strlen($neta) > mb_strlen($mesLlarga)) {
        $mesLlarga = $neta;
    }
}
echo "Paraula més llarga: $mesLlarga (" . mb_strlen($mesLlarga) . " lletres)" . PHP_EOL;

PHP;

$sol3 = $sol2 . <<<'PHP'

if ($argc >= 3) {
    $clau = $argv[2];
    // En majúscules tots dos, «PHP» i «php» són la mateixa paraula
    $conte = str_contains(mb_strtoupper($text), mb_strtoupper($clau));
    echo "Conté «{$clau}»: " . ($conte ? "sí" : "no") . PHP_EOL;
}

PHP;

$text = 'El servidor executa PHP i retorna HTML al navegador.';
$accents = 'Això és una cançó molt llarga, però què hi farem!';

return [
    'id' => 'analitzador-text',
    'ordre' => 130,
    'titol' => 'Analitzador de text',
    'fitxer' => 'analitzador.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.2 Cadenes de text',
    'origen' => [
        'fitxer' => 'Tema 3 - Funcions integrades de PHP.pdf',
        'descripcio' => 'Activitat proposada 2 del Tema 3, «Analitzador de text» (David Porti)',
    ],
    'resum' => 'Donat un paràgraf amb accents, compta caràcters (mb_strlen) i paraules, troba la més llarga i mira si conté una paraula clau (str_contains).',
    'xp_final' => 35,
    'paraules_clau' => ['mb_strlen', 'explode', 'str_contains', 'mb_strtoupper', 'trim', 'accents'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Analitzador de text: php analitzador.php "<text>" [paraula clau]

        PHP,

    'context' => <<<'HTML'
        <p>L'activitat demana analitzar un paràgraf <strong>amb accents</strong>: comptar-ne els caràcters i les paraules,
        trobar la paraula més llarga i dir si conté una paraula clau.</p>
        <p><strong>Adaptació:</strong> el text i la paraula clau arriben com a arguments, perquè les proves puguin fer servir
        textos diferents. El text va entre cometes perquè és un sol argument.</p>
        <pre class="term">$ php analitzador.php "El servidor executa PHP i retorna HTML al navegador." php
        Caràcters: 52
        Paraules: 9
        Paraula més llarga: navegador (9 lletres)
        Conté «php»: sí</pre>
        HTML,

    'resum_apres' => [
        'mb_strlen() compta lletres; strlen() compta bytes, i a UTF-8 una lletra amb accent n\'ocupa 2.',
        'explode() parteix un text en un array, i array_filter() en treu els trossos buits.',
        'trim() amb una llista de caràcters treu la puntuació del principi i del final d\'una paraula.',
        'Per comparar sense distingir majúscules, passa els dos textos a majúscules (mb_strtoupper) abans de str_contains().',
    ],

    'passos' => [
        [
            'titol' => 'Caràcters i paraules',
            'conceptes' => ['cadenes de text', 'mb_strlen', 'explode'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Si no hi ha text (o només hi ha espais), mostra <code>Ús: php analitzador.php "&lt;text&gt;" [paraula clau]</code> i <code>exit(1)</code>.</li>
                  <li>Mostra quants caràcters té el text (sense els espais del principi i del final) amb <code>mb_strlen()</code>:
                      <code>Caràcters: 52</code>.</li>
                  <li>Parteix-lo per espais amb <code>explode()</code> i mostra quantes paraules té: <code>Paraules: 9</code>. Si hi ha
                      dos espais seguits, el tros buit del mig <strong>no</strong> és una paraula.</li>
                </ul>
                <p class="nota">Prova-ho amb <code>"Això és una cançó"</code>: <code>strlen()</code> diria 21 i <code>mb_strlen()</code>, 17.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bmb_strlen\s*\(/', 'missatge' => 'Compta els caràcters amb mb_strlen() (no amb strlen()).'],
                ['regex' => '/\bexplode\s*\(/', 'missatge' => 'Parteix el text en paraules amb explode().'],
            ],
            'proves' => [
                ['nom' => 'Sense text', 'args' => [], 'conte' => ['Ús: php analitzador.php "<text>" [paraula clau]'], 'codi_sortida' => 1],
                ['nom' => 'Text normal', 'args' => [$text], 'conte' => ['Caràcters: 52', 'Paraules: 9']],
                ['nom' => 'Text amb accents', 'args' => [$accents], 'conte' => ['Caràcters: 49', 'Paraules: 10']],
                ['nom' => 'Espais repetits', 'args' => ['  hola   món  '], 'conte' => ['Caràcters: 10', 'Paraules: 2']],
            ],
            'pistes' => [
                'Treu primer els espais de les puntes. Per a les paraules, parteix el text i no comptis els trossos que hagin quedat buits.',
                'trim($argv[1]) neteja les puntes. explode(" ", $text) dona un array; array_filter($array, fn($p) => $p !== "") en treu els buits, i count() et dona el nombre.',
                "\$text = trim(\$argv[1]);\necho \"Caràcters: \" . mb_strlen(\$text) . PHP_EOL;\n\$paraules = array_filter(explode(\" \", \$text), fn(\$p) => \$p !== \"\");",
            ],
            'preguntes' => [
                'Per què strlen("cançó") no dona 5?',
                'Què hi ha a l\'array que retorna explode(" ", "hola   món")?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'mb_strlen() compta lletres i no bytes. array_filter() descarta els trossos buits que deixen els espais seguits.',
        ],
        [
            'titol' => 'La paraula més llarga',
            'conceptes' => ['bucle foreach', 'trim', 'mb_strlen'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> recorre les paraules i troba la més llarga. Abans de comparar-la, treu-li els signes de
                puntuació enganxats (<code>. , ; : ! ? ¡ ¿ « » " ( )</code>) amb <code>trim($paraula, "...")</code>. Si n'hi ha dues
                d'iguals, queda la primera.</p>
                <pre class="term">Paraula més llarga: navegador (9 lletres)</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bforeach\s*\(/', 'missatge' => 'Recorre les paraules amb un foreach.'],
            ],
            'proves' => [
                ['nom' => 'Amb punt al final', 'args' => [$text], 'conte' => ['Paraula més llarga: navegador (9 lletres)']],
                ['nom' => 'Amb accents i coma', 'args' => [$accents], 'conte' => ['Paraula més llarga: llarga (6 lletres)']],
                ['nom' => 'Entre cometes', 'args' => ['Diu «arquitectura» i para.'], 'conte' => ['Paraula més llarga: arquitectura (12 lletres)']],
            ],
            'pistes' => [
                'Guarda la més llarga que has trobat fins ara (comença amb un text buit) i, a cada paraula, mira si la nova és més llarga.',
                'trim($paraula, ".,;:!?¡¿«»\"()") treu aquests caràcters de les puntes. Compara amb mb_strlen(), no amb strlen(): «cançó» té 5 lletres.',
                "\$mesLlarga = \"\";\nforeach (\$paraules as \$paraula) {\n    \$neta = trim(\$paraula, \".,;:!?¡¿«»\\\"()\");\n    if (mb_strlen(\$neta) > mb_strlen(\$mesLlarga)) {\n        // ...\n    }\n}",
            ],
            'preguntes' => [
                'Per què amb > (i no >=) es queda la primera de les paraules iguals?',
                'Què faria trim() si no li passessis el segon argument?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Recorregut amb «la millor fins ara»: només canvia quan en trobem una de més llarga. trim() amb llista neteja la puntuació.',
        ],
        [
            'titol' => 'Una paraula clau',
            'conceptes' => ['str_contains', 'mb_strtoupper'],
            'xp' => 10,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> si hi ha un segon argument, és una paraula clau. Indica si el text la conté amb
                <code>str_contains()</code>, <strong>sense distingir majúscules</strong> (<code>php</code> i <code>PHP</code> són iguals):</p>
                <pre class="term">Conté «php»: sí
                Conté «java»: no</pre>
                <p>Si no hi ha segon argument, no mostris aquesta línia.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bstr_contains\s*\(/', 'missatge' => 'Mira si el text conté la paraula amb str_contains().'],
            ],
            'proves' => [
                ['nom' => 'Clau en minúscules', 'args' => [$text, 'php'], 'conte' => ['Conté «php»: sí']],
                ['nom' => 'Clau que no hi és', 'args' => [$text, 'java'], 'conte' => ['Conté «java»: no']],
                ['nom' => 'Clau amb accent', 'args' => [$accents, 'CANÇÓ'], 'conte' => ['Conté «CANÇÓ»: sí']],
                ['nom' => 'Sense clau', 'args' => [$text], 'no_conte' => ['Conté']],
            ],
            'pistes' => [
                'Només cal fer-ho si $argc indica que hi ha un tercer element. Per no distingir majúscules, posa els dos textos igual abans de comparar.',
                'str_contains($pallissa, $agulla) retorna true o false. mb_strtoupper() passa a majúscules també les lletres amb accent.',
                "if (\$argc >= 3) {\n    \$conte = str_contains(mb_strtoupper(\$text), mb_strtoupper(\$argv[2]));\n    // ...\n}",
            ],
            'preguntes' => [
                'Per què strtoupper("cançó") no funciona bé i mb_strtoupper() sí?',
                'str_contains() és de PHP 8. Com ho hauries fet abans amb strpos()?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Passant els dos textos a majúscules, la comparació ja no depèn de com els ha escrit l\'usuari.',
        ],
    ],
];
