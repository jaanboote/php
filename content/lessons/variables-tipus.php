<?php
/**
 * Lliçó: Variables, tipus i constants (Tema 2, apartats 2.1 i 2.2).
 */

$origen = 'Tema 2 - Fonaments PHP.pdf';

return [
    'id' => 'variables-tipus',
    'ordre' => 10,
    'titol' => 'Variables, tipus i constants',
    'tema' => 'Tema 2 – Fonaments de PHP',
    'apartat' => '2.1 El llenguatge PHP · 2.2 Sintaxi, variables i tipus',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'alerta-meteo',
    'resum' => 'Com mostra PHP la informació, com funcionen les variables i els tipus, les cometes, les conversions i les constants.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'PHP s\'executa al servidor',
            'origen' => "$origen · pàg. 4-8",
            'html' => <<<'HTML'
                <p>PHP és un llenguatge <strong>interpretat</strong> que s'executa al <strong>servidor</strong> (Apache, Nginx), no al navegador.
                Quan demanes <code>producte.php</code>, el servidor executa el codi i només retorna HTML: l'usuari mai no veu el PHP.</p>
                <p>També es pot executar des de la terminal (<strong>CLI</strong>) amb <code>php fitxer.php</code>, ideal per a scripts d'administració.
                Els exemples d'aquesta app s'executen així, per això fem servir <code>PHP_EOL</code> per saltar de línia (en una pàgina web seria <code>&lt;br&gt;</code>).</p>
                <table class="taula">
                  <tr><th>Instrucció</th><th>Per a què</th></tr>
                  <tr><td>echo / print</td><td>Mostrar text</td></tr>
                  <tr><td>print_r()</td><td>Veure l'estructura (arrays)</td></tr>
                  <tr><td>var_dump()</td><td>Veure tipus i valors</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: echo, print_r i var_dump',
            'fitxer' => 'vars.php',
            'html' => '<p>Canvia els valors i mira com canvien el tipus i la sortida.</p>',
            'codi' => <<<'PHP'
                <?php
                $nom = "Laia";
                echo "Hola, $nom" . PHP_EOL;
                echo "Suma: ", 2 + 3, PHP_EOL;

                $dades = [10, "text", true];
                print_r($dades);   // estructura
                var_dump($dades);  // tipus i valors

                $x = 42;
                echo gettype($x) . PHP_EOL;  // integer
                $x = "42";                   // el tipus pot canviar!
                echo gettype($x) . PHP_EOL;  // string
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Variables: noms i tipatge dinàmic',
            'origen' => "$origen · pàg. 15-16",
            'html' => <<<'HTML'
                <ul>
                  <li>Comencen amb <code>$</code> seguit d'una lletra o de <code>_</code>. Després: lletres, números i guions baixos.</li>
                  <li>Distingeixen majúscules: <code>$nom</code> i <code>$Nom</code> són dues variables diferents.</li>
                  <li>No cal declarar el tipus: depèn del valor i pot canviar durant l'execució (<strong>tipatge dinàmic</strong>).</li>
                </ul>
                <p>Bona pràctica: noms descriptius i un estil coherent, <code>$preuTotal</code> (camelCase) o <code>$preu_total</code> (snake_case).</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['variables'],
            'pregunta' => 'Quin d\'aquests noms de variable <strong>no</strong> és vàlid?',
            'opcions' => ['$_total', '$preu2', '$2preu', '$Nom'],
            'correcta' => 2,
            'explicacio' => 'Després del $ hi ha d\'anar una lletra o un guió baix, mai un número. També estarien malament $preu-total (guió) i $preu total (espai).',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Cometes dobles, simples i concatenació',
            'origen' => "$origen · pàg. 25-27",
            'html' => <<<'HTML'
                <ul>
                  <li><strong>Cometes dobles</strong> <code>" "</code>: substitueixen les variables i interpreten <code>\n</code>, <code>\t</code>...</li>
                  <li><strong>Cometes simples</strong> <code>' '</code>: el text tal qual, sense substituir res.</li>
                  <li>Les claus <code>{ }</code> delimiten la variable: <code>"{$nom}s"</code> funciona, però <code>"$noms"</code> buscaria la variable <code>$noms</code>.</li>
                  <li>El punt <code>.</code> concatena i <code>.=</code> afegeix al final. El <code>+</code> <strong>només suma</strong>: <code>"Hola " + $nom</code> dona TypeError.</li>
                </ul>
                <p>Compte en copiar codi de Word o PowerPoint: les cometes tipogràfiques (“ ”) donen error.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: cometes',
            'fitxer' => 'cometes.php',
            'codi' => <<<'PHP'
                <?php
                $nom = "Laia";
                $cognom = 'Puig';

                echo "Hola $nom $cognom" . PHP_EOL;
                echo 'Hola $nom' . PHP_EOL;
                echo "Hola {$nom}!" . PHP_EOL;
                echo "Diu: \"Bon dia\"" . PHP_EOL;

                $text = "Línia 1";
                $text .= PHP_EOL . "Línia 2";
                echo $text . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['strings i cometes'],
            'pregunta' => 'Què imprimeix aquest codi?',
            'codi' => <<<'PHP'
                <?php
                $nom = "Laia";
                echo 'Hola $nom' . " i {$nom}s";
                PHP,
            'resposta' => 'Hola $nom i Laias',
            'explicacio' => 'Amb cometes simples, $nom no se substitueix. Amb cometes dobles i claus, {$nom}s dona "Laias".',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Booleans i conversions de tipus',
            'origen' => "$origen · pàg. 29-31",
            'html' => <<<'HTML'
                <p>Quan PHP converteix a <code>bool</code>, són <strong>false</strong>: <code>0</code>, <code>0.0</code>, <code>""</code>, <code>"0"</code>, <code>[]</code> i <code>null</code>.
                Tota la resta és <strong>true</strong>, fins i tot <code>"0.0"</code>, <code>" "</code> i <code>"false"</code>!</p>
                <p>Conversió automàtica: <code>"10" + 5</code> dona 15 i <code>"10" . 5</code> dona "105". Conversió explícita (<em>casting</em>):
                <code>(int)</code>, <code>(float)</code>, <code>(string)</code>, <code>(bool)</code>. Compte: <code>(int) 7.9</code> és 7 (trunca, no arrodoneix).</p>
                <p>Per comprovar el tipus: <code>is_int()</code>, <code>is_float()</code>, <code>is_string()</code>, <code>is_bool()</code>, <code>is_array()</code> i <code>is_numeric()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: conversions',
            'fitxer' => 'casting.php',
            'codi' => <<<'PHP'
                <?php
                var_dump((bool) "0");     // false
                var_dump((bool) "0.0");   // true !
                var_dump((bool) []);      // false
                var_dump((int) 7.9);      // 7
                var_dump("10" + 5);       // 15
                var_dump("10" . 5);       // "105"
                var_dump(intval("12abc"));
                var_dump(is_numeric("12.5"));
                var_dump(0.1 + 0.2 == 0.3);   // false! error de coma flotant
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q3',
            'conceptes' => ['tipus i casting'],
            'pregunta' => 'Què mostra <code>var_dump((bool) "0.0");</code>?',
            'opcions' => ['bool(false)', 'bool(true)', 'float(0)', 'Un error'],
            'correcta' => 1,
            'explicacio' => 'Només "" i "0" són strings falsos. "0.0" és un text no buit diferent de "0", per tant és true.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Constants',
            'origen' => "$origen · pàg. 23",
            'html' => <<<'HTML'
                <p>Una constant guarda un valor que no pot canviar. No porta <code>$</code> i, per convenció, va en MAJÚSCULES.
                Es defineix amb <code>const IVA = 0.21;</code> (recomanat) o <code>define("MAX_USUARIS", 50);</code>.</p>
                <p>Constants predefinides útils: <code>PHP_VERSION</code>, <code>PHP_OS</code>, <code>PHP_EOL</code>, <code>PHP_INT_MAX</code>, <code>__FILE__</code>, <code>__DIR__</code>, <code>__LINE__</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Àmbit: local, global i static',
            'origen' => "$origen · pàg. 19-20",
            'html' => <<<'HTML'
                <p>Les variables creades dins d'una funció són <strong>locals</strong>: només existeixen allà. I una funció
                <strong>no veu les variables de fora</strong> si no les rep com a paràmetre.</p>
                <p><code>global $x;</code> o <code>$GLOBALS['x']</code> permeten modificar una global des d'una funció (fes-los servir poc).
                <code>static</code> fa que una variable local <strong>conservi el valor</strong> entre crides.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: àmbit i static',
            'fitxer' => 'ambit.php',
            'html' => '<p>Executa-ho: la funció <code>mostrar()</code> provoca un avís perquè no veu <code>$missatge</code>. Arregla-ho passant-la com a paràmetre.</p>',
            'codi' => <<<'PHP'
                <?php
                $missatge = "Hola";

                function mostrar() {
                    echo $missatge . PHP_EOL;   // no la veu!
                }
                mostrar();

                function visites() {
                    static $total = 0;   // es conserva entre crides
                    $total++;
                    return $total;
                }
                echo visites(), visites(), visites(), PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['àmbit de variables'],
            'pregunta' => 'Volem que la funció compti quantes vegades s\'ha cridat (1, 2, 3...). Què hi va a ___ ?',
            'codi' => "function comptador() {\n    ___ \$n = 0;\n    \$n++;\n    return \$n;\n}",
            'respostes' => ['static'],
            'explicacio' => 'static fa que $n no es reiniciï a 0 a cada crida: conserva el valor de la crida anterior.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Còpia o referència?',
            'origen' => "$origen · pàg. 17",
            'html' => <<<'HTML'
                <p>Per defecte PHP <strong>copia</strong>: <code>$copia = $original;</code> crea un valor independent.
                Amb <code>&amp;</code> es crea una <strong>referència</strong>: <code>$ref = &amp;$original;</code> són dos noms per a un sol valor.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q5',
            'conceptes' => ['referències'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$a = 10;\n\$b = \$a;\n\$c = &\$a;\n\$b = 20;\n\$c = 30;\necho \$a . \$b;",
            'resposta' => '3020',
            'explicacio' => '$b és una còpia (val 20 i no afecta $a). $c és una referència a $a, així que $c = 30 canvia $a. Resultat: "30" . "20".',
        ],
    ],
];
