<?php
/**
 * Configuració de PHPLab (PLANTILLA).
 *
 * Copia aquest fitxer com a config.php i omple'l amb els teus valors.
 * config.php NO es puja al repositori (és al .gitignore) perquè hi ha la clau de l'API.
 */

return [
    // Carpeta de dades privades: base de dades SQLite, temari pujat i sandbox.
    // Ha d'estar FORA de htdocs perquè Apache no la pugui servir mai.
    // Per defecte: C:\xampp\phplab-data (dues carpetes amunt de htdocs\phplab).
    'data_dir' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'phplab-data',

    // Xarxes des d'on es pot accedir a l'aplicació (a més de localhost).
    // Es comprova també al PHP, per si el .htaccess no s'aplica.
    'allowed_networks' => ['127.0.0.1/32', '::1/128', '192.168.1.0/24'],

    // Execució del codi de l'alumne (sandbox).
    'sandbox' => [
        'php_cli'      => 'C:\\xampp\\php\\php.exe',   // a Linux: /usr/bin/php
        'php_cgi'      => 'C:\\xampp\\php\\php-cgi.exe', // laboratoris web (si no hi és, es busca al costat de php_cli)
        'timeout'      => 5,        // segons màxims per execució
        'memory_limit' => '64M',
        'max_output'   => 100000,   // bytes màxims de sortida
        'max_parallel' => 2,        // execucions simultànies
    ],

    // Tutor amb IA (API de Claude). Si 'api_key' és buit, la app funciona en mode sense connexió.
    'ai' => [
        'api_key'            => '',                 // sk-ant-...
        'model'              => 'claude-haiku-5-5',
        'effort'             => 'low',              // low | medium | high: més alt = respostes més pensades però més cares
        'max_tokens'         => 4000,               // màxim per resposta (inclou el "pensament" del model)
        'max_requests_day'   => 80,                 // límit de peticions diàries
        'max_tokens_day'     => 300000,             // límit de tokens diaris (entrada + sortida)
        // Certificats per a HTTPS. A XAMPP sovint cal indicar-lo o fallarà amb
        // "unable to get local issuer certificate". XAMPP ja en porta un:
        'ca_bundle'          => 'C:\\xampp\\apache\\bin\\curl-ca-bundle.crt',
    ],

    // Pujada de temari.
    'upload' => [
        'max_file_mb'       => 50,     // mida màxima per fitxer pujat
        'max_zip_entries'   => 500,    // fitxers màxims dins d'un ZIP
        'max_unzipped_mb'   => 200,    // mida màxima descomprimida (protecció "zip bomb")
    ],

    // Seguretat del login.
    'auth' => [
        'max_attempts'    => 5,    // intents fallits permesos...
        'lockout_minutes' => 15,   // ...en aquests minuts
        'session_minutes' => 480,  // la sessió caduca després d'aquest temps d'inactivitat
    ],
];
