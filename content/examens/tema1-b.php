<?php
/**
 * Simulacre B del Tema 1 (Introducció al desenvolupament web).
 */

return [
    'id' => 'tema1-b',
    'ordre' => 12,
    'titol' => 'Tema 1 · Simulacre B',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'resum' => 'Mètodes i codis HTTP, l\'arquitectura MVC, l\'entorn de desenvolupament (XAMPP) i els primers programes en PHP.',
    'minuts' => 35,
    'xp' => 40,
    'punts_teoria' => 7,

    'teoria' => [
        [
            'id' => 'q1', 'tipus' => 'test', 'conceptes' => ['mètodes HTTP'],
            'pregunta' => 'Amb quin mètode HTTP les dades d\'un formulari <strong>viatgen a la URL</strong>?',
            'opcions' => ['POST', 'GET', 'PUT', 'DELETE'],
            'correcta' => 1,
            'explicacio' => 'Amb GET les dades van a la URL (?nom=Anna). Amb POST van dins del cos de la petició.',
        ],
        [
            'id' => 'q2', 'tipus' => 'test', 'conceptes' => ['codis d\'estat'],
            'pregunta' => 'Demanes <code>/contacte.html</code> però el fitxer no existeix. Quin codi retorna el servidor?',
            'opcions' => ['200', '301', '404', '500'],
            'correcta' => 2,
            'explicacio' => '404 Not Found: és un error del client (4xx), que ha demanat una adreça que no existeix.',
        ],
        [
            'id' => 'q3', 'tipus' => 'test', 'conceptes' => ['entorn de desenvolupament'],
            'pregunta' => 'Dins de XAMPP, quin component fa de <strong>servidor web</strong>?',
            'opcions' => ['MySQL', 'Apache', 'phpMyAdmin', 'FileZilla'],
            'correcta' => 1,
            'explicacio' => 'Apache és el servidor web (rep les peticions HTTP i executa el PHP). MySQL/MariaDB és la base de dades.',
        ],
        [
            'id' => 'q4', 'tipus' => 'test', 'conceptes' => ['entorn de desenvolupament'],
            'pregunta' => 'A XAMPP per a Windows, a quina carpeta van els fitxers de la web?',
            'opcions' => ['C:\\xampp\\php', 'C:\\xampp\\htdocs', 'C:\\xampp\\apache\\bin', 'C:\\Windows\\www'],
            'correcta' => 1,
            'explicacio' => 'htdocs és l\'arrel de documents d\'Apache: http://localhost/ hi correspon.',
        ],
        [
            'id' => 'q5', 'tipus' => 'test', 'conceptes' => ['MVC'],
            'pregunta' => 'En MVC, on hi ha de ser la consulta a la base de dades?',
            'opcions' => ['A la vista', 'Al model', 'Al full d\'estils', 'Al navegador'],
            'correcta' => 1,
            'explicacio' => 'El model gestiona les dades (BD, validacions de negoci). La vista només presenta.',
        ],
        [
            'id' => 'q6', 'tipus' => 'sortida', 'conceptes' => ['PHP i HTML'],
            'pregunta' => 'Quin HTML genera?',
            'codi' => "<?php\n\$hora = 9;\nif (\$hora < 12) {\n    echo \"<p>Bon dia</p>\";\n} else {\n    echo \"<p>Bona tarda</p>\";\n}",
            'resposta' => '<p>Bon dia</p>',
            'explicacio' => 'Com a la pàgina dinàmica del Tema 1: el servidor tria quin HTML envia segons una condició.',
        ],
        [
            'id' => 'q7', 'tipus' => 'sortida', 'conceptes' => ['operadors'],
            'pregunta' => 'Què imprimeix?',
            'codi' => "<?php\n\$preu = 10;\n\$quantitat = 3;\necho \"Total: \" . \$preu * \$quantitat . \" €\";",
            'resposta' => 'Total: 30 €',
            'explicacio' => 'La multiplicació es fa abans de concatenar: 10 × 3 = 30.',
        ],
        [
            'id' => 'q8', 'tipus' => 'error', 'conceptes' => ['PHP i HTML'],
            'pregunta' => 'Parse error! A quina línia és l\'error?',
            'codi' => "<?php\n\$nom = \"Pau\";\necho \"<h1>Hola, \$nom</h1>;\necho \"<p>Benvingut</p>\";",
            'linia' => 3,
            'corregit' => "<?php\n\$nom = \"Pau\";\necho \"<h1>Hola, \$nom</h1>\";\necho \"<p>Benvingut</p>\";",
            'esperat' => '<h1>Hola, Pau</h1><p>Benvingut</p>',
            'explicacio' => 'A la línia 3 no es tanquen les cometes del text: tot el que segueix queda dins del text i PHP es perd.',
        ],
        [
            'id' => 'q9', 'tipus' => 'test', 'conceptes' => ['HTTP i HTTPS'],
            'pregunta' => 'Què afegeix HTTPS respecte d\'HTTP?',
            'opcions' => ['Més velocitat', 'Xifrat de la comunicació (TLS) entre navegador i servidor', 'Suport per a PHP', 'Bases de dades'],
            'correcta' => 1,
            'explicacio' => 'HTTPS és HTTP sobre TLS: les dades viatgen xifrades i el servidor s\'identifica amb un certificat.',
        ],
        [
            'id' => 'q10', 'tipus' => 'ordenar', 'conceptes' => ['MVC'],
            'pregunta' => 'Ordena què passa quan l\'usuari demana la llista de productes en una aplicació MVC:',
            'elements' => [
                'El navegador envia GET /productes',
                'El controlador rep la petició',
                'El controlador demana els productes al model',
                'El model els llegeix de la base de dades',
                'La vista genera l\'HTML de la llista',
                'El servidor envia la resposta',
            ],
            'explicacio' => 'El controlador coordina: model per a les dades, vista per a l\'HTML.',
        ],
    ],

    'practica' => [
        [
            'id' => 'p1',
            'titol' => 'Explicador de mètodes HTTP',
            'fitxer' => 'metode.php',
            'punts' => 3,
            'enunciat' => <<<'HTML'
                <p>Fes un programa que expliqui un mètode HTTP. Rep el mètode com a argument, en majúscules o minúscules.</p>
                <pre class="term">$ php metode.php get
                GET: demana un recurs. Les dades viatgen a la URL.
                $ php metode.php POST
                POST: envia dades al servidor dins del cos de la petició.
                $ php metode.php PATCH
                ERROR: el mètode «PATCH» no és GET ni POST.</pre>
                <ul>
                  <li>Sense argument: <code>Ús: php metode.php &lt;mètode&gt;</code> i <code>exit(1)</code>.</li>
                  <li>Un mètode que no sigui GET ni POST: el missatge d'error de l'exemple (amb el mètode en majúscules) i <code>exit(1)</code>.</li>
                  <li>Per passar un text a majúscules: <code>strtoupper($text)</code>.</li>
                </ul>
                HTML,
            'codi_inicial' => "<?php\n// Explicador de mètodes HTTP: php metode.php <mètode>\n\n",
            'proves' => [
                ['nom' => 'GET', 'args' => ['GET'], 'exacte' => 'GET: demana un recurs. Les dades viatgen a la URL.'],
                ['nom' => 'get en minúscules', 'args' => ['get'], 'conte' => ['GET: demana un recurs. Les dades viatgen a la URL.']],
                ['nom' => 'POST', 'args' => ['Post'], 'conte' => ['POST: envia dades al servidor dins del cos de la petició.']],
                ['nom' => 'Mètode desconegut', 'args' => ['patch'], 'conte' => ['ERROR: el mètode «PATCH» no és GET ni POST.'], 'codi_sortida' => 1],
                ['nom' => 'Sense argument', 'args' => [], 'conte' => ['Ús: php metode.php <mètode>'], 'codi_sortida' => 1],
            ],
            'solucio' => <<<'PHP'
                <?php
                // Explicador de mètodes HTTP: php metode.php <mètode>

                if ($argc != 2) {
                    echo "Ús: php metode.php <mètode>" . PHP_EOL;
                    exit(1);
                }
                $metode = strtoupper($argv[1]);

                if ($metode == "GET") {
                    echo "GET: demana un recurs. Les dades viatgen a la URL." . PHP_EOL;
                } elseif ($metode == "POST") {
                    echo "POST: envia dades al servidor dins del cos de la petició." . PHP_EOL;
                } else {
                    echo "ERROR: el mètode «{$metode}» no és GET ni POST." . PHP_EOL;
                    exit(1);
                }
                PHP,
            'solucio_explicacio' => 'Passant el mètode a majúscules abans de comparar, «get», «Get» i «GET» són el mateix.',
        ],
    ],
];
