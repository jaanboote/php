<?php
/**
 * Laboratori: Generador de regles .htaccess.
 * Basat en l'Exercici 3 d'«Exercicis d'arguments per consola» (Exercicis 02, prof. David Porti).
 */

$ajuda = <<<'PHP'
function mostraAjuda(): void
{
    echo "Ús: php htaccess.php <comanda> [arguments]" . PHP_EOL;
    echo "Comandes disponibles:" . PHP_EOL;
    echo "  https                        Força HTTPS a tot el lloc" . PHP_EOL;
    echo "  bloqueja <ip> [<ip> ...]     Bloqueja una o més IP" . PHP_EOL;
    echo "  redirigeix <antiga> <nova>   Redirecció 301 d'una pàgina" . PHP_EOL;
    echo "  help                         Mostra aquesta ajuda" . PHP_EOL;
}
PHP;

$esIp = <<<'PHP'

// Una IP té 4 números de 0 a 255 separats per punts
function esIpValida(string $ip): bool
{
    $parts = explode(".", $ip);
    if (count($parts) != 4) {
        return false;
    }
    foreach ($parts as $part) {
        if (!ctype_digit($part) || $part > 255) {
            return false;
        }
    }
    return true;
}
PHP;

$inici = <<<'PHP'
<?php
// Generador de regles .htaccess: php htaccess.php <comanda> [arguments]


PHP;

$principi = <<<'PHP'


if ($argc < 2) {
    echo "ERROR: cal indicar una comanda." . PHP_EOL . PHP_EOL;
    mostraAjuda();
    exit(1);
}

// HTTPS, Https i https són la mateixa comanda
$comanda = strtolower($argv[1]);
switch ($comanda) {
    case "https":
        echo "# Forçar HTTPS" . PHP_EOL;
        echo "RewriteEngine On" . PHP_EOL;
        echo "RewriteCond %{HTTPS} off" . PHP_EOL;
        echo 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]' . PHP_EOL;
        break;
PHP;

$bloqueja = <<<'PHP'

    case "bloqueja":
        if ($argc < 3) {
            echo "ERROR: cal com a mínim una IP." . PHP_EOL;
            exit(1);
        }
        // Primer es comproven totes; només si són bones es mostra el bloc
        for ($i = 2; $i < $argc; $i++) {
            if (!esIpValida($argv[$i])) {
                echo "ERROR: \"{$argv[$i]}\" no és una IP vàlida." . PHP_EOL;
                exit(1);
            }
        }
        echo "# Bloqueig de " . ($argc - 2) . " IP" . PHP_EOL;
        echo "<RequireAll>" . PHP_EOL;
        echo "    Require all granted" . PHP_EOL;
        for ($i = 2; $i < $argc; $i++) {
            echo "    Require not ip {$argv[$i]}" . PHP_EOL;
        }
        echo "</RequireAll>" . PHP_EOL;
        break;
PHP;

$redirigeix = <<<'PHP'

    case "redirigeix":
        if ($argc != 4) {
            echo "ERROR: redirigeix necessita exactament 2 rutes." . PHP_EOL;
            exit(1);
        }
        $antiga = $argv[2];
        $nova = $argv[3];
        // substr() i no $antiga[0]: amb una ruta buida («""»), $antiga[0] donaria un avís de PHP
        if (substr($antiga, 0, 1) != "/" || substr($nova, 0, 1) != "/") {
            echo "ERROR: les rutes han de començar per /." . PHP_EOL;
            exit(1);
        }
        echo "# Redirecció permanent" . PHP_EOL;
        echo "Redirect 301 $antiga $nova" . PHP_EOL;
        break;
PHP;

$final = <<<'PHP'

    case "help":
        mostraAjuda();
        break;
    default:
        echo "ERROR: la comanda \"{$argv[1]}\" no existeix." . PHP_EOL . PHP_EOL;
        mostraAjuda();
        exit(1);
}

PHP;

$sol1 = $inici . $ajuda . $principi . $final;
$sol2 = $inici . $ajuda . $esIp . $principi . $bloqueja . $final;
$sol3 = $inici . $ajuda . $esIp . $principi . $bloqueja . $redirigeix . $final;

$liniaAjuda = 'redirigeix <antiga> <nova>   Redirecció 301 d\'una pàgina';

return [
    'id' => 'htaccess',
    'ordre' => 60,
    'titol' => 'Generador de regles .htaccess',
    'fitxer' => 'htaccess.php',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.6 Funcions · 2.7 Arguments per consola',
    'origen' => [
        'fitxer' => 'Exercicis_02.Arguments_per_consola_Enunciat.pdf',
        'descripcio' => 'Exercici 3 de «Exercicis d\'arguments per consola» (David Porti)',
    ],
    'resum' => 'Una eina de terminal com git: el primer argument és la comanda (https, bloqueja, redirigeix, help) i la resta, les dades.',
    'xp_final' => 45,
    'paraules_clau' => ['$argv', '$argc', 'switch', 'strtolower', 'htaccess', 'funció'],
    'codi_inicial' => <<<'PHP'
        <?php
        // Generador de regles .htaccess: php htaccess.php <comanda> [arguments]

        PHP,

    'context' => <<<'HTML'
        <p>En un servidor Apache, el fitxer <code>.htaccess</code> permet configurar un lloc web sense tocar la configuració
        general: forçar HTTPS, bloquejar visitants molestos o redirigir pàgines que han canviat de nom. Escriure aquestes regles
        a mà provoca errors, així que farem una eina que les generi.</p>
        <table class="taula">
          <tr><th>Comanda</th><th>Arguments</th><th>Què fa</th></tr>
          <tr><td>https</td><td>—</td><td>Regles per redirigir tot el lloc de http a https.</td></tr>
          <tr><td>bloqueja</td><td>Una o més IP</td><td>Un bloc que denega l'accés a totes les IP.</td></tr>
          <tr><td>redirigeix</td><td>Ruta antiga i ruta nova</td><td>Una redirecció permanent (301).</td></tr>
          <tr><td>help</td><td>—</td><td>Mostra l'ajuda.</td></tr>
        </table>
        <p>El programa <strong>no pot mostrar mai avisos o errors de PHP</strong>, sigui quin sigui l'argument rebut.</p>
        HTML,

    'resum_apres' => [
        'El primer argument decideix què fa el programa (com git o composer), i un switch ho organitza.',
        'strtolower() fa que la comanda s\'accepti en majúscules o minúscules.',
        'Una funció (mostraAjuda) evita repetir el mateix text a diversos llocs.',
        'Recórrer un nombre variable d\'arguments amb un for des de $argv[2] fins a $argc.',
        'Validar totes les dades abans de mostrar cap resultat.',
    ],

    'passos' => [
        [
            'titol' => 'Comandes, ajuda i https',
            'conceptes' => ['arguments per consola', 'switch', 'funcions'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong></p>
                <ol>
                  <li>Crea una funció <code>mostraAjuda()</code> que mostri exactament aquest text:</li>
                </ol>
                <pre class="term">Ús: php htaccess.php &lt;comanda&gt; [arguments]
                Comandes disponibles:
                  https                        Força HTTPS a tot el lloc
                  bloqueja &lt;ip&gt; [&lt;ip&gt; ...]     Bloqueja una o més IP
                  redirigeix &lt;antiga&gt; &lt;nova&gt;   Redirecció 301 d'una pàgina
                  help                         Mostra aquesta ajuda</pre>
                <ol start="2">
                  <li>Si no hi ha cap comanda, mostra <code>ERROR: cal indicar una comanda.</code>, una línia en blanc, l'ajuda i <code>exit(1)</code>.</li>
                  <li>Passa la comanda a minúscules amb <code>strtolower()</code> i tria què fer amb un <code>switch</code>:
                    <ul>
                      <li><code>help</code>: mostra l'ajuda.</li>
                      <li><code>https</code>: mostra aquestes regles:</li>
                    </ul>
                  </li>
                </ol>
                <pre class="term"># Forçar HTTPS
                RewriteEngine On
                RewriteCond %{HTTPS} off
                RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]</pre>
                <ol start="4">
                  <li>Qualsevol altra comanda: <code>ERROR: la comanda "esborra" no existeix.</code> (amb la comanda tal com l'han escrit), una línia en blanc, l'ajuda i <code>exit(1)</code>.</li>
                </ol>
                <p class="nota">Amb <code>$</code> dins de cometes dobles, PHP buscaria una variable: escriu la línia del RewriteRule entre cometes simples.</p>
                HTML,
            'codi' => [
                ['regex' => '/function\s+mostraAjuda\s*\(/', 'missatge' => 'Posa el text de l\'ajuda en una funció mostraAjuda().'],
                ['regex' => '/\bswitch\s*\(/', 'missatge' => 'Tria la comanda amb un switch.'],
                ['regex' => '/\bstrtolower\s*\(/', 'missatge' => 'Accepta la comanda en majúscules o minúscules amb strtolower().'],
            ],
            'proves' => [
                ['nom' => 'Sense comanda', 'args' => [], 'conte' => ['ERROR: cal indicar una comanda.', 'Ús: php htaccess.php <comanda> [arguments]'], 'codi_sortida' => 1],
                ['nom' => 'help', 'args' => ['help'], 'conte' => ['Comandes disponibles:', $liniaAjuda], 'no_conte' => ['ERROR']],
                ['nom' => 'https', 'args' => ['https'], 'conte' => ['# Forçar HTTPS', 'RewriteEngine On', 'RewriteCond %{HTTPS} off', 'RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]']],
                ['nom' => 'HTTPS en majúscules', 'args' => ['HTTPS'], 'conte' => ['# Forçar HTTPS'], 'no_conte' => ['ERROR']],
                ['nom' => 'Comanda desconeguda', 'args' => ['esborra'], 'conte' => ['ERROR: la comanda "esborra" no existeix.', 'Comandes disponibles:'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Primer, la funció de l\'ajuda (fora de cap if). Després, comprova que hi ha comanda. I finalment, un switch amb un case per comanda i un default per a les que no existeixen.',
                'Si $argc < 2 no hi ha comanda. $comanda = strtolower($argv[1]); i switch ($comanda) { case "https": ... break; case "help": ... break; default: ... }',
                "function mostraAjuda(): void\n{\n    echo \"Ús: php htaccess.php <comanda> [arguments]\" . PHP_EOL;\n    // ...\n}\n\nswitch (strtolower(\$argv[1])) {\n    case \"help\":\n        mostraAjuda();\n        break;\n    // ...\n}",
            ],
            'preguntes' => [
                'Per què és millor posar l\'ajuda en una funció que copiar-la als tres llocs on surt?',
                'A l\'error de comanda desconeguda, per què mostrem $argv[1] i no $comanda?',
                'Què passaria a la línia del RewriteRule si la posessis entre cometes dobles?',
            ],
            'solucio' => $sol1,
            'solucio_explicacio' => 'La funció es defineix una vegada i es crida a help i als errors. strtolower() fa que el switch només hagi de comparar minúscules.',
        ],
        [
            'titol' => 'Bloquejar IP',
            'conceptes' => ['bucle for', 'funcions', 'validació de dades'],
            'xp' => 20,
            'explicacio' => <<<'HTML'
                <p>A <code>bloqueja</code>, les IP van des de <code>$argv[2]</code> fins a l'últim argument: recorre-les amb un
                <code>for</code> fins a <code>$argc</code>.</p>
                <p><strong>Objectiu:</strong></p>
                <ul>
                  <li>Sense cap IP: <code>ERROR: cal com a mínim una IP.</code> i <code>exit(1)</code>.</li>
                  <li>Crea una funció <code>esIpValida($ip)</code>: una IP té <strong>4 números de 0 a 255</strong> separats per punts
                      (<code>explode(".", $ip)</code> i <code>ctype_digit()</code>).</li>
                  <li>Comprova-les <strong>totes abans de mostrar res</strong>. Si alguna falla: <code>ERROR: "hola" no és una IP vàlida.</code> i <code>exit(1)</code>.</li>
                  <li>Si totes són bones, mostra el bloc:</li>
                </ul>
                <pre class="term">$ php htaccess.php bloqueja 203.0.113.45 198.51.100.7
                # Bloqueig de 2 IP
                &lt;RequireAll&gt;
                    Require all granted
                    Require not ip 203.0.113.45
                    Require not ip 198.51.100.7
                &lt;/RequireAll&gt;</pre>
                HTML,
            'codi' => [
                ['regex' => '/function\s+esIpValida\s*\(/', 'missatge' => 'Crea la funció esIpValida($ip).'],
                ['regex' => '/\bfor\s*\(/', 'missatge' => 'Recorre les IP amb un for fins a $argc.'],
            ],
            'proves' => [
                ['nom' => 'Dues IP', 'args' => ['bloqueja', '203.0.113.45', '198.51.100.7'],
                 'conte' => ['# Bloqueig de 2 IP', '<RequireAll>', 'Require all granted', 'Require not ip 203.0.113.45', 'Require not ip 198.51.100.7', '</RequireAll>']],
                ['nom' => 'Una IP', 'args' => ['bloqueja', '10.0.0.1'], 'conte' => ['# Bloqueig de 1 IP', 'Require not ip 10.0.0.1']],
                ['nom' => 'Sense IP', 'args' => ['bloqueja'], 'conte' => ['ERROR: cal com a mínim una IP.'], 'codi_sortida' => 1],
                ['nom' => 'Una IP que és un text', 'args' => ['bloqueja', '203.0.113.45', 'hola'], 'conte' => ['ERROR: "hola" no és una IP vàlida.'], 'no_conte' => ['RequireAll'], 'codi_sortida' => 1],
                ['nom' => 'Número massa gran', 'args' => ['bloqueja', '10.0.0.300'], 'conte' => ['ERROR: "10.0.0.300" no és una IP vàlida.'], 'codi_sortida' => 1],
                ['nom' => 'Només 3 números', 'args' => ['bloqueja', '10.0.0'], 'conte' => ['ERROR: "10.0.0" no és una IP vàlida.'], 'codi_sortida' => 1],
                ['nom' => 'BLOQUEJA en majúscules', 'args' => ['BLOQUEJA', '10.0.0.1'], 'conte' => ['# Bloqueig de 1 IP']],
            ],
            'pistes' => [
                'Fes dos recorreguts: el primer només valida (i s\'atura al primer error) i el segon mostra les línies. Així no surt mai mig bloc.',
                'A esIpValida: $parts = explode(".", $ip); si count($parts) != 4, no és vàlida. Després, cada part ha de complir ctype_digit($part) i $part <= 255.',
                "for (\$i = 2; \$i < \$argc; \$i++) {\n    if (!esIpValida(\$argv[\$i])) {\n        echo \"ERROR: \\\"{\$argv[\$i]}\\\" no és una IP vàlida.\" . PHP_EOL;\n        exit(1);\n    }\n}\necho \"# Bloqueig de \" . (\$argc - 2) . \" IP\" . PHP_EOL;",
            ],
            'preguntes' => [
                'Per què el for comença a 2 i no a 0?',
                'Quantes IP hi ha si $argc val 5?',
                'Per què ctype_digit("") retorna false? Quina IP incorrecta detecta això?',
            ],
            'solucio' => $sol2,
            'solucio_explicacio' => 'El primer for valida totes les IP abans de mostrar res; el segon munta el bloc. esIpValida() separa la idea de «què és una IP» del programa principal.',
        ],
        [
            'titol' => 'Redirigir una pàgina',
            'conceptes' => ['arguments per consola', 'validació de dades'],
            'xp' => 15,
            'explicacio' => <<<'HTML'
                <p><strong>Objectiu:</strong> a <code>redirigeix</code>, comprova que hi ha <strong>exactament 2 rutes</strong> i que
                <strong>totes dues comencen per /</strong>:</p>
                <ul>
                  <li>Si no n'hi ha 2: <code>ERROR: redirigeix necessita exactament 2 rutes.</code> i <code>exit(1)</code>.</li>
                  <li>Si alguna no comença per <code>/</code>: <code>ERROR: les rutes han de començar per /.</code> i <code>exit(1)</code>.</li>
                </ul>
                <pre class="term">$ php htaccess.php redirigeix /contacte.html /contacte
                # Redirecció permanent
                Redirect 301 /contacte.html /contacte</pre>
                <p class="nota">El primer caràcter d'un text és <code>substr($text, 0, 1)</code>. També existeix <code>$text[0]</code>, però amb un text buit (<code>""</code>) dona un avís.</p>
                HTML,
            'codi' => [],
            'proves' => [
                ['nom' => 'Redirecció correcta', 'args' => ['redirigeix', '/contacte.html', '/contacte'], 'conte' => ['# Redirecció permanent', 'Redirect 301 /contacte.html /contacte']],
                ['nom' => 'Ruta sense /', 'args' => ['redirigeix', 'contacte.html', '/contacte'], 'conte' => ['ERROR: les rutes han de començar per /.'], 'codi_sortida' => 1],
                ['nom' => 'Segona ruta sense /', 'args' => ['redirigeix', '/a.html', 'b.html'], 'conte' => ['ERROR: les rutes han de començar per /.'], 'codi_sortida' => 1],
                ['nom' => 'Només una ruta', 'args' => ['redirigeix', '/a.html'], 'conte' => ['ERROR: redirigeix necessita exactament 2 rutes.'], 'codi_sortida' => 1],
                ['nom' => 'Ruta buida', 'args' => ['redirigeix', '', '/b'], 'conte' => ['ERROR: les rutes han de començar per /.'], 'codi_sortida' => 1],
                ['nom' => 'Tres rutes', 'args' => ['redirigeix', '/a', '/b', '/c'], 'conte' => ['ERROR: redirigeix necessita exactament 2 rutes.'], 'codi_sortida' => 1],
            ],
            'pistes' => [
                'Primer compta els arguments (si no n\'hi ha 2, no té sentit mirar-ne el primer caràcter) i després mira el primer caràcter de cada ruta.',
                'Amb la comanda i 2 rutes, $argc val 4. substr($antiga, 0, 1) != "/" vol dir que no comença per barra.',
                "if (\$argc != 4) {\n    echo \"ERROR: redirigeix necessita exactament 2 rutes.\" . PHP_EOL;\n    exit(1);\n}\nif (substr(\$argv[2], 0, 1) != \"/\" || substr(\$argv[3], 0, 1) != \"/\") {\n    // ...\n}",
            ],
            'preguntes' => [
                'Per què cal comprovar el nombre d\'arguments abans de mirar la primera lletra de $argv[2]?',
                'Què vol dir el 301 de la redirecció? Quan faries servir un 302?',
            ],
            'solucio' => $sol3,
            'solucio_explicacio' => 'L\'ordre de les comprovacions importa: si faltés una ruta, $argv[3] no existiria i PHP mostraria un avís.',
        ],
    ],
];
