<?php
/**
 * Banc de reptes del Tema 1 (Introducció al desenvolupament web). Format: mira content/reptes/tema2.php.
 */

return [
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'items' => [
        [
            'id' => 't1-https', 'tipus' => 'test', 'conceptes' => ['HTTP i HTTPS'],
            'pregunta' => 'Per quin port escolta un servidor web per a HTTPS?',
            'opcions' => ['80', '443', '8080', '3306'],
            'correcta' => 1,
            'explicacio' => 'HTTP fa servir el 80 i HTTPS el 443. El 3306 és el de MySQL/MariaDB.',
        ],
        [
            'id' => 't1-mvc-validar', 'tipus' => 'test', 'conceptes' => ['MVC'],
            'pregunta' => 'En una aplicació MVC, quina capa rep la petició i valida les dades d\'un formulari?',
            'opcions' => ['El Model', 'La Vista', 'El Controlador', 'La base de dades'],
            'correcta' => 2,
            'explicacio' => 'El Controlador rep la petició, valida les dades, crida el Model i passa el resultat a la Vista.',
        ],
        [
            'id' => 't1-dinamic', 'tipus' => 'test', 'conceptes' => ['estàtic i dinàmic'],
            'pregunta' => 'Quin d\'aquests llocs ha de ser <strong>dinàmic</strong>?',
            'opcions' => ['El portfoli personal d\'un dissenyador', 'La documentació d\'un programa', 'Una botiga en línia amb carret i estoc', 'Una pàgina de presentació d\'un esdeveniment'],
            'correcta' => 2,
            'explicacio' => 'La botiga mostra dades que canvien (estoc, preus, carret de cada usuari) i necessita una base de dades.',
        ],
        [
            'id' => 't1-404', 'tipus' => 'test', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Què vol dir el codi d\'estat <strong>404</strong>?',
            'opcions' => ['Tot correcte', 'Error intern del servidor', 'La URL demanada no existeix', 'Cal iniciar sessió'],
            'correcta' => 2,
            'explicacio' => '404 Not Found: el servidor funciona, però no troba res en aquella ruta.',
        ],
        [
            'id' => 't1-errata-fitxer', 'tipus' => 'error', 'conceptes' => ['variables'], 'errata' => 'Tema 1, diapositiva 20',
            'pregunta' => 'Aquest exemple de la diapositiva 20 del temari no funciona. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $fitxer = fopen("fitxer.txt", "w");
                fwrite($file, "Contingut del fitxer");
                fclose($fitxer);
                echo "Fet" . PHP_EOL;
                PHP,
            'linia' => 3,
            'corregit' => <<<'PHP'
                <?php
                $fitxer = fopen("fitxer.txt", "w");
                fwrite($fitxer, "Contingut del fitxer");
                fclose($fitxer);
                echo "Fet" . PHP_EOL;
                PHP,
            'esperat' => 'Fet',
            'explicacio' => 'El fitxer s\'obre a $fitxer però fwrite() fa servir $file, que no existeix. És una errata real del temari.',
        ],
        [
            'id' => 't1-errata-cos', 'tipus' => 'test', 'conceptes' => ['PHP i HTML'], 'errata' => 'Tema 1, diapositiva 17',
            'pregunta' => 'L\'exemple de la diapositiva 17 té un error d\'HTML. Quin?',
            'codi' => "<html>\n  <cos>\n    <h1>Hola, <?php echo \$name; ?>!</h1>\n  </body>\n</html>",
            'opcions' => ['Falta el punt i coma després de l\'echo', '<cos> hauria de ser <body>: obre una etiqueta que no existeix i en tanca una altra', 'No es pot posar PHP dins d\'un <h1>', '$name hauria d\'anar entre cometes'],
            'correcta' => 1,
            'explicacio' => 'L\'etiqueta d\'obertura s\'ha traduït per error a «cos». Ha de ser <body> perquè coincideixi amb </body>. (L\'últim ; abans de ?> és opcional.)',
        ],
        [
            'id' => 't1-ord-peticio', 'tipus' => 'ordenar', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Ordena el que passa quan escrius una URL al navegador:',
            'linies' => [
                'Resolució DNS: nom de domini → adreça IP',
                'Connexió TCP/IP amb el servidor (port 80 o 443)',
                'Petició HTTP (GET, POST...)',
                'El servidor executa el PHP i consulta la BD',
                'Resposta HTTP amb codi d\'estat i contingut',
                'El navegador mostra la pàgina',
            ],
            'explicacio' => 'Sense IP no es pot connectar, sense connexió no es pot demanar, i el servidor processa abans de respondre.',
        ],
        [
            'id' => 't1-codi-500', 'tipus' => 'test', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Una pàgina respon amb <code>500 Internal Server Error</code>. On és el problema?',
            'opcions' => ['Al navegador de l\'usuari', 'Al servidor: el programa ha fallat en generar la resposta', 'Al DNS, que no troba el domini', 'A l\'adreça, que està mal escrita'],
            'correcta' => 1,
            'explicacio' => 'Els codis 5xx són errors del servidor (per exemple, un Fatal error de PHP). Una adreça mal escrita donaria un 404, que és de la família 4xx: errors del client.',
        ],
        [
            'id' => 't1-codi-301', 'tipus' => 'test', 'conceptes' => ['cicle de petició'],
            'pregunta' => 'Què vol dir que una resposta HTTP porti el codi <code>301</code>?',
            'opcions' => ['Que tot ha anat bé', 'Que la pàgina s\'ha mogut per sempre a una altra adreça', 'Que no tens permís per veure-la', 'Que el servidor està saturat'],
            'correcta' => 1,
            'explicacio' => 'Els 3xx són redireccions. El 301 és permanent (Moved Permanently): el navegador i els cercadors han d\'anar a la nova adreça a partir d\'ara.',
        ],
        [
            'id' => 't1-on-php', 'tipus' => 'test', 'conceptes' => ['client i servidor'],
            'pregunta' => 'On s\'executa el codi PHP d\'una pàgina web?',
            'opcions' => ['Al navegador, com el JavaScript', 'Al servidor, abans d\'enviar la resposta al client', 'Al servidor DNS', 'Dins de la base de dades'],
            'correcta' => 1,
            'explicacio' => 'PHP és un llenguatge de servidor: s\'executa abans d\'enviar la resposta, i el navegador només rep l\'HTML que ha generat. Per això no pots veure el PHP amb «Mostra el codi font».',
        ],
        [
            'id' => 't1-mvc-vista', 'tipus' => 'test', 'conceptes' => ['MVC'],
            'pregunta' => 'En una aplicació MVC, quina part genera l\'HTML que veu l\'usuari?',
            'opcions' => ['El model', 'La vista', 'El controlador', 'El servidor DNS'],
            'correcta' => 1,
            'explicacio' => 'La vista presenta les dades (l\'HTML). El model gestiona les dades i el controlador rep la petició i decideix què fer.',
        ],
        [
            'id' => 't1-echo-html', 'tipus' => 'sortida', 'conceptes' => ['PHP i HTML'],
            'pregunta' => 'Què envia aquest PHP al navegador?',
            'codi' => "<?php\n\$nom = \"Anna\";\necho \"<h1>Hola, \$nom!</h1>\";",
            'resposta' => '<h1>Hola, Anna!</h1>',
            'explicacio' => 'Entre cometes dobles, PHP substitueix $nom pel seu valor. El navegador rep l\'HTML ja fet, sense cap rastre del PHP.',
        ],
        [
            'id' => 't1-cometes', 'tipus' => 'sortida', 'conceptes' => ['variables'],
            'pregunta' => 'Què imprimeix? (Compte amb les cometes.)',
            'codi' => "<?php\n\$nom = 'Anna';\necho 'Hola, \$nom' . \"!\";",
            'resposta' => 'Hola, $nom!',
            'explicacio' => 'Entre cometes simples, PHP no substitueix les variables: $nom surt tal qual. Només les cometes dobles les interpreten.',
        ],
        [
            'id' => 't1-punt-coma', 'tipus' => 'error', 'conceptes' => ['sintaxi'],
            'pregunta' => 'Aquest programa dona un Parse error. A quina línia és l\'error?',
            'codi' => <<<'PHP'
                <?php
                $curs = "ASIX2"
                echo "Benvinguts a $curs" . PHP_EOL;
                PHP,
            'linia' => 2,
            'corregit' => <<<'PHP'
                <?php
                $curs = "ASIX2";
                echo "Benvinguts a $curs" . PHP_EOL;
                PHP,
            'esperat' => 'Benvinguts a ASIX2',
            'explicacio' => 'Falta el punt i coma al final de la línia 2. PHP es queixa a la línia 3 («unexpected echo») perquè és on se n\'adona, però l\'error és a la 2.',
        ],
        [
            'id' => 't1-ord-mvc', 'tipus' => 'ordenar', 'conceptes' => ['MVC'],
            'pregunta' => 'Ordena el recorregut d\'una petició en una aplicació MVC:',
            'linies' => [
                'El navegador envia la petició',
                'El controlador la rep i decideix què cal fer',
                'El model consulta o desa les dades',
                'La vista genera l\'HTML amb aquestes dades',
                'El servidor envia la resposta al navegador',
            ],
            'explicacio' => 'El controlador coordina: demana les dades al model i les passa a la vista, que genera l\'HTML de la resposta.',
        ],
    ],
];
