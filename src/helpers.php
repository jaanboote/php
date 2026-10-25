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
