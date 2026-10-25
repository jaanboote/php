<?php
/**
 * Laboratori: Laboratori de frases.
 * Basat en l'Exercici 17 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti).
 * Els passos 1-3 proven les funcions cridant-les directament (append); el pas 4 prova el programa amb l'entrada.
 */

$sol1 = <<<'PHP'
<?php
// Laboratori de frases

function comptarVocals(string $frase): int
{
    $vocals = ['a', 'e', 'i', 'o', 'u'];
    $total = 0;
    for ($i = 0; $i < strlen($frase); $i++) {
        if (in_array(strtolower($frase[$i]), $vocals)) {
            $total++;
        }
    }
    return $total;
}

PHP;

$sol2 = $sol1 . <<<'PHP'

function esPalindrom(string $frase): bool
{
    $neta = strtolower(str_replace(' ', '', $frase));
    return $neta === strrev($neta);
}

PHP;

$sol3 = $sol2 . <<<'PHP'

function invertir(string $frase): string
{
    return strrev($frase);
}

function formatTitol(string $frase): string
{
    return ucwords(strtolower($frase));
}

function amagarVocals(string $frase): string
{
    return str_replace(['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'], '*', $frase);
}

PHP;

$sol4 = $sol3 . <<<'PHP'

// ---- Programa principal ----
$frase = readline("Escriu una frase: ");

echo PHP_EOL . "--- ANÀLISI DE LA FRASE ---" . PHP_EOL;
echo "Longitud:        " . strlen($frase) . " caràcters" . PHP_EOL;
echo "Paraules:        " . str_word_count($frase) . PHP_EOL;
echo "Vocals:          " . comptarVocals($frase) . PHP_EOL;
echo "És palíndrom?    " . (esPalindrom($frase) ? "Sí" : "No") . PHP_EOL;
echo "Invertida:       " . invertir($frase) . PHP_EOL;
echo "Format títol:    " . formatTitol($frase) . PHP_EOL;
echo "Majúscules:      " . strtoupper($frase) . PHP_EOL;
echo "Vocals amagades: " . amagarVocals($frase) . PHP_EOL;

PHP;

return [
    'id' => 'frases',
    'ordre' => 130,
    'titol' => 'Laboratori de frases',
    'fitxer' => 'Exercici17_laboratori_frases.php',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.2 Cadenes de text',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Exercici 17 de «Exercicis de Fonaments PHP» (David Porti)',
    ],
    'resum' => 'Analitza i transforma una frase: vocals, palíndroms, invertir, format títol i amagar vocals, amb funcions pròpies i de PHP.',
    'xp_final' => 40,
    'paraules_clau' => ['strrev', 'str_replace', 'ucwords', 'in_array', 'str_word_count'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Laboratori de frases

        PHP,

    'context' => <<<'HTML'
        <p>Crearem un «laboratori» que analitza i transforma una frase de diverses maneres. Així coneixerem les funcions de strings
        que més es fan servir. <strong>Per simplificar, les frases no porten accents.</strong></p>
        <pre class="term">Escriu una frase: Hola Mon

        --- ANÀLISI DE LA FRASE ---
        Longitud:        8 caràcters
        Paraules:        2
        Vocals:          3
        És palíndrom?    No
        Invertida:       noM aloH
        Format títol:    Hola Mon
        Majúscules:      HOLA MON
        Vocals amagades: H*l* M*n</pre>
        <p class="nota">Als passos 1 a 3, les proves criden les teves funcions directament. El programa principal es fa al pas 4.</p>
        HTML,

    'resum_apres' => [
        'Recórrer un string caràcter a caràcter i comprovar-los amb in_array().',
        'Combinar funcions de cadenes: str_replace(), strtolower(), strrev(), ucwords().',
        'str_replace() accepta un array: substitueix diversos caràcters alhora.',
        'Separar la lògica (funcions que retornen) de la presentació (el programa principal).',
    ],

    'passos' => [
        [
            'titol' => 'comptarVocals()',
            'conceptes' => ['funcions', 'strings com a arrays', 'in_array'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> crea <code>function comptarVocals(string $frase): int</code> que recorri la frase i compti quantes vocals té,
                fent servir un <strong>array de vocals</strong> i <code>in_array()</code>. Ha de comptar també les vocals en majúscula.</p>
                <p class="nota">La prova executa <code>echo comptarVocals("Hola Mon"), ",", comptarVocals("AEIOU aeiou"), ",", comptarVocals("xyz");</code> → <code>3,10,0</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+comptarVocals\s*\(\s*string\s+\$\w+\s*\)\s*:\s*int/i', 'missatge' => 'La signatura ha de ser: function comptarVocals(string $frase): int'],
                ['regex' => '/\bin_array\s*\(/', 'missatge' => 'Fes servir un array de vocals i in_array() (requisit de l\'enunciat).'],
            ],
            'proves' => [
                ['nom' => '"Hola Mon", "AEIOU aeiou" i "xyz"', 'append' => 'echo comptarVocals("Hola Mon"), ",", comptarVocals("AEIOU aeiou"), ",", comptarVocals("xyz");', 'exacte' => '3,10,0'],
            ],
            'pistes' => [
                'Mira els caràcters d\'un en un. Per a cada un, pregunta\'t: és a la llista de vocals? Si sí, suma 1 a un comptador.',
                'for ($i = 0; $i < strlen($frase); $i++) i $frase[$i] et dona cada caràcter. Passa\'l a minúscula amb strtolower() abans de buscar-lo amb in_array().',
                "\$vocals = ['a', 'e', 'i', 'o', 'u'];\n\$total = 0;\nfor (\$i = 0; \$i < strlen(\$frase); \$i++) {\n    if (in_array(strtolower(\$frase[\$i]), \$vocals)) {\n        \$total++;\n    }\n}\nreturn \$total;",
            ],
            'preguntes' => [
                'Com obtens el caràcter de la posició 3 d\'un string?',
                'Per què «A» no es troba amb in_array("A", ["a", "e", ...])?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Passant cada caràcter a minúscula, n\'hi ha prou amb un array de 5 vocals.',
        ],
        [
            'titol' => 'esPalindrom()',
            'conceptes' => ['funcions', 'funcions de cadenes'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>Un palíndrom es llegeix igual d'esquerra a dreta que de dreta a esquerra, sense tenir en compte espais ni majúscules.</p>
                <p><strong>Objectiu:</strong> crea <code>function esPalindrom(string $frase): bool</code>: elimina els espais (<code>str_replace</code>),
                passa la frase a minúscules i compara-la amb la seva inversa (<code>strrev</code>).</p>
                <p class="nota">La prova: <code>var_dump(esPalindrom("Anul la lluna"), esPalindrom("Hola Mon"), esPalindrom("Ana"));</code> → true, false, true.</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+esPalindrom\s*\(\s*string\s+\$\w+\s*\)\s*:\s*bool/i', 'missatge' => 'La signatura ha de ser: function esPalindrom(string $frase): bool'],
                ['regex' => '/\bstrrev\s*\(/', 'missatge' => 'Inverteix el text amb strrev().'],
            ],
            'proves' => [
                ['nom' => '"Anul la lluna", "Hola Mon" i "Ana"', 'append' => 'var_dump(esPalindrom("Anul la lluna"), esPalindrom("Hola Mon"), esPalindrom("Ana"));', 'exacte' => "bool(true)\nbool(false)\nbool(true)"],
            ],
            'pistes' => [
                'Primer prepara el text (sense espais i tot en minúscules) i després compara\'l amb el mateix text del revés.',
                'str_replace(\' \', \'\', $frase) treu els espais. La comparació $a === $b ja és un booleà que pots retornar directament.',
                "\$neta = strtolower(str_replace(' ', '', \$frase));\nreturn \$neta === strrev(\$neta);",
            ],
            'preguntes' => [
                'Què queda d\'«Anul la lluna» si treus els espais i ho passes a minúscules?',
                'Cal un if per retornar true o false?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'La comparació === ja retorna un booleà: no cal cap if.',
        ],
        [
            'titol' => 'invertir(), formatTitol() i amagarVocals()',
            'conceptes' => ['funcions de cadenes', 'str_replace amb arrays'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> crea tres funcions curtes, totes <code>(string $frase): string</code>:</p>
                <ul>
                  <li><code>invertir()</code>: la frase del revés.</li>
                  <li><code>formatTitol()</code>: primera lletra de cada paraula en majúscula i la resta en minúscula (<code>ucwords</code>).</li>
                  <li><code>amagarVocals()</code>: substitueix totes les vocals (minúscules i majúscules) per <code>*</code>.</li>
                </ul>
                <p class="nota">La prova: <code>echo invertir("Hola Mon"), "|", formatTitol("hOLA a tothom"), "|", amagarVocals("Hola Mon"), "|", amagarVocals("ARBRE");</code><br>
                → <code>noM aloH|Hola A Tothom|H*l* M*n|*RBR*</code></p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+invertir\s*\(\s*string\s+\$\w+\s*\)\s*:\s*string/i', 'missatge' => 'Crea function invertir(string $frase): string'],
                ['regex' => '/function\s+formatTitol\s*\(\s*string\s+\$\w+\s*\)\s*:\s*string/i', 'missatge' => 'Crea function formatTitol(string $frase): string'],
                ['regex' => '/function\s+amagarVocals\s*\(\s*string\s+\$\w+\s*\)\s*:\s*string/i', 'missatge' => 'Crea function amagarVocals(string $frase): string'],
            ],
            'proves' => [
                ['nom' => 'Les tres funcions', 'append' => 'echo invertir("Hola Mon"), "|", formatTitol("hOLA a tothom"), "|", amagarVocals("Hola Mon"), "|", amagarVocals("ARBRE");', 'exacte' => 'noM aloH|Hola A Tothom|H*l* M*n|*RBR*'],
            ],
            'pistes' => [
                'Totes tres són d\'una sola línia: només cal triar bé la funció de PHP que fa la feina.',
                'strrev() inverteix. ucwords() posa majúscules però no passa la resta a minúscules: combina-la amb strtolower(). str_replace() accepta un array de coses a buscar.',
                "return str_replace(['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'], '*', \$frase);",
            ],
            'preguntes' => [
                'Què fa ucwords("hOLA") si no passes abans el text a minúscules?',
                'Com pots substituir 10 caràcters diferents amb una sola crida a str_replace()?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'str_replace() amb un array de cerques substitueix totes les vocals d\'un cop.',
        ],
        [
            'titol' => 'L\'anàlisi completa',
            'conceptes' => ['funcions de cadenes', 'funcions'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> al programa principal, demana una frase amb <code>readline("Escriu una frase: ")</code> i mostra'n l'anàlisi
                tal com surt a «De què va?»: longitud (<code>strlen</code>), paraules (<code>str_word_count</code>), el resultat de cada funció i la frase
                en majúscules (<code>strtoupper</code>). A «És palíndrom?» escriu <code>Sí</code> o <code>No</code>.</p>
                <p class="nota">L'alineació no compta. Prova-ho amb <code>Hola Mon</code> i amb <code>Anul la lluna</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bstr_word_count\s*\(/', 'missatge' => 'Compta les paraules amb str_word_count().'],
            ],
            'proves' => [
                ['nom' => '"Hola Mon"', 'stdin' => "Hola Mon\n", 'conte' => [
                    "--- ANÀLISI DE LA FRASE ---\nLongitud: 8 caràcters\nParaules: 2\nVocals: 3\nÉs palíndrom? No\nInvertida: noM aloH\nFormat títol: Hola Mon\nMajúscules: HOLA MON\nVocals amagades: H*l* M*n",
                ]],
                ['nom' => '"Anul la lluna"', 'stdin' => "Anul la lluna\n", 'conte' => ['Paraules: 3', 'Vocals: 5', 'És palíndrom? Sí', 'Format títol: Anul La Lluna']],
            ],
            'pistes' => [
                'Ja tens totes les funcions: ara només cal cridar-les i anar escrivint una línia per a cada resultat.',
                'Per a «És palíndrom?», el ternari esPalindrom($frase) ? "Sí" : "No" converteix el booleà en text. Posa\'l entre parèntesis si el concatenes.',
                "echo \"És palíndrom?    \" . (esPalindrom(\$frase) ? \"Sí\" : \"No\") . PHP_EOL;",
            ],
            'preguntes' => [
                'Què mostra echo true? I echo false? Per què cal el ternari?',
                'Per què cal posar el ternari entre parèntesis quan el concatenes amb el punt?',
            ],
            'solucio' => $sol4,
            'solucio_explicacio' => 'El programa principal només crida funcions i mostra resultats: si un dia cal canviar una anàlisi, es canvia en un sol lloc.',
        ],
    ],
];
