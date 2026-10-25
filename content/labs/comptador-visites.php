<?php
/**
 * Laboratori: Comptador de visites.
 * Basat en l'activitat 1 del Tema 4 (prof. David Porti).
 * Les proves preparen un visites.txt amb visites anteriors i comproven com queda el fitxer.
 */

$sol1 = <<<'PHP'
<?php
// Comptador de visites

const FITXER = "visites.txt";

// FILE_APPEND afegeix al final sense esborrar les visites anteriors
file_put_contents(FITXER, date("d/m/Y H:i:s") . PHP_EOL, FILE_APPEND);
echo "Visita registrada." . PHP_EOL;

PHP;

$sol2 = $sol1 . <<<'PHP'

$visites = file(FITXER, FILE_IGNORE_NEW_LINES);
echo "Total de visites: " . count($visites) . PHP_EOL;

echo "Últimes 10 visites:" . PHP_EOL;
foreach (array_slice($visites, -10) as $visita) {
    echo "  - $visita" . PHP_EOL;
}

PHP;

$sol3 = <<<'PHP'
<?php
// Comptador de visites

const FITXER = "visites.txt";

function registrarVisita(string $fitxer): void
{
    // L'@ evita el Warning: l'error ja el gestionem amb l'excepció
    if (@file_put_contents($fitxer, date("d/m/Y H:i:s") . PHP_EOL, FILE_APPEND) === false) {
        throw new RuntimeException("No es pot escriure al fitxer $fitxer");
    }
}

function llegirVisites(string $fitxer): array
{
    if (!is_file($fitxer) || !is_readable($fitxer)) {
        throw new RuntimeException("No es pot llegir el fitxer $fitxer");
    }
    return file($fitxer, FILE_IGNORE_NEW_LINES);
}

try {
    registrarVisita(FITXER);
    echo "Visita registrada." . PHP_EOL;

    $visites = llegirVisites(FITXER);
    echo "Total de visites: " . count($visites) . PHP_EOL;

    echo "Últimes 10 visites:" . PHP_EOL;
    foreach (array_slice($visites, -10) as $visita) {
        echo "  - $visita" . PHP_EOL;
    }
} catch (RuntimeException $e) {
    error_log($e->getMessage());                                   // detall tècnic al log
    echo "Error: no s'ha pogut registrar la visita." . PHP_EOL;    // missatge amable
}

PHP;

$anteriors = '';
for ($i = 1; $i <= 11; $i++) {
    $anteriors .= sprintf("01/10/2026 10:00:%02d\n", $i);
}
$dues = "30/09/2026 09:15:00\n30/09/2026 18:40:12\n";
$data = '\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}';

return [
    'id' => 'comptador-visites',
    'ordre' => 220,
    'titol' => 'Comptador de visites',
    'fitxer' => 'visites.php',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.2 Fitxers',
    'origen' => [
        'fitxer' => 'Tema 4 - Gestor d\'exepcions fitxers.pdf',
        'descripcio' => 'Activitat 1 del Tema 4, «Comptador de visites» (David Porti)',
    ],
    'resum' => 'Cada execució desa la data i l\'hora a visites.txt i mostra el total i les 10 darreres. Si el fitxer falla, RuntimeException.',
    'xp_final' => 40,
    'paraules_clau' => ['file_put_contents', 'FILE_APPEND', 'file', 'RuntimeException', 'array_slice'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Comptador de visites

        PHP,

    'context' => <<<'HTML'
        <p>Guarda a <code>visites.txt</code> cada accés (data i hora) amb <code>file_put_contents()</code> i mostra el total i les 10 darreres.
        Si el fitxer falla, llança i captura una <code>RuntimeException</code>.</p>
        <p><strong>Prova-ho:</strong> executa'l diverses vegades. Els fitxers d'aquest laboratori <strong>es conserven</strong> entre execucions
        i els veuràs sota la sortida («Carpeta del programa»), amb un botó per buidar-la.</p>
        <pre class="term">Visita registrada.
        Total de visites: 3
        Últimes 10 visites:
          - 25/10/2026 18:02:11
          - 25/10/2026 18:02:30
          - 25/10/2026 18:03:05</pre>
        HTML,

    'resum_apres' => [
        'Afegir línies a un fitxer sense esborrar-lo amb FILE_APPEND.',
        'Llegir un fitxer línia a línia amb file() i FILE_IGNORE_NEW_LINES.',
        'Agafar els últims elements d\'un array amb array_slice($array, -10).',
        'Convertir el false de les funcions de fitxers en una RuntimeException i capturar-la.',
        'Missatge amable per a l\'usuari i detall tècnic al log amb error_log().',
    ],

    'passos' => [
        [
            'titol' => 'Registrar la visita',
            'conceptes' => ['fitxers', 'date'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> afegeix al final de <code>visites.txt</code> una línia amb la data i l'hora actuals en format
                <code>d/m/Y H:i:s</code> (per exemple <code>25/10/2026 18:02:11</code>) i mostra <code>Visita registrada.</code></p>
                <p class="nota">Les proves comproven que el fitxer té la línia nova i que <strong>no s'han esborrat</strong> les visites anteriors.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bfile_put_contents\s*\(/', 'missatge' => 'Desa la visita amb file_put_contents() (requisit de l\'activitat).'],
                ['regex' => '/\bFILE_APPEND\b/', 'missatge' => 'Fes servir FILE_APPEND per no esborrar les visites anteriors.'],
            ],
            'proves' => [
                ['nom' => 'Primera visita (no hi ha fitxer)', 'conte' => ['Visita registrada.'],
                 'fitxer_regex' => ['visites.txt' => ["/^$data\\r?\\n\$/" => 'exactament una línia amb la data i l\'hora (dd/mm/aaaa hh:mm:ss)']]],
                ['nom' => 'Ja hi havia 2 visites', 'fitxers' => ['visites.txt' => $dues], 'conte' => ['Visita registrada.'],
                 'fitxer_conte' => ['visites.txt' => [$dues]],
                 'fitxer_regex' => ['visites.txt' => ["/^(.+\\r?\\n){3}\$/" => '3 línies: les 2 d\'abans i la nova']]],
            ],
            'pistes' => [
                'Has d\'escriure una línia (data + salt de línia) al final del fitxer, sense tocar el que ja hi ha.',
                'date("d/m/Y H:i:s") dona la data. El tercer paràmetre de file_put_contents() pot ser FILE_APPEND.',
                "file_put_contents(\"visites.txt\", date(\"d/m/Y H:i:s\") . PHP_EOL, FILE_APPEND);",
            ],
            'preguntes' => [
                'Què passaria amb les visites anteriors si no fessis servir FILE_APPEND?',
                'Per què cal posar PHP_EOL al final de cada visita?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'FILE_APPEND afegeix al final i, si el fitxer no existeix, el crea.',
        ],
        [
            'titol' => 'Total i 10 darreres',
            'conceptes' => ['fitxers', 'funcions d\'arrays'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> llegeix el fitxer amb <code>file(..., FILE_IGNORE_NEW_LINES)</code> i mostra el total i les 10 darreres visites:</p>
                <pre class="term">Total de visites: 12
                Últimes 10 visites:
                  - 01/10/2026 10:00:03
                  ...</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bfile\s*\(/', 'missatge' => 'Llegeix les línies amb file().'],
                ['regex' => '/\barray_slice\s*\(/', 'missatge' => 'Agafa les 10 darreres amb array_slice().'],
            ],
            'proves' => [
                ['nom' => 'Ja hi havia 11 visites', 'fitxers' => ['visites.txt' => $anteriors],
                 'conte' => ['Total de visites: 12', 'Últimes 10 visites:', '- 01/10/2026 10:00:03', '- 01/10/2026 10:00:11'],
                 'no_conte' => ['10:00:01', '10:00:02']],
                ['nom' => 'Primera visita', 'conte' => ['Total de visites: 1'],
                 'conte_regex' => ["/- $data/" => 'la visita nova a la llista']],
            ],
            'pistes' => [
                'Després de registrar, torna a llegir el fitxer: cada línia és una visita.',
                'count() dona el total. array_slice($visites, -10) dona els 10 últims elements (si n\'hi ha menys, tots).',
                "\$visites = file(\"visites.txt\", FILE_IGNORE_NEW_LINES);\necho \"Total de visites: \" . count(\$visites) . PHP_EOL;\nforeach (array_slice(\$visites, -10) as \$visita) { ... }",
            ],
            'preguntes' => [
                'Què fa un número negatiu com a segon paràmetre d\'array_slice()?',
                'Per què FILE_IGNORE_NEW_LINES?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'array_slice amb -10 compta des del final de l\'array.',
        ],
        [
            'titol' => 'Si el fitxer falla: RuntimeException',
            'conceptes' => ['excepcions amb fitxers', 'try / catch / finally'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><code>file_put_contents()</code> i <code>file()</code> no llancen excepcions: retornen <code>false</code> i un Warning.</p>
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Crea <code>registrarVisita(string $fitxer): void</code> i <code>llegirVisites(string $fitxer): array</code>, que llancin
                  <code>RuntimeException</code> si no poden escriure o llegir.</li>
                  <li>Al programa principal, captura-la: mostra <code>Error: no s'ha pogut registrar la visita.</code> i desa el detall amb
                  <code>error_log($e-&gt;getMessage())</code> (anirà a <code>php_errors.log</code>).</li>
                  <li>El programa no ha de mostrar cap Warning de PHP (fes servir <code>@</code> o comprova abans).</li>
                </ul>
                <p class="nota">Per simular l'error, una prova crea una <strong>carpeta</strong> anomenada <code>visites.txt</code>: no s'hi pot escriure com si fos un fitxer.</p>
                HTML,
            'codi' => [
                ['regex' => '/throw\s+new\s+\\\\?RuntimeException/', 'missatge' => 'Llança una RuntimeException quan el fitxer falla.'],
                ['regex' => '/catch\s*\(\s*\\\\?RuntimeException\s+\$\w+\s*\)/', 'missatge' => 'Captura-la amb catch (RuntimeException $e).'],
                ['regex' => '/\berror_log\s*\(/', 'missatge' => 'Desa el detall de l\'error amb error_log().'],
            ],
            'proves' => [
                ['nom' => 'No es pot escriure (visites.txt és una carpeta)', 'fitxers' => ['visites.txt/' => ''],
                 'conte' => ["Error: no s'ha pogut registrar la visita."], 'no_conte' => ['Total de visites'],
                 'fitxer_regex' => ['php_errors.log' => ['/visites\.txt/' => 'el detall de l\'error, amb el nom del fitxer']]],
                ['nom' => 'Funcionament normal', 'fitxers' => ['visites.txt' => $dues], 'conte' => ['Visita registrada.', 'Total de visites: 3'], 'no_conte' => ['Error']],
            ],
            'pistes' => [
                'Mou l\'escriptura i la lectura a dues funcions. Cada funció comprova si l\'operació ha funcionat i, si no, llança l\'excepció.',
                'file_put_contents() retorna false si falla: compara amb === false. Posa @ davant per evitar el Warning. Per llegir, comprova abans is_file() i is_readable().',
                "function registrarVisita(string \$fitxer): void\n{\n    if (@file_put_contents(\$fitxer, date(\"d/m/Y H:i:s\") . PHP_EOL, FILE_APPEND) === false) {\n        throw new RuntimeException(\"No es pot escriure al fitxer \$fitxer\");\n    }\n}",
            ],
            'preguntes' => [
                'Què retorna file_put_contents() quan no pot escriure?',
                'Per què el missatge per a l\'usuari i el del log són diferents?',
                'Què fa l\'@ davant d\'una funció? Quan està justificat fer-lo servir?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Les funcions converteixen el false en una excepció. El programa principal decideix: missatge amable a l\'usuari i detall al log.',
        ],
    ],
];
