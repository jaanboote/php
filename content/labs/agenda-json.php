<?php
/**
 * Laboratori: Agenda en JSON.
 * Basat en l'activitat 3 del Tema 4 (prof. David Porti). Adaptació: les ordres arriben com a arguments
 * (php agenda.php llista / afegeix <nom> <telèfon>), com a l'eina htaccess.php d'Exercicis 02.
 */

$sol1 = <<<'PHP'
<?php
// Agenda en JSON
// Ús: php agenda.php llista | afegeix <nom> <telèfon>

const FITXER = "contactes.json";

function llegirContactes(string $fitxer): array
{
    if (!file_exists($fitxer)) {
        return [];   // encara no hi ha agenda
    }
    return json_decode(file_get_contents($fitxer), true, 512, JSON_THROW_ON_ERROR);
}

$contactes = llegirContactes(FITXER);
if (count($contactes) === 0) {
    echo "L'agenda és buida." . PHP_EOL;
} else {
    echo "Contactes (" . count($contactes) . "):" . PHP_EOL;
    foreach ($contactes as $c) {
        echo "- {$c['nom']}: {$c['telefon']}" . PHP_EOL;
    }
}

PHP;

$sol2 = <<<'PHP'
<?php
// Agenda en JSON
// Ús: php agenda.php llista | afegeix <nom> <telèfon>

const FITXER = "contactes.json";

function mostraAjuda(): void
{
    echo "Ús: php agenda.php llista" . PHP_EOL;
    echo "    php agenda.php afegeix <nom> <telèfon>" . PHP_EOL;
}

function llegirContactes(string $fitxer): array
{
    if (!file_exists($fitxer)) {
        return [];   // encara no hi ha agenda
    }
    return json_decode(file_get_contents($fitxer), true, 512, JSON_THROW_ON_ERROR);
}

function desarContactes(string $fitxer, array $contactes): void
{
    file_put_contents($fitxer, json_encode($contactes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$ordre = $argv[1] ?? '';

switch ($ordre) {
    case 'llista':
        $contactes = llegirContactes(FITXER);
        if (count($contactes) === 0) {
            echo "L'agenda és buida." . PHP_EOL;
        } else {
            echo "Contactes (" . count($contactes) . "):" . PHP_EOL;
            foreach ($contactes as $c) {
                echo "- {$c['nom']}: {$c['telefon']}" . PHP_EOL;
            }
        }
        break;

    case 'afegeix':
        if ($argc !== 4) {
            mostraAjuda();
            exit(1);
        }
        $contactes = llegirContactes(FITXER);
        $contactes[] = ["nom" => $argv[2], "telefon" => $argv[3]];
        desarContactes(FITXER, $contactes);
        echo "Contacte afegit: {$argv[2]} ({$argv[3]})" . PHP_EOL;
        break;

    default:
        mostraAjuda();
        exit(1);
}

PHP;

$sol3 = <<<'PHP'
<?php
// Agenda en JSON
// Ús: php agenda.php llista | afegeix <nom> <telèfon>

const FITXER = "contactes.json";

function mostraAjuda(): void
{
    echo "Ús: php agenda.php llista" . PHP_EOL;
    echo "    php agenda.php afegeix <nom> <telèfon>" . PHP_EOL;
}

function llegirContactes(string $fitxer): array
{
    if (!file_exists($fitxer)) {
        return [];   // encara no hi ha agenda
    }
    if (!is_file($fitxer) || !is_readable($fitxer)) {
        throw new RuntimeException("No es pot llegir $fitxer");
    }
    // Si el JSON està malmès, llança JsonException (i no sobreescrivim res)
    return json_decode(file_get_contents($fitxer), true, 512, JSON_THROW_ON_ERROR);
}

function desarContactes(string $fitxer, array $contactes): void
{
    $json = json_encode($contactes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    if (@file_put_contents($fitxer, $json) === false) {
        throw new RuntimeException("No es pot escriure $fitxer");
    }
}

$ordre = $argv[1] ?? '';

try {
    switch ($ordre) {
        case 'llista':
            $contactes = llegirContactes(FITXER);
            if (count($contactes) === 0) {
                echo "L'agenda és buida." . PHP_EOL;
            } else {
                echo "Contactes (" . count($contactes) . "):" . PHP_EOL;
                foreach ($contactes as $c) {
                    echo "- {$c['nom']}: {$c['telefon']}" . PHP_EOL;
                }
            }
            break;

        case 'afegeix':
            if ($argc !== 4) {
                mostraAjuda();
                exit(1);
            }
            $contactes = llegirContactes(FITXER);
            $contactes[] = ["nom" => $argv[2], "telefon" => $argv[3]];
            desarContactes(FITXER, $contactes);
            echo "Contacte afegit: {$argv[2]} ({$argv[3]})" . PHP_EOL;
            break;

        default:
            mostraAjuda();
            exit(1);
    }
} catch (JsonException $e) {
    echo "El fitxer " . FITXER . " està malmès: " . $e->getMessage() . PHP_EOL;
    exit(2);
} catch (RuntimeException $e) {
    error_log($e->getMessage());
    echo "Error: no s'ha pogut accedir a l'agenda." . PHP_EOL;
    exit(3);
}

PHP;

$dos = json_encode([["nom" => "Marta", "telefon" => "600111222"], ["nom" => "Jordi", "telefon" => "611333444"]], JSON_PRETTY_PRINT);
$malmes = '[{"nom": "Marta", "telefon": "600111222"},';

return [
    'id' => 'agenda-json',
    'ordre' => 230,
    'titol' => 'Agenda en JSON',
    'fitxer' => 'agenda.php',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.2 Fitxers i JSON',
    'origen' => [
        'fitxer' => 'Tema 4 - Gestor d\'exepcions fitxers.pdf',
        'descripcio' => 'Activitat 3 del Tema 4, «Agenda en JSON» (David Porti)',
    ],
    'resum' => 'Un programa CLI que afegeix i llista contactes a contactes.json, amb JSON_THROW_ON_ERROR i un catch diferent per a JsonException i RuntimeException.',
    'xp_final' => 50,
    'paraules_clau' => ['json_encode', 'json_decode', 'JsonException', 'JSON_THROW_ON_ERROR', 'RuntimeException'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Agenda en JSON
        // Ús: php agenda.php llista | afegeix <nom> <telèfon>

        PHP,

    'context' => <<<'HTML'
        <p>Programa CLI que afegeix i llista contactes a <code>contactes.json</code>. Fa servir <code>JSON_THROW_ON_ERROR</code> i un
        <code>catch</code> diferent per a <code>JsonException</code> i <code>RuntimeException</code>.</p>
        <p><strong>Adaptació:</strong> les ordres arriben com a arguments, com a l'eina <code>htaccess.php</code> d'Exercicis 02.</p>
        <pre class="term">$ php agenda.php afegeix Anna 622555666
        Contacte afegit: Anna (622555666)

        $ php agenda.php llista
        Contactes (1):
        - Anna: 622555666</pre>
        <p class="nota">Els fitxers d'aquest laboratori es conserven entre execucions: veuràs com creix <code>contactes.json</code>.</p>
        HTML,

    'resum_apres' => [
        'Desar dades estructurades en un fitxer amb json_encode() (PRETTY_PRINT, UNESCAPED_UNICODE).',
        'Recuperar-les amb json_decode(..., true) com a array associatiu.',
        'Detectar un JSON malmès amb JSON_THROW_ON_ERROR i JsonException, sense sobreescriure\'l.',
        'Tenir catch diferents per a errors diferents (dades malmeses / no es pot accedir al fitxer).',
    ],

    'passos' => [
        [
            'titol' => 'Llegir i llistar',
            'conceptes' => ['JSON', 'fitxers'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>L'agenda es desa com una llista d'objectes: <code>[{"nom": "Marta", "telefon": "600111222"}, ...]</code>.</p>
                <p><strong>Objectiu:</strong> crea <code>llegirContactes(string $fitxer): array</code>. Si el fitxer no existeix, retorna <code>[]</code>;
                si existeix, el descodifica amb <code>json_decode(..., true, 512, JSON_THROW_ON_ERROR)</code>. Després mostra els contactes:</p>
                <pre class="term">Contactes (2):
                - Marta: 600111222
                - Jordi: 611333444</pre>
                <p>o <code>L'agenda és buida.</code> si no n'hi ha cap.</p>
                <p class="nota">De moment, el programa sempre llista (les proves l'executen amb <code>llista</code>).</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+llegirContactes\s*\(\s*string\s+\$\w+\s*\)\s*:\s*array/i', 'missatge' => 'Crea function llegirContactes(string $fitxer): array'],
                ['regex' => '/\bJSON_THROW_ON_ERROR\b/', 'missatge' => 'Descodifica amb JSON_THROW_ON_ERROR (requisit de l\'activitat).'],
            ],
            'proves' => [
                ['nom' => 'Sense fitxer', 'args' => ['llista'], 'conte' => ["L'agenda és buida."]],
                ['nom' => 'Amb 2 contactes', 'args' => ['llista'], 'fitxers' => ['contactes.json' => $dos],
                 'conte' => ["Contactes (2):\n- Marta: 600111222\n- Jordi: 611333444"]],
                ['nom' => 'Fitxer amb una llista buida', 'args' => ['llista'], 'fitxers' => ['contactes.json' => '[]'], 'conte' => ["L'agenda és buida."]],
            ],
            'pistes' => [
                'Si el fitxer no existeix, l\'agenda és buida. Si existeix, llegeix-lo i converteix el JSON en un array.',
                'file_exists() per saber si hi és. json_decode($text, true, 512, JSON_THROW_ON_ERROR): true perquè retorni arrays associatius.',
                "if (!file_exists(\$fitxer)) {\n    return [];\n}\nreturn json_decode(file_get_contents(\$fitxer), true, 512, JSON_THROW_ON_ERROR);",
            ],
            'preguntes' => [
                'Què retorna json_decode() si no li passes el segon paràmetre true?',
                'Per què és important distingir «no hi ha fitxer» de «el fitxer està malmès»?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Sense fitxer, l\'agenda simplement està buida. Amb fitxer, json_decode amb true dona un array de contactes.',
        ],
        [
            'titol' => 'Afegir contactes',
            'conceptes' => ['JSON', '$argv i $argc', 'switch'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> llegeix l'ordre de <code>$argv[1]</code> i decideix amb un <code>switch</code>:</p>
                <ul>
                  <li><code>llista</code>: el que ja fas.</li>
                  <li><code>afegeix &lt;nom&gt; &lt;telèfon&gt;</code>: afegeix el contacte, desa l'agenda i mostra <code>Contacte afegit: Anna (622555666)</code>.
                  Crea <code>desarContactes(string $fitxer, array $contactes): void</code> amb
                  <code>json_encode(..., JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)</code>.</li>
                  <li>Qualsevol altra cosa (o falten dades): mostra l'ajuda, que comença per <code>Ús: php agenda.php</code>, i <code>exit(1)</code>.</li>
                </ul>
                HTML,
            'codi' => [
                ['regex' => '/function\s+desarContactes\s*\(/i', 'missatge' => 'Crea function desarContactes(string $fitxer, array $contactes): void'],
                ['regex' => '/\bJSON_UNESCAPED_UNICODE\b/', 'missatge' => 'Desa el JSON amb JSON_UNESCAPED_UNICODE (accents llegibles).'],
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'Tria l\'ordre amb switch.'],
            ],
            'proves' => [
                ['nom' => 'Afegir el primer contacte', 'args' => ['afegeix', 'Anna', '622555666'],
                 'conte' => ['Contacte afegit: Anna (622555666)'],
                 'fitxer_conte' => ['contactes.json' => ['"nom": "Anna"', '"telefon": "622555666"']]],
                ['nom' => 'Afegir a una agenda que ja en té 2', 'args' => ['afegeix', 'Pau', '633777888'], 'fitxers' => ['contactes.json' => $dos],
                 'fitxer_conte' => ['contactes.json' => ['"nom": "Marta"', '"nom": "Jordi"', '"nom": "Pau"']]],
                ['nom' => 'Accents sense À', 'args' => ['afegeix', 'Àlex', '644999000'], 'fitxer_conte' => ['contactes.json' => ['"nom": "Àlex"']]],
                ['nom' => 'Llistar continua funcionant', 'args' => ['llista'], 'fitxers' => ['contactes.json' => $dos], 'conte' => ['- Jordi: 611333444']],
                ['nom' => 'Ordre desconeguda', 'args' => ['esborra'], 'conte' => ['Ús: php agenda.php'], 'codi_sortida' => 1],
                ['nom' => 'Falta el telèfon', 'args' => ['afegeix', 'Anna'], 'conte' => ['Ús: php agenda.php'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Per afegir: llegeix l\'agenda, afegeix-hi un element nou i torna-la a desar sencera.',
                '$contactes[] = ["nom" => $argv[2], "telefon" => $argv[3]]; i després desarContactes(). Amb afegeix calen 4 elements a $argv.',
                "case 'afegeix':\n    if (\$argc !== 4) {\n        mostraAjuda();\n        exit(1);\n    }\n    \$contactes = llegirContactes(FITXER);\n    \$contactes[] = [\"nom\" => \$argv[2], \"telefon\" => \$argv[3]];\n    desarContactes(FITXER, \$contactes);",
            ],
            'preguntes' => [
                'Si no llegeixes l\'agenda abans de desar, què passa amb els contactes que ja hi havia?',
                'Com queda «Àlex» al fitxer sense JSON_UNESCAPED_UNICODE?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'Llegir → modificar → desar: el fitxer sempre conté l\'agenda sencera.',
        ],
        [
            'titol' => 'Dos catch: JsonException i RuntimeException',
            'conceptes' => ['JSON', 'excepcions amb fitxers', 'jerarquia d\'excepcions'],
            'xp' => 35,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> posa el switch dins d'un <code>try</code> amb dos <code>catch</code>:</p>
                <ul>
                  <li><code>JsonException</code> (el fitxer existeix però el JSON està malmès):
                  <code>El fitxer contactes.json està malmès: Syntax error</code> i <code>exit(2)</code>. <strong>No s'ha de sobreescriure el fitxer.</strong></li>
                  <li><code>RuntimeException</code> (no es pot llegir o escriure): <code>Error: no s'ha pogut accedir a l'agenda.</code>,
                  el detall amb <code>error_log()</code> i <code>exit(3)</code>.</li>
                </ul>
                <p><code>llegirContactes()</code> i <code>desarContactes()</code> han de llançar <code>RuntimeException</code> quan el fitxer no es pot llegir o escriure.</p>
                <p class="nota">Una prova simula l'error de lectura creant una <strong>carpeta</strong> anomenada <code>contactes.json</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/catch\s*\(\s*\\\\?JsonException\s+\$\w+\s*\)/', 'missatge' => 'Captura el JSON malmès amb catch (JsonException $e).'],
                ['regex' => '/catch\s*\(\s*\\\\?RuntimeException\s+\$\w+\s*\)/', 'missatge' => 'Captura els errors de fitxer amb catch (RuntimeException $e).'],
            ],
            'proves' => [
                ['nom' => 'Llistar amb el JSON malmès', 'args' => ['llista'], 'fitxers' => ['contactes.json' => $malmes],
                 'conte' => ['El fitxer contactes.json està malmès'], 'codi_sortida' => 2],
                ['nom' => 'Afegir amb el JSON malmès: no el sobreescriu', 'args' => ['afegeix', 'Pau', '633777888'], 'fitxers' => ['contactes.json' => $malmes],
                 'conte' => ['està malmès'], 'no_conte' => ['Contacte afegit'], 'codi_sortida' => 2,
                 'fitxer_conte' => ['contactes.json' => [$malmes]]],
                ['nom' => 'No es pot llegir (és una carpeta)', 'args' => ['llista'], 'fitxers' => ['contactes.json/' => ''],
                 'conte' => ["Error: no s'ha pogut accedir a l'agenda."], 'codi_sortida' => 3],
                ['nom' => 'Tot correcte', 'args' => ['afegeix', 'Anna', '622555666'], 'fitxers' => ['contactes.json' => $dos],
                 'conte' => ['Contacte afegit: Anna (622555666)'], 'codi_sortida' => 0],
            ],
            'pistes' => [
                'Hi ha dos problemes diferents: les dades del fitxer estan malament (JSON) o no es pot accedir al fitxer (sistema). Cada un té el seu catch.',
                'JSON_THROW_ON_ERROR ja fa que json_decode llanci JsonException. Per al fitxer: is_file()/is_readable() abans de llegir i comprovar el false de file_put_contents() (amb @).',
                "try {\n    switch (\$ordre) { ... }\n} catch (JsonException \$e) {\n    echo \"El fitxer \" . FITXER . \" està malmès: \" . \$e->getMessage() . PHP_EOL;\n    exit(2);\n} catch (RuntimeException \$e) {\n    error_log(\$e->getMessage());\n    echo \"Error: no s'ha pogut accedir a l'agenda.\" . PHP_EOL;\n    exit(3);\n}",
            ],
            'preguntes' => [
                'Si el JSON està malmès i afegeixes un contacte, per què és tan important no desar?',
                'JsonException és filla d\'Exception. Si posessis catch (Exception $e) primer, què passaria?',
                'Quina diferència hi ha entre «el fitxer no existeix» i «el fitxer no es pot llegir»?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'Com que llegirContactes() llança l\'excepció abans de desar, un fitxer malmès mai no es sobreescriu: l\'usuari pot recuperar-lo.',
        ],
    ],
];
