<?php
/**
 * Lliçó: Excepcions (Tema 4, apartat 4.1, segona part).
 */

$origen = 'Tema 4 - Gestor d\'exepcions fitxers.pdf';

return [
    'id' => 'excepcions',
    'ordre' => 220,
    'titol' => 'Excepcions: try, catch, finally i throw',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.1 Errors i excepcions',
    'origen' => $origen,
    'minuts' => 20,
    'xp_final' => 20,
    'lab' => 'calculadora-segura',
    'resum' => 'Llançar una excepció quan una funció no pot continuar, capturar-la, diversos catch, la jerarquia Throwable i bones pràctiques.',

    'blocs' => [
        [
            'tipus' => 'text',
            'titol' => 'Com funcionen les excepcions',
            'origen' => "$origen · pàg. 6-7",
            'html' => <<<'HTML'
                <ul>
                  <li>Una excepció és un objecte que representa un error i que es <strong>llança</strong> amb <code>throw</code>.</li>
                  <li>En llançar-se, <strong>la resta del <code>try</code> no s'executa</strong>: PHP salta al primer <code>catch</code> compatible.</li>
                  <li>Si cap <code>catch</code> la captura, el programa s'atura amb <code>Fatal error: Uncaught ...</code>.</li>
                  <li><code>finally</code> (opcional) s'executa <strong>sempre</strong>: ideal per tancar fitxers o connexions.</li>
                </ul>
                <pre class="code">try {
                    // codi que pot llançar una excepció
                } catch (TipusExcepcio $e) {
                    // què fem si passa això
                } catch (AltreTipus | UnAltre $e) {
                    // diversos tipus en un sol bloc (PHP 7.1+)
                } finally {
                    // s'executa SEMPRE
                }</pre>
                <p><strong>Ordre dels catch:</strong> del més específic al més general. Si poses <code>catch (Exception $e)</code> primer, ho captura tot.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: validar abans de dividir',
            'fitxer' => 'dividir.php',
            'codi' => <<<'PHP'
                <?php
                function dividir(int $a, int $b): float
                {
                    if ($b === 0) {
                        throw new InvalidArgumentException("No es pot dividir per zero.");
                    }
                    return $a / $b;     // si s'ha llançat l'excepció, aquí no s'arriba
                }

                try {
                    echo dividir(10, 2) . PHP_EOL;
                    echo dividir(10, 0) . PHP_EOL;
                    echo "Aquesta línia no s'executa" . PHP_EOL;
                } catch (InvalidArgumentException $e) {
                    echo "Error: " . $e->getMessage() . PHP_EOL;
                } finally {
                    echo "Execució completada." . PHP_EOL;
                }
                PHP,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q1',
            'conceptes' => ['try / catch / finally'],
            'pregunta' => 'Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                try {
                    echo "A";
                    throw new Exception("ups");
                    echo "B";
                } catch (Exception $e) {
                    echo "C";
                } finally {
                    echo "D";
                }
                echo "E";
                PHP,
            'resposta' => 'ACDE',
            'explicacio' => 'Després del throw, "B" no s\'executa. El catch mostra C, finally sempre mostra D, i com que l\'excepció s\'ha capturat el programa continua amb E.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Diversos catch i multi-catch',
            'origen' => "$origen · pàg. 9",
            'html' => <<<'HTML'
                <p><code>$e-&gt;getMessage()</code> retorna el text de l'excepció. Pots tenir diversos <code>catch</code> per a tipus diferents,
                agrupar-ne amb <code>|</code>, i acabar amb un <code>catch (Exception $e)</code> que reculli qualsevol altra excepció no prevista.</p>
                HTML,
        ],
        [
            'tipus' => 'codi',
            'titol' => 'Prova-ho: arrel quadrada',
            'fitxer' => 'arrel.php',
            'codi' => <<<'PHP'
                <?php
                function arrelQuadrada(mixed $num): float
                {
                    if (!is_numeric($num)) {
                        throw new InvalidArgumentException("'$num' no és un número.");
                    }
                    if ($num < 0) {
                        throw new DomainException("No hi ha arrel real de $num.");
                    }
                    return sqrt($num);
                }

                foreach (["16", "abc", -4] as $valor) {
                    try {
                        echo arrelQuadrada($valor) . PHP_EOL;
                    } catch (InvalidArgumentException | DomainException $e) {
                        echo "Dada incorrecta: " . $e->getMessage() . PHP_EOL;
                    } catch (Exception $e) {
                        echo "Error general: " . $e->getMessage() . PHP_EOL;
                    }
                }
                PHP,
        ],
        [
            'tipus' => 'text',
            'titol' => 'La jerarquia (PHP 8)',
            'origen' => "$origen · pàg. 10",
            'html' => <<<'HTML'
                <pre class="code">Throwable (interfície)
                ├── Error  ← errors interns de PHP
                │   ├── TypeError
                │   │   └── ArgumentCountError
                │   ├── ValueError
                │   ├── ArithmeticError
                │   │   └── DivisionByZeroError
                │   ├── CompileError
                │   │   └── ParseError
                │   └── UnhandledMatchError
                └── Exception  ← errors de l'aplicació (els nostres)
                    ├── ErrorException
                    ├── JsonException
                    ├── RuntimeException
                    └── LogicException
                        ├── InvalidArgumentException
                        └── DomainException</pre>
                <p>Un <code>catch</code> d'una classe captura també les seves filles: <code>catch (LogicException $e)</code> captura
                <code>InvalidArgumentException</code>. <code>catch (Throwable $t)</code> ho captura absolutament tot. Compte:
                <code>catch (Exception $e)</code> <strong>no</strong> captura un <code>DivisionByZeroError</code>, perquè és un <code>Error</code>.</p>
                HTML,
        ],
        [
            'tipus' => 'test',
            'id' => 'q2',
            'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Fem <code>try { echo 5 % 0; } catch (Exception $e) { echo "capturada"; }</code>. Què passa?',
            'opcions' => ['Mostra «capturada»', 'Fatal error: DivisionByZeroError no capturat', 'Mostra 0', 'Mostra un Warning i continua'],
            'correcta' => 1,
            'explicacio' => 'DivisionByZeroError és fill d\'Error, no d\'Exception. Per capturar-lo caldria catch (DivisionByZeroError $e), catch (Error $e) o catch (Throwable $t).',
        ],
        [
            'tipus' => 'completa',
            'id' => 'q3',
            'conceptes' => ['throw'],
            'pregunta' => 'Completa la instrucció que llança l\'excepció quan l\'edat és negativa:',
            'codi' => "if (\$edat < 0) {\n    ___ new InvalidArgumentException(\"L'edat no pot ser negativa.\");\n}",
            'respostes' => ['throw'],
            'explicacio' => 'throw llança l\'excepció i atura la funció a l\'instant.',
        ],
        [
            'tipus' => 'text',
            'titol' => 'Bones pràctiques',
            'origen' => "$origen · pàg. 11",
            'html' => <<<'HTML'
                <ul>
                  <li><strong>Amaga, però registra:</strong> display_errors = Off i log_errors = On en producció.</li>
                  <li><strong>E_ALL en desenvolupament:</strong> veure tots els avisos ajuda a detectar errors aviat.</li>
                  <li><strong>Captura el que puguis resoldre:</strong> res de <code>catch</code> buits, que amaguen el problema.</li>
                  <li><strong>Excepcions específiques:</strong> llança i captura classes concretes, no sempre <code>Exception</code>.</li>
                  <li><strong>Allibera recursos:</strong> tanca fitxers i connexions al bloc <code>finally</code>.</li>
                  <li><strong>Missatges útils:</strong> per a l'usuari, clars i amables; per al log, detallats i tècnics.</li>
                </ul>
                HTML,
        ],
        [
            'tipus' => 'sortida',
            'id' => 'q4',
            'conceptes' => ['jerarquia d\'excepcions'],
            'pregunta' => 'Quin catch s\'executa? Què imprimeix?',
            'codi' => <<<'PHP'
                <?php
                try {
                    throw new DomainException("fora de rang");
                } catch (InvalidArgumentException $e) {
                    echo "1";
                } catch (LogicException $e) {
                    echo "2";
                } catch (Exception $e) {
                    echo "3";
                }
                PHP,
            'resposta' => '2',
            'explicacio' => 'DomainException no és una InvalidArgumentException, però sí una LogicException (la seva mare). S\'executa el primer catch compatible: el 2.',
        ],
    ],
];
