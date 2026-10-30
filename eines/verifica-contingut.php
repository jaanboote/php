<?php
/**
 * Eina per a qui escriu contingut: comprova totes les lliçons i laboratoris.
 *  - Laboratoris: la solució de referència de cada pas ha de superar les proves d'aquell pas,
 *    i la del pas anterior no (si no, les proves del pas no comproven res de nou).
 *  - Lliçons: els exemples «prova-ho» s'han d'executar sense errors fatals, i les preguntes
 *    «què imprimeix?» han de donar exactament la resposta indicada.
 *  - Reptes: «què imprimeix?» i «ordena» donen la sortida indicada; a «troba l'error», el codi
 *    corregit funciona, el que té l'error no, i la línia marcada és la que canvia entre tots dos.
 *  - Exàmens: la teoria com els reptes; a la pràctica, el programa de referència supera les proves i
 *    el codi inicial no. Els punts de cada examen sumen 10.
 *
 * Ús (des de la carpeta del projecte):
 *     C:\xampp\php\php.exe eines\verifica-contingut.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit('Només es pot executar des del terminal.');
}
require __DIR__ . '/../src/bootstrap.php';

use PhpLab\{Examens, Labs, Lessons, Sandbox};

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

/**
 * Problema d'una pregunta amb el format dels reptes (també les de la teoria dels exàmens), o null si és correcta:
 * «què imprimeix?» i «ordena» donen la sortida indicada; a «troba l'error», el codi corregit funciona, el que té
 * l'error no, i la línia marcada és la que canvia entre tots dos.
 */
function problemaItem(array $item): ?string
{
    $args = Sandbox::parseArgs($item['args'] ?? '');
    $fitxers = $item['fitxers'] ?? [];
    if ($item['tipus'] === 'sortida') {
        $r = Sandbox::run($item['codi'], $args, '', 'main.php', null, $fitxers);
        if (Labs::normalize($r['stdout']) !== Labs::normalize($item['resposta']) || trim($r['stderr']) !== '') {
            return "esperat «{$item['resposta']}», surt «{$r['stdout']}» " . trim($r['stderr']);
        }
    } elseif ($item['tipus'] === 'error') {
        $bo = Sandbox::run($item['corregit'], $args, '', 'main.php', null, $fitxers);
        $dolent = Sandbox::run($item['codi'], $args, '', 'main.php', null, $fitxers);
        $linesBug = explode("\n", $item['codi']);
        $linesOk = explode("\n", $item['corregit']);
        if (Labs::normalize($bo['stdout']) !== Labs::normalize($item['esperat']) || trim($bo['stderr']) !== '') {
            return "el codi corregit no dona «{$item['esperat']}»: «{$bo['stdout']}» " . trim($bo['stderr']);
        }
        if (Labs::normalize($dolent['stdout']) === Labs::normalize($item['esperat']) && trim($dolent['stderr']) === '') {
            return 'el codi amb error funciona igual que el corregit';
        }
        if (($linesBug[$item['linia'] - 1] ?? null) === ($linesOk[$item['linia'] - 1] ?? null)) {
            return "la línia {$item['linia']} és igual al codi erroni i al corregit (la línia marcada no és la de l'error)";
        }
    } elseif ($item['tipus'] === 'ordenar' && isset($item['sortida'])) {
        $r = Sandbox::run(implode("\n", $item['linies']) . "\n", [], '', 'ordena.php', null, $fitxers);
        if (Labs::normalize($r['stdout']) !== Labs::normalize($item['sortida']) || trim($r['stderr']) !== '') {
            return "en ordre correcte no dona «{$item['sortida']}»: «{$r['stdout']}» " . trim($r['stderr']);
        }
    } elseif ($item['tipus'] === 'test' && !isset($item['opcions'][$item['correcta']])) {
        return "l'opció correcta no existeix";
    } elseif ($item['tipus'] === 'completa') {
        // Amb la primera resposta acceptada al lloc dels ___, el codi s'ha d'executar sense errors
        if (empty($item['respostes']) || !str_contains($item['codi'], '___')) {
            return 'falten les respostes o els ___ al codi';
        }
        $r = Sandbox::run(str_replace('___', $item['respostes'][0], $item['codi']), $args, '', 'main.php', null, $fitxers);
        if (trim($r['stderr']) !== '') {
            return 'amb la resposta posada, el codi dona error: ' . trim($r['stderr']);
        }
    }
    return null;
}

// ---- Banc de reptes ----
$ids = [];
foreach (glob(APP_ROOT . '/content/reptes/*.php') as $file) {
    foreach ((require $file)['items'] as $item) {
        $label = "REPTE {$item['id']} ({$item['tipus']})";
        $problema = isset($ids[$item['id']]) ? 'id repetit' : problemaItem($item);
        $ids[$item['id']] = true;
        if ($problema === null) {
            echo "  ok  $label\n";
        } else {
            $errors++;
            echo "FALLA $label: $problema\n";
        }
    }
}

// ---- Exàmens ----
// Teoria: com els reptes. Pràctica: el programa de referència supera totes les proves i el codi inicial no.
// I els punts han de sumar 10.
foreach (Examens::all() as $ex) {
    $suma = (float) $ex['punts_teoria'] + array_sum(array_column($ex['practica'], 'punts'));
    if (abs($suma - 10) > 0.001) {
        $errors++;
        echo "FALLA EXAMEN {$ex['id']}: els punts sumen $suma i no 10\n";
    }
    $idsEx = [];
    foreach ($ex['teoria'] as $item) {
        $label = "EXAMEN {$ex['id']} · {$item['id']} ({$item['tipus']})";
        $problema = isset($idsEx[$item['id']]) ? 'id repetit dins de l\'examen' : problemaItem($item);
        $idsEx[$item['id']] = true;
        if ($problema === null) {
            echo "  ok  $label\n";
        } else {
            $errors++;
            echo "FALLA $label: $problema\n";
        }
    }
    foreach ($ex['practica'] as $p) {
        $label = "EXAMEN {$ex['id']} · programa {$p['id']} ({$p['titol']})";
        $lab = ['id' => 'verifica-' . $ex['id'], 'fitxer' => $p['fitxer'], 'passos' => [['codi' => $p['codi'] ?? [], 'proves' => $p['proves']]]];
        $r = Labs::evaluate($lab, 0, $p['solucio']);
        if (!$r['superat']) {
            $errors++;
            $fallen = array_column(array_filter($r['proves'], fn($t) => !$t['ok']), 'nom');
            echo "FALLA $label: la solució no supera: " . implode(', ', $fallen) . "\n";
        } elseif (Labs::evaluate($lab, 0, $p['codi_inicial'])['superat']) {
            $errors++;
            echo "FALLA $label: el codi inicial ja supera totes les proves\n";
        } else {
            echo "  ok  $label\n";
        }
    }
}

echo $errors === 0 ? "\nTot correcte.\n" : "\n$errors problema(es).\n";
exit($errors === 0 ? 0 : 1);
