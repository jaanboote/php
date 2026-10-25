<?php
/**
 * Lliçó: Arguments per consola ($argv i $argc).
 * Basada en «Com treballar aquests exercicis» (Exercicis d'arguments per consola, Tema 2.7).
 *
 * Tipus de blocs:
 *  - text:     HTML
 *  - codi:     exemple executable (prova-ho) amb arguments/entrada per defecte
 *  - test:     pregunta tipus test (opcions + índex de la correcta)
 *  - completa: completar codi (respostes acceptades)
 *  - sortida:  "què imprimeix?" (resposta exacta, amb espais normalitzats)
 * Qualsevol bloc pot portar 'ampliacio' => true si no surt del temari.
 */

$origen = 'Exercicis_02.Arguments_per_consola_Enunciat.pdf';

return [
    'id' => 'arguments-consola',
    'ordre' => 1,
    'titol' => 'Arguments per consola: $argv i $argc',
    'tema' => 'Tema 2 – Fonaments PHP',
    'apartat' => '2.7 Arguments per consola',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'permisos',
    'resum' => 'Com rep un script PHP les dades quan l\'executes des del terminal, i com validar-les perquè no peti mai.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'De readline() als arguments',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>Fins ara, els programes demanaven les dades <em>mentre s'executaven</em> amb <code>readline()</code>.
                Però la majoria d'eines de terminal que ja coneixes reben les dades d'una altra manera: s'escriuen
                a la mateixa línia de comandes, just quan llances el programa.</p>
                <pre class="term">$ ping -c 4 8.8.8.8
                $ git commit -m "Primera versió"
                $ php xarxa.php 192.168.1.37 24</pre>
                <p>Cada valor separat per espais és un <strong>argument</strong>. És com quan configures un servei:
                en lloc que et pregunti les opcions una a una, li passes tot d'entrada. Això permet fer servir els
                scripts en tasques automàtiques (un <code>cron</code> que fa còpies de seguretat cada nit no pot respondre preguntes!).</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Com rep PHP els arguments',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>PHP deixa els arguments a dues variables predefinides que ja existeixen quan el programa comença:</p>
                <ul>
                  <li><code>$argv</code>: un <strong>array</strong> amb tots els elements de la línia.</li>
                  <li><code>$argc</code>: <strong>quants</strong> elements té <code>$argv</code>.</li>
                </ul>
                <table class="taula">
                  <tr><th>Element</th><th>Valor</th><th>Observació</th></tr>
                  <tr><td><code>$argv[0]</code></td><td>"xarxa.php"</td><td>El nom del script. <strong>Sempre hi és.</strong></td></tr>
                  <tr><td><code>$argv[1]</code></td><td>"192.168.1.37"</td><td>Primer argument</td></tr>
                  <tr><td><code>$argv[2]</code></td><td>"24"</td><td>Segon argument</td></tr>
                  <tr><td><code>$argc</code></td><td>3</td><td>Nombre total d'elements de $argv</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: mira què hi ha a $argv',
            'fitxer' => 'xarxa.php',
            'args' => '192.168.1.37 24',
            'html' => '<p>Executa-ho i després canvia els arguments de la caixa (afegeix-ne, treu-ne...). Fixa\'t com canvia <code>$argc</code>.</p>',
            'codi' => <<<'PHP'
                <?php
                print_r($argv);
                echo "Hi ha $argc elements a \$argv" . PHP_EOL;
                echo "L'adreça és: " . $argv[1] . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['$argv i $argc'],
            'pregunta' => 'Executem <code>php xarxa.php 10.0.0.1 16</code>. Quant val <code>$argc</code>?',
            'opcions' => ['2', '3', '1', '16'],
            'correcta' => 1,
            'explicacio' => '$argc compta tots els elements de $argv, i el primer sempre és el nom del script: ["xarxa.php", "10.0.0.1", "16"] → 3.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Tot arriba com a text',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>Compte: <strong>tots els arguments arriben com a <code>string</code></strong>, encara que semblin números.
                <code>"24"</code> no és el mateix que <code>24</code>. PHP de vegades els converteix sol quan fas operacions,
                però per validar-los fes servir funcions com <code>is_numeric()</code> i converteix-los amb <code>(int)</code> o <code>(float)</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: de quin tipus és?',
            'fitxer' => 'tipus.php',
            'args' => '24 hola 1.5',
            'html' => '<p>Mira què diu <code>var_dump()</code> de cada argument. Prova a canviar-los.</p>',
            'codi' => <<<'PHP'
                <?php
                for ($i = 1; $i < $argc; $i++) {
                    var_dump($argv[$i]);
                    echo is_numeric($argv[$i]) ? "  → és numèric" : "  → NO és numèric";
                    echo PHP_EOL;
                }
                $mascara = (int) $argv[1];
                var_dump($mascara);
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['$argv i $argc', 'concatenació'],
            'pregunta' => 'Executem <code>php suma.php 3 4</code>. Què imprimeix?',
            'codi' => "<?php\necho \$argv[1] . \$argv[2];",
            'resposta' => '34',
            'explicacio' => 'El punt (.) concatena textos: "3" . "4" és "34". Per sumar caldria fer servir + (i millor validar abans amb is_numeric()).',
        ],
        [
            'tipus' => 'text',
            'titol' => 'El programa no ha de petar mai',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>L'usuari pot escriure qualsevol cosa, o no escriure res. Si llegeixes <code>$argv[1]</code> i no existeix,
                PHP mostra un avís (<code>Warning: Undefined array key 1</code>). Un bon script <strong>detecta el problema i l'explica</strong>.</p>
                <p>El patró és sempre el mateix: <strong>primer comprovar <code>$argc</code>, després llegir <code>$argv</code></strong>.
                Si hi ha un error, s'explica i s'atura el programa amb <code>exit</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: arregla aquest script',
            'fitxer' => 'salutacio.php',
            'args' => '',
            'html' => '<p>Executa\'l <strong>sense arguments</strong>: veuràs l\'avís explicat. Després descomenta les línies de la validació (treu les <code>//</code>) i torna-ho a provar.</p>',
            'codi' => <<<'PHP'
                <?php
                // if ($argc != 2) {
                //     echo "ERROR: s'esperava 1 argument (el teu nom)." . PHP_EOL;
                //     exit(1);
                // }
                $nom = $argv[1];
                echo "Hola, $nom!" . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['$argv i $argc', 'validació de dades'],
            'pregunta' => 'Un script necessita <strong>exactament 2 arguments</strong>. Completa la condició:',
            'codi' => "if (\$argc ___ 3) {\n    echo \"ERROR: calen 2 arguments.\" . PHP_EOL;\n    exit(1);\n}",
            'respostes' => ['!=', '!==', '<>'],
            'explicacio' => 'Amb 2 arguments, $argc val 3 (el nom del script + 2). Si és diferent de 3 (!=), hi ha un error.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Espais i cometes',
            'origen' => $origen,
            'html' => <<<'HTML'
                <p>L'espai separa arguments. Si un valor conté espais, s'ha d'escriure entre cometes:
                <code>"Fer la compra"</code> és <strong>un sol</strong> argument; <code>Fer la compra</code> en són <strong>tres</strong>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: amb i sense cometes',
            'fitxer' => 'tasca.php',
            'args' => '"Fer la compra" alta',
            'html' => '<p>Prova primer així i després treu les cometes de la caixa d\'arguments.</p>',
            'codi' => <<<'PHP'
                <?php
                echo "Arguments rebuts: " . ($argc - 1) . PHP_EOL;
                for ($i = 1; $i < $argc; $i++) {
                    echo "  [$i] => " . $argv[$i] . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['$argv i $argc'],
            'pregunta' => 'Executem <code>php tasca.php Fer la compra</code>. Què val <code>$argv[1]</code>?',
            'opcions' => ['"Fer la compra"', '"Fer"', '"tasca.php"', 'No existeix'],
            'correcta' => 1,
            'explicacio' => 'Sense cometes, cada paraula és un argument: $argv[1] = "Fer", $argv[2] = "la", $argv[3] = "compra".',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Per saber-ne més',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <ul>
                  <li><code>$argv</code> i <code>$argc</code> només existeixen quan executes PHP des del terminal (CLI).
                  Quan PHP l'executa Apache (una pàgina web), les dades arriben per <code>$_GET</code> i <code>$_POST</code>:
                  ho veureu amb els formularis.</li>
                  <li><code>ctype_digit($text)</code> retorna <code>true</code> si <em>tots</em> els caràcters són dígits (0-9).
                  És més estricta que <code>is_numeric()</code>, que també accepta <code>"1.5"</code> o <code>"-3"</code>.</li>
                  <li>El valor de <code>exit(1)</code> és el <em>codi de sortida</em>: 0 vol dir «tot bé» i qualsevol altre, error.
                  En un script de bash el pots consultar amb <code>echo $?</code>.</li>
                </ul>
                HTML,
        ],
    ],
];
