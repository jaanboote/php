<?php
/**
 * Navegador dels laboratoris web: serveix la web que escriu l'alumne.
 *
 * URL: web.php/<testimoni>/<laboratori>/<fitxer>?<consulta>
 * La carrega un <iframe> aïllat de la pàgina del laboratori. Les rutes relatives dels formularis i enllaços
 * de l'alumne (action="processa.php") tornen a passar per aquí, amb el mateix testimoni.
 *
 * Seguretat:
 *  - El testimoni (signat i amb caducitat) substitueix la sessió: un iframe aïllat no envia les galetes de la app.
 *  - Content-Security-Policy «sandbox»: encara que s'obri la URL directament, el codi de l'alumne s'executa
 *    en un origen aïllat i no pot llegir res de PHPLab.
 */
declare(strict_types=1);

define('PHPLAB_NO_SESSION', true);
require __DIR__ . '/src/bootstrap.php';

use PhpLab\{Db, Labs, Sandbox, WebLab};

header('Content-Security-Policy: sandbox allow-forms allow-scripts allow-modals; frame-ancestors \'self\'');
header('Cache-Control: no-store');

// Part de la URL després de web.php/
$uri = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$pos = strpos($uri, '/web.php/');
$rest = $pos === false ? '' : substr($uri, $pos + strlen('/web.php/'));
$parts = explode('/', $rest, 3);
[$token, $labId, $path] = array_pad($parts, 3, '');
$path = rawurldecode($path) ?: 'index.php';

$lab = Labs::get($labId);
if ($lab === null || !WebLab::isWeb($lab) || !WebLab::checkToken($labId, $token)) {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    exit('<p style="font-family:sans-serif">L\'enllaç ha caducat. Torna a obrir la web des del laboratori (botó «Desa i obre»).</p>');
}

// La petició, tal com l'ha fet el navegador
$method = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'POST' : 'GET';
$req = ['method' => $method, 'path' => $path, 'query' => (string) ($_SERVER['QUERY_STRING'] ?? '')];
if ($method === 'POST') {
    $type = (string) ($_SERVER['CONTENT_TYPE'] ?? '');
    $fields = Sandbox::flattenFields($_POST);
    if (str_starts_with($type, 'multipart/form-data')) {
        // PHP ja ha llegit el cos (php://input és buit): el tornem a construir amb $_POST i $_FILES
        $files = Sandbox::flattenFiles($_FILES);
        $m = Sandbox::multipart($fields, $files);
        $req += ['body' => $m['body'], 'content_type' => $m['content_type'], 'fields' => $fields, 'files' => $files];
    } else {
        $req += ['body' => (string) file_get_contents('php://input'), 'content_type' => $type ?: 'application/x-www-form-urlencoded', 'fields' => $fields];
    }
}

$base = substr($uri, 0, $pos) . '/web.php/' . $token . '/' . $labId;
$code = (string) Db::value('SELECT code FROM lab_state WHERE lab_id = ?', [$labId]);
$r = WebLab::handle($lab, $code !== '' ? $code : Labs::initialCode($lab), $req, $base);

http_response_code($r['status']);
header('Content-Type: ' . $r['content_type']);
if ($r['location'] !== null) {
    // Les redireccions absolutes («/index.php») es queden dins de la web del laboratori
    $loc = preg_replace('#^https?://[^/]+#', '', $r['location']);
    header('Location: ' . (str_starts_with($loc, '/') && !str_starts_with($loc, $base) ? $base . $loc : $loc));
}
echo $r['body'];
