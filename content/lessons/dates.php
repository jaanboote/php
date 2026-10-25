<?php
/**
 * Lliçó: Data i hora (Tema 3, apartat 3.1, segona part).
 * Les preguntes fan servir dates fixes perquè la resposta no depengui del dia.
 */

$origen = 'Tema 3 - Funcions integrades.pdf';

return [
    'id' => 'dates',
    'ordre' => 120,
    'titol' => 'Data i hora: date() i DateTime',
    'tema' => 'Tema 3 – Funcions integrades de PHP',
    'apartat' => '3.1 Tipus, números i dates',
    'origen' => $origen,
    'minuts' => 15,
    'xp_final' => 20,
    'lab' => 'lliuraments',
    'resum' => 'Formatar dates amb date(), treballar amb timestamps i strtotime(), i calcular diferències i terminis amb DateTime.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Funcions de data i hora',
            'origen' => "$origen · pàg. 12",
            'html' => <<<'HTML'
                <ul>
                  <li><code>date($format, $ts)</code>: data formatada (ara, o d'un timestamp).</li>
                  <li><code>time()</code>: timestamp actual, els segons des de l'1/1/1970.</li>
                  <li><code>strtotime($text)</code>: text (<code>"+1 week"</code>, <code>"2026-12-18"</code>) → timestamp.</li>
                  <li><code>date_default_timezone_set('Europe/Madrid')</code>: fixa la zona horària. Sense, l'hora pot sortir desplaçada 1-2 h (UTC).</li>
                </ul>
                <table class="taula">
                  <tr><th>Format</th><th>Significat</th><th>Exemple</th></tr>
                  <tr><td>d / j</td><td>Dia (amb / sense zero)</td><td>07 / 7</td></tr>
                  <tr><td>m / n</td><td>Mes (amb / sense zero)</td><td>10 / 10</td></tr>
                  <tr><td>Y / y</td><td>Any (4 / 2 xifres)</td><td>2026 / 26</td></tr>
                  <tr><td>H / i / s</td><td>Hora / minuts / segons</td><td>17 / 05 / 09</td></tr>
                  <tr><td>N</td><td>Dia de la setmana (1 = dilluns)</td><td>3</td></tr>
                  <tr><td>D / l</td><td>Nom del dia (en anglès)</td><td>Wed / Wednesday</td></tr>
                </table>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: date() i strtotime()',
            'fitxer' => 'dates.php',
            'html' => '<p>El sandbox ja té la zona horària Europe/Madrid. Prova altres formats: <code>"l, j/n/y"</code>, <code>"H:i:s"</code>...</p>',
            'codi' => <<<'PHP'
                <?php
                date_default_timezone_set('Europe/Madrid');

                echo date("d/m/Y H:i") . PHP_EOL;     // ara
                echo date("N") . PHP_EOL;             // 1 = dilluns ... 7 = diumenge
                echo time() . PHP_EOL;                // segons des de 1970

                $ts = strtotime("+1 week");
                echo "D'aquí a una setmana: " . date("d/m/Y", $ts) . PHP_EOL;

                $ts = strtotime("2026-12-18");
                echo date("l j/n/Y", $ts) . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['date'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $ts = strtotime("2026-03-05 09:07");
                echo date("j/n/y H:i", $ts);
                PHP,
            'resposta' => '5/3/26 09:07',
            'explicacio' => 'j i n són el dia i el mes sense zero, y és l\'any amb 2 xifres, i H i i porten zero.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'DateTime: l\'opció recomanada',
            'origen' => "$origen · pàg. 13",
            'html' => <<<'HTML'
                <ul>
                  <li><code>new DateTime('2026-10-07')</code> crea una data (sense argument = ara). <code>-&gt;format('d/m/Y')</code> la mostra.</li>
                  <li><code>$d1-&gt;diff($d2)</code> retorna la diferència (un <code>DateInterval</code>): <code>-&gt;days</code> és el total de dies.</li>
                  <li><code>-&gt;modify('+30 days')</code> suma o resta temps, però <strong>modifica l'original</strong> (per això el temari fa <code>(clone $avui)</code>).</li>
                  <li><code>DateTimeImmutable</code> no modifica mai l'original: <code>-&gt;add(new DateInterval('P1D'))</code> retorna una data nova.</li>
                </ul>
                <p>Encara no hem fet POO: de moment, quedat amb què <code>-&gt;</code> serveix per cridar una funció (mètode) d'aquell objecte.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: diferències i terminis',
            'fitxer' => 'datetime.php',
            'codi' => <<<'PHP'
                <?php
                $avui = new DateTime('2026-10-07');
                echo $avui->format('d/m/Y') . PHP_EOL;

                $entrega = new DateTime('2026-12-18');
                $falten = $avui->diff($entrega);
                echo "Falten {$falten->days} dies" . PHP_EOL;     // 72

                $termini = (clone $avui)->modify('+30 days');
                echo $termini->format('d/m/Y') . PHP_EOL;         // 06/11/2026

                $d = new DateTimeImmutable('2026-01-31');
                echo $d->add(new DateInterval('P1D'))->format('Y-m-d') . PHP_EOL;   // 2026-02-01
                echo $d->format('Y-m-d') . PHP_EOL;               // no ha canviat

                $dl = new DateTime('2026-10-12');
                echo $dl->format('N') . " = dilluns" . PHP_EOL;
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q2',
            'conceptes' => ['DateTime'],
            'pregunta' => 'Quants dies hi ha de l\'1 de març al 15 de març de 2026? Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                $d1 = new DateTime('2026-03-01');
                $d2 = new DateTime('2026-03-15');
                echo $d1->diff($d2)->days;
                PHP,
            'resposta' => '14',
            'explicacio' => 'diff()->days compta els dies que hi ha entre les dues dates: del dia 1 al 15 n\'hi ha 14.',
        ],
        [
            'tipus' => 'test',
            'id' => 'q3',
            'conceptes' => ['DateTime'],
            'pregunta' => '<code>$a = new DateTime(\'2026-01-31\'); $b = $a-&gt;modify(\'+1 day\');</code> Quina data té <code>$a</code> després?',
            'opcions' => ['2026-01-31 (no canvia)', '2026-02-01', '2026-02-31', 'Dona error'],
            'correcta' => 1,
            'explicacio' => 'modify() canvia el mateix objecte DateTime. Si vols conservar l\'original, fes servir clone o DateTimeImmutable.',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q4',
            'conceptes' => ['date'],
            'pregunta' => 'Volem el número del dia de la setmana (1 = dilluns, 7 = diumenge). Quina lletra de format hi va?',
            'codi' => "echo date(\"___\");",
            'respostes' => ['N'],
            'explicacio' => 'N dona el dia de la setmana ISO (1 = dilluns). Compte: en minúscula, n seria el mes!',
        ],
    ],
];
