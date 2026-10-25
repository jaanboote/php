<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Extreu el text d'un fitxer del temari segons la seva extensió.
 */
final class TextExtractor
{
    public const EXTENSIONS = ['pdf', 'docx', 'php', 'html', 'htm', 'txt', 'md'];

    /** @return array{status:string, text:string, message:?string} */
    public static function extract(string $path): array
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        try {
            $text = match ($ext) {
                'pdf'         => self::pdf($path),
                'docx'        => self::docx($path),
                'html', 'htm' => self::html((string) file_get_contents($path)),
                default       => (string) file_get_contents($path),
            };
        } catch (MissingDependency $e) {
            return ['status' => 'pendent', 'text' => '', 'message' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'text' => '', 'message' => 'No s\'ha pogut llegir: ' . $e->getMessage()];
        }

        $text = self::toUtf8($text);
        $text = self::clean($text);
        if (trim($text) === '') {
            return ['status' => 'error', 'text' => '', 'message' => 'No s\'ha trobat text (potser és un PDF escanejat com a imatge).'];
        }
        return ['status' => 'ok', 'text' => $text, 'message' => null];
    }

    private static function pdf(string $path): string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new MissingDependency('Per llegir PDF cal instal·lar les dependències: composer install');
        }
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($path);
        $pages = [];
        foreach ($pdf->getPages() as $i => $page) {
            $pages[] = "[[Pàgina " . ($i + 1) . "]]\n" . $page->getText();
        }
        return implode("\n\n", $pages);
    }

    /** Un .docx és un ZIP amb un XML a dins (word/document.xml). */
    private static function docx(string $path): string
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new MissingDependency('Per llegir DOCX cal activar extension=zip al php.ini');
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('el DOCX està malmès');
        }
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if ($xml === false) {
            throw new \RuntimeException('no és un DOCX vàlid');
        }
        // Cada paràgraf (<w:p>) és una línia; els tabuladors i salts es conserven
        $xml = preg_replace(['/<w:tab\/>/', '/<w:br\/>/', '/<\/w:p>/'], ["\t", "\n", "\n"], $xml);
        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private static function html(string $html): string
    {
        // Treiem scripts i estils, i convertim blocs en salts de línia
        $html = preg_replace('#<(script|style)\b.*?</\1>#is', '', $html);
        $html = preg_replace('#<(br|/p|/div|/h\d|/li|/tr)\b[^>]*>#i', "\n", $html);
        return html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private static function toUtf8(string $text): string
    {
        if (str_starts_with($text, "\xEF\xBB\xBF")) {
            $text = substr($text, 3); // BOM
        }
        return mb_check_encoding($text, 'UTF-8') ? $text : mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
    }

    private static function clean(string $text): string
    {
        $text = str_replace(["\r\n", "\r", "\u{00A0}"], ["\n", "\n", ' '], $text);
        $text = preg_replace("/[ \t]+\n/", "\n", $text);
        return preg_replace("/\n{3,}/", "\n\n", $text);
    }
}

final class MissingDependency extends \RuntimeException
{
}
