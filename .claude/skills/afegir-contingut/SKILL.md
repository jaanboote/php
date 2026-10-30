---
name: afegir-contingut
description: Com crear o modificar lliçons, laboratoris (de consola i web) i reptes a content/, i validar-los. Usa-la quan es demani afegir o canviar contingut didàctic.
---

# Afegir o modificar contingut de PHPLab

Tot el contingut didàctic és a `content/`: **un fitxer PHP per element, que retorna un array**. No cal tocar cap altre fitxer:
`Lessons::all()`, `Labs::all()` i `Reptes::bank()` llegeixen la carpeta sencera.

| Carpeta | Què hi va | Qui ho llegeix |
|---|---|---|
| `content/lessons/<id>.php` | Una lliçó | `src/Lessons.php`, `templates/leccio.php` |
| `content/labs/<id>.php` | Un laboratori (de consola o web) | `src/Labs.php`, `src/WebLab.php`, `templates/lab.php` |
| `content/reptes/temaN.php` | El banc de reptes d'un tema | `src/Reptes.php` |

Abans d'escriure, **llegeix un fitxer semblant de la mateixa carpeta** i copia'n l'estil. Bons exemples:
`content/lessons/fitxers-json.php`, `content/labs/pagina-dinamica.php` (consola), `content/labs/carta-reis.php` (web) i
`content/reptes/tema2.php` (té el format comentat a dalt).

## Regles comunes

- L'`id` és el nom del fitxer sense `.php` i només pot tenir `a-z`, `0-9` i `-`.
- `tema` ha de ser **exactament** un dels textos existents, per exemple `'Tema 2 – Fonaments de PHP'` (amb guió llarg `–`).
  El número del tema dona el color i agrupa a Inici i a Progrés.
- `ordre` col·loca l'element: Tema 1 → 1-9, Tema 2 → 10-60, Tema 3 → 110-140, Tema 4 → 210-230, Tema 5 → 310-330.
  Un laboratori té el mateix `ordre` que la lliçó que el prepara.
- El codi d'exemple va en *nowdoc* (`<<<'PHP'`), perquè `$variables` no s'interpolin. Les línies del *nowdoc* no poden tenir
  menys sagnat que la marca de tancament (si no, «Invalid body indentation level»).
- Les claus `html`, `explicacio`, `context` i `pregunta` són HTML de confiança (es mostren sense escapar). Escapa-hi tu
  `<` i `>` com `&lt;` i `&gt;` quan parlis d'etiquetes.

## Lliçons (`content/lessons/<id>.php`)

```php
<?php
/**
 * Lliçó: Fitxers i JSON (Tema 4, apartat 4.2).
 */
$origen = 'Tema 4 - Gestor d\'exepcions fitxers.pdf';

return [
    'id' => 'fitxers-json',
    'ordre' => 230,
    'titol' => 'Fitxers i JSON',
    'tema' => 'Tema 4 – Errors, excepcions i fitxers',
    'apartat' => '4.2 Fitxers i JSON',
    'origen' => $origen,              // d'on surt (el fitxer del temari)
    'minuts' => 25,
    'xp_final' => 20,                 // XP en encertar totes les preguntes
    'lab' => 'comptador-visites',     // opcional: id del laboratori per practicar-ho
    'resum' => 'Una frase: què aprendràs.',
    'blocs' => [ /* blocs en ordre */ ],
];
```

Tipus de bloc (`'tipus'`):

| Tipus | Claus | Notes |
|---|---|---|
| `text` | `titol`, `html`, `origen` (opcional, p. ex. `"$origen · pàg. 13"`) | Explicació. |
| `codi` | `titol`, `codi`, `fitxer` (per defecte `main.php`), `args` (text: `'192.168.1.37 24'`), `stdin`, `fitxers` (`['dades.txt' => "..."]`), `html` (opcional), `error_intencionat` | Exemple «Prova-ho» editable. Ha d'executar-se sense Fatal error, tret que porti `'error_intencionat' => true`. |
| `test` | `id`, `conceptes`, `pregunta`, `opcions`, `correcta` (índex des de 0), `explicacio` | |
| `completa` | `id`, `conceptes`, `pregunta`, `codi` (amb `___`), `respostes` (llista d'acceptades), `explicacio` | Es compara sense espais; **les majúscules compten**. |
| `sortida` | `id`, `conceptes`, `pregunta`, `codi`, `resposta`, `explicacio`; opcionals `args`, `fitxers` | «Què imprimeix?». La resposta es compara normalitzada (espais repetits, línies buides del final). |
| `ordenar` | `id`, `conceptes`, `pregunta`, `elements` (en l'**ordre correcte**), `explicacio` | Es mostren barrejats; només val l'ordre exacte. |

- Els `id` de les preguntes (`q1`, `q2`...) han de ser únics dins de la lliçó: s'hi desen les respostes i el repàs espaciat.
- Qualsevol bloc pot portar `'ampliacio' => true`.
- Els exemples s'executen a la **consola**. Per a formularis, simula les dades: `$_POST = ['nom' => 'Anna'];`.

## Laboratoris de consola (`content/labs/<id>.php`)

```php
return [
    'id' => 'pagina-dinamica',
    'ordre' => 3,
    'titol' => 'Pàgina dinàmica: Bon dia, bona tarda, bona nit',
    'fitxer' => 'salutacio.php',      // nom del programa ($argv[0])
    'tema' => 'Tema 1 – Introducció al desenvolupament web',
    'apartat' => '1.1 Llocs estàtics i dinàmics',
    'origen' => ['fitxer' => 'Tema 1 - Introducció al desenvolupament web.pdf', 'descripcio' => 'Exemple del Tema 1, diapositiva 13 (David Porti)'],
    'resum' => '...',
    'xp_final' => 30,
    'paraules_clau' => ['if', 'date', 'echo'],   // ajuden el tutor a buscar al temari
    'codi_inicial' => <<<'PHP'
        <?php
        // Pàgina dinàmica: salutació segons l'hora

        PHP,
    'context' => '<p>HTML: l\'enunciat general.</p>',
    'resum_apres' => ['Què has après (es mostra en acabar).'],
    'passos' => [
        [
            'titol' => 'L\'hora',
            'conceptes' => ['variables', 'validació de dades'],
            'xp' => 15,
            'explicacio' => '<p><strong>Objectiu:</strong> ...</p>',
            'codi' => [   // comprovacions sobre el codi, sense executar-lo (els comentaris no compten)
                ['regex' => '/\bdate\s*\(\s*[\'"]H[\'"]\s*\)/', 'missatge' => 'Sense argument, agafa l\'hora amb date(\'H\').'],
                // o bé ['no_regex' => '/.../', 'missatge' => '...']
            ],
            'proves' => [
                ['nom' => 'Hora 25', 'args' => ['25'], 'conte' => ["ERROR: l'hora ha de ser un número entre 0 i 23."], 'codi_sortida' => 1],
            ],
            'pistes' => ['nivell 1', 'nivell 2', 'nivell 3'],
            'preguntes' => ['Preguntes que farà el tutor sense connexió.'],
            'solucio' => $sol1,
            'solucio_explicacio' => 'Per què funciona.',
        ],
    ],
];
```

Normalment les solucions es construeixen a dalt del fitxer i s'encadenen: `$sol2 = $sol1 . <<<'PHP' ... PHP;`.

Claus d'una prova de consola (`src/Labs.php`, `runTest`):

| Clau | Què fa |
|---|---|
| `nom` | Títol de la prova (obligatori). |
| `args` | Arguments, com a **array**: `['afegeix', 'Anna', '622555666']`. |
| `stdin` | Respostes a `readline()`, una per línia: `"25\n"`. |
| `fitxers` | Fitxers que hi ha abans d'executar: `['contactes.json' => $json]`. Un nom acabat en `/` crea una carpeta. |
| `append` | Codi que s'executa després del de l'alumne (per cridar les seves funcions). |
| `exacte` | La sortida ha de ser exactament aquesta (normalitzada). |
| `conte` / `no_conte` | Textos que la sortida ha de contenir o no. |
| `conte_regex` | `['/regex/' => 'descripció per a l\'alumne']`. |
| `codi_sortida` | El valor d'`exit()` esperat. |
| `fitxer_conte` | `['fitxer' => ['text', ...]]`: el fitxer ha d'existir i contenir-los. |
| `fitxer_regex` | `['fitxer' => ['/regex/' => 'descripció']]`. |
| `permet_avisos` | `true` si la prova accepta Warnings. Per defecte, qualsevol sortida a stderr fa fallar la prova. |

`'repas' => true` en un pas indica que la solució del pas anterior també el pot superar (un pas de repàs).

## Laboratoris web (`content/labs/<id>.php` amb `'tipus' => 'web'`)

Les diferències amb els de consola (exemple complet: `content/labs/carta-reis.php`):

- `'tipus' => 'web'` i `'fitxer' => 'index.php'`.
- `codi_inicial` i cada `solucio` són un **array de fitxers**: `['index.php' => $html, 'processa.php' => $php, 'estil.css' => $css]`.
  L'alumne només pot editar els fitxers que hi ha a `codi_inicial`.
- A `codi`, cada comprovació pot portar `'fitxer' => 'processa.php'` per mirar només aquell fitxer.

Una prova és una seqüència de peticions, com les faria una persona amb el navegador, i les comprovacions es fan sobre
l'última resposta. Les redireccions se segueixen soles i les galetes (la sessió) es conserven dins de la prova:

```php
['nom' => 'Una joguina',
 'peticions' => [
     ['GET', 'index.php'],
     ['GET', 'salutacio.php?nom=Anna&idioma=ca'],                     // o bé amb 'query' => ['nom' => 'Anna']
     ['POST', 'processa.php',
      'camps' => ['nom' => 'Anna Puig', 'edat' => '9'],
      'pujades' => ['imatge1' => ['tren.png', '@png']]],              // [nom, contingut, tipus MIME opcional]
 ],
 'text' => ['La teva carta ha estat enviada als Reis Mags!'],
 'fitxer_existeix' => ['uploads/tren.png']],
```

Claus d'una prova web (`src/WebLab.php`, `runTest`):

| Clau | Què fa |
|---|---|
| `peticions` | Llista de `[mètode, ruta]` amb `query`, `camps` (POST) i `pujades` (fitxers). |
| `fitxers` | Fitxers que hi ha al «servidor» abans de la prova. |
| `pujades` | `'camp' => [nom, contingut]`. Contingut: `'@png'`, `'@gif'`, `'@jpg'` (imatges mínimes vàlides), `'@gran'` (PNG de més de 2 MB) o un text. Un camp buit: `['', '']`. Per a `nom[]`, una llista de fitxers. |
| `conte` / `no_conte` | Text dins de l'**HTML** de la resposta (amb els espais compactats). |
| `text` / `no_text` | Text **visible** a la pàgina (sense etiquetes; no distingeix majúscules). |
| `conte_regex` | `['/regex/i' => 'descripció']` sobre l'HTML. |
| `estat` | Codi HTTP final esperat (p. ex. `200`). |
| `redirigeix` | Alguna resposta ha redirigit a una adreça que conté aquest text. |
| `sense_redireccio` | `true`: cap resposta pot redirigir. |
| `fitxer_existeix` / `fitxer_no_existeix` | Fitxers del servidor després de la prova (`'uploads/tren.png'`). |
| `fitxer_regex` | `['fitxer' => ['/regex/' => 'descripció']]`. |
| `permet_avisos` | `true` si s'accepten Warnings. Per defecte, qualsevol error de PHP fa fallar la prova. |

Per comprovar atributs d'HTML en qualsevol ordre, fes servir *lookaheads*:
`'/<input(?=[^>]*type=["\']number["\'])(?=[^>]*name=["\']edat["\'])[^>]*>/i'`.

## Reptes (`content/reptes/temaN.php`)

```php
return [
    'tema' => 'Tema 2 – Fonaments de PHP',
    'items' => [ /* reptes */ ],
];
```

Cada repte té `id` (únic a tots els fitxers; convenció `t2-...`), `tipus`, `conceptes`, `pregunta`, `explicacio` i, segons el tipus:

| Tipus | Claus | Mode on surt |
|---|---|---|
| `sortida` | `codi`, `resposta`; opcional `args` | «Què imprimeix?» |
| `error` | `codi` (amb l'error), `linia` (on és, des de 1), `corregit` (el codi bo), `esperat` (la sortida del corregit); opcionals `args`, `errata` | «Troba l'error» |
| `ordenar` | `linies` (en l'ordre correcte) + `sortida`; o bé `elements` (text, no codi) | «Ordena el codi» |
| `test` | `opcions`, `correcta` | Només a contrarellotge i repàs |

Els 4 modes (`Reptes::MODES`) són: **contrarellotge** (`rapid`, de tots els tipus), **Troba l'error** (`error`),
**Què imprimeix?** (`sortida`) i **Ordena el codi** (`ordenar`). El **repàs espaciat** (`repas`) reutilitza les preguntes fallades
de reptes i de lliçons.

- A `ordenar` amb `linies` + `sortida`, s'accepta qualsevol ordre que, executat, doni la mateixa sortida.
- `'errata' => 'Tema 2, pàgina 77'` marca un error que surt tal qual al temari (compta per a la insígnia «Caçador d'errates»).

## Regles didàctiques

- **Segueix el temari de classe.** Cita d'on surt cada cosa (`origen`, `apartat`) i fes servir els exemples i els noms
  dels enunciats del professor. Si adaptes un enunciat per poder-lo corregir (p. ex. arguments en lloc de `readline()`), digues-ho a `context`.
- **Marca com a ampliació** (`'ampliacio' => true`, o «Ampliació:» a `origen.descripcio` d'un laboratori) tot el que no surti al temari.
- **Pistes en 3 nivells**, de menys a més concretes: (1) què cal fer, sense codi; (2) les funcions o l'estructura que calen;
  (3) gairebé el codi, però no la solució sencera.
- **No facis servir POO ni conceptes de temes posteriors**: res de classes ni objectes propis, i a un tema no surt el que
  s'explica en un d'acabat (p. ex. al Tema 2 no hi ha `try`/`catch`, que és del Tema 4, ni `$_POST`, que és del Tema 5).
  Fes servir només el que l'alumne ja ha vist fins a aquell tema.
- Els missatges que comproven les proves s'han de donar **literals** a l'explicació del pas (p. ex. «mostra `Falta el nom.`»).
- Textos en català, en segona persona i en to proper. Els errors expliquen què passa i com arreglar-ho.

## Pas final obligatori

```
php eines/verifica-contingut.php
```

Ha de sortir **«Tot correcte.»** L'eina comprova:

- Cada laboratori: la `solucio` de cada pas **supera** les seves proves, i la del pas anterior (o `codi_inicial` al primer pas)
  **no** les supera. Si les supera, les proves no comproven res de nou: afegeix-ne una que distingeixi els dos passos
  (o marca el pas amb `'repas' => true` si és un pas de repàs).
- Lliçons: els blocs `codi` s'executen sense Fatal error i cada `sortida` dona exactament la `resposta`.
- Reptes: `sortida` i `ordenar` donen la sortida indicada; a `error`, el `corregit` dona `esperat`, el `codi` erroni no,
  i la línia `linia` és diferent entre tots dos; a `test`, l'opció `correcta` existeix. No hi ha `id` repetits.

Per a un sol laboratori, en desenvolupament, pots fer servir `Labs::evaluate($lab, $pas, $lab['passos'][$pas]['solucio'])`
des d'un script de prova, però abans del commit executa sempre l'eina sencera.
