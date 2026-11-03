# PHPLab per dins

> Per a qui vol saber com està fet PHPLab: estructura del codi, seguretat i funcionament dels laboratoris.
> Per instal·lar-lo i fer-lo servir, mira la [guia d'instal·lació](GUIA.md).

## 1. Estructura del projecte

```
phplab/
├── index.php              ← pàgines (index.php?p=inici, leccio, lab, temari, diagnostic)
├── api.php                ← API JSON per al JavaScript (executar, comprovar, pistes, tutor)
├── config.example.php     ← plantilla de configuració (copia-la a config.php)
├── .htaccess              ← qui pot entrar i què no es pot descarregar
├── src/                   ← la lògica, una classe per fitxer
│   ├── bootstrap.php      ← arrencada: config, autoload, control d'IP, sessió
│   ├── Auth.php           ← login, sessions, CSRF i límit d'intents
│   ├── Db.php             ← SQLite amb PDO i creació automàtica de taules
│   ├── Sandbox.php        ← executa el codi de l'alumne amb límits
│   ├── ErrorExplainer.php ← tradueix els errors de PHP al català
│   ├── Uploader.php       ← pujada de fitxers i extracció segura de ZIP
│   ├── TextExtractor.php  ← text de PDF, DOCX, HTML...
│   ├── Indexer.php        ← indexa el temari i busca fragments rellevants
│   ├── Labs.php           ← motor dels laboratoris (proves, pistes, solució, XP)
│   ├── Lessons.php        ← lliçons i correcció de preguntes
│   ├── Tutor.php          ← tutor socràtic (Claude o sense connexió)
│   ├── Progress.php       ← XP, nivells i ratxa
│   └── View.php           ← plantilles
├── templates/             ← HTML de cada pàgina
├── content/
│   ├── lessons/           ← lliçons (un fitxer PHP que retorna un array)
│   └── labs/              ← laboratoris (un fitxer PHP que retorna un array)
├── assets/                ← CSS, JS i CodeMirror (l'editor, copiat aquí perquè funcioni sense Internet)
└── eines/                 ← scripts de terminal
```

I fora de htdocs, a `C:\xampp\phplab-data\`:

```
phplab-data/
├── phplab.sqlite          ← progrés, XP, temari indexat, xat del tutor
├── temari/                ← els fitxers que has pujat
└── sandbox/
    ├── jobs/              ← carpetes temporals on s'executa el teu codi (s'esborren soles)
    └── workspaces/        ← fitxers que creen els programes de cada laboratori
```

**Flux d'un «Comprova» al laboratori:**
`lab.js` → `api.php?a=lab_check` → `Labs::check()` → per a cada prova, `Sandbox::run()` executa el
teu codi amb uns arguments → es compara la sortida amb l'esperada → es guarda l'intent i, si
l'has superat, l'XP.

**Comprovar el contingut:** després de crear o modificar lliçons i laboratoris, executa
`C:\xampp\php\php.exe eines\verifica-contingut.php`. Comprova que la solució de cada pas supera les
seves proves i que les respostes de les preguntes «què imprimeix?» són correctes.

**Afegir un laboratori nou a mà:** copia `content/labs/permisos.php` amb un altre nom, canvia
`id`, `fitxer` i els passos. Cada pas té `proves` amb `args` i el que ha de sortir (`conte`,
`no_conte` o `exacte`).

---

## 2. Seguretat (què fa la app i per què)

### Execució del teu codi (sandbox)

Executar codi escrit per l'usuari és el més perillós d'una app com aquesta. Aquestes són les capes de protecció:

| Mesura | Què fa | Contra què protegeix |
|---|---|---|
| **Procés separat** (`proc_open`) | El codi s'executa amb `php.exe` en un procés a part, no dins d'Apache. | Si peta, l'app continua funcionant. |
| **Timeout de 5 s** | Si el procés no acaba, es mata (`proc_terminate`). | Bucles infinits. |
| **`memory_limit=64M`** | El procés no pot fer servir més memòria. | Programes que s'ho mengen tot. |
| **`open_basedir`** | PHP només pot obrir fitxers dins de la carpeta temporal de l'execució. | Llegir `config.php`, la base de dades o fitxers del sistema. |
| **`disable_functions`** | Desactiva `exec`, `shell_exec`, `system`, `proc_open`, sockets, `curl`, `ini_set`... | Executar ordres del Windows, connectar-se a la xarxa o treure's els límits. |
| **`allow_url_fopen=Off`** | `file_get_contents("http://...")` no funciona. | Descarregar coses d'Internet. |
| **Entorn net** | El procés no rep les variables d'entorn d'Apache. | Filtrar informació del servidor. |
| **Màxim 2 execucions alhora** | Bloquejos amb `flock`. | Saturar el PC amb moltes execucions. |
| **Límit de sortida** | Si escriu més de 100 KB, s'atura. | Omplir la memòria o el disc. |
| **Fitxers limitats** | Cada laboratori té la seva carpeta (`phplab-data\sandbox\workspaces\<laboratori>`), on es conserven els fitxers entre execucions (com a molt 1 MB). | Que un programa ompli el disc o toqui fitxers d'un altre laboratori. |

Totes les directives es passen amb `-d` a `php.exe` (mira `Sandbox::iniSettings()`).
`ini_set()` està desactivada (si no, el codi es podria treure els límits); `ini_get()` i `error_reporting()` funcionen.
`error_log()` escriu al fitxer `php_errors.log` de la carpeta del programa, com el log d'un servidor real.
No és un aïllament perfecte com el d'un contenidor o una màquina virtual, però per a ús personal
a la xarxa de casa és raonable. Per això **no s'ha d'obrir mai l'app a Internet**.

### Accés i sessió

- **Només xarxa local:** ho limita el `.htaccess` (`Require local` + `Require ip 192.168.1.0/24`) i,
  per si el `.htaccess` no s'aplica, també el PHP (`allowed_networks`). Això és *defensa en profunditat*.
- **Contrasenya amb `password_hash()`:** a la base de dades només hi ha el hash (bcrypt), mai la contrasenya.
- **Límit d'intents:** amb 5 errors en 15 minuts, aquella IP queda bloquejada una estona (contra la força bruta).
- **Cookie de sessió `HttpOnly` i `SameSite=Strict`:** el JavaScript no la pot llegir (contra XSS)
  i el navegador no l'envia des d'altres webs (contra CSRF). En entrar es regenera l'ID de sessió (contra la fixació de sessió).
- **Tokens CSRF:** cada formulari i cada crida a l'API porten un token aleatori que es comprova amb `hash_equals()`.
- **Escapament amb `htmlspecialchars()`** (funció `e()`) a totes les dades que es mostren (contra XSS).
- **Consultes preparades amb PDO:** mai es concatenen dades a l'SQL (contra la injecció SQL).

### Fitxers pujats

- Es guarden **fora del webroot** (`C:\xampp\phplab-data`): encara que pugessis un `.php`, Apache no el podria executar.
- **Llista blanca d'extensions:** pdf, zip, docx, php, html, htm, txt i md.
- **Zip slip:** cap entrada d'un ZIP pot sortir de la carpeta. Es rebutgen les rutes amb `..`, les absolutes i les que porten lletra d'unitat.
- **Zip bomb:** es limiten el nombre de fitxers i la mida descomprimida, comptant els bytes reals.
- La clau de l'API només és a `config.php`, que el `.htaccess` impedeix descarregar. Les crides a Claude
  es fan des del servidor: la clau no arriba mai al navegador.

---

## 3. Reptes i repàs espaciat

A la pàgina **Reptes** hi ha quatre modes: ⏱ Contrarellotge (10 preguntes en 150 s), 🐞 Troba l'error (amb errates reals del temari),
🖨️ Què imprimeix? i ↕️ Ordena el codi (s'accepta qualsevol ordre que funcioni: s'executa al sandbox). Es poden filtrar per tema.

**Repàs espaciat (sistema Leitner):** cada pregunta que falles (de les lliçons o dels reptes) entra a la caixa 1 i torna a sortir
l'endemà. Cada encert al repàs la fa pujar de caixa i s'espera més: 1, 3, 7, 14 i 30 dies. Si la falles, torna a la caixa 1.
Quan l'encertes a la caixa 5, surt del repàs. Inici avisa quan tens preguntes per repassar.

Els reptes donen com a molt 80 XP al dia (el repàs no té límit). Les preguntes són a `content/reptes/temaN.php` i
`eines/verifica-contingut.php` les comprova totes.

## 4. Progrés: mapa de temes, insígnies i estadístiques

La pàgina **Progrés** té tres parts:

- **Mapa de temes:** cada tema amb les lliçons i els laboratoris en ordre (fet, en curs o per començar), el percentatge
  d'encerts als reptes del tema i el domini de cada concepte: ● dominat (80 % d'encerts o més), ◐ practicant, ◔ per reforçar.
- **Insígnies:** 23 fites (acabar lliçons i laboratoris, un laboratori sense pistes, trobar les errates del temari, una
  contrarellotge perfecta, ratxes de dies, superar cada tema...). Les que encara no tens mostren quant et falta. Quan n'aconsegueixes
  una, surt un avís a qualsevol pàgina. Una insígnia aconseguida no es perd mai. Les definicions són a `src/Badges.php`.
- **Estadístiques:** XP de cada dia dels últims 30 dies, d'on ve l'XP, partides i encert per mode de repte, i els conceptes que més costen.

## 5. Laboratoris web (Tema 5)

Als laboratoris web escrius diversos fitxers (`index.php`, `processa.php`, `estil.css`...) i els proves en un **navegador integrat**
a la dreta de la pàgina: els formularis s'envien, les imatges es pugen i les redireccions i sessions funcionen com a XAMPP.

- **Laboratoris:** formulari de salutació amb GET, la **Carta als Reis Mags** (l'Activitat de Nadal: POST, validació i pujada de fins a 3
  imatges a `uploads/`) i una zona privada amb inici de sessió, `$_SESSION` i `password_verify()`.
- **Inspector:** a cada petició mostra el mètode, el codi HTTP, `$_GET`, `$_POST`, `$_FILES`, les galetes, el contingut de la sessió,
  els fitxers del «servidor» i els errors de PHP explicats.
- **Lliçons:** formularis GET i POST, pujar fitxers amb `$_FILES` i galetes, sessions i contrasenyes (aquesta última és ampliació:
  els apartats 5.10–5.12 del mòdul encara no tenen apunts de classe).

**Com funciona per dins:** el navegador és un `<iframe>` que carrega `web.php/<testimoni>/<laboratori>/<fitxer>`. `web.php` executa
el fitxer amb `php-cgi.exe` (el mateix sandbox: temps límit, memòria, `open_basedir`, funcions desactivades) i en retorna la resposta.

| Mesura | Per què |
|---|---|
| L'iframe porta `sandbox` i `web.php` envia `Content-Security-Policy: sandbox` | El codi de la teva web (HTML i JavaScript) s'executa en un origen aïllat: no pot llegir les galetes de PHPLab ni tocar la pàgina del laboratori, encara que obris la URL directament. |
| Testimoni signat a la URL (caduca en 12 h) | Un iframe aïllat no envia la sessió de la app; el testimoni prova que l'has obert tu des del laboratori. |
| Galetes i sessions de la teva web desades al servidor (`phplab-data\sandbox\web`) | No es barregen amb les de PHPLab, i l'inspector les pot ensenyar. |
| Fitxers del «servidor» a la carpeta del laboratori (com a molt 8 MB) | Les imatges pujades es conserven entre peticions, sense sortir de la carpeta del sandbox. |

Si a **Diagnòstic** surt «php-cgi per als laboratoris web» en vermell, revisa `sandbox.php_cgi` a `config.php`.

## 6. Exàmens

La pàgina **Exàmens** té dos simulacres per a cada tema de l'1 al 4 i un examen final. Cada examen té una part teòrica
(preguntes amb el format dels reptes) i una part pràctica (programes que es corregeixen amb proves, com els laboratoris).
Les respostes es desen soles mentre es fa, i tot es corregeix al servidor en entregar-lo (`src/Examens.php`). Els exàmens
són a `content/examens/` i `eines/verifica-contingut.php` també els comprova.

## 7. Properes fases

- Accés a bases de dades amb PDO i MySQL (apartat 6 del mòdul).
