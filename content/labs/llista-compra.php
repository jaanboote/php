<?php
/**
 * Laboratori: Llista de la compra en un fitxer.
 * Adaptació de l'Exercici 11 d'«Exercicis de Fonaments PHP» (Exercicis 01, prof. David Porti) als continguts del
 * Tema 4: la llista es desa a compra.txt i els errors es gestionen amb excepcions.
 */

$sol1 = <<<'PHP'
<?php
// Llista de la compra: php compra.php <afegeix|llista> [producte]

$fitxer = "compra.txt";

// Un producte per línia; si el fitxer encara no existeix, la llista és buida
function llegeixLlista(string $fitxer): array
{
    if (!file_exists($fitxer)) {
        return [];
    }
    return file($fitxer, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

if ($argc < 2) {
    echo "ERROR: cal indicar una comanda." . PHP_EOL;
    exit(1);
}

switch ($argv[1]) {
    case "afegeix":
        if ($argc < 3 || trim($argv[2]) === "") {
            echo "ERROR: falta el producte." . PHP_EOL;
            exit(1);
        }
        // Pa i pa són el mateix producte
        $producte = strtolower(trim($argv[2]));
        if (in_array($producte, llegeixLlista($fitxer))) {
            echo "'$producte' ja és a la llista." . PHP_EOL;
            break;
        }
        file_put_contents($fitxer, $producte . PHP_EOL, FILE_APPEND);
        echo "Afegit: $producte" . PHP_EOL;
        break;
    case "llista":
        $llista = llegeixLlista($fitxer);
        if (count($llista) == 0) {
            echo "La llista és buida." . PHP_EOL;
            break;
        }
        sort($llista);
        echo "--- LLISTA DE LA COMPRA ---" . PHP_EOL;
        foreach ($llista as $index => $producte) {
            echo ($index + 1) . ". " . ucfirst($producte) . PHP_EOL;
        }
        echo "Total de productes: " . count($llista) . PHP_EOL;
        break;
    default:
        echo "ERROR: la comanda \"{$argv[1]}\" no existeix." . PHP_EOL;
        exit(1);
}

PHP;

$sol2 = str_replace(
    "    default:\n",
    <<<'PHP'
        case "treu":
            if ($argc < 3 || trim($argv[2]) === "") {
                echo "ERROR: falta el producte." . PHP_EOL;
                exit(1);
            }
            $producte = strtolower(trim($argv[2]));
            $llista = llegeixLlista($fitxer);
            $posicio = array_search($producte, $llista);
            if ($posicio === false) {
                echo "'$producte' no és a la llista." . PHP_EOL;
                break;
            }
            unset($llista[$posicio]);
            // Es torna a escriure el fitxer sencer, sense el producte tret
            file_put_contents($fitxer, count($llista) > 0 ? implode(PHP_EOL, $llista) . PHP_EOL : "");
            echo "Tret: $producte" . PHP_EOL;
            break;
        default:

    PHP,
    str_replace('<afegeix|llista>', '<afegeix|treu|llista>', $sol1)
);

$sol3 = <<<'PHP'
<?php
// Llista de la compra: php compra.php <afegeix|treu|llista> [producte]

$fitxer = "compra.txt";

function llegeixLlista(string $fitxer): array
{
    if (!file_exists($fitxer)) {
        return [];
    }
    if (!is_file($fitxer)) {
        throw new RuntimeException("No es pot llegir $fitxer.");
    }
    $linies = @file($fitxer, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linies === false) {
        throw new RuntimeException("No es pot llegir $fitxer.");
    }
    return $linies;
}

function escriuLlista(string $fitxer, string $contingut, int $opcions = 0): void
{
    if (@file_put_contents($fitxer, $contingut, $opcions) === false) {
        throw new RuntimeException("No es pot escriure a $fitxer.");
    }
}

function producte(int $argc, array $argv): string
{
    if ($argc < 3 || trim($argv[2]) === "") {
        throw new InvalidArgumentException("falta el producte.");
    }
    return strtolower(trim($argv[2]));
}

try {
    if ($argc < 2) {
        throw new InvalidArgumentException("cal indicar una comanda.");
    }
    switch ($argv[1]) {
        case "afegeix":
            $producte = producte($argc, $argv);
            if (in_array($producte, llegeixLlista($fitxer))) {
                echo "'$producte' ja és a la llista." . PHP_EOL;
                break;
            }
            escriuLlista($fitxer, $producte . PHP_EOL, FILE_APPEND);
            echo "Afegit: $producte" . PHP_EOL;
            break;
        case "treu":
            $producte = producte($argc, $argv);
            $llista = llegeixLlista($fitxer);
            $posicio = array_search($producte, $llista);
            if ($posicio === false) {
                echo "'$producte' no és a la llista." . PHP_EOL;
                break;
            }
            unset($llista[$posicio]);
            escriuLlista($fitxer, count($llista) > 0 ? implode(PHP_EOL, $llista) . PHP_EOL : "");
            echo "Tret: $producte" . PHP_EOL;
            break;
        case "llista":
            $llista = llegeixLlista($fitxer);
            if (count($llista) == 0) {
                echo "La llista és buida." . PHP_EOL;
                break;
            }
            sort($llista);
            echo "--- LLISTA DE LA COMPRA ---" . PHP_EOL;
            foreach ($llista as $index => $producte) {
                echo ($index + 1) . ". " . ucfirst($producte) . PHP_EOL;
            }
            echo "Total de productes: " . count($llista) . PHP_EOL;
            break;
        default:
            throw new InvalidArgumentException("la comanda \"{$argv[1]}\" no existeix.");
    }
} catch (InvalidArgumentException $e) {
    // Error de qui fa servir el programa
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
} catch (RuntimeException $e) {
    // Error del sistema: el fitxer
    echo "ERROR DE FITXER: " . $e->getMessage() . PHP_EOL;
    exit(2);
}

PHP;

$tres = "pa\nllet\nous\n";

return [
    'id' => 'llista-compra',
    'ordre' => 230,
    'titol' => 'Llista de la compra en un fitxer',
    'fitxer' => 'compra.php',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.1 Excepcions · 4.2 Fitxers',
    'origen' => [
        'fitxer' => 'Exercicis 01B.Fonaments bàsics PHP (Enunciat).pdf',
        'descripcio' => 'Adaptació de l\'Exercici 11 de «Exercicis de Fonaments PHP» (David Porti) als fitxers i les excepcions del Tema 4',
    ],
    'resum' => 'La llista de la compra de l\'Exercici 11, però desada a compra.txt: afegeix, treu i llista productes, i gestiona els errors amb excepcions.',
    'xp_final' => 45,
    'paraules_clau' => ['file', 'file_put_contents', 'FILE_APPEND', 'in_array', 'InvalidArgumentException', 'RuntimeException'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Llista de la compra: php compra.php <afegeix|llista> [producte]

        PHP,

    'context' => <<<'HTML'
        <p>A l'Exercici 11 la llista de la compra s'esborrava en acabar el programa. Ara la desarem a <code>compra.txt</code>,
        <strong>un producte per línia</strong>, i cada execució farà una sola cosa, com una eina de terminal.</p>
        <p><strong>Adaptació:</strong> en lloc de demanar els productes amb <code>readline()</code>, arriben com a arguments.
        Els fitxers d'aquest laboratori es conserven entre execucions.</p>
        <pre class="term">$ php compra.php afegeix Pa
        Afegit: pa
        $ php compra.php afegeix llet
        Afegit: llet
        $ php compra.php afegeix pa
        'pa' ja és a la llista.
        $ php compra.php llista
        --- LLISTA DE LA COMPRA ---
        1. Llet
        2. Pa
        Total de productes: 2</pre>
        HTML,

    'resum_apres' => [
        'file() amb FILE_IGNORE_NEW_LINES llegeix un fitxer com un array de línies.',
        'file_put_contents() amb FILE_APPEND afegeix al final; sense, substitueix tot el contingut.',
        'Per treure una línia, es llegeix tot, es modifica l\'array i es torna a escriure el fitxer.',
        'InvalidArgumentException per als errors de qui fa servir el programa, RuntimeException per als del sistema, i un catch per a cadascuna.',
    ],

    'passos' => [
        [
            'titol' => 'Afegir i llistar',
            'conceptes' => ['fitxers', 'file', 'file_put_contents', 'in_array'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Crea una funció <code>llegeixLlista($fitxer)</code>: si el fitxer no existeix, retorna <code>[]</code>; si
                      existeix, el llegeix amb <code>file($fitxer, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)</code>.</li>
                  <li><code>afegeix &lt;producte&gt;</code>: neteja el producte amb <code>trim()</code> i passa'l a minúscules. Si ja
                      hi és (<code>in_array</code>), mostra <code>'pa' ja és a la llista.</code>; si no, afegeix-lo al final de
                      <code>compra.txt</code> amb <code>FILE_APPEND</code> i mostra <code>Afegit: pa</code>.</li>
                  <li><code>llista</code>: si està buida, <code>La llista és buida.</code> Si no, ordena-la amb <code>sort()</code> i
                      mostra-la numerada des de l'1, amb la primera lletra en majúscula (<code>ucfirst</code>):</li>
                </ul>
                <pre class="term">--- LLISTA DE LA COMPRA ---
                1. Llet
                2. Ous
                3. Pa
                Total de productes: 3</pre>
                <p>Errors (tots amb <code>exit(1)</code>): sense comanda, <code>ERROR: cal indicar una comanda.</code>; sense producte,
                <code>ERROR: falta el producte.</code>; i una comanda que no existeix, <code>ERROR: la comanda "x" no existeix.</code></p>
                HTML,
            'codi' => [
                ['regex' => '/\bfile\s*\(/', 'missatge' => 'Llegeix la llista amb file().'],
                ['regex' => '/FILE_APPEND/', 'missatge' => 'Afegeix els productes al final amb FILE_APPEND.'],
                ['regex' => '/\bin_array\s*\(/', 'missatge' => 'Comprova els repetits amb in_array().'],
            ],
            'proves' => [
                ['nom' => 'Afegir el primer producte', 'args' => ['afegeix', 'Pa'], 'conte' => ['Afegit: pa'], 'fitxer_conte' => ['compra.txt' => ['pa']]],
                ['nom' => 'Afegir-ne un altre', 'args' => ['afegeix', 'llet'], 'fitxers' => ['compra.txt' => "pa\n"], 'conte' => ['Afegit: llet'], 'fitxer_conte' => ['compra.txt' => ['pa', 'llet']]],
                ['nom' => 'Repetit amb majúscules', 'args' => ['afegeix', ' PA '], 'fitxers' => ['compra.txt' => "pa\nllet\n"], 'conte' => ["'pa' ja és a la llista."], 'no_conte' => ['Afegit']],
                ['nom' => 'Llistar', 'args' => ['llista'], 'fitxers' => ['compra.txt' => $tres], 'conte' => ['--- LLISTA DE LA COMPRA ---', '1. Llet', '2. Ous', '3. Pa', 'Total de productes: 3']],
                ['nom' => 'Llista sense fitxer', 'args' => ['llista'], 'conte' => ['La llista és buida.']],
                ['nom' => 'Sense comanda', 'args' => [], 'conte' => ['ERROR: cal indicar una comanda.'], 'codi_sortida' => 1],
                ['nom' => 'Sense producte', 'args' => ['afegeix'], 'conte' => ['ERROR: falta el producte.'], 'codi_sortida' => 1],
                ['nom' => 'Comanda desconeguda', 'args' => ['compra'], 'conte' => ['ERROR: la comanda "compra" no existeix.'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Cada execució fa una cosa: decideix-la amb un switch sobre $argv[1]. El fitxer és la «memòria» entre execucions.',
                'file_exists() diu si el fitxer hi és. file_put_contents($fitxer, $producte . PHP_EOL, FILE_APPEND) afegeix una línia. Per numerar des de l\'1, mostra $index + 1.',
                "function llegeixLlista(string \$fitxer): array\n{\n    if (!file_exists(\$fitxer)) {\n        return [];\n    }\n    return file(\$fitxer, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);\n}",
            ],
            'preguntes' => [
                'Què faria file_put_contents() sense FILE_APPEND?',
                'Per què cal FILE_IGNORE_NEW_LINES perquè in_array("pa", ...) trobi el producte?',
                'Per què passem el producte a minúscules abans de desar-lo?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'El fitxer guarda un producte per línia: file() el converteix en array i FILE_APPEND hi afegeix línies sense esborrar les altres.',
        ],
        [
            'titol' => 'Treure un producte',
            'conceptes' => ['fitxers', 'array_search', 'implode'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Per treure una línia d'un fitxer no hi ha cap funció directa: es llegeix tot, es modifica l'array i es torna a
                escriure sencer.</p>
                <p><strong>Objectiu:</strong> afegeix la comanda <code>treu &lt;producte&gt;</code>:</p>
                <ul>
                  <li>Busca'l amb <code>array_search()</code>. Si no hi és: <code>'pa' no és a la llista.</code></li>
                  <li>Si hi és, treu-lo amb <code>unset()</code>, torna a escriure <code>compra.txt</code> (sense <code>FILE_APPEND</code>)
                      amb <code>implode(PHP_EOL, ...)</code> i mostra <code>Tret: pa</code>.</li>
                  <li>Sense producte: <code>ERROR: falta el producte.</code> i <code>exit(1)</code>.</li>
                </ul>
                HTML,
            'codi' => [
                ['regex' => '/\bimplode\s*\(/', 'missatge' => 'Torna a muntar el contingut del fitxer amb implode().'],
            ],
            'proves' => [
                ['nom' => 'Treure un producte', 'args' => ['treu', 'Llet'], 'fitxers' => ['compra.txt' => $tres], 'conte' => ['Tret: llet'],
                 'fitxer_regex' => ['compra.txt' => ['/^pa\R+ous\R*$/' => 'només pa i ous, un per línia']]],
                ['nom' => 'Treure l\'últim', 'args' => ['treu', 'pa'], 'fitxers' => ['compra.txt' => "pa\n"], 'conte' => ['Tret: pa'],
                 'fitxer_regex' => ['compra.txt' => ['/^\s*$/' => 'quedar buit']]],
                ['nom' => 'Un producte que no hi és', 'args' => ['treu', 'formatge'], 'fitxers' => ['compra.txt' => $tres], 'conte' => ["'formatge' no és a la llista."]],
                ['nom' => 'Sense producte', 'args' => ['treu'], 'conte' => ['ERROR: falta el producte.'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Llegeix la llista amb la funció que ja tens, treu l\'element de l\'array i escriu el fitxer de nou, sencer.',
                'array_search($producte, $llista) retorna la posició o false (compara amb ===). unset($llista[$posicio]) el treu.',
                "\$posicio = array_search(\$producte, \$llista);\nif (\$posicio === false) {\n    // ...\n}\nunset(\$llista[\$posicio]);\nfile_put_contents(\$fitxer, implode(PHP_EOL, \$llista) . PHP_EOL);",
            ],
            'preguntes' => [
                'Per què cal comparar array_search() amb === false i no amb == false?',
                'Què quedaria al fitxer si fessis servir FILE_APPEND per tornar-lo a escriure?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Llegir, modificar l\'array i reescriure: és la manera de canviar una línia d\'un fitxer de text.',
        ],
        [
            'titol' => 'Errors amb excepcions',
            'conceptes' => ['excepcions', 'InvalidArgumentException', 'RuntimeException', 'try / catch'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Fins ara cada error feia <code>echo</code> i <code>exit</code> allà on passava. Amb excepcions, el codi que detecta
                l'error només el <strong>llança</strong>, i un sol lloc decideix què fer-ne.</p>
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Posa tot el programa dins d'un <code>try</code>.</li>
                  <li>Els errors de qui fa servir el programa (sense comanda, sense producte, comanda desconeguda) llancen una
                      <code>InvalidArgumentException</code> amb el mateix text d'abans (sense l'<code>ERROR: </code>).</li>
                  <li>Si el fitxer no es pot llegir o escriure, llança una <code>RuntimeException</code>:
                      <code>No es pot llegir compra.txt.</code> o <code>No es pot escriure a compra.txt.</code>
                      Fes servir <code>@</code> davant de <code>file()</code> i <code>file_put_contents()</code> i comprova si retornen <code>false</code>.</li>
                  <li>Captura-les per separat:</li>
                </ul>
                <pre class="term">catch (InvalidArgumentException $e)  →  ERROR: falta el producte.            i exit(1)
                catch (RuntimeException $e)          →  ERROR DE FITXER: No es pot llegir compra.txt.   i exit(2)</pre>
                <p class="nota">Les proves fan que <code>compra.txt</code> sigui una <strong>carpeta</strong>: és la manera de provocar
                que el fitxer no es pugui llegir. Mira-ho amb <code>is_file()</code> abans de llegir-lo.</p>
                HTML,
            'codi' => [
                ['regex' => '/throw\s+new\s+\\\\?InvalidArgumentException/', 'missatge' => 'Llança una InvalidArgumentException per als errors d\'ús.'],
                ['regex' => '/throw\s+new\s+\\\\?RuntimeException/', 'missatge' => 'Llança una RuntimeException quan el fitxer falla.'],
                ['regex' => '/catch\s*\(\s*\\\\?InvalidArgumentException\s+\$\w+\s*\)/', 'missatge' => 'Captura la InvalidArgumentException.'],
                ['regex' => '/catch\s*\(\s*\\\\?RuntimeException\s+\$\w+\s*\)/', 'missatge' => 'Captura la RuntimeException en un catch a part.'],
            ],
            'proves' => [
                ['nom' => 'compra.txt és una carpeta (afegeix)', 'args' => ['afegeix', 'pa'], 'fitxers' => ['compra.txt/' => ''],
                 'conte' => ['ERROR DE FITXER: No es pot llegir compra.txt.'], 'codi_sortida' => 2],
                ['nom' => 'compra.txt és una carpeta (llista)', 'args' => ['llista'], 'fitxers' => ['compra.txt/' => ''],
                 'conte' => ['ERROR DE FITXER: No es pot llegir compra.txt.'], 'codi_sortida' => 2],
                ['nom' => 'Sense producte', 'args' => ['afegeix'], 'conte' => ['ERROR: falta el producte.'], 'codi_sortida' => 1],
                ['nom' => 'Comanda desconeguda', 'args' => ['compra'], 'conte' => ['ERROR: la comanda "compra" no existeix.'], 'codi_sortida' => 1],
                ['nom' => 'Tot continua funcionant', 'args' => ['llista'], 'fitxers' => ['compra.txt' => $tres], 'conte' => ['1. Llet', 'Total de productes: 3']],
            ],
            'pistes' => [
                'Substitueix cada echo d\'error + exit per un throw. Al final de tot, dos catch: un per a cada tipus d\'excepció, cadascun amb el seu prefix i codi de sortida.',
                'A llegeixLlista: si file_exists() però !is_file(), throw new RuntimeException("No es pot llegir $fitxer."). Per escriure: if (@file_put_contents(...) === false) throw ...',
                "try {\n    // ... tot el programa ...\n} catch (InvalidArgumentException \$e) {\n    echo \"ERROR: \" . \$e->getMessage() . PHP_EOL;\n    exit(1);\n} catch (RuntimeException \$e) {\n    echo \"ERROR DE FITXER: \" . \$e->getMessage() . PHP_EOL;\n    exit(2);\n}",
            ],
            'preguntes' => [
                'Per què tenen codis de sortida diferents (1 i 2) els dos tipus d\'error?',
                'Què fa la @ davant de file_put_contents() i per què aquí és acceptable fer-la servir?',
                'InvalidArgumentException és filla de LogicException. Si l\'ordre dels catch fos un altre, canviaria res?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Les funcions només llancen excepcions; el programa principal les captura totes en un lloc i tria el missatge i el codi de sortida segons el tipus.',
        ],
    ],
];
