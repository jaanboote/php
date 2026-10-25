<?php
/**
 * Lliçó: Tipus d'errors i configuració a php.ini (Tema 4, apartat 4.1, primera part).
 */

$origen = 'Tema 4 - Gestor d\'exepcions fitxers.pdf';

return [
    'id' => 'errors-phpini',
    'ordre' => 210,
    'titol' => 'Tipus d\'errors i php.ini',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.1 Errors i excepcions',
    'origen' => $origen,
    'minuts' => 12,
    'xp_final' => 20,
    'lab' => 'calculadora-segura',
    'resum' => 'Quins errors aturen l\'script i quins no, com es configuren a php.ini i per què en producció no s\'han de mostrar mai.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Cinc tipus d\'errors',
            'origen' => "$origen · pàg. 4",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Tipus</th><th>Constant</th><th>Què passa</th><th>Exemple</th></tr>
                  <tr><td>Parse error</td><td>E_PARSE</td><td><strong>Atura</strong>: l'script no arriba a executar-se</td><td>Falta un ; o una }</td></tr>
                  <tr><td>Fatal error</td><td>E_ERROR</td><td><strong>Atura</strong> durant l'execució</td><td>Funció inexistent, sense memòria</td></tr>
                  <tr><td>Warning</td><td>E_WARNING</td><td>Continua (però alguna cosa va malament)</td><td>Fitxer no trobat, variable no definida</td></tr>
                  <tr><td>Notice</td><td>E_NOTICE</td><td>Continua: avís menor</td><td>—</td></tr>
                  <tr><td>Deprecated</td><td>E_DEPRECATED</td><td>Continua: deixarà de funcionar en el futur</td><td>strftime()</td></tr>
                </table>
                <p>Ja els has vist molts cops en aquesta app: el panell de sortida els explica en català.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: Warning contra Fatal error',
            'error_intencionat' => true,   // l'exemple mostra un Fatal error a propòsit
            'fitxer' => 'errors.php',
            'html' => '<p>Executa-ho: el Warning deixa continuar el programa, però el Fatal error l\'atura i l\'última línia no surt mai.</p>',
            'codi' => <<<'PHP'
                <?php
                echo "1. Comencem" . PHP_EOL;
                echo $total;                         // Warning: continua
                echo "2. Després del Warning" . PHP_EOL;
                calcula();                           // Fatal error: s'atura aquí
                echo "3. Això no surt mai" . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['tipus d\'errors'],
            'pregunta' => 'Un script té <code>echo "Hola"</code> sense punt i coma a la línia 10. Què passa amb les línies 1 a 9?',
            'opcions' => ['S\'executen i s\'atura a la 10', 'No s\'executa cap línia', 'S\'executa tot amb un Warning', 'Només surt un Deprecated'],
            'correcta' => 1,
            'explicacio' => 'És un Parse error: PHP analitza tot el fitxer abans d\'executar-lo i, si la sintaxi és incorrecta, no n\'executa res.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Configurar els errors a php.ini',
            'origen' => "$origen · pàg. 5",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Directiva</th><th>Què fa</th><th>Desenvolupament</th><th>Producció</th></tr>
                  <tr><td>error_reporting</td><td>Quins errors es notifiquen</td><td>E_ALL</td><td>E_ALL</td></tr>
                  <tr><td>display_errors</td><td>Mostrar-los a la pàgina</td><td>On</td><td><strong>Off</strong></td></tr>
                  <tr><td>display_startup_errors</td><td>Errors en arrencar PHP</td><td>On</td><td>Off</td></tr>
                  <tr><td>log_errors</td><td>Desar-los en un fitxer</td><td>On</td><td>On</td></tr>
                  <tr><td>error_log</td><td>Ruta del fitxer de log</td><td>(opcional)</td><td>/var/log/php/error.log</td></tr>
                </table>
                <p><strong>Per què Off en producció?</strong> Els missatges revelen rutes, consultes SQL i versions: informació valuosa per a un atacant.
                <strong>On és el php.ini?</strong> <code>php --ini</code> o <code>phpinfo()</code>. A Ubuntu: <code>/etc/php/8.x/apache2/php.ini</code>;
                a XAMPP: <code>C:\xampp\php\php.ini</code>. Reinicia Apache després de canviar-lo.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: la configuració del sandbox',
            'fitxer' => 'config.php',
            'html' => '<p><code>ini_get()</code> llegeix una directiva. Mira com està configurat el sandbox on s\'executa el teu codi.</p>',
            'codi' => <<<'PHP'
                <?php
                $directives = ['display_errors', 'log_errors', 'error_reporting', 'memory_limit',
                               'max_execution_time', 'open_basedir', 'allow_url_fopen'];

                foreach ($directives as $d) {
                    echo str_pad($d, 20) . ": " . ini_get($d) . PHP_EOL;
                }
                echo PHP_EOL . "E_ALL val " . E_ALL . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'I ini_set()?',
            'ampliacio' => true,
            'html' => <<<'HTML'
                <p>Moltes directives també es poden canviar des del codi amb <code>ini_set('display_errors', '0')</code>. En aquest sandbox,
                <code>ini_set()</code> està <strong>desactivada</strong> (<code>disable_functions</code>): si no, el teu codi podria treure's el límit de
                memòria o de temps. És el que fan molts allotjaments compartits per protegir el servidor.</p>
                <p>Sí que pots fer servir <code>error_reporting()</code> per canviar quins errors es notifiquen, i <code>error_log("missatge")</code>,
                que aquí escriu al fitxer <code>php_errors.log</code> de la carpeta del programa.</p>
                HTML,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q2',
            'conceptes' => ['php.ini'],
            'pregunta' => 'Configures el php.ini d\'un servidor de <strong>producció</strong>. Completa la línia:',
            'codi' => "display_errors = ___",
            'respostes' => ['Off', 'off', '0'],
            'explicacio' => 'En producció els errors no es mostren (display_errors = Off) però sí que es registren (log_errors = On).',
        ],
        [
            'tipus' => 'test',
            'id' => 'q3',
            'conceptes' => ['php.ini'],
            'pregunta' => 'Per què no s\'han de mostrar els errors a la pàgina en un servidor de producció?',
            'opcions' => ['Perquè fan anar la web més lenta', 'Perquè revelen rutes, consultes i versions a un possible atacant', 'Perquè els navegadors no els entenen', 'No passa res si es mostren'],
            'correcta' => 1,
            'explicacio' => 'Un missatge com «in /var/www/html/includes/db.php on line 12» explica a un atacant com està organitzat el servidor. Els errors van al log.',
        ],
    ],
];
