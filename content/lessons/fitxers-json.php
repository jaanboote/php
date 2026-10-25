<?php
/**
 * Lliçó: Fitxers i JSON (Tema 4, apartat 4.2).
 * Els exemples tenen fitxers de partida ('fitxers'): es creen a la carpeta del programa abans d'executar.
 */

$origen = 'Tema 4 - Gestor d\'exepcions fitxers.pdf';

$dades = "Primera línia\nSegona línia\nTercera línia\n";

return [
    'id' => 'fitxers-json',
    'ordre' => 230,
    'titol' => 'Fitxers i JSON',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.2 Fitxers i JSON',
    'origen' => $origen,
    'minuts' => 25,
    'xp_final' => 20,
    'lab' => 'comptador-visites',
    'resum' => 'Llegir i escriure fitxers amb el mètode ràpid i amb fopen/fgets, controlar els errors amb excepcions i desar dades en JSON.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Dues maneres de treballar amb fitxers',
            'origen' => "$origen · pàg. 13",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Mètode ràpid</th><th>Mètode manual (recurs)</th></tr>
                  <tr><td><code>file_get_contents()</code> llegeix tot<br><code>file_put_contents()</code> escriu tot<br><code>file()</code> cada línia a un array</td>
                      <td><code>fopen()</code> → <code>fgets()</code> / <code>fread()</code> / <code>fwrite()</code> → <code>fclose()</code></td></tr>
                  <tr><td>Fitxers petits: configuració, JSON, logs curts</td><td>Control total: fitxers grans línia a línia sense carregar-los sencers</td></tr>
                </table>
                <p class="nota">Els exemples d'aquesta lliçó ja tenen a la carpeta un fitxer <code>dades.txt</code> de 3 línies. Després d'executar, sota la sortida
                veuràs els fitxers que hi ha quedat. Cada execució comença de zero.</p>
                HTML,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Obrir un fitxer: fopen() i els modes',
            'origen' => "$origen · pàg. 14",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Mode</th><th>Operació</th><th>Si no existeix</th><th>Contingut</th></tr>
                  <tr><td>"r" / "r+"</td><td>Lectura (i escriptura)</td><td>Error</td><td>Punter a l'inici</td></tr>
                  <tr><td>"w" / "w+"</td><td>Escriptura (i lectura)</td><td>El crea</td><td><strong>Esborra el contingut</strong></td></tr>
                  <tr><td>"a" / "a+"</td><td>Escriptura (i lectura)</td><td>El crea</td><td>Afegeix al final</td></tr>
                  <tr><td>"x" / "x+"</td><td>Escriptura (i lectura)</td><td>El crea</td><td>Error si ja existeix</td></tr>
                </table>
                <p><code>fopen()</code> retorna <code>false</code> si falla (i emet un Warning). En lloc de <code>die()</code>, llancem una excepció que es pot capturar.
                Cada <code>fopen()</code> necessita el seu <code>fclose()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: quatre maneres de llegir',
            'fitxer' => 'llegir.php',
            'fitxers' => ['dades.txt' => $dades],
            'codi' => <<<'PHP'
                <?php
                // 1) Tot de cop
                echo file_get_contents("dades.txt");

                // 2) Línia a línia (fitxers grans)
                $f = fopen("dades.txt", "r");
                while (($linia = fgets($f)) !== false) {
                    echo "> " . $linia;
                }
                fclose($f);

                // 3) Cada línia en una posició de l'array (sense el \n final)
                $linies = file("dades.txt", FILE_IGNORE_NEW_LINES);
                foreach ($linies as $num => $linia) {
                    echo "Línia $num: $linia" . PHP_EOL;
                }

                // 4) N bytes
                $f = fopen("dades.txt", "r");
                echo fread($f, 7) . PHP_EOL;     // "Primera"
                fclose($f);
                PHP,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: escriure',
            'fitxer' => 'escriure.php',
            'fitxers' => ['dades.txt' => $dades],
            'html' => '<p>Mira com queda <code>dades.txt</code> sota la sortida. Prova a canviar <code>FILE_APPEND</code> o el mode "w" per "a".</p>',
            'codi' => <<<'PHP'
                <?php
                // Mode "w": crea o SOBREESCRIU
                $f = fopen("nou.txt", "w");
                fwrite($f, "Hola món!\n");
                fwrite($f, "Segona línia\n");
                fclose($f);

                // Mode "a": AFEGEIX al final
                $f = fopen("nou.txt", "a");
                fwrite($f, "Línia nova afegida\n");
                fclose($f);

                // Mètode ràpid: afegir sense esborrar (LOCK_EX evita escriptures simultànies)
                file_put_contents("dades.txt", "Quarta línia\n", FILE_APPEND | LOCK_EX);

                echo "Fet! nou.txt té " . filesize("nou.txt") . " bytes." . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q1',
            'conceptes' => ['fitxers'],
            'pregunta' => 'Tens un registre de visites i vols <strong>afegir</strong> una línia sense esborrar les anteriors. Quin mode de fopen()?',
            'opcions' => ['"r"', '"w"', '"a"', '"x"'],
            'correcta' => 2,
            'explicacio' => '"a" (append) afegeix al final i crea el fitxer si no existeix. "w" esborraria tot el que hi havia.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Fitxers amb excepcions',
            'origen' => "$origen · pàg. 17-18",
            'html' => <<<'HTML'
                <p><code>file_get_contents()</code> i <code>file_put_contents()</code> <strong>no llancen excepcions</strong>: retornen <code>false</code> i un Warning.
                Ho comprovem nosaltres i fem <code>throw new RuntimeException(...)</code>. <code>is_readable()</code> comprova abans de llegir que el fitxer existeix
                i que tenim permís.</p>
                <p>L'<code>@</code> davant d'una funció (<code>@fopen(...)</code>) evita el Warning duplicat quan ja gestionem l'error amb una excepció.
                I el bloc <code>finally</code> garanteix que el fitxer es tanca passi el que passi.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: llegir i desar amb excepcions',
            'fitxer' => 'excepcions_fitxers.php',
            'codi' => <<<'PHP'
                <?php
                function llegirFitxer(string $ruta): string
                {
                    if (!is_readable($ruta)) {
                        throw new RuntimeException("No es pot llegir: $ruta");
                    }
                    return file_get_contents($ruta);
                }

                function desarFitxer(string $ruta, string $text): void
                {
                    if (file_put_contents($ruta, $text, FILE_APPEND) === false) {
                        throw new RuntimeException("No es pot escriure: $ruta");
                    }
                }

                try {
                    desarFitxer("log.txt", date("H:i") . " Accés" . PHP_EOL);
                    echo llegirFitxer("log.txt");
                    echo llegirFitxer("no_existeix.txt");   // llança l'excepció
                } catch (RuntimeException $e) {
                    error_log($e->getMessage());                 // detall tècnic al log
                    echo "Error: no s'ha pogut accedir al fitxer." . PHP_EOL;   // missatge amable
                }
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Altres operacions amb fitxers i carpetes',
            'origen' => "$origen · pàg. 19",
            'html' => <<<'HTML'
                <p><code>file_exists()</code>, <code>filesize()</code>, <code>rename()</code> (reanomenar o moure), <code>copy()</code>, <code>unlink()</code> (esborrar, no es pot desfer),
                <code>is_file()</code>, <code>is_dir()</code>, <code>mkdir("uploads/2026", 0755, true)</code> (crea recursivament), <code>scandir()</code> i
                <code>pathinfo("foto.jpg", PATHINFO_EXTENSION)</code>.</p>
                <p>Error «Permission denied»? L'usuari del servidor web (<code>www-data</code>) ha de tenir permís d'escriptura a la carpeta (evita el 777).
                I fes servir <code>basename()</code> amb noms que venen de l'usuari per evitar el <em>path traversal</em> (<code>../../etc/passwd</code>).</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['fitxers'],
            'fitxers' => ['dades.txt' => $dades],
            'pregunta' => 'La carpeta té <code>dades.txt</code> amb 3 línies («Primera línia», «Segona línia», «Tercera línia»). Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                file_put_contents("dades.txt", "Nova\n");
                $linies = file("dades.txt", FILE_IGNORE_NEW_LINES);
                echo count($linies) . " " . $linies[0];
                PHP,
            'resposta' => '1 Nova',
            'explicacio' => 'Sense FILE_APPEND, file_put_contents() sobreescriu el fitxer: ara només té una línia.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'JSON: intercanviar dades estructurades',
            'origen' => "$origen · pàg. 22-23",
            'html' => <<<'HTML'
                <ul>
                  <li><code>json_encode($dades)</code>: PHP → text JSON. Opcions: <code>JSON_PRETTY_PRINT</code> (sagnat) i <code>JSON_UNESCAPED_UNICODE</code> (accents sense <code>à</code>).</li>
                  <li><code>json_decode($json, true)</code>: JSON → array associatiu (amb <code>true</code>).</li>
                  <li>Amb <code>JSON_THROW_ON_ERROR</code>, un JSON malmès llança una <code>JsonException</code> en lloc de retornar <code>null</code> sense avisar.</li>
                </ul>
                <p>És el format de les API REST, de molts fitxers de configuració i de moltes aplicacions web.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: JSON',
            'fitxer' => 'json.php',
            'fitxers' => ['malmes.json' => '{"nom": "Anna", "edat": 25'],
            'codi' => <<<'PHP'
                <?php
                $alumne = ["nom" => "Anna", "edat" => 25, "moduls" => ["IAW", "BD"], "ciutat" => "Mataró"];

                $json = json_encode($alumne, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                echo $json . PHP_EOL;
                file_put_contents("alumne.json", $json);

                $dades = json_decode(file_get_contents("alumne.json"), true);
                echo $dades["moduls"][0] . PHP_EOL;     // IAW

                try {
                    json_decode(file_get_contents("malmes.json"), true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    echo "JSON invàlid: " . $e->getMessage() . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['JSON'],
            'pregunta' => 'Volem que un JSON malmès llanci una <code>JsonException</code>. Quina constant hi va?',
            'codi' => "\$dades = json_decode(\$text, true, 512, ___);",
            'respostes' => ['JSON_THROW_ON_ERROR'],
            'explicacio' => 'Sense JSON_THROW_ON_ERROR, json_decode() retorna null i no t\'assabentes que el fitxer està malmès.',
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q4',
            'conceptes' => ['JSON'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $json = '[{"nom":"Anna","edat":25},{"nom":"Joan","edat":32}]';
                $persones = json_decode($json, true);
                echo count($persones) . " " . $persones[1]["nom"];
                PHP,
            'resposta' => '2 Joan',
            'explicacio' => 'El JSON és una llista de 2 objectes. Amb true, cada objecte és un array associatiu: $persones[1]["nom"] és Joan.',
        ],
    ],
];
