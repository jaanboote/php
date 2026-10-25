<?php
/**
 * Eina per a qui escriu contingut: comprova totes les lliçons i laboratoris.
 *  - Laboratoris: la solució de referència de cada pas ha de superar les proves d'aquell pas,
 *    i la del pas anterior no (si no, les proves del pas no comproven res de nou).
 *  - Lliçons: els exemples «prova-ho» s'han d'executar sense errors fatals, i les preguntes
 *    «què imprimeix?» han de donar exactament la resposta indicada.
 *  - Reptes: «què imprimeix?» i «ordena» donen la sortida indicada; a «troba l'error», el codi
 *    corregit funciona, el que té l'error no, i la línia marcada és la que canvia entre tots dos.
 *
 * Ús (des de la carpeta del projecte):
 *     C:\xampp\php\php.exe eines\verifica-contingut.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Només es pot executar des del terminal.');
}
require __DIR__ . '/../src/bootstrap.php';

use PhpLab\{Labs, Lessons, Sandbox};

$errors = 0;

foreach (Labs::all() as $lab) {
    foreach ($lab['passos'] as $i => $step) {
        $r = Labs::evaluate($lab, $i, $step['solucio']);
        $label = "LAB {$lab['id']} · pas " . ($i + 1) . " ({$step['titol']})";
        if ($r['superat']) {
            // La solució del pas anterior NO ha de superar aquest pas: si ho fa, les proves no comproven res de nou
            $anterior = $i === 0 ? $lab['codi_inicial'] : $lab['passos'][$i - 1]['solucio'];
            if (empty($step['repas']) && Labs::evaluate($lab, $i, $anterior)['superat']) {
                $errors++;
                echo "FALLA $label: el codi del pas anterior també supera aquest pas (les proves són massa fluixes)\n";
                continue;
            }
            echo "  ok  $label\n";
            continue;
        }
        $errors++;
        echo "FALLA $label\n";
        foreach ($r['codi'] as $c) {
            if (!$c['ok']) {
                echo "      codi: {$c['missatge']}\n";
            }
        }
        foreach ($r['proves'] as $t) {
            if (!$t['ok']) {
                echo "      prova «{$t['nom']}»: {$t['motiu']}\n      sortida: " . str_replace("\n", "\n               ", trim($t['obtingut'])) . "\n";
            }
        }
    }
}

foreach (Lessons::all() as $lesson) {
    foreach ($lesson['blocs'] as $i => $b) {
        $label = "LLIÇÓ {$lesson['id']} · bloc " . ($i + 1);
        if ($b['tipus'] === 'codi') {
            $r = Sandbox::run($b['codi'], Sandbox::parseArgs($b['args'] ?? ''), $b['stdin'] ?? '', $b['fitxer'] ?? 'main.php', null, $b['fitxers'] ?? []);
            $fatal = $r['timeout'] || preg_match('/^(Parse error|Fatal error)/m', $r['stderr']);
            // Alguns exemples mostren un error a propòsit ('error_intencionat' => true)
            if ($fatal && empty($b['error_intencionat'])) {
                $errors++;
                echo "FALLA $label (codi «{$b['titol']}»): " . trim($r['stderr']) . "\n";
            } else {
                echo "  ok  $label (codi)" . (trim($r['stderr']) !== '' ? ' [amb avís, revisa que sigui intencionat]' : '') . "\n";
            }
        } elseif ($b['tipus'] === 'sortida') {
            $r = Sandbox::run($b['codi'], Sandbox::parseArgs($b['args'] ?? ''), $b['stdin'] ?? '', 'main.php', null, $b['fitxers'] ?? []);
            if (Labs::normalize($r['stdout']) === Labs::normalize($b['resposta']) && trim($r['stderr']) === '') {
                echo "  ok  $label (què imprimeix)\n";
            } else {
                $errors++;
                echo "FALLA $label (què imprimeix): esperat «{$b['resposta']}», surt «{$r['stdout']}» " . trim($r['stderr']) . "\n";
            }
        }
    }
}

// ---- Banc de reptes ----
$ids = [];
foreach (glob(APP_ROOT . '/content/reptes/*.php') as $file) {
    foreach ((require $file)['items'] as $item) {
        $label = "REPTE {$item['id']} ({$item['tipus']})";
        $problema = null;
        if (isset($ids[$item['id']])) {
            $problema = 'id repetit';
        }
        $ids[$item['id']] = true;
        $args = Sandbox::parseArgs($item['args'] ?? '');
        $fitxers = $item['fitxers'] ?? [];

        if ($problema === null && $item['tipus'] === 'sortida') {
            $r = Sandbox::run($item['codi'], $args, '', 'main.php', null, $fitxers);
            if (Labs::normalize($r['stdout']) !== Labs::normalize($item['resposta']) || trim($r['stderr']) !== '') {
                $problema = "esperat «{$item['resposta']}», surt «{$r['stdout']}» " . trim($r['stderr']);
            }
        } elseif ($problema === null && $item['tipus'] === 'error') {
            $bo = Sandbox::run($item['corregit'], $args, '', 'main.php', null, $fitxers);
            $dolent = Sandbox::run($item['codi'], $args, '', 'main.php', null, $fitxers);
            $linesBug = explode("\n", $item['codi']);
            $linesOk = explode("\n", $item['corregit']);
            if (Labs::normalize($bo['stdout']) !== Labs::normalize($item['esperat']) || trim($bo['stderr']) !== '') {
                $problema = "el codi corregit no dona «{$item['esperat']}»: «{$bo['stdout']}» " . trim($bo['stderr']);
            } elseif (Labs::normalize($dolent['stdout']) === Labs::normalize($item['esperat']) && trim($dolent['stderr']) === '') {
                $problema = 'el codi amb error funciona igual que el corregit';
            } elseif (($linesBug[$item['linia'] - 1] ?? null) === ($linesOk[$item['linia'] - 1] ?? null)) {
                $problema = "la línia {$item['linia']} és igual al codi erroni i al corregit (la línia marcada no és la de l'error)";
            }
        } elseif ($problema === null && $item['tipus'] === 'ordenar' && isset($item['sortida'])) {
            $r = Sandbox::run(implode("\n", $item['linies']) . "\n", [], '', 'ordena.php', null, $fitxers);
            if (Labs::normalize($r['stdout']) !== Labs::normalize($item['sortida']) || trim($r['stderr']) !== '') {
                $problema = "en ordre correcte no dona «{$item['sortida']}»: «{$r['stdout']}» " . trim($r['stderr']);
            }
        } elseif ($problema === null && $item['tipus'] === 'test' && !isset($item['opcions'][$item['correcta']])) {
            $problema = "l'opció correcta no existeix";
        }

        if ($problema === null) {
            echo "  ok  $label\n";
        } else {
            $errors++;
            echo "FALLA $label: $problema\n";
        }
    }
}

echo $errors === 0 ? "\nTot correcte.\n" : "\n$errors problema(es).\n";
exit($errors === 0 ? 0 : 1);
