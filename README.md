# PHPLab

Aplicació web per aprendre PHP de manera pràctica a partir del temari de classe
(M0376 · Implantació d'aplicacions web · ASIX2). Està feta en **PHP 8 sense framework**
perquè també puguis llegir i entendre com funciona per dins.

**Què hi ha ara:**

- Login d'un sol usuari, accés només des de la xarxa de casa.
- **Temari:** pujar-lo (PDF, ZIP, DOCX, PHP, HTML, TXT, MD), indexador amb detecció de canvis i lectura per temes.
- **Aprendre:** 16 lliçons dels Temes 1 a 4, amb exemples «prova-ho» executables i preguntes (test, completa, què imprimeix, ordena).
- **Laboratori:** 14 exercicis guiats pas a pas (basats en Exercicis 01 i 02 i les activitats dels temes), amb proves
  automàtiques, pistes de 3 nivells, solució després de 3 intents i un tutor socràtic (IA de Claude o sense connexió).
- **Reptes:** contrarellotge, «Troba l'error», «Què imprimeix?», «Ordena el codi» i repàs espaciat.
- **Sandbox** segur per executar el codi, amb fitxers que es conserven per laboratori.
- XP, nivells i ratxa diària.

---

## 1. Instal·lació (Windows + XAMPP)

### 1.1 Activa l'extensió `zip` de PHP

Al teu XAMPP (PHP 8.2.12) l'extensió `zip` està desactivada, i cal per als ZIP i els DOCX.

1. Obre `C:\xampp\php\php.ini` amb un editor de text.
2. Busca la línia `;extension=zip` i treu-li el `;` del davant: `extension=zip`.
3. Al mateix fitxer, revisa aquests valors per poder pujar fitxers grans:
   ```ini
   upload_max_filesize = 50M
   post_max_size = 120M
   ```
4. Reinicia Apache des del panell de control de XAMPP (Stop → Start).

### 1.2 Descarrega el projecte

Amb [Git per a Windows](https://git-scm.com/download/win), obre un terminal (`cmd`):

```bat
cd C:\xampp\htdocs
git clone https://github.com/jaanboote/php phplab
```

El projecte queda a `C:\xampp\htdocs\phplab`.

### 1.3 Instal·la Composer i les dependències

Composer és el gestor de llibreries de PHP. PHPLab en fa servir tres: `smalot/pdfparser` (llegir PDF),
`anthropic-ai/sdk` (l'SDK oficial de Claude) i `guzzlehttp/guzzle` (connexions HTTPS).

1. Descarrega i executa **Composer-Setup.exe** des de <https://getcomposer.org/download/>.
   Quan et demani el PHP, tria `C:\xampp\php\php.exe`.
2. Tanca i torna a obrir el terminal i comprova-ho: `composer -V`
3. Instal·la les dependències:
   ```bat
   cd C:\xampp\htdocs\phplab
   composer install
   ```

> Sense Composer la app també funciona, però no llegirà PDF i el tutor anirà sempre en mode sense connexió.

### 1.4 Configura `config.php`

```bat
copy config.example.php config.php
```

Obre `config.php` i revisa'l. Les opcions estan comentades. Les importants:

| Opció | Per a què serveix |
|---|---|
| `data_dir` | Carpeta de dades privades (base de dades, temari, sandbox). Per defecte és `C:\xampp\phplab-data`, **fora de htdocs**. |
| `allowed_networks` | Xarxes que poden entrar. Per defecte: aquest PC i `192.168.1.0/24` (la teva xarxa de casa). |
| `sandbox.php_cli` | El PHP que executa el teu codi: `C:\xampp\php\php.exe`. |
| `ai.api_key` | Clau de l'API de Claude (opcional, mira el punt següent). |

`config.php` no es puja mai a GitHub: és al `.gitignore` perquè hi ha la clau.

### 1.5 Clau de l'API de Claude (opcional)

1. Entra a <https://console.anthropic.com/>, crea un compte i afegeix-hi crèdit.
2. A **API Keys**, crea una clau i copia-la a `config.php` → `'api_key' => 'sk-ant-...'`.
3. El model per defecte és `claude-haiku-5-5`, el més econòmic. Pots limitar la despesa amb
   `max_requests_day`, `max_tokens_day` i `max_tokens`.

Si el tutor diu *«problema de certificats SSL»*, revisa que `ai.ca_bundle` apunti a
`C:\xampp\apache\bin\curl-ca-bundle.crt`, que ja ve amb XAMPP.

### 1.6 Primer accés

1. Des del **PC servidor**, obre <http://localhost/phplab>.
2. Crea el teu usuari i la contrasenya (mínim 10 caràcters). Aquesta pantalla només surt la primera
   vegada i **només des de localhost**.
3. Ves a **Diagnòstic** i comprova que tot surt en verd.

### 1.7 Accés des del portàtil o el mòbil

- Obre `http://192.168.1.72/phplab`, amb la IP del PC servidor (la veus amb `ipconfig`).
- Si no carrega, cal obrir el tallafoc del Windows: *Tallafoc de Windows Defender → Permet una aplicació* →
  marca **Apache HTTP Server** a les xarxes **privades**. Comprova també que la xarxa de casa
  estigui configurada com a «Privada».
- La IP `192.168.56.1` que surt a `ipconfig` és l'adaptador de VirtualBox. No és la teva xarxa de casa.
- Si et canvia la IP, pots reservar-la al router (DHCP estàtic).

### 1.8 Actualitzar a una versió nova

```bat
cd C:\xampp\htdocs\phplab
git pull
composer install
```

La base de dades s'actualitza sola: les taules noves es creen automàticament.

### Si has oblidat la contrasenya

```bat
cd C:\xampp\htdocs\phplab
C:\xampp\php\php.exe eines\canvia-contrasenya.php
```

---

## 2. Com afegir temari nou

1. Ves a **Temari** → **Pujar temari** i tria els fitxers. Pots pujar-ne diversos alhora o un ZIP sencer.
2. La app guarda els fitxers a `data_dir\temari`, n'extreu el text i detecta el tema llegint la
   capçalera dels PDF del professor («Tema 2 – Fonaments PHP»). Si no la troba, fa servir el nom
   del fitxer o de la carpeta.
3. En acabar, et diu quins fitxers són nous i si han aparegut **temes nous**.
4. Si un fitxer ja existia amb el mateix contingut, no es duplica (es compara el hash SHA-256).

Des de la pàgina d'**Inici** o de **Temari**, fes clic en un tema per **llegir-ne el text** dins de l'app.

El botó **Actualitzar temari** torna a revisar la carpeta i només processa els fitxers nous o modificats.
També pots copiar fitxers directament a `C:\xampp\phplab-data\temari` i després prémer aquest botó.

> **Avís:** quan s'extreu el text d'un PDF, el codi perd la indentació. Per això els laboratoris
> estan escrits a mà a partir dels enunciats.

---

## 3. Com funciona (estructura del projecte)

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
`no_conte` o `exacte`). A la fase 2, la IA en generarà a partir del temari.

---

## 4. Seguretat (què fa la app i per què)

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

## 5. Problemes freqüents

| Problema | Solució |
|---|---|
| «Falta config.php» | `copy config.example.php config.php` |
| 403 des del mòbil | La IP del mòbil no és a `192.168.1.x`. Revisa el `.htaccess` i `allowed_networks`. |
| No es pot entrar des d'un altre PC | Tallafoc del Windows (punt 1.7). |
| «cal activar extension=zip» | Punt 1.1 i reinicia Apache. |
| Els PDF surten com a «pendent» | Executa `composer install` i després prem «Actualitzar temari». |
| PDF «sense text» | És un PDF escanejat (una imatge). Cal un PDF amb text. |
| La pujada falla sense cap missatge | Augmenta `post_max_size` i `upload_max_filesize` al `php.ini`. |
| El tutor diu «problema de certificats SSL» | Revisa `ai.ca_bundle` (punt 1.5). |
| `readline()` | Al sandbox, `readline()` llegeix de la caixa «Entrada» (una resposta per línia) i mostra el que respons, com faria el terminal. |

Si alguna cosa falla, mira el log d'Apache: `C:\xampp\apache\logs\error.log`.

---

## 6. Reptes i repàs espaciat

A la pàgina **Reptes** hi ha quatre modes: ⏱ Contrarellotge (10 preguntes en 150 s), 🐞 Troba l'error (amb errates reals del temari),
🖨️ Què imprimeix? i ↕️ Ordena el codi (s'accepta qualsevol ordre que funcioni: s'executa al sandbox). Es poden filtrar per tema.

**Repàs espaciat (sistema Leitner):** cada pregunta que falles (de les lliçons o dels reptes) entra a la caixa 1 i torna a sortir
l'endemà. Cada encert al repàs la fa pujar de caixa i s'espera més: 1, 3, 7, 14 i 30 dies. Si la falles, torna a la caixa 1.
Quan l'encertes a la caixa 5, surt del repàs. Inici avisa quan tens preguntes per repassar.

Els reptes donen com a molt 80 XP al dia (el repàs no té límit). Les preguntes són a `content/reptes/temaN.php` i
`eines/verifica-contingut.php` les comprova totes.

## 7. Properes fases

- **Fase 4:** mapa de temes, insígnies i estadístiques.
- **Fase 5:** laboratoris web (formularis `$_GET`/`$_POST`, pujada de fitxers, sessions, cookies i MySQL) executats amb `php-cgi.exe`.
- Més exercicis d'Exercicis 01 com a laboratoris.
