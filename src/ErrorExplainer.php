<?php
declare(strict_types=1);

namespace PhpLab;

/**
 * Tradueix els errors de PHP a explicacions senzilles en català.
 * Cada regla té una expressió regular i una funció que construeix l'explicació.
 */
final class ErrorExplainer
{
    /**
     * @return array<int, array{tipus:string, linia:?int, original:string, titol:string, explicacio:string}>
     */
    public static function explain(string $stderr, bool $timeout = false, bool $truncated = false): array
    {
        $out = [];
        if ($timeout) {
            $out[] = self::item('temps', null, '', 'El programa ha trigat massa',
                "S'ha aturat després de " . Config::get('sandbox.timeout', 5) . " segons. Normalment és un bucle infinit: "
                . "revisa que la condició del while/for acabi sent falsa (s'incrementa el comptador?) o que no estiguis esperant més línies d'entrada de les que has escrit.");
        }
        if ($truncated) {
            $out[] = self::item('sortida', null, '', 'Massa sortida',
                "El programa ha escrit tant de text que s'ha aturat. Probablement hi ha un bucle infinit amb un echo a dins.");
        }

        // Cada missatge de PHP comença per "Tipus: missatge in fitxer on line N"
        preg_match_all('/^(Parse error|Fatal error|Warning|Notice|Deprecated): (.+?)(?: in (\S+?)(?: on line |:)(\d+))?$/m', $stderr, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $type = $m[1];
            $msg  = $m[2];
            $line = isset($m[4]) && $m[4] !== '' ? (int) $m[4] : null;
            $out[] = self::explainOne($type, $msg, $line, $m[0]);
        }
        return $out;
    }

    private static function explainOne(string $type, string $msg, ?int $line, string $original): array
    {
        $tipus = match ($type) {
            'Parse error' => 'sintaxi',
            'Fatal error' => 'fatal',
            'Warning'     => 'avis',
            default       => 'nota',
        };
        $on = $line !== null ? " (línia $line)" : '';

        $rules = [
            // ---- Sintaxi ----
            '/unexpected end of file/' => fn() => [
                'Falta tancar alguna cosa',
                "PHP ha arribat al final del fitxer esperant-ne més. Normalment falta una clau de tancament }, un parèntesi ) o unes cometes. "
                . "Compta les { i les } : n'hi ha d'haver les mateixes. També pot faltar un ; a l'última línia.",
            ],
            '/unexpected (?:token|identifier|variable) "?([^",]+)"?, expecting ";"/' => fn($g) => [
                'Falta un punt i coma',
                "Sembla que falta un ; just abans de «{$g[1]}». Mira el final de la línia " . ($line ? $line - 1 : 'anterior') . " i de la línia $line. "
                . 'A PHP cada instrucció acaba amb ;',
            ],
            '/expecting ";"|expecting "," or ";"/' => fn() => [
                'Falta un punt i coma',
                'A PHP cada instrucció acaba amb ;. Revisa el final de la línia indicada i la de just abans.',
            ],
            '/unexpected token "\)"/' => fn() => [
                'Hi ha un parèntesi de més (o falta alguna cosa abans)',
                "Revisa els parèntesis de la línia: cada ( ha de tenir la seva ). També pot ser que hi hagi un ; o una coma on no toca abans del ).",
            ],
            '/unexpected token "\}"/' => fn() => [
                'Clau } inesperada',
                "Hi ha una } que PHP no esperava. Pot ser que en sobri una o que a la línia anterior falti un ; o un parèntesi.",
            ],
            '/unexpected token "else(?:if)?"/' => fn() => [
                'else sense if',
                "Hi ha un else que no queda enganxat a cap if. Revisa les claus: potser has tancat l'if amb un ; (if (...); { ) o hi ha codi entre la } i l'else.",
            ],
            '/unexpected double-quote mark|unexpected (?:token|string content) ".*?", expecting "-" or identifier or variable or number/' => fn() => [
                'Problema amb les cometes o una variable dins d\'un text',
                "Hi ha un problema dins d'un text entre cometes dobles. Si poses un array dins del text, fes-ho així: \"{\$a['clau']}\" o concatena amb el punt.",
            ],
            '/unexpected (?:token|identifier|variable) "?([^"]+)"?/' => fn($g) => [
                'Error de sintaxi',
                "PHP no esperava trobar «{$g[1]}» en aquest punt$on. Mira bé aquesta línia i l'anterior: sovint falta un ;, un punt per concatenar, una coma o un parèntesi.",
            ],
            '/Unclosed \'(.)\'/' => fn($g) => [
                "Falta tancar un «{$g[1]}»",
                "Has obert un «{$g[1]}» i no l'has tancat. Comprova que cada obertura tingui el seu tancament.",
            ],
            '/Unmatched \'(.)\'/' => fn($g) => [
                "«{$g[1]}» de més",
                "Hi ha un «{$g[1]}» que tanca alguna cosa que no estava oberta. Revisa les parelles de claus i parèntesis.",
            ],

            // ---- Avisos habituals ----
            '/Undefined variable \$(\w+)/' => fn($g) => [
                "La variable \${$g[1]} no existeix",
                "Fas servir \${$g[1]} abans de donar-li cap valor. Està ben escrita (majúscules/minúscules)? "
                . "Si és dins d'una funció, recorda que les variables de fora no s'hi veuen: passa-la com a paràmetre.",
            ],
            '/Undefined array key (.+)/' => fn($g) => [
                "La posició {$g[1]} de l'array no existeix",
                $g[1] === '1' || $g[1] === '2' || $g[1] === '3'
                    ? "Si és \$argv[{$g[1]}], vol dir que l'usuari no ha escrit prou arguments. Comprova \$argc ABANS de llegir \$argv."
                    : "Estàs llegint una posició/clau de l'array que no hi és. Pots comprovar-ho abans amb isset() o amb l'operador ??.",
            ],
            '/Uninitialized string offset (-?\d+)/' => fn($g) => [
                'Posició de text inexistent',
                "Intentes llegir el caràcter {$g[1]} d'un text que és més curt. Comprova la longitud amb strlen() abans.",
            ],
            '/Array to string conversion/' => fn() => [
                'No es pot fer echo d\'un array',
                "Has intentat mostrar un array com si fos un text. Fes servir print_r(), var_dump(), implode() o recorre'l amb un foreach.",
            ],
            '/A non-numeric value encountered/' => fn() => [
                'Operació amb un valor que no és un número',
                "Estàs fent una operació matemàtica amb un text que no és un número. Valida-ho abans amb is_numeric().",
            ],
            '/Division by zero|Modulo by zero/' => fn() => [
                'Divisió per zero',
                "No es pot dividir per 0. Comprova el divisor abans de fer l'operació.",
            ],
            '/Undefined constant "?(\w+)"?/' => fn($g) => [
                "La constant {$g[1]} no existeix",
                "PHP no troba cap constant que es digui {$g[1]}. Si volies una variable, falta el \$. Si és una constant, defineix-la abans amb const o define(). Si era un text, falten les cometes.",
            ],

            // ---- Errors fatals ----
            '/Call to undefined function (\w+)\(\)/' => fn($g) => in_array(strtolower($g[1]), Sandbox::DISABLED_FUNCTIONS, true)
                ? ["{$g[1]}() està desactivada", "Per seguretat, al sandbox no es pot fer servir {$g[1]}() (podria executar ordres al servidor o connectar-se a la xarxa)."]
                : ["La funció {$g[1]}() no existeix", "Està ben escrita? Si és una funció teva, comprova que l'has definit (function {$g[1]}(...) { ... }) i que el nom coincideix."],
            '/Cannot redeclare (?:function )?(\w+)\(\)/' => fn($g) => [
                "La funció {$g[1]}() està definida dues vegades",
                "Només pots definir una funció amb el mateix nom una vegada. Esborra la definició repetida.",
            ],
            '/Allowed memory size of \d+ bytes exhausted/' => fn() => [
                'Sense memòria',
                "El programa ha fet servir més de " . Config::get('sandbox.memory_limit', '64M') . " de memòria. Normalment és un bucle que afegeix elements a un array sense parar.",
            ],
            '/Maximum execution time/' => fn() => [
                'Temps màxim superat',
                "El programa ha trigat massa. Revisa els bucles: n'hi ha algun que no acaba mai?",
            ],
            '/open_basedir restriction/' => fn() => [
                'Fitxer fora del sandbox',
                "Només pots obrir fitxers dins de la carpeta del teu programa. Fes servir rutes relatives com \"dades.txt\".",
            ],
            '/Uncaught TypeError: (.+)/' => fn($g) => [
                'Tipus de dada incorrecte',
                "Una funció ha rebut un valor d'un tipus que no espera. Detall: " . self::cleanUncaught($g[1]) . ". Potser cal convertir el valor (int), (float)... o validar-lo abans.",
            ],
            '/Uncaught ArgumentCountError: (.+)/' => fn($g) => [
                'Nombre d\'arguments incorrecte',
                "Has cridat una funció amb massa pocs (o massa) arguments. Detall: " . self::cleanUncaught($g[1]),
            ],
            '/Uncaught DivisionByZeroError/' => fn() => [
                'Divisió per zero',
                "No es pot dividir per 0 (ni fer % 0). Comprova el divisor abans.",
            ],
            '/Uncaught ValueError: (.+)/' => fn($g) => [
                'Valor no vàlid',
                "Una funció ha rebut un valor que no accepta. Detall: " . self::cleanUncaught($g[1]),
            ],
            '/Uncaught (\w+): (.+)/' => fn($g) => [
                "Excepció no capturada ({$g[1]})",
                "S'ha llançat una excepció i ningú l'ha capturat amb try/catch. Missatge: " . self::cleanUncaught($g[2]),
            ],
            '/Unsupported operand types: (.+)/' => fn($g) => [
                'Operació no permesa entre aquests tipus',
                "No es pot fer aquesta operació amb {$g[1]}. Per exemple, no pots sumar un array i un número.",
            ],
        ];

        foreach ($rules as $regex => $fn) {
            if (preg_match($regex, $msg, $g)) {
                [$titol, $explicacio] = $fn($g);
                return self::item($tipus, $line, $original, $titol, $explicacio);
            }
        }

        $generic = match ($tipus) {
            'sintaxi' => "PHP no entén el codi$on. Revisa punts i coma, parèntesis, claus i cometes.",
            'fatal'   => "El programa s'ha aturat per un error$on.",
            'avis'    => "És un avís$on: el programa continua, però alguna cosa no és correcta.",
            default   => "Missatge informatiu de PHP$on.",
        };
        return self::item($tipus, $line, $original, $type, $generic);
    }

    private static function cleanUncaught(string $s): string
    {
        return trim(preg_replace('/ in \S+:\d+$/', '', $s));
    }

    private static function item(string $tipus, ?int $line, string $original, string $titol, string $explicacio): array
    {
        return ['tipus' => $tipus, 'linia' => $line, 'original' => $original, 'titol' => $titol, 'explicacio' => $explicacio];
    }
}
