<?php
/**
 * Lliçó: Desenvolupament web i PHP (Tema 1, apartats 1.1 i 1.2).
 */

$origen = 'Tema 1 - Introducció al desenvolupament web.pdf';

return [
    'id' => 'desenvolupament-web',
    'ordre' => 1,
    'titol' => 'Desenvolupament web: frontend, backend i PHP',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.1 Desenvolupament web · 1.2 Conceptes bàsics de PHP',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'pagina-dinamica',
    'resum' => 'Què cal analitzar abans d\'instal·lar un servidor, les tres peces d\'una web, estàtic contra dinàmic i per què (i quan) fer servir PHP.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Abans d\'instal·lar: anàlisi de requeriments',
            'origen' => "$origen · diapositives 2-4",
            'html' => <<<'HTML'
                <p>Abans d'instal·lar qualsevol servidor d'aplicacions web, un administrador ha de saber:</p>
                <ul>
                  <li>Quins <strong>serveis</strong> calen (servidor web, base de dades, intèrpret de codi...).</li>
                  <li>Quin <strong>maquinari</strong> hi ha i quin <strong>sistema operatiu</strong> es farà servir.</li>
                  <li>Quins requisits de <strong>seguretat</strong>: qui hi tindrà accés, usuaris i permisos mínims, HTTPS, còpies de seguretat.</li>
                  <li>Si l'entorn és de <strong>desenvolupament</strong> o de <strong>producció</strong>.</li>
                </ul>
                <table class="taula">
                  <tr><th>Servidor local</th><th>Mínim</th><th>Recomanat</th></tr>
                  <tr><td>Processador</td><td>1 GHz</td><td>2 GHz o més</td></tr>
                  <tr><td>RAM</td><td>2 GB</td><td>4-8 GB</td></tr>
                  <tr><td>Disc</td><td>10 GB lliures</td><td>SSD de 20 GB o més</td></tr>
                </table>
                <p>En producció, els requisits creixen segons el nombre d'usuaris i la complexitat de l'aplicació.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Frontend, backend i base de dades',
            'origen' => "$origen · diapositives 5-8",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Peça</th><th>On</th><th>Què fa</th><th>Tecnologies</th></tr>
                  <tr><td>Frontend</td><td>Client (navegador)</td><td>El que veu i toca l'usuari</td><td>HTML, CSS, JavaScript</td></tr>
                  <tr><td>Backend</td><td>Servidor</td><td>La lògica i les operacions amb dades</td><td>PHP, Python, Node.js, Java</td></tr>
                  <tr><td>Base de dades</td><td>Servidor</td><td>Desa i gestiona les dades</td><td>MySQL, MariaDB, PostgreSQL, MongoDB</td></tr>
                </table>
                <p><strong>HTML = esquelet · CSS = roba · JavaScript = moviment.</strong> El codi de servidor s'executa
                <em>abans</em> d'enviar la resposta: el client mai no el veu.</p>
                <p>Servidors web més usats: <strong>Nginx</strong> (~35 %, alt rendiment, proxy invers), <strong>Apache</strong> (~30 %, molt configurable amb mòduls),
                IIS (~10 %, Windows Server) i LiteSpeed (~10 %).</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['client i servidor'],
            'pregunta' => 'Quin d\'aquests llenguatges s\'executa al <strong>navegador</strong> de l\'usuari?',
            'opcions' => ['PHP', 'JavaScript', 'SQL', 'Python'],
            'correcta' => 1,
            'explicacio' => 'JavaScript (amb HTML i CSS) és del costat client. PHP i Python s\'executen al servidor, i SQL el fa servir el servidor per parlar amb la base de dades.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Llocs web estàtics i dinàmics',
            'origen' => "$origen · diapositives 11-13",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th></th><th>Estàtic</th><th>Dinàmic</th></tr>
                  <tr><td>Contingut</td><td>Igual per a tothom</td><td>Canvia segons l'usuari, la sessió o les dades</td></tr>
                  <tr><td>Al servidor</td><td>Fitxers HTML, CSS i JS fets</td><td>Scripts que generen l'HTML en temps d'execució</td></tr>
                  <tr><td>Base de dades</td><td>No en necessita</td><td>Imprescindible</td></tr>
                  <tr><td>Rendiment</td><td>Molt ràpid</td><td>Depèn del processament</td></tr>
                  <tr><td>Exemples</td><td>Portfolis, documentació</td><td>Xarxes socials, botigues, panells d'administració</td></tr>
                </table>
                <p>Una pàgina dinàmica té una part estàtica (HTML) i blocs de codi entre <code>&lt;?php</code> i <code>?&gt;</code>. El servidor busca aquests blocs,
                els executa i els <strong>substitueix per la seva sortida</strong>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: una pàgina dinàmica',
            'fitxer' => 'salutacio.php',
            'html' => '<p>Aquest és l\'exemple de la diapositiva 13. Executa\'l: la sortida és l\'HTML que rebria el navegador. El PHP ha desaparegut!</p>',
            'codi' => <<<'PHP'
                <!DOCTYPE html>
                <html lang="ca">
                <body>
                    <?php
                        $hora = date('H');
                        if ($hora < 12) {
                            echo '<p>Bon dia!</p>';
                        } elseif ($hora < 20) {
                            echo '<p>Bona tarda!</p>';
                        } else {
                            echo '<p>Bona nit!</p>';
                        }
                    ?>
                    <p>Avui és: <?php echo date('d/m/Y'); ?></p>
                </body>
                </html>
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['estàtic i dinàmic'],
            'pregunta' => 'Obres una pàgina <code>.php</code> al navegador i fas «Veure el codi font». Què hi trobes?',
            'opcions' => ['El codi PHP tal com l\'ha escrit el programador', 'Només l\'HTML que ha generat el PHP', 'Res: el codi font de les pàgines PHP està xifrat', 'L\'HTML i el PHP barrejats'],
            'correcta' => 1,
            'explicacio' => 'El servidor executa els blocs PHP i els substitueix per la seva sortida. Al navegador només hi arriba HTML, CSS i JavaScript.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Què ofereix PHP',
            'origen' => "$origen · diapositives 14-23",
            'html' => <<<'HTML'
                <p>PHP (<em>PHP: Hypertext Preprocessor</em>, un acrònim recursiu) és un llenguatge de servidor de <strong>codi obert</strong>. Permet:</p>
                <ul>
                  <li><strong>Contingut dinàmic</strong>: el nom de l'usuari, productes amb el preu actual, recomanacions.</li>
                  <li><strong>Bases de dades</strong>: amb <code>PDO</code> (genèric, permet canviar de motor) o <code>MySQLi</code> (MySQL/MariaDB).</li>
                  <li><strong>Formularis</strong> (<code>$_POST['usuari']</code>), <strong>sessions</strong> (àrees privades, carrets) i <strong>fitxers</strong> (text, CSV, imatges, PDF).</li>
                  <li><strong>Seguretat</strong>: <code>filter_var()</code>, <code>password_hash()</code> / <code>password_verify()</code>, consultes preparades, HTTPS i cookies segures.</li>
                  <li><strong>Frameworks</strong>: Laravel (el més popular), Symfony (empresarial), CodeIgniter (lleuger), Slim / Lumen (APIs).</li>
                  <li><strong>Multiplataforma</strong> (Windows, Linux, macOS; Apache, Nginx, IIS) i integració amb APIs REST, JSON, GD...</li>
                </ul>
                <p>Casos d'èxit: WordPress, Joomla, Drupal, Moodle, Magento, PrestaShop, phpBB i Facebook als seus inicis.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Troba l\'error: l\'exemple de la diapositiva 20',
            'origen' => "$origen · diapositiva 20",
            'html' => <<<'HTML'
                <p>Al temari, l'exemple de «Generació de fitxers» té un error. Fixa't en el nom de la variable a cada línia:</p>
                <pre class="code">$fitxer = fopen("fitxer.txt", "w");
                fwrite($file, "Contingut del fitxer");
                fclose($fitxer);</pre>
                <p>Executa'l a sota i mira què diu PHP. Després arregla'l.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: arregla l\'exemple',
            'fitxer' => 'fitxer.php',
            'error_intencionat' => true,
            'codi' => <<<'PHP'
                <?php
                $fitxer = fopen("fitxer.txt", "w");
                fwrite($file, "Contingut del fitxer");
                fclose($fitxer);
                echo "Fitxer creat" . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q3',
            'conceptes' => ['variables', 'tipus d\'errors'],
            'pregunta' => 'Per què falla l\'exemple de la diapositiva 20?',
            'opcions' => ['fopen() no pot crear fitxers', 'La variable s\'obre amb el nom $fitxer però fwrite() fa servir $file, que no existeix', 'Falta un punt i coma', 'El mode "w" només serveix per llegir'],
            'correcta' => 1,
            'explicacio' => '$file no està definida (Warning: Undefined variable) i val null, així que fwrite() rep null en lloc del fitxer obert: TypeError. Les variables han de tenir exactament el mateix nom.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Quan PHP i quan una altra cosa?',
            'origen' => "$origen · diapositives 25-27",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Avantatges de PHP</th><th>Desavantatges</th></tr>
                  <tr><td>Optimitzat per a la web<br>Allotjament econòmic i fàcil<br>Gran comunitat<br>Gran compatibilitat amb BD</td>
                      <td>No és ideal en temps real<br>El codi es complica en projectes grans<br>Riscos si no se segueixen bones pràctiques</td></tr>
                </table>
                <p><strong>Bona opció:</strong> webs amb CMS (WordPress...), aplicacions que s'han d'executar en qualsevol allotjament, webs client-servidor tradicionals.
                <strong>Alternatives:</strong> temps real (xats, jocs) → Node.js; dades i IA → Python; sistemes molt escalables → Node.js o Python.</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['tecnologies web'],
            'pregunta' => 'Un client et demana un xat en temps real amb milers d\'usuaris connectats. Segons el temari, quina tecnologia de backend encaixa millor?',
            'opcions' => ['PHP amb WordPress', 'Node.js', 'HTML i CSS', 'Un lloc estàtic'],
            'correcta' => 1,
            'explicacio' => 'Node.js és no bloquejant i ideal per a temps real (xats, notificacions). PHP brilla en webs tradicionals i CMS.',
        ],
    ],
];
