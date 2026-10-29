# PHPLab

App web per aprendre PHP a partir del temari de classe (M0376 · Implantació d'aplicacions web · ASIX2).
Funciona amb XAMPP a Windows, per a un sol usuari i només a la xarxa local. La presentació és al `README.md` i la guia
d'instal·lació i seguretat a `docs/GUIA.md`.

## Estructura

- `index.php`: pàgines (inici, lliçó, laboratori, reptes, progrés, temari, diagnòstic).
- `api.php`: accions en JSON per al JavaScript (executar, corregir, pistes, tutor...).
- `web.php`: navegador integrat dels laboratoris web (executa amb `php-cgi`).
- `src/`: lògica (Sandbox, Labs, WebLab, Lessons, Reptes, Review, Badges, Stats, Auth, Db, Uploader...).
- `templates/`: una vista PHP per pàgina.
- `content/lessons`, `content/labs`, `content/reptes`: el contingut didàctic, un array PHP per fitxer.
- `eines/`: `verifica-contingut.php` i `canvia-contrasenya.php`.

## Regles

- PHP 8.2 pur, sense framework. No afegeixis frameworks ni dependències noves (Composer o npm) sense demanar-ho.
- Cada fitxer PHP de `src/`, `api.php`, `web.php` i `index.php` comença amb `declare(strict_types=1);`.
- Codi, comentaris, textos de la interfície i missatges de commit en català. Mantén la mateixa densitat de comentaris
  que el codi del voltant: un comentari explica el perquè, no repeteix el codi.
- JavaScript sense dependències (només CodeMirror, que ja és a `assets/vendor`).
- Tot ha de funcionar sense Internet: res de CDN; fonts, scripts i estils locals a `assets/`.

## Seguretat (obligatori)

- A les plantilles, escapa sempre amb `e()`. L'HTML cru només és per al contingut de `content/` (l'escrivim nosaltres).
- Base de dades: sempre consultes preparades amb `Db::run`, `Db::one`, `Db::all` o `Db::value`. Mai concatenis valors a l'SQL.
- Tota acció POST porta token CSRF: formularis amb el camp `csrf`, i `api.php` amb la capçalera `X-CSRF-Token`.
- El navegador no rep mai les respostes correctes: es corregeix sempre al servidor.
- Migracions noves: afegeix-les al final de l'array de `Db::migrate` amb el número següent. No modifiquis mai les antigues.

## Verificació

Després de tocar `content/` o `src/Sandbox.php`, executa:

```
php eines/verifica-contingut.php
```

Ha de sortir **«Tot correcte.»** Si alguna línia diu `FALLA`, arregla-ho abans de fer commit.

## Skills del projecte

- `.claude/skills/afegir-contingut`: per crear o modificar lliçons, laboratoris (de consola i web) i reptes. Té el format
  exacte de cada fitxer de `content/`.
- `.claude/skills/seguretat-sandbox`: checklist obligatòria abans de tocar `src/Sandbox.php`, `src/WebLab.php`, `web.php`,
  `src/Uploader.php`, `src/Auth.php` o `src/bootstrap.php`, o qualsevol cosa que executi el codi de l'alumne.
