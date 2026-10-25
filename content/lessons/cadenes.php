<?php
/**
 * Lliçó: Cadenes de text i UTF-8 (Tema 3, apartat 3.2).
 */

$origen = 'Tema 3 - Funcions integrades.pdf';

return [
    'id' => 'cadenes',
    'ordre' => 130,
    'titol' => 'Cadenes de text i accents (mb_*)',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.2 Cadenes de text',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'frases',
    'resum' => 'Netejar, buscar, tallar i transformar textos. Per què strlen("Català") dona 7 i com ho arreglen les funcions mb_*.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Les funcions de cadenes més útils',
            'origen' => "$origen · pàg. 15",
            'html' => <<<'HTML'
                <table class="taula">
                  <tr><th>Funció</th><th>Què fa</th></tr>
                  <tr><td>strlen / mb_strlen</td><td>Longitud en bytes / en caràcters (UTF-8)</td></tr>
                  <tr><td>strtolower / strtoupper</td><td>Minúscules / majúscules</td></tr>
                  <tr><td>ucfirst / ucwords</td><td>Primera lletra / cada paraula en majúscula</td></tr>
                  <tr><td>trim</td><td>Treu els espais de l'inici i el final</td></tr>
                  <tr><td>substr($s, $ini, $long)</td><td>Extreu un tros</td></tr>
                  <tr><td>strpos($s, $buscar)</td><td>Posició d'una subcadena (o false)</td></tr>
                  <tr><td>str_replace($a, $b, $s)</td><td>Substitueix $a per $b (accepta arrays)</td></tr>
                  <tr><td>explode / implode</td><td>Cadena → array / array → cadena</td></tr>
                  <tr><td>sprintf</td><td>Text amb format (%d, %s, %.2f)</td></tr>
                  <tr><td>str_pad / str_repeat</td><td>Omple fins a una longitud / repeteix</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: netejar i trossejar',
            'fitxer' => 'cadenes.php',
            'codi' => <<<'PHP'
                <?php
                $text = "  Hola món  ";
                $net = trim($text);

                echo "[" . $net . "]" . PHP_EOL;
                echo ucwords("hola a tothom") . PHP_EOL;
                echo substr($net, 0, 4) . PHP_EOL;
                echo str_replace("Hola", "Adéu", $net) . PHP_EOL;

                $parts = explode(" ", $net);
                echo implode("-", $parts) . PHP_EOL;

                echo sprintf("%05.2f €", 3.5) . PHP_EOL;
                echo str_pad("7", 3, "0", STR_PAD_LEFT) . PHP_EOL;
                echo str_repeat("=", 20) . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'Compte amb strpos()',
            'origen' => "$origen · pàg. 16",
            'html' => <<<'HTML'
                <p><code>strpos()</code> pot retornar <strong>0</strong> si el text buscat és al principi, i 0 es considera fals.
                Per això cal comparar sempre amb <code>!== false</code>, mai fer <code>if (strpos(...))</code>.
                A PHP 8 tens <code>str_contains()</code>, que és més clar.</p>
                <p>Fes <code>trim()</code> abans de processar dades d'usuari: els espais invisibles fan fallar comparacions i <code>explode()</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['strpos'],
            'pregunta' => 'Què imprimeix? (fixa\'t on és «Hola»)',
            'codi' => <<<'PHP'
                <?php
                $text = "Hola món";
                if (strpos($text, "Hola")) {
                    echo "A";
                } else {
                    echo "B";
                }
                if (strpos($text, "Hola") !== false) {
                    echo "C";
                }
                PHP,
            'resposta' => 'BC',
            'explicacio' => '«Hola» és a la posició 0, i if (0) és fals: surt B encara que sí que hi és. Amb !== false funciona bé i surt C.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Accents i UTF-8: funcions mb_*',
            'origen' => "$origen · pàg. 17",
            'html' => <<<'HTML'
                <p>En UTF-8, lletres com <strong>à, é, ç, ŀ</strong> ocupen 2 bytes o més. Les funcions clàssiques treballen amb <strong>bytes</strong>,
                no amb caràcters. Per a textos en català o castellà, fes servir les versions <code>mb_</code> (extensió <code>mbstring</code>):</p>
                <table class="taula">
                  <tr><th>Byte (clàssica)</th><th>Multibyte</th></tr>
                  <tr><td>strlen()</td><td>mb_strlen()</td></tr>
                  <tr><td>substr()</td><td>mb_substr()</td></tr>
                  <tr><td>strtoupper()</td><td>mb_strtoupper()</td></tr>
                  <tr><td>strpos()</td><td>mb_strpos()</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: amb accents',
            'fitxer' => 'accents.php',
            'codi' => <<<'PHP'
                <?php
                $paraula = "Català";

                echo strlen($paraula) . PHP_EOL;          // 7 (la «à» ocupa 2 bytes)
                echo mb_strlen($paraula) . PHP_EOL;       // 6
                echo strtoupper($paraula) . PHP_EOL;      // CATALà (malament)
                echo mb_strtoupper($paraula) . PHP_EOL;   // CATALÀ (bé)
                echo mb_substr("Pàgina", 0, 2) . PHP_EOL; // Pà
                PHP,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Què retorna <code>strlen("Món")</code>?',
            'opcions' => ['3', '4', '6', 'Un error'],
            'correcta' => 1,
            'explicacio' => 'strlen() compta bytes: M (1) + ó (2) + n (1) = 4. Per comptar caràcters, mb_strlen("Món") dona 3.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['UTF-8 i mb_*'],
            'pregunta' => 'Volem «ÀNGEL» a partir de «àngel», amb l\'accent bé. Quina funció?',
            'codi' => "echo ___(\"àngel\");",
            'respostes' => ['mb_strtoupper'],
            'explicacio' => 'strtoupper() deixaria la «à» en minúscula perquè no entén els caràcters de 2 bytes. mb_strtoupper() sí.',
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q4',
            'conceptes' => ['funcions de cadenes'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $data = "2026-10-07";
                $parts = explode("-", $data);
                echo $parts[2] . "/" . $parts[1] . "/" . substr($parts[0], 2);
                PHP,
            'resposta' => '07/10/26',
            'explicacio' => 'explode separa ["2026", "10", "07"]. substr("2026", 2) agafa des de la posició 2 fins al final: "26".',
        ],
    ],
];
