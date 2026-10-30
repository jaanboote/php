---
name: seguretat-sandbox
description: Checklist de seguretat per a qualsevol canvi a src/Sandbox.php, src/WebLab.php, web.php, src/Uploader.php, src/Auth.php o src/bootstrap.php. Usa-la sempre que es toquin aquests fitxers o l'execució del codi de l'alumne.
---

# Seguretat del sandbox i de l'accés

PHPLab executa codi que escriu l'alumne al PC on corre la app. Aquests fitxers són la frontera entre aquest codi i la resta
del sistema. Abans de fer commit de qualsevol canvi en un d'ells, **revisa tota la checklist de l'apartat que toques**, no
només la línia que has canviat. Si un punt deixa de complir-se, atura't i explica-ho abans de continuar.

| Fitxer | Apartats |
|---|---|
| `src/Sandbox.php` | 1, 2, 3 |
| `src/WebLab.php`, `web.php` | 3, 4 |
| `src/Uploader.php` | 5 |
| `src/Auth.php`, `src/bootstrap.php`, `.htaccess` | 6 |

## 1. Sandbox de consola (`Sandbox::run`, `Sandbox::spawn`, `Sandbox::iniSettings`)

- [ ] El codi s'executa en un **procés separat** amb `proc_open()` passant l'ordre com a array i `'bypass_shell' => true`
      (mai una cadena que passi per la shell).
- [ ] **Timeout de paret**: el bucle de `spawn()` mata el procés (`proc_terminate`) quan passa `sandbox.timeout`, encara que
      el procés estigui bloquejat esperant (no n'hi ha prou amb `max_execution_time`: a Linux no compta el temps d'espera).
- [ ] Límit de sortida (`sandbox.max_output`): s'atura si en surt massa.
- [ ] `memory_limit` aplicat amb `-d`.
- [ ] `open_basedir` limitat a la carpeta del job (`$jobDir` acabat en separador), i res més.
- [ ] `DISABLED_FUNCTIONS` completa: execució d'ordres, `proc_*`, `pcntl_*`, `posix_*`, sockets, `curl_*`, `ftp_*`, `mail`, DNS,
      `ini_set`/`ini_alter`/`ini_restore`, `set_time_limit`, `putenv`, `dl`, `symlink`/`link`, `chown`/`chgrp`...
      **No en treguis cap sense justificar-ho** al missatge de commit; si n'afegeixes, comprova que cap laboratori la necessiti.
- [ ] `allow_url_fopen` i `allow_url_include` a `0`; `enable_dl` a `0`.
- [ ] **Entorn net**: el procés només rep `TEMP`, `TMP`, `TMPDIR`, `PATH` (la carpeta del binari) i, a Windows, `SystemRoot`.
      Als laboratoris web, a més, només les variables CGI de la petició (`REQUEST_METHOD`, `QUERY_STRING`, `HTTP_COOKIE`...).
      Mai l'entorn d'Apache.
- [ ] Com a molt `sandbox.max_parallel` execucions alhora, amb `flock` (`acquireSlot`), i el bloqueig s'allibera al `finally`.
- [ ] Les **rutes internes s'amaguen** de stdout, stderr i del cos de la resposta (`str_replace` de `$jobDir`).
- [ ] Les carpetes del job (`$jobDir` i `$ioDir`) s'esborren al `finally`, també quan hi ha timeout o error.
- [ ] `$ioDir` (stdin, stdout, stderr) és **fora** de l'`open_basedir`: el codi de l'alumne no hi pot escriure.

## 2. Carpeta del job i noms de fitxer

- [ ] **La carpeta del job es crea amb `createJobDirs()`**, que comprova `mkdir()` i `realpath()` i que la ruta real sigui una
      carpeta dins de `sandbox/jobs/`. Si `realpath()` retornés `false`, `open_basedir` quedaria buit o apuntaria a l'arrel, i el codi
      de l'alumne tindria accés a tota la unitat. Si falla, `run()` i `runWeb()` alliberen el bloqueig i retornen un error sense executar res.
      No creïs mai la carpeta del job d'una altra manera.
- [ ] Noms de fitxer que venen de l'alumne o del contingut: només **rutes relatives segures** (`safeRelativePath`):
      sense `..`, sense ruta absoluta (`/` inicial) i sense lletra d'unitat (`C:`).
- [ ] El nom del programa passa per `safeScriptName()` (només `[a-zA-Z0-9_-]` i `.php`).
- [ ] `copyDir()` no segueix enllaços simbòlics.
- [ ] Les carpetes que es conserven entre execucions (`workspaces/`) tenen límit de mida (1 MB de consola, 8 MB web).

## 3. FFI desactivat

- [ ] `Sandbox::iniSettings()` té `'ffi.enable' => '0'`. FFI permet cridar funcions de C des de PHP i se saltaria
      `disable_functions` (abans del canvi, `FFI::cdef("int getpid(void);")->getpid()` funcionava). No depenem del `php.ini`:
      no el treguis. Comprovació: aquest codi ha de donar «FFI API is restricted by "ffi.enable"».

## 4. Laboratoris web (`src/WebLab.php`, `web.php`, `templates/lab.php`)

- [ ] L'`<iframe id="browser">` porta `sandbox="allow-forms allow-scripts allow-modals"`. **Mai `allow-same-origin`** ni
      `allow-top-navigation`: amb `allow-same-origin` + `allow-scripts`, el JavaScript de l'alumne podria llegir les galetes de PHPLab.
- [ ] `web.php` envia `Content-Security-Policy: sandbox allow-forms allow-scripts allow-modals; frame-ancestors 'self'`,
      també sense `allow-same-origin`. Així, encara que algú obri la URL directament, la pàgina queda aïllada.
- [ ] `web.php` defineix `PHPLAB_NO_SESSION` abans del `bootstrap.php`: no obre la sessió de la app.
- [ ] Testimoni de la URL: HMAC-SHA256 amb `secret.key` (a la carpeta de dades, fora del webroot), amb **caducitat**,
      comparat amb `hash_equals()`. Sense testimoni vàlid, 403.
- [ ] Les **redireccions absolutes** (`Location: /index.php`) es reescriuen perquè es quedin dins de `web.php/<testimoni>/<lab>/`.
      A les proves (`WebLab::runTest`), una `Location` amb domini es tracta com una ruta del mateix laboratori.
- [ ] De les capçaleres que genera el PHP de l'alumne, només es reenvien `Status`, `Content-Type` i `Location`.
      Mai `Set-Cookie` ni capçaleres de seguretat que puguin treure la CSP.
- [ ] Galetes i sessions de l'alumne **separades** de les de la app: el pot de galetes és a `sandbox/web/<lab>.cookies.json`,
      les sessions a `.sessions/` dins de la carpeta del laboratori, i no s'envien mai al navegador real.
- [ ] Els fitxers estàtics es serveixen només des de les fonts de l'alumne o de la seva carpeta; mai fitxers que comencin
      per `.` ni els interns (`_phplab_errors.log`).
- [ ] `php-cgi` s'executa amb els mateixos límits que el CLI (`iniSettings()` + els propis del web) i amb `REDIRECT_STATUS`.

## 5. Pujades del temari (`src/Uploader.php`)

- [ ] **Llista blanca** d'extensions (`TextExtractor::EXTENSIONS` i `zip`). Res de llistes negres.
- [ ] Protecció **zip slip**: cada entrada passa per `safeZipPath()` (sense `..`, sense rutes absolutes ni lletra d'unitat).
- [ ] Protecció **zip bomb**: límit d'entrades (`upload.max_zip_entries`) i de mida descomprimida (`upload.max_unzipped_mb`),
      **comptant els bytes reals** que es llegeixen del flux, no la mida que declara el ZIP.
- [ ] Com a molt un nivell de ZIP dins de ZIP (`$depth`).
- [ ] Els fitxers es desen a la carpeta de dades (`data_dir`), **fora del webroot**: Apache no els pot servir mai.

## 6. Accés i sessió (`src/Auth.php`, `src/bootstrap.php`, `.htaccess`)

- [ ] Contrasenyes amb `password_hash()` / `password_verify()` (i `password_needs_rehash()`); mai en clar ni amb md5/sha1.
- [ ] **Bloqueig per intents**: `auth.max_attempts` intents fallits en `auth.lockout_minutes` minuts, per IP.
- [ ] `password_verify()` es fa encara que l'usuari no existeixi (temps de resposta igual).
- [ ] `session_regenerate_id(true)` en iniciar la sessió (fixació de sessió) i en caducar per inactivitat.
- [ ] Galeta de sessió `HttpOnly` i `SameSite=Strict`, i `session.use_only_cookies`.
- [ ] CSRF: `Auth::checkCsrf()` compara amb `hash_equals()`. Tot formulari POST porta el camp `csrf` i `api.php` comprova
      la capçalera `X-CSRF-Token` abans de qualsevol acció.
- [ ] Control d'IP a **dos llocs**: `.htaccess` (`Require local` + la xarxa local) i `bootstrap.php` (`Auth::ipAllowed()`).
      Si en canvies un, canvia l'altre. L'única excepció és el mateix PC entrant per la seva IP de xarxa (`$mateixPc`).
- [ ] `.htaccess` bloqueja `config.php`, els fitxers que comencen per `.` i les carpetes internes
      (`src`, `templates`, `content`, `vendor`, `eines`, `docs`).
- [ ] `display_errors` a `0` a la app: els errors van al log, no al navegador.
- [ ] El primer usuari només es pot crear des del mateix PC.

## 7. Verificació després de qualsevol canvi

1. Executa l'eina de contingut i comprova que surt **«Tot correcte.»**:

   ```
   php eines/verifica-contingut.php
   ```

2. Prova a mà, en un exemple «Prova-ho» o en un laboratori. Les tres coses **han de fallar**:

   | Prova | Codi | Resultat esperat |
   |---|---|---|
   | Bucle infinit | `<?php while (true) {}` | S'atura al cap de `sandbox.timeout` segons amb el missatge de temps esgotat. |
   | Fitxer fora de la carpeta | `<?php echo file_get_contents('../../config.php');` | Warning d'`open_basedir restriction`; no surt cap contingut. |
   | Executar ordres | `<?php echo exec('whoami');` | `Fatal error: Call to undefined function exec()` (a PHP 8, les funcions de `disable_functions` no existeixen). |

3. Si has tocat els laboratoris web: obre'n un, envia un formulari, puja una imatge i comprova que l'inspector
   funciona i que, des de la consola del navegador, `document.cookie` dins de l'iframe dona error d'origen aïllat (no pot llegir `PHPLABSESS`).
