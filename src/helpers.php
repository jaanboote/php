<?php
/**
 * Funcions globals petites que fan servir les plantilles.
 */
declare(strict_types=1);

/** Escapa text per mostrar-lo a HTML (protecció XSS). Cal fer-la servir SEMPRE amb dades variables. */
function e(mixed $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Converteix el format senzill de les respostes del tutor (`codi`, **negreta**, _cursiva_) a HTML segur. */
function mini_markdown(string $text): string
{
    $html = e($text);
    $html = preg_replace('/```(?:php)?\n?(.*?)```/s', '<pre class="code">$1</pre>', $html);
    $html = preg_replace('/`([^`\n]+)`/', '<code>$1</code>', $html);
    $html = preg_replace('/\*\*([^*\n]+)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/(?<![\w])_([^_\n]+)_(?![\w])/u', '<em>$1</em>', $html);
    return nl2br($html, false);
}

/** Classe CSS del color d'un tema ("Tema 3 – ..." => "t3"), o buit si no en té número. */
function tema_class(?string $tema): string
{
    return preg_match('/Tema\s+(\d+)/u', (string) $tema, $m) ? 't' . $m[1] : '';
}

/**
 * Icona SVG d'un tema (dibuix propi, amb el color del text). És HTML nostre: es pot imprimir sense escapar.
 * T1 finestra del navegador · T2 claus de codi · T3 engranatge · T4 fitxer amb avís · T5 formulari.
 */
function tema_icona(?string $tema, int $mida = 22): string
{
    $num = preg_match('/Tema\s+(\d+)/u', (string) $tema, $m) ? (int) $m[1] : 0;
    $dibuix = match ($num) {
        1 => '<rect x="3" y="4" width="18" height="16" rx="2.5"/><path d="M3 9h18"/><circle cx="6.5" cy="6.5" r=".6"/><circle cx="9" cy="6.5" r=".6"/><path d="M8 14l2.5 2.5L8 19M13 19h3"/>',
        2 => '<path d="M8 4c-2 0-3 1-3 3v2c0 1.5-.8 2.5-2 3 1.2.5 2 1.5 2 3v2c0 2 1 3 3 3M16 4c2 0 3 1 3 3v2c0 1.5.8 2.5 2 3-1.2.5-2 1.5-2 3v2c0 2-1 3-3 3"/><circle cx="12" cy="12" r="1.2"/>',
        3 => '<circle cx="12" cy="12" r="3.2"/><path d="M12 2.8v2.6M12 18.6v2.6M21.2 12h-2.6M5.4 12H2.8M18.5 5.5l-1.8 1.8M7.3 16.7l-1.8 1.8M18.5 18.5l-1.8-1.8M7.3 7.3L5.5 5.5"/>',
        4 => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M12 11v4M12 18h.01"/>',
        5 => '<rect x="3" y="3" width="18" height="18" rx="2.5"/><path d="M7 8h10M7 12.5h6"/><rect x="7" y="15.5" width="5" height="2.5" rx="1"/><path d="M15 16.7l1.2 1.2 2.3-2.4"/>',
        default => '<path d="M8 7l-5 5 5 5M16 7l5 5-5 5"/>',
    };
    return '<svg class="tema-icona" width="' . $mida . '" height="' . $mida . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $dibuix . '</svg>';
}

/**
 * Icona SVG d'un fitxer segons l'extensió: un full amb la cantonada doblegada i una etiqueta de color
 * (PDF vermell, ZIP taronja, DOCX blau, PHP lila...). És HTML nostre: es pot imprimir sense escapar.
 */
function fitxer_icona(string $ext): string
{
    $ext = strtolower($ext);
    $color = match ($ext) {
        'pdf' => '#ff6b7a',
        'zip' => '#f3a648',
        'doc', 'docx' => '#5cb4ee',
        'php' => '#9a9ef0',
        'html', 'htm' => '#ff8f5a',
        'md' => '#3fcf9f',
        default => '#8d8ea4',
    };
    $etiqueta = htmlspecialchars(strtoupper(mb_substr($ext !== '' ? $ext : '?', 0, 4)), ENT_QUOTES);
    return '<svg class="fitxer-icona" width="40" height="48" viewBox="0 0 40 48" aria-hidden="true">'
        . '<path d="M4 3h22l10 10v32H4z" fill="#15151d" stroke="' . $color . '" stroke-width="2" stroke-linejoin="round"/>'
        . '<path d="M26 3v10h10" fill="none" stroke="' . $color . '" stroke-width="2" stroke-linejoin="round"/>'
        . '<path d="M10 20h16M10 25h20M10 30h12" stroke="#3a3a4a" stroke-width="2" stroke-linecap="round"/>'
        . '<rect x="1" y="34" width="' . (strlen($etiqueta) * 7 + 8) . '" height="11" rx="3" fill="' . $color . '"/>'
        . '<text x="5" y="42.5" font-family="JetBrains Mono, monospace" font-size="8.5" font-weight="800" fill="#0b0b10">' . $etiqueta . '</text>'
        . '</svg>';
}

/**
 * Joc d'icones de la interfície (dibuix propi, traç de 24×24). Substitueixen els emojis, que es veuen diferents a cada
 * sistema i donen un aire poc professional. Es dibuixen una sola vegada al sprite de layout.php i es reutilitzen amb <use>.
 */
function icones(): array
{
    return [
        'llibre' => '<path d="M3 5.5A1.5 1.5 0 0 1 4.5 4H9a3 3 0 0 1 3 3v13a2.5 2.5 0 0 0-2.5-2.5H3z"/><path d="M21 5.5A1.5 1.5 0 0 0 19.5 4H15a3 3 0 0 0-3 3v13a2.5 2.5 0 0 1 2.5-2.5H21z"/>',
        'matras' => '<path d="M9 3h6M10 3v6.2L4.6 18.4A1.7 1.7 0 0 0 6.1 21h11.8a1.7 1.7 0 0 0 1.5-2.6L14 9.2V3"/><path d="M7.2 15h9.6"/>',
        'repas' => '<path d="M20 11a8 8 0 0 0-14.4-4.6L4 8.5"/><path d="M4 3.5v5h5"/><path d="M4 13a8 8 0 0 0 14.4 4.6L20 15.5"/><path d="M20 20.5v-5h-5"/>',
        'diana' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.2"/>',
        'flama' => '<path d="M12 3c.6 3.4 5.5 5.6 5.5 10.6a5.5 5.5 0 0 1-11 0c0-2.1 1-3.8 2.4-4.9.2 1.6 1 2.7 2.1 3.2-.5-3.4-.3-6.4 1-8.9z"/>',
        'rellotge' => '<circle cx="12" cy="13.5" r="7.5"/><path d="M12 13.5V9.5M10 2.5h4M18.3 6.7l1.5-1.5"/>',
        'trofeu' => '<path d="M7 4h10v5a5 5 0 0 1-10 0z"/><path d="M7 6H4.5v1.5a3 3 0 0 0 3 3M17 6h2.5v1.5a3 3 0 0 1-3 3"/><path d="M12 14v4M8 21h8M10 18h4"/>',
        'bombeta' => '<path d="M9.5 18h5M10.5 21h3"/><path d="M12 3a6 6 0 0 0-3.6 10.8c.7.6 1.1 1.3 1.1 2.2h5c0-.9.4-1.6 1.1-2.2A6 6 0 0 0 12 3z"/>',
        'cadenat' => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7.5a4 4 0 0 1 8 0V11"/>',
        'carpeta' => '<path d="M3 7a2 2 0 0 1 2-2h4l2 2.5h8a2 2 0 0 1 2 2V18a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
        'document' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
        'check' => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
        'creu' => '<path d="M6.5 6.5l11 11M17.5 6.5l-11 11"/>',
        'refresca' => '<path d="M20 12a8 8 0 1 1-2.6-5.9L20 8.5"/><path d="M20 3.5v5h-5"/>',
        'tornar' => '<path d="M4 4v5h5"/><path d="M4.6 9A8 8 0 1 1 4 13"/>',
        'puja' => '<path d="M12 15V4M7 9l5-5 5 5"/><path d="M4 15v3.5A1.5 1.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V15"/>',
        'bitxo' => '<rect x="7" y="8" width="10" height="12" rx="5"/><path d="M12 12v8M9.5 5l1 3M14.5 5l-1 3M7 13H3.5M20.5 13H17M7.4 17l-3 2M16.6 17l3 2M7.4 10l-3-2M16.6 10l3-2"/>',
        'terminal' => '<rect x="3" y="4" width="18" height="16" rx="2.5"/><path d="M7 9.5l3 2.5-3 2.5M12.5 15H17"/>',
        'impressora' => '<path d="M7 9V3.5h10V9"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M7 14h10v6.5H7z"/>',
        'ordena' => '<path d="M7 4v16M3.5 7.5 7 4l3.5 3.5M17 20V4M13.5 16.5 17 20l3.5-3.5"/>',
        'pregunta' => '<circle cx="12" cy="12" r="9"/><path d="M9.5 9.5a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 .9-1 1.7M12 17h.01"/>',
        'codi' => '<path d="M8 7l-5 5 5 5M16 7l5 5-5 5M13.5 5l-3 14"/>',
        'avis' => '<path d="M10.3 4.3 2.6 18a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 4.3a2 2 0 0 0-3.4 0z"/><path d="M12 10v4M12 17.5h.01"/>',
        'xip' => '<rect x="6" y="6" width="12" height="12" rx="2"/><path d="M10 10h4v4h-4zM9 2.5V6M15 2.5V6M9 18v3.5M15 18v3.5M2.5 9H6M2.5 15H6M18 9h3.5M18 15h3.5"/>',
        'xarxa' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.4 2.5 3.7 5.5 3.7 9s-1.3 6.5-3.7 9c-2.4-2.5-3.7-5.5-3.7-9S9.6 5.5 12 3z"/>',
        'servidor' => '<rect x="3" y="4" width="18" height="7" rx="2"/><rect x="3" y="13" width="18" height="7" rx="2"/><path d="M7 7.5h.01M7 16.5h.01"/>',
        'mapa' => '<path d="M9 4 3 6.5v13.5l6-2.5 6 2.5 6-2.5V4l-6 2.5z"/><path d="M9 4v13.5M15 6.5V20"/>',
        'grafic' => '<path d="M4 4v16h16"/><path d="M8.5 16v-4M12.5 16V8M16.5 16v-6"/>',
        'medalla' => '<circle cx="12" cy="15" r="5.5"/><path d="M8.6 10.7 6 3h4l2 5 2-5h4l-2.6 7.7"/>',
        'xat' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-4.5 4v-4A2.5 2.5 0 0 1 4 13.5z"/><path d="M8.5 8h7M8.5 11.5h4.5"/>',
        'galeta' => '<path d="M12 3a9 9 0 1 0 9 9 3 3 0 0 1-3.5-3A3 3 0 0 1 14 5.5 2.5 2.5 0 0 1 12 3z"/><path d="M8.5 9.5h.01M8 14.5h.01M12.5 12.5h.01M15.5 16h.01"/>',
        'paperera' => '<path d="M4 7h16M10 3.5h4M6 7l1 12.5A1.5 1.5 0 0 0 8.5 21h7a1.5 1.5 0 0 0 1.5-1.5L18 7M10 11v6M14 11v6"/>',
        'historial' => '<path d="M3.5 12a8.5 8.5 0 1 0 2.5-6L3.5 8.5"/><path d="M3.5 3.5v5h5M12 7.5V12l3 2"/>',
        'llista' => '<path d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01"/>',
        'estrelles' => '<path d="M11 3l1.8 5.2L18 10l-5.2 1.8L11 17l-1.8-5.2L4 10l5.2-1.8z"/><path d="M19 14.5l.8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8z"/>',
        'fletxa' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'usuari' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
    ];
}

/** Una icona del joc: hereta el color del text i fa 1em i escaig. És HTML nostre: es pot imprimir sense escapar. */
function icona(string $nom, string $classe = ''): string
{
    return '<svg class="ic' . ($classe !== '' ? ' ' . e($classe) : '') . '" aria-hidden="true" focusable="false"><use href="#i-' . e($nom) . '"/></svg>';
}

/** Sprite amb totes les icones, per posar-lo un cop a l'inici del <body>. */
function icones_sprite(): string
{
    $html = '<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>';
    foreach (icones() as $nom => $dibuix) {
        $html .= '<symbol id="i-' . $nom . '" viewBox="0 0 24 24">' . $dibuix . '</symbol>';
    }
    return $html . '</defs></svg>';
}
