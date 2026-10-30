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
