<?php
/**
 * Laboratori: Traductor de permisos Linux.
 * Basat en l'Exercici 1 de «Exercicis d'arguments per consola» (Tema 2, prof. David Porti).
 *
 * Format d'un pas:
 *  - titol, explicacio (HTML), conceptes (per a les estadístiques), xp
 *  - codi:   comprovacions sobre el codi font (regex / no_regex)
 *  - proves: execucions amb arguments; 'conte', 'no_conte' o 'exacte' sobre la sortida
 *            'append' = codi PHP que s'executa després del de l'alumne (per provar funcions/constants)
 *  - pistes: 3 nivells (orientació, concepte/funció clau, fragment de codi)
 *  - preguntes: preguntes socràtiques per al tutor sense connexió
 *  - solucio: codi complet del programa fins a aquest pas
 */

$sol1 = <<<'PHP'
<?php
// Traductor de permisos Linux
// Ús: php permisos.php <permisos>

// La posició de l'array és el dígit: SIMBOLS[5] és "r-x"
const SIMBOLS = ['---', '--x', '-w-', '-wx', 'r--', 'r-x', 'rw-', 'rwx'];

PHP;

$sol2 = $sol1 . <<<'PHP'

function mostraAjuda(): void
{
    echo "Ús: php permisos.php <permisos>" . PHP_EOL;
    echo "  permisos   3 dígits entre 0 i 7 (ex.: 754)" . PHP_EOL;
}

PHP;

$sol3 = $sol2 . <<<'PHP'

// $argc compta també el nom del script: amb 1 argument, $argc val 2
if ($argc != 2) {
    echo "ERROR: s'esperava 1 argument i se n'han rebut " . ($argc - 1) . "." . PHP_EOL . PHP_EOL;
    mostraAjuda();
    exit(1);
}

$permisos = $argv[1];

PHP;

$sol4 = $sol3 . <<<'PHP'

if (strlen($permisos) != 3) {
    echo "ERROR: han de ser 3 dígits." . PHP_EOL;
    exit(1);
}

PHP;

$sol5 = $sol4 . <<<'PHP'

// Un string es pot recórrer com un array: $permisos[0] és el primer caràcter
for ($i = 0; $i < 3; $i++) {
    $caracter = $permisos[$i];
    if (!is_numeric($caracter)) {
        echo "ERROR: \"$caracter\" no és un dígit." . PHP_EOL;
        exit(1);
    }
    if ($caracter > 7) {
        echo "ERROR: \"$caracter\" no és vàlid. Cada dígit ha d'estar entre 0 i 7." . PHP_EOL;
        exit(1);
    }
}

PHP;

$sol6 = $sol5 . <<<'PHP'

$grups = ['Propietari', 'Grup', 'Altres'];
$resultat = '';
$detall = '';
for ($i = 0; $i < 3; $i++) {
    $digit = (int) $permisos[$i];
    $resultat .= SIMBOLS[$digit];
    $detall .= "  " . $grups[$i] . ": " . SIMBOLS[$digit] . " ($digit)" . PHP_EOL;
}

echo "Permisos $permisos → $resultat" . PHP_EOL;
echo $detall;

PHP;

$sortida754 = <<<'TXT'
Permisos 754 → rwxr-xr--
  Propietari: rwx (7)
  Grup:       r-x (5)
  Altres:     r-- (4)
TXT;

$sortida640 = <<<'TXT'
Permisos 640 → rw-r-----
  Propietari: rw- (6)
  Grup:       r-- (4)
  Altres:     --- (0)
TXT;

$errorArgs0 = "ERROR: s'esperava 1 argument i se n'han rebut 0.";

return [
    'id' => 'permisos',
    'ordre' => 1,
    'titol' => 'Traductor de permisos Linux',
    'fitxer' => 'permisos.php',
    'tema' => 'Tema 2 – Fonaments PHP',
    'apartat' => '2.7 Arguments per consola ($argv, $argc)',
    'origen' => [
        'fitxer' => 'Exercicis_02.Arguments_per_consola_Enunciat.pdf',
        'descripcio' => 'Exercici 1 de «Exercicis d\'arguments per consola» (David Porti)',
    ],
    'resum' => 'Converteix permisos numèrics (754) en lletres (rwxr-xr--), com fa ls -l. Practicaràs $argv, $argc, validació, constants, arrays i for.',
    'xp_final' => 50,
    'paraules_clau' => ['argv', 'argc', 'permisos', 'strlen', 'const', 'for'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Traductor de permisos Linux
        // Ús: php permisos.php <permisos>

        PHP,

    'context' => <<<'HTML'
        <p>Quan fem <code>ls -l</code> a un servidor, Linux mostra els permisos amb lletres (<code>rwxr-xr--</code>),
        però quan els canviem amb <code>chmod</code> fem servir números (<code>754</code>). Un administrador ha de saber
        passar d'un format a l'altre sense dubtar. Farem un programa que ho faci per nosaltres.</p>
        <pre class="term">$ php permisos.php 754
        Permisos 754 → rwxr-xr--
          Propietari: rwx (7)
          Grup:       r-x (5)
          Altres:     r-- (4)</pre>
        HTML,

    'resum_apres' => [
        'Llegir arguments de consola amb $argv i comptar-los amb $argc (que inclou el nom del script).',
        'Validar les dades abans de fer-les servir, perquè el programa no mostri mai avisos de PHP.',
        'Guardar una taula fixa en una constant de tipus array (const SIMBOLS = [...]).',
        'Recórrer un string caràcter a caràcter amb un for i $text[$i].',
        'Agrupar codi repetit en una funció (mostraAjuda()).',
    ],

    'passos' => [
        // ---------------------------------------------------------------- PAS 1
        [
            'titol' => 'La taula de símbols',
            'conceptes' => ['constants', 'arrays indexats'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Cada dígit (0-7) es converteix en tres lletres. Com que només hi ha 8 possibilitats, les guardarem
                en un <strong>array on la posició és el dígit</strong>:</p>
                <table class="taula">
                  <tr><th>Dígit</th><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td></tr>
                  <tr><th>Símbols</th><td>---</td><td>--x</td><td>-w-</td><td>-wx</td><td>r--</td><td>r-x</td><td>rw-</td><td>rwx</td></tr>
                </table>
                <p><strong>Objectiu:</strong> crea una <strong>constant</strong> anomenada <code>SIMBOLS</code> amb aquest array
                (requisit de l'enunciat: <code>const SIMBOLS = [...]</code>).</p>
                <p class="nota">Per comprovar-ho, després del teu codi s'executarà <code>echo implode(',', SIMBOLS);</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bconst\s+SIMBOLS\s*=/', 'missatge' => 'Defineix la constant amb la paraula clau const: const SIMBOLS = [...];'],
            ],
            'proves' => [
                ['nom' => 'La constant té els 8 símbols en ordre', 'args' => ['754'],
                 'append' => "echo PHP_EOL, implode(',', SIMBOLS), PHP_EOL;",
                 'conte' => ['---,--x,-w-,-wx,r--,r-x,rw-,rwx']],
            ],
            'pistes' => [
                'Una constant és com una variable que no pot canviar. Fixa\'t que la taula ja té els símbols ordenats del 0 al 7: si els poses en aquest ordre dins d\'un array, la posició coincidirà amb el dígit.',
                'La sintaxi és const NOM = valor; i el valor pot ser un array: [\'a\', \'b\', ...]. Les constants no porten $ i, per convenció, van en MAJÚSCULES.',
                "const SIMBOLS = ['---', '--x', '-w-', /* ... completa fins a 'rwx' */];",
            ],
            'preguntes' => [
                'Si l\'array comença per la posició 0, quin element hi ha d\'haver a la posició 5?',
                'Quina diferència hi ha entre una variable i una constant? Per què aquí és millor una constant?',
                'Les constants porten el símbol $ davant?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'L\'array té 8 elements i, com que comença per 0, SIMBOLS[7] és "rwx". Amb const el valor no es pot modificar per error.',
        ],

        // ---------------------------------------------------------------- PAS 2
        [
            'titol' => 'La funció mostraAjuda()',
            'conceptes' => ['funcions', 'echo i PHP_EOL'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p>Quan l'usuari s'equivoqui, el programa li mostrarà com s'ha de fer servir. Com que l'ajuda es pot mostrar
                des de diversos llocs, la posarem dins d'una <strong>funció</strong> (requisit de l'enunciat).</p>
                <p><strong>Objectiu:</strong> crea la funció <code>mostraAjuda()</code> que escrigui exactament:</p>
                <pre class="term">Ús: php permisos.php &lt;permisos&gt;
                  permisos   3 dígits entre 0 i 7 (ex.: 754)</pre>
                <p class="nota">Els espais per alinear no compten a la comprovació. Per provar-la, s'afegirà una crida
                <code>mostraAjuda();</code> al final del teu codi.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bfunction\s+mostraAjuda\s*\(/i', 'missatge' => 'Defineix la funció amb: function mostraAjuda() { ... }'],
            ],
            'proves' => [
                ['nom' => 'mostraAjuda() escriu l\'ajuda d\'ús', 'args' => ['754'],
                 'append' => "echo PHP_EOL; mostraAjuda();",
                 'conte' => ["Ús: php permisos.php <permisos>\npermisos 3 dígits entre 0 i 7 (ex.: 754)"]],
            ],
            'pistes' => [
                'Una funció és un bloc de codi amb nom que s\'executa quan la crides. Definir-la no l\'executa: només la "prepara".',
                'Estructura: function nom(): void { ... }. A dins, dos echo, un per línia, acabats amb PHP_EOL (el salt de línia).',
                "function mostraAjuda(): void\n{\n    echo \"Ús: php permisos.php <permisos>\" . PHP_EOL;\n    echo \"  permisos   ...\" . PHP_EOL;\n}",
            ],
            'preguntes' => [
                'Quan defineixes una funció, s\'executa el seu codi immediatament? Quan s\'executa, doncs?',
                'Què fa PHP_EOL i per què és millor que escriure "\n" directament?',
                'Quina sortida veus si executes ara el programa? Per què?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'La funció no retorna res (void): només escriu. La crida mostraAjuda(); la farem servir en els errors.',
        ],

        // ---------------------------------------------------------------- PAS 3
        [
            'titol' => 'Exactament 1 argument ($argc)',
            'conceptes' => ['$argv i $argc', 'validació de dades', 'exit'],
            'xp' => 25,
            'explicacio' => <<<'HTML'
                <p>PHP deixa els arguments a <code>$argv</code> (array) i el nombre d'elements a <code>$argc</code>.
                Recorda que <code>$argv[0]</code> sempre és el nom del script:</p>
                <pre class="term">$ php permisos.php 754   →   $argv = ["permisos.php", "754"]   $argc = 2</pre>
                <p><strong>Objectiu:</strong> si no s'ha rebut <em>exactament 1 argument</em>, mostra l'error, una línia en blanc
                i l'ajuda, i <strong>atura el programa</strong>:</p>
                <pre class="term">$ php permisos.php
                ERROR: s'esperava 1 argument i se n'han rebut 0.

                Ús: php permisos.php &lt;permisos&gt;
                  permisos   3 dígits entre 0 i 7 (ex.: 754)</pre>
                <p>Si n'hi ha 2, ha de dir «se n'han rebut 2». Si l'argument és correcte, no ha de sortir cap ERROR.
                Després guarda l'argument en una variable, p. ex. <code>$permisos</code>.</p>
                <p class="nota">Prova-ho amb la caixa «Arguments»: deixa-la buida, posa <code>754</code> o <code>754 640</code>.</p>
                HTML,
            'codi' => [
                ['regex' => '/\$argc\b/', 'missatge' => 'Fes servir $argc per saber quants arguments s\'han rebut.'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [],
                 'conte' => [$errorArgs0, 'Ús: php permisos.php <permisos>']],
                ['nom' => 'Massa arguments', 'args' => ['754', '640'],
                 'conte' => ["ERROR: s'esperava 1 argument i se n'han rebut 2."]],
                ['nom' => 'Un argument correcte: cap error', 'args' => ['754'],
                 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'Pensa què val $argc quan executes "php permisos.php" (sense res més) i quan executes "php permisos.php 754". Quin valor de $argc és el correcte?',
                'Compara $argc amb 2 dins d\'un if. El nombre d\'arguments "reals" és $argc - 1. Després de mostrar l\'error i l\'ajuda, fes servir exit; perquè el programa no continuï.',
                "if (\$argc != 2) {\n    echo \"ERROR: s'esperava 1 argument i se n'han rebut \" . (\$argc - 1) . \".\" . PHP_EOL . PHP_EOL;\n    mostraAjuda();\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Si executes "php permisos.php 754", quants elements té $argv? Quins són?',
                'Què passaria si llegissis $argv[1] quan l\'usuari no ha escrit cap argument?',
                'Després de mostrar l\'error, el programa continua executant les línies de sota? Com ho pots evitar?',
                'Per què el missatge diu ($argc - 1) i no $argc?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => '$argc inclou el nom del script, per això amb un argument val 2. exit(1) atura el programa (el 1 indica "ha acabat amb error").',
        ],

        // ---------------------------------------------------------------- PAS 4
        [
            'titol' => 'Tres caràcters (strlen)',
            'conceptes' => ['strlen', 'validació de dades'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>Els permisos han de tenir exactament <strong>3 caràcters</strong>.</p>
                <p><strong>Objectiu:</strong> comprova la longitud amb <code>strlen()</code>. Si no és 3, mostra un error que
                contingui «3 dígits» i atura el programa:</p>
                <pre class="term">$ php permisos.php 7544
                ERROR: han de ser 3 dígits.</pre>
                <p class="nota">Prova també amb <code>75</code> i amb <code>""</code> (un argument buit).</p>
                HTML,
            'codi' => [
                ['regex' => '/\bstrlen\s*\(/', 'missatge' => 'Fes servir strlen() per comprovar la longitud (és un requisit de l\'enunciat).'],
            ],
            'proves' => [
                ['nom' => 'Massa dígits', 'args' => ['7544'], 'conte' => ['ERROR', '3 dígits']],
                ['nom' => 'Massa pocs dígits', 'args' => ['75'], 'conte' => ['ERROR', '3 dígits']],
                ['nom' => 'Argument buit', 'args' => [''], 'conte' => ['ERROR', '3 dígits']],
                ['nom' => '3 dígits: cap error', 'args' => ['754'], 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'Abans de mirar cada dígit, assegura\'t que n\'hi ha exactament 3. Si no, no té sentit continuar.',
                'strlen($text) retorna el nombre de caràcters d\'un string. Compara-ho amb 3 i, si és diferent, mostra l\'error i fes exit.',
                "if (strlen(\$permisos) != 3) {\n    echo \"ERROR: han de ser 3 dígits.\" . PHP_EOL;\n    exit(1);\n}",
            ],
            'preguntes' => [
                'Quin valor retorna strlen("7544")? I strlen("")?',
                'Aquesta comprovació ha d\'anar abans o després de la de $argc? Per què?',
            ],
            'solucio' => $sol4,
            'solucio_explicacio' => 'Validem de més general a més concret: primer que hi hagi argument, després la longitud i finalment cada caràcter.',
        ],

        // ---------------------------------------------------------------- PAS 5
        [
            'titol' => 'Cada dígit entre 0 i 7 (for)',
            'conceptes' => ['bucle for', 'strings com a arrays', 'is_numeric', 'validació de dades'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Ara cal mirar els 3 caràcters un per un. Un string es pot llegir com un array:
                si <code>$permisos = "754"</code>, aleshores <code>$permisos[0]</code> és <code>"7"</code>.</p>
                <p><strong>Objectiu:</strong> recorre els 3 caràcters amb un <code>for</code> i comprova que cadascun és un dígit
                entre 0 i 7. El missatge ha d'indicar <strong>quin caràcter falla</strong>, entre cometes:</p>
                <pre class="term">$ php permisos.php 789
                ERROR: "8" no és vàlid. Cada dígit ha d'estar entre 0 i 7.

                $ php permisos.php 7a4
                ERROR: "a" no és un dígit.</pre>
                HTML,
            'codi' => [
                ['regex' => '/\bfor\s*\(/', 'missatge' => 'Recorre els caràcters amb un bucle for (requisit de l\'enunciat).'],
            ],
            'proves' => [
                ['nom' => 'Dígit més gran que 7', 'args' => ['789'],
                 'conte' => ['ERROR: "8" no és vàlid. Cada dígit ha d\'estar entre 0 i 7.']],
                ['nom' => 'Caràcter no numèric', 'args' => ['7a4'], 'conte' => ['ERROR', '"a"']],
                ['nom' => 'Un guió no és un dígit', 'args' => ['7-4'], 'conte' => ['ERROR', '"-"']],
                ['nom' => 'L\'últim caràcter també es comprova', 'args' => ['759'], 'conte' => ['ERROR', '"9"']],
                ['nom' => '777 és correcte', 'args' => ['777'], 'no_conte' => ['ERROR']],
                ['nom' => '000 és correcte', 'args' => ['000'], 'no_conte' => ['ERROR']],
            ],
            'pistes' => [
                'Fes un bucle que passi per les posicions 0, 1 i 2 del text. A cada volta, agafa el caràcter i fes-li dues preguntes: és un número? és més gran que 7?',
                'for ($i = 0; $i < 3; $i++) { $c = $permisos[$i]; ... }. Per saber si és un número tens is_numeric($c). Recorda que "a" no és numèric però "8" sí: són dos errors diferents.',
                "for (\$i = 0; \$i < 3; \$i++) {\n    \$caracter = \$permisos[\$i];\n    if (!is_numeric(\$caracter)) {\n        echo \"ERROR: \\\"\$caracter\\\" no és un dígit.\" . PHP_EOL;\n        exit(1);\n    }\n    // i ara: és més gran que 7?\n}",
            ],
            'preguntes' => [
                'Quins valors pren $i al teu bucle? Arriba a mirar l\'últim caràcter?',
                'Què retorna is_numeric("8")? I is_numeric("a")? Com distingeixes els dos casos d\'error?',
                'Com pots posar unes cometes dobles dins d\'un text que ja està entre cometes dobles?',
            ],
            'solucio' => $sol5,
            'solucio_explicacio' => 'Primer descartem el que no és número (is_numeric) i després els números massa grans. "8" > 7 funciona perquè PHP converteix el text en número per comparar.',
        ],

        // ---------------------------------------------------------------- PAS 6
        [
            'titol' => 'La conversió',
            'conceptes' => ['arrays indexats', 'bucle for', 'concatenació', 'casting'],
            'xp' => 30,
            'explicacio' => <<<'HTML'
                <p>Ja tenim les dades validades. Ara toca convertir cada dígit amb la constant <code>SIMBOLS</code>.</p>
                <p><strong>Objectiu:</strong> mostra el resultat complet i el detall de cada grup, exactament així
                (l'alineació no compta, i pots escriure <code>-&gt;</code> en lloc de <code>→</code>):</p>
                <pre class="term">$ php permisos.php 754
                Permisos 754 → rwxr-xr--
                  Propietari: rwx (7)
                  Grup:       r-x (5)
                  Altres:     r-- (4)</pre>
                HTML,
            'codi' => [
                ['regex' => '/SIMBOLS\s*\[/', 'missatge' => 'Fes servir la constant SIMBOLS per convertir (SIMBOLS[$digit]).'],
            ],
            'proves' => [
                ['nom' => 'Permisos 754', 'args' => ['754'], 'exacte' => $sortida754],
                ['nom' => 'Permisos 640', 'args' => ['640'], 'exacte' => $sortida640],
                ['nom' => 'Permisos 777', 'args' => ['777'], 'conte' => ['Permisos 777 → rwxrwxrwx']],
                ['nom' => 'Permisos 000', 'args' => ['000'], 'conte' => ['Permisos 000 → ---------', 'Altres: --- (0)']],
            ],
            'pistes' => [
                'Per a cada un dels 3 dígits has de fer el mateix: buscar els seus símbols a la taula. El resultat complet és la unió dels tres trossos.',
                'SIMBOLS[$digit] et dona els símbols d\'un dígit. Converteix el caràcter a enter amb (int). Pots guardar els noms dels grups en un array [\'Propietari\', \'Grup\', \'Altres\'] i fer servir la mateixa $i. Per anar enganxant text: $resultat .= ...',
                "\$grups = ['Propietari', 'Grup', 'Altres'];\n\$resultat = '';\nfor (\$i = 0; \$i < 3; \$i++) {\n    \$digit = (int) \$permisos[\$i];\n    \$resultat .= SIMBOLS[\$digit];\n}\necho \"Permisos \$permisos → \$resultat\" . PHP_EOL;\n// falta el detall de cada grup",
            ],
            'preguntes' => [
                'Si $digit val 5, què et retorna SIMBOLS[$digit]?',
                'Com pots anar afegint text al final d\'una variable sense esborrar el que ja tenia?',
                'Has de mostrar primer la línia "Permisos ..." i després el detall. Com ho pots fer si el detall el calcules dins del mateix bucle?',
            ],
            'solucio' => $sol6,
            'solucio_explicacio' => 'Fem un sol bucle que va construint dues coses alhora: el resultat (rwxr-xr--) i el detall de cada grup. Al final mostrem les dues.',
        ],

        // ---------------------------------------------------------------- PAS 7
        [
            'titol' => 'Prova final (com la del professor)',
            'conceptes' => ['validació de dades', '$argv i $argc'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>Última revisió: es passen tots els casos de la taula «Validacions i errors» de l'enunciat i els exemples
                d'execució. Recorda els requisits:</p>
                <ul>
                  <li>La taula de símbols en una constant <code>const SIMBOLS = [...]</code>.</li>
                  <li>L'ajuda dins de la funció <code>mostraAjuda()</code>.</li>
                  <li>El programa <strong>no pot mostrar mai</strong> avisos ni errors de PHP, sigui quin sigui l'argument.</li>
                </ul>
                <p class="nota">Si tot ja funcionava, només cal prémer «Comprova». Si no, mira quin cas falla.</p>
                HTML,
            'codi' => [
                ['regex' => '/\bconst\s+SIMBOLS\s*=/', 'missatge' => 'Falta la constant const SIMBOLS = [...].'],
                ['regex' => '/\bfunction\s+mostraAjuda\s*\(/i', 'missatge' => 'Falta la funció mostraAjuda().'],
            ],
            'proves' => [
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => [$errorArgs0, 'Ús: php permisos.php <permisos>']],
                ['nom' => 'Massa dígits', 'args' => ['7544'], 'conte' => ['ERROR', '3 dígits']],
                ['nom' => 'Dígit més gran que 7', 'args' => ['789'], 'conte' => ['"8" no és vàlid']],
                ['nom' => 'Caràcter no numèric', 'args' => ['7a4'], 'conte' => ['ERROR', '"a"']],
                ['nom' => 'Text amb espais', 'args' => ['7 5'], 'conte' => ['ERROR']],
                ['nom' => 'Exemple 754', 'args' => ['754'], 'exacte' => $sortida754],
                ['nom' => 'Exemple 640', 'args' => ['640'], 'exacte' => $sortida640],
            ],
            'pistes' => [
                'Mira quin cas falla i executa\'l tu mateix posant els mateixos arguments a la caixa «Arguments».',
                'Si surt un avís de PHP (Warning), vol dir que alguna dada no s\'ha validat abans de fer-la servir. Revisa l\'ordre de les comprovacions.',
                "L'ordre ha de ser: 1) \$argc, 2) strlen(), 3) for amb cada caràcter, 4) conversió. Cada error acaba amb exit.",
            ],
            'preguntes' => [
                'Quin dels casos falla? Què esperava i què ha sortit?',
                'Què passa amb "7 5"? Quants caràcters té? Quin és el del mig?',
            ],
            'solucio' => $sol6,
            'solucio_explicacio' => 'Aquest és el programa complet de l\'exercici.',
        ],
    ],
];
