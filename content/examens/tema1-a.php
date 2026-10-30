<?php
/**
 * Simulacre A del Tema 1 (Introducció al desenvolupament web).
 */

return [
    'id' => 'tema1-a',
    'ordre' => 11,
    'titol' => 'Tema 1 · Simulacre A',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'resum' => 'Frontend i backend, llocs estàtics i dinàmics, el model client-servidor i com PHP genera HTML.',
    'minuts' => 35,
    'xp' => 40,
    'punts_teoria' => 7,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'test', 'conceptes' => ['frontend i backend'],
            'pregunta' => 'Quina d\'aquestes tecnologies és de <strong>backend</strong>?',
            'opcions' => ['CSS', 'HTML', 'PHP', 'JavaScript al navegador'],
            'correcta' => 2,
            'explicacio' => 'PHP s\'executa al servidor (backend). HTML, CSS i el JavaScript del navegador són frontend.',
        ],
        [
            'id' => 'q2', 'tipus' => 'test', 'conceptes' => ['estàtic i dinàmic'],
            'pregunta' => 'Què caracteritza un lloc web <strong>dinàmic</strong>?',
            'opcions' => ['Que té animacions CSS', 'Que el contingut es genera al servidor en cada petició i pot canviar', 'Que només fa servir HTML', 'Que no necessita servidor'],
            'correcta' => 1,
            'explicacio' => 'Un lloc dinàmic genera la pàgina en cada petició (amb PHP, dades d\'una BD...). Un d\'estàtic envia sempre els mateixos fitxers.',
        ],
        [
            'id' => 'q3', 'tipus' => 'test', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Què fa el navegador <strong>just abans</strong> de connectar-se al servidor?',
            'opcions' => ['Renderitza l\'HTML', 'Resol el nom de domini amb el DNS per obtenir la IP', 'Executa el PHP', 'Envia la resposta HTTP'],
            'correcta' => 1,
            'explicacio' => 'Sense la IP no es pot connectar: primer es resol el domini (DNS) i després s\'obre la connexió TCP/IP.',
        ],
        [
            'id' => 'q4', 'tipus' => 'test', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Quin port fa servir per defecte HTTPS?',
            'opcions' => ['80', '443', '3306', '8080'],
            'correcta' => 1,
            'explicacio' => 'HTTP fa servir el 80 i HTTPS el 443. El 3306 és el de MySQL.',
        ],
        [
            'id' => 'q5', 'tipus' => 'sortida', 'conceptes' => ['PHP i HTML'],
            'pregunta' => 'Quin HTML rep el navegador?',
            'codi' => "<?php\n\$titol = \"Botiga\";\n\$productes = 3;\necho \"<h1>\$titol</h1><p>\$productes productes</p>\";",
            'resposta' => '<h1>Botiga</h1><p>3 productes</p>',
            'explicacio' => 'PHP substitueix les variables dins de les cometes dobles i envia només l\'HTML resultant.',
        ],
        [
            'id' => 'q6', 'tipus' => 'test', 'conceptes' => ['client i servidor'],
            'pregunta' => 'Si fas «Mostra el codi font» d\'una pàgina feta amb PHP, què veus?',
            'opcions' => ['El codi PHP original', 'L\'HTML que ha generat el PHP', 'Res, està xifrat', 'El PHP i l\'HTML barrejats'],
            'correcta' => 1,
            'explicacio' => 'El PHP s\'executa al servidor: al navegador només hi arriba el resultat (HTML, CSS, JS).',
        ],
        [
            'id' => 'q7', 'tipus' => 'sortida', 'conceptes' => ['variables'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$curs = \"ASIX\";\necho \"Curs: \$curs\" . ' i \$curs';",
            'resposta' => 'Curs: ASIX i $curs',
            'explicacio' => 'Les cometes dobles interpreten $curs; les simples el deixen tal qual.',
        ],
        [
            'id' => 'q8', 'tipus' => 'test', 'conceptes' => ['MVC'],
            'pregunta' => 'En MVC, quina part rep la petició i decideix què cal fer?',
            'opcions' => ['El model', 'La vista', 'El controlador', 'El navegador'],
            'correcta' => 2,
            'explicacio' => 'El controlador rep la petició, demana les dades al model i les passa a la vista.',
        ],
        [
            'id' => 'q9', 'tipus' => 'error', 'conceptes' => ['sintaxi'],
            'pregunta' => 'Aquest programa no funciona. A quina línia és l\'error?',
            'codi' => "<?php\n\$nom = \"Anna\";\necho \"Hola, \" . nom . \"!\" . PHP_EOL;",
            'linia' => 3,
            'corregit' => "<?php\n\$nom = \"Anna\";\necho \"Hola, \" . \$nom . \"!\" . PHP_EOL;",
            'esperat' => 'Hola, Anna!',
            'explicacio' => 'Falta el $ davant de nom: sense ell, PHP busca una constant «nom» que no existeix (Fatal error).',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Ordena el cicle d\'una petició web:',
            'elements' => [
                'L\'usuari escriu una URL',
                'Resolució DNS',
                'Connexió TCP/IP amb el servidor',
                'Petició HTTP',
                'El servidor executa el PHP',
                'Resposta HTTP amb codi d\'estat',
                'El navegador renderitza la pàgina',
            ],
            'explicacio' => 'És el cicle de 7 passos de la diapositiva 34 del Tema 1.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Targeta de presentació en HTML',
            'fitxer' => 'targeta.php',
            'punts' => 3,
            'enunciat' => <<<'HTML'
                <p>Fes un programa que generi l'HTML d'una targeta de presentació. Rep dos arguments: el nom i el cicle.</p>
                <pre class="term">$ php targeta.php Anna ASIX2
                &lt;div class="targeta"&gt;
                  &lt;h2&gt;Anna&lt;/h2&gt;
                  &lt;p&gt;Alumne/a de ASIX2&lt;/p&gt;
                &lt;/div&gt;</pre>
                <ul>
                  <li>Si no hi ha exactament 2 arguments, mostra <code>Ús: php targeta.php &lt;nom&gt; &lt;cicle&gt;</code> i atura el programa amb <code>exit(1)</code>.</li>
                  <li>Respecta les línies i els espais de l'exemple.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Targeta de presentació: php targeta.php <nom> <cicle>\n\n",
            'proves' => [
                ['nom' => 'Anna, ASIX2', 'args' => ['Anna', 'ASIX2'], 'exacte' => "<div class=\"targeta\">\n  <h2>Anna</h2>\n  <p>Alumne/a de ASIX2</p>\n</div>"],
                ['nom' => 'Pau, DAW1', 'args' => ['Pau', 'DAW1'], 'conte' => ['<h2>Pau</h2>', '<p>Alumne/a de DAW1</p>']],
                ['nom' => 'Sense arguments', 'args' => [], 'conte' => ['Ús: php targeta.php <nom> <cicle>'], 'codi_sortida' => 1],
                ['nom' => 'Només el nom', 'args' => ['Anna'], 'conte' => ['Ús: php targeta.php <nom> <cicle>'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Targeta de presentació: php targeta.php <nom> <cicle>

                if ($argc != 3) {
                    echo "Ús: php targeta.php <nom> <cicle>" . PHP_EOL;
                    exit(1);
                }
                $nom = $argv[1];
                $cicle = $argv[2];

                echo '<div class="targeta">' . PHP_EOL;
                echo "  <h2>$nom</h2>" . PHP_EOL;
                echo "  <p>Alumne/a de $cicle</p>" . PHP_EOL;
                echo "</div>" . PHP_EOL;
                PHP,
            'solucio_explicacio' => 'L\'atribut class porta cometes dobles: és més fàcil escriure aquella línia entre cometes simples. A les altres, les cometes dobles permeten posar-hi les variables.',
        ],
    ],
];
