<?php
/**
 * Lliçó: Model client-servidor (Tema 1, apartat 1.3).
 */

$origen = 'Tema 1 - Introducció al desenvolupament web.pdf';

return [
    'id' => 'client-servidor',
    'ordre' => 2,
    'titol' => 'El model client-servidor i el cicle d\'una petició',
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.3 Model client-servidor',
    'origen' => $origen,
    'minuts' => 12,
    'xp_final' => 20,
    'lab' => 'pagina-dinamica',
    'resum' => 'Qui fa què entre el navegador i el servidor, HTTP i HTTPS, els 7 passos d\'una petició web i els codis d\'estat.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Client i servidor',
            'origen' => "$origen · diapositives 31-32",
            'html' => <<<'HTML'
                <ul>
                  <li><strong>Servidor</strong>: proporciona serveis (dades o funcionalitats).</li>
                  <li><strong>Client</strong>: consumeix els serveis i és qui <strong>inicia</strong> la comunicació amb una petició.</li>
                  <li><strong>Separació de responsabilitats</strong>: el client gestiona la presentació; el servidor, la lògica i les dades.</li>
                  <li>El servidor pot atendre molts clients alhora. Normalment són màquines separades, però en desenvolupament local
                  (com XAMPP al teu PC) tots dos són a la mateixa màquina.</li>
                </ul>
                <table class="taula">
                  <tr><th>Protocol</th><th>Port</th><th>Seguretat</th></tr>
                  <tr><td>HTTP</td><td>80</td><td>Sense xifrar</td></tr>
                  <tr><td>HTTPS</td><td>443</td><td>Xifrat (certificat SSL/TLS)</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q1',
            'conceptes' => ['HTTP i HTTPS'],
            'pregunta' => 'Al panell de XAMPP, Apache escolta als ports «80, 443». Quin port fa servir HTTPS?',
            'codi' => "https://localhost  →  port ___",
            'respostes' => ['443'],
            'explicacio' => 'HTTP fa servir el port 80 i HTTPS el 443. Per això el panell de XAMPP mostra tots dos.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Codi de client i codi de servidor',
            'origen' => "$origen · diapositiva 33",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th></th><th>Costat client</th><th>Costat servidor</th></tr>
                  <tr><td>On s'executa</td><td>Navegador de l'usuari</td><td>Servidor web</td></tr>
                  <tr><td>Codi visible</td><td>L'usuari el pot veure</td><td>Invisible per a l'usuari</td></tr>
                  <tr><td>Accés a la BD</td><td>No directe</td><td>Sí</td></tr>
                  <tr><td>Interactivitat</td><td>Alta (resposta immediata)</td><td>Cal una petició HTTP</td></tr>
                  <tr><td>Exemples</td><td>HTML, CSS, JavaScript</td><td>PHP, Python, Node.js, Java</td></tr>
                </table>
                <p><strong>Conseqüència de seguretat:</strong> una validació feta només amb JavaScript l'usuari se la pot saltar. Les dades s'han de
                tornar a validar sempre al servidor.</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['client i servidor'],
            'pregunta' => 'On ha d\'anar la contrasenya de la base de dades d\'una aplicació web?',
            'opcions' => ['Al JavaScript de la pàgina', 'En un comentari HTML', 'Al codi PHP del servidor (o en un fitxer de configuració del servidor)', 'A la URL'],
            'correcta' => 2,
            'explicacio' => 'Tot el que s\'envia al navegador (HTML, CSS, JS) l\'usuari ho pot veure. Les credencials només poden viure al servidor, com el config.php d\'aquesta app.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'El cicle d\'una petició web',
            'origen' => "$origen · diapositiva 34",
            'html' => <<<'HTML'
                <p>Què passa des que escrius una URL fins que veus la pàgina? Com a futur administrador de sistemes, cada pas és un punt on alguna cosa pot fallar
                (DNS, tallafoc, servidor web, PHP, base de dades...).</p>
                <table class="taula">
                  <tr><th>Codi d'estat</th><th>Significat</th></tr>
                  <tr><td>200 OK</td><td>Tot correcte</td></tr>
                  <tr><td>404 Not Found</td><td>No existeix aquesta URL (com quan Apache no trobava phplab!)</td></tr>
                  <tr><td>500 Internal Server Error</td><td>Error al servidor (sovint, un error de PHP)</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'ordenar',
            'id' => 'q3',
            'conceptes' => ['cicle de petició'],
            'pregunta' => 'Ordena els passos del cicle d\'una petició web, del primer a l\'últim:',
            'elements' => [
                'L\'usuari escriu una URL o fa clic en un enllaç',
                'El navegador resol el nom de domini amb DNS per obtenir la IP del servidor',
                'El navegador estableix una connexió TCP/IP amb el servidor (port 80 o 443)',
                'El navegador envia una petició HTTP (GET, POST...)',
                'El servidor processa la petició (executa PHP, consulta la BD) i genera la resposta',
                'El servidor retorna una resposta HTTP amb un codi d\'estat i el contingut',
                'El navegador interpreta l\'HTML, el CSS i el JavaScript i mostra la pàgina',
            ],
            'explicacio' => 'Primer cal saber a quina IP anar (DNS), després connectar-s\'hi (TCP), demanar (HTTP), processar al servidor, respondre i, finalment, el navegador ho pinta.',
        ],
        [
            'tipus' => 'test',
            'id' => 'q4',
            'conceptes' => ['cicle de petició'],
            'pregunta' => 'Una pàgina PHP té un error fatal al servidor. Quin codi d\'estat és més probable que rebi el navegador?',
            'opcions' => ['200', '301', '404', '500'],
            'correcta' => 3,
            'explicacio' => '500 Internal Server Error indica que el servidor ha fallat en processar la petició. 404 seria si la URL no existís.',
        ],
    ],
];
