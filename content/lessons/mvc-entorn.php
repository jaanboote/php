<?php
/**
 * Lliçó: Arquitectura MVC i entorn de desenvolupament (Tema 1, apartats 1.4 i 1.5).
 */

$origen = 'Tema 1 - Introducció al desenvolupament web.pdf';

return [
    'id' => 'mvc-entorn',
    'ordre' => 3,
    'titol' => 'Arquitectura MVC i entorn de treball',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.4 Arquitectura MVC · 1.5 Entorn de desenvolupament web',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'pagina-dinamica',
    'resum' => 'Model, Vista i Controlador: qui fa què i per què separar-ho. I l\'entorn: XAMPP, httpd.conf, VS Code, Xdebug i Composer.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'MVC: separar responsabilitats',
            'origen' => "$origen · diapositives 35-42",
            'html' => <<<'HTML'
                <p><strong>MVC</strong> (Model-Vista-Controlador) és un patró que organitza el codi en tres capes. Cada capa té una funció i no ha de conèixer
                els detalls interns de les altres.</p>
                <table class="taula">
                  <tr><th>Capa</th><th>Responsabilitat</th><th>Tecnologies</th></tr>
                  <tr><td><strong>Model</strong></td><td>Les dades: estructura, operacions CRUD, validar-ne la integritat, regles de negoci.
                    És l'<strong>única</strong> capa que parla amb la base de dades.</td><td>SQL, ORM (Eloquent, Doctrine)</td></tr>
                  <tr><td><strong>Vista</strong></td><td>Genera la interfície (HTML) amb les dades que rep. Només lògica de presentació (bucles per llistar, condicionals per amagar).</td>
                    <td>HTML, CSS, JS, plantilles (Blade, Twig)</td></tr>
                  <tr><td><strong>Controlador</strong></td><td>Rep les peticions, valida les dades d'entrada, crida el Model i passa el resultat a la Vista.
                    Fa de pont: la Vista no parla mai directament amb el Model.</td><td>PHP; Apache/Nginx hi enruten les peticions</td></tr>
                </table>
                <p><strong>Beneficis:</strong> codi més reutilitzable, més fàcil d'ampliar i mantenir, es pot canviar la interfície sense tocar les dades (i al revés)
                i la capa de dades queda aïllada de l'exterior, cosa que millora la seguretat.</p>
                HTML,
        ],
        [
            'tipus' => 'ordenar',
            'id' => 'q1',
            'conceptes' => ['MVC'],
            'pregunta' => 'Ordena el flux bàsic d\'una petició en una aplicació MVC:',
            'elements' => [
                'L\'usuari interactua amb la Vista (obre una URL, envia un formulari)',
                'El Controlador rep la petició, valida les dades i decideix quina acció executar',
                'El Controlador crida el Model per obtenir o actualitzar dades',
                'El Model consulta la base de dades i retorna el resultat al Controlador',
                'El Controlador passa les dades a la Vista adequada',
                'La Vista genera l\'HTML que es retorna al navegador',
            ],
            'explicacio' => 'El Controlador és al mig de tot: rep, demana dades al Model i les passa a la Vista. La Vista i el Model no es parlen mai directament.',
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['MVC'],
            'pregunta' => 'En una aplicació MVC, a quina capa ha d\'anar la consulta SQL <code>SELECT * FROM productes</code>?',
            'opcions' => ['A la Vista', 'Al Controlador', 'Al Model', 'Al fitxer CSS'],
            'correcta' => 2,
            'explicacio' => 'El Model és l\'única capa que interactua amb la base de dades. El Controlador li demana les dades i les passa a la Vista.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Saps que aquesta app és (gairebé) MVC?',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <p>PHPLab no fa servir cap framework, però separa les capes de la mateixa manera:</p>
                <ul>
                  <li><strong>Model</strong>: les classes de <code>src/</code> que parlen amb la base de dades (<code>Db.php</code>, <code>Labs.php</code>, <code>Progress.php</code>...).</li>
                  <li><strong>Vista</strong>: els fitxers de <code>templates/</code>, que només generen HTML amb <code>&lt;?= ?&gt;</code>, <code>foreach</code> i <code>if</code>.</li>
                  <li><strong>Controlador</strong>: <code>index.php</code> i <code>api.php</code>, que reben la petició, criden el model i trien la plantilla.</li>
                </ul>
                <p>Pots obrir aquests fitxers a la carpeta <code>C:\xampp\htdocs\phplab</code> i veure-ho.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'L\'entorn de desenvolupament: XAMPP',
            'origen' => "$origen · diapositives 45-48",
            'html' => <<<'HTML'
                <p><strong>XAMPP</strong> (X-Apache-MySQL-PHP-Perl) integra en una sola instal·lació el servidor web <strong>Apache</strong>, la base de dades <strong>MariaDB</strong>,
                el motor <strong>PHP 8.2</strong> i el panell <strong>phpMyAdmin</strong>. Passos a Windows: descarregar-lo d'apachefriends.org, executar l'instal·lador com a
                administrador, triar components i carpeta (<code>C:\xampp</code>), obrir el tauler de control, iniciar Apache i MySQL i comprovar <code>http://localhost</code>.</p>
                <p>Configuració principal d'Apache: <code>C:\xampp\apache\conf\httpd.conf</code> (a Linux, <code>/etc/apache2/apache2.conf</code>):</p>
                <pre class="code"># Port d'escolta (per defecte 80)
                Listen 80

                # Directori arrel dels llocs web
                DocumentRoot "C:/xampp/htdocs"

                # Fitxers d'índex per defecte
                DirectoryIndex index.php index.html</pre>
                HTML,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['configuració d\'Apache'],
            'pregunta' => 'Quina directiva de <code>httpd.conf</code> indica la carpeta d\'on Apache serveix les pàgines? (És la que tenies canviada a la carpeta de classe!)',
            'codi' => "___ \"C:/xampp/htdocs\"",
            'respostes' => ['DocumentRoot'],
            'explicacio' => 'DocumentRoot és el directori arrel. Si apunta a una altra carpeta, Apache respon «Not Found» per a tot el que hi ha a htdocs. Per això vam afegir un Alias per a /phplab.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Eines: VS Code, Xdebug i Composer',
            'origen' => "$origen · diapositives 49-53",
            'html' => <<<'HTML'
                <ul>
                  <li><strong>Visual Studio Code</strong> com a editor, amb les extensions obligatòries <em>PHP Extension Pack</em>, <em>PHP Intelephense</em>
                  (autocompletat) i <em>PHP Debug</em>, i recomanades com <em>Format HTML in PHP</em>, <em>Auto Rename Tag</em> o <em>PHP DocBlocker</em>.</li>
                  <li><strong>Xdebug</strong> per depurar pas a pas: es copia la DLL a <code>C:\xampp\php\ext</code> com a <code>php_xdebug.dll</code> i s'activa al
                  <code>php.ini</code> (<code>zend_extension=xdebug</code>, <code>xdebug.mode=debug</code>, <code>xdebug.start_with_request=yes</code>). Després, reiniciar Apache.</li>
                  <li><strong>Composer</strong>, el gestor de dependències de PHP (com npm a Node.js o pip a Python): declara les llibreries del projecte i les instal·la.
                  Ja l'has fet servir: <code>composer install</code> va descarregar el lector de PDF i l'SDK de Claude d'aquesta app.</li>
                </ul>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['entorn de desenvolupament'],
            'pregunta' => 'Canvies el <code>php.ini</code> per activar Xdebug, però no passa res. Què t\'has oblidat de fer?',
            'opcions' => ['Esborrar la carpeta htdocs', 'Reiniciar Apache des del tauler de XAMPP', 'Executar composer install', 'Canviar el port a 8080'],
            'correcta' => 1,
            'explicacio' => 'Apache llegeix el php.ini quan arrenca. Qualsevol canvi (Xdebug, extension=zip, límits de pujada...) requereix reiniciar-lo.',
        ],
    ],
];
