# Guia d'instal·lació de PHPLab

> Aquesta guia és per a qualsevol persona, encara que no hagi instal·lat mai res d'aquest tipus.
> Si vols saber com funciona PHPLab per dins (codi, seguretat...), mira [PHPLab per dins](TECNIC.md).

## Abans de començar

**Què tindràs al final:** PHPLab funcionant al teu ordinador. L'obriràs amb el navegador (Chrome, Edge, Firefox...), com
qualsevol web, i també el podràs obrir des del mòbil o d'un altre ordinador de casa teva.

**Què necessites:**

- Un ordinador amb **Windows 10 o 11**.
- **Internet**, només mentre ho instal·les. Després PHPLab funciona sense connexió.
- Uns **30 minuts**.

**Tres paraules que sortiran:**

| Paraula | Què vol dir |
|---|---|
| **XAMPP** | Un programa gratuït que converteix el teu ordinador en un petit servidor web. PHPLab funciona a dins seu. |
| **Carpeta** | Una carpeta normal de Windows, com «Documents». Les obriràs amb l'Explorador de fitxers (la icona de la carpeta groga). |
| **Terminal** | Una finestra negra on s'escriuen ordres. Només l'hauràs de fer servir per copiar i enganxar dues o tres ordres que et donem. |

> **Com obrir el terminal:** prem la tecla **Windows** del teclat, escriu `cmd` i prem **Retorn**. S'obrirà una finestra negra.
> Per enganxar-hi una ordre que has copiat, fes **clic amb el botó dret** a dins de la finestra.

---

## Pas 1 · Instal·la XAMPP

1. Entra a <https://www.apachefriends.org> i descarrega **XAMPP per a Windows**, la versió amb **PHP 8.2**.
2. Obre el fitxer que has descarregat i ves prement **Next** (Següent). No cal canviar res: deixa que s'instal·li a
   `C:\xampp`, que és la carpeta que dona per defecte.
   - Si surt un avís sobre el «Control de comptes d'usuari» (UAC), prem **OK**: no afecta PHPLab.
   - Si Windows pregunta si vols permetre l'accés a la xarxa a «Apache HTTP Server», marca **Xarxes privades** i prem **Permet l'accés**.
3. En acabar, obre el **XAMPP Control Panel** (el trobaràs al menú Inici escrivint «XAMPP»).
4. A la fila **Apache**, prem **Start**. El nom «Apache» es posarà en **verd**.
5. Comprova-ho: obre el navegador i escriu a la barra d'adreces `localhost`. Ha de sortir la pàgina de benvinguda de XAMPP.

> **Apache no es posa en verd?** Normalment és perquè un altre programa (com Skype o un altre servidor) ja fa servir el
> mateix «port». Tanca aquest programa i torna a prémer Start.

## Pas 2 · Activa una opció de PHP (l'extensió «zip»)

PHPLab la necessita per llegir els fitxers ZIP i els documents de Word del temari.

1. Al **XAMPP Control Panel**, a la fila **Apache**, prem **Config** i tria **PHP (php.ini)**. S'obrirà un fitxer de text al Bloc de notes.
2. Prem **Ctrl + F** (cercar) i escriu `;extension=zip`. Prem **Retorn**.
3. Esborra **només el punt i coma** del davant, perquè quedi així: `extension=zip`
4. Desa el fitxer (menú **Fitxer** → **Desa**) i tanca el Bloc de notes.
5. Al **XAMPP Control Panel**, prem **Stop** a la fila Apache i, quan s'aturi, **Start** una altra vegada.

## Pas 3 · Descarrega PHPLab

1. Entra a <https://github.com/jaanboote/php>.
2. Prem el botó verd **Code** i després **Download ZIP**.
3. Ves a la carpeta **Baixades**, fes clic amb el botó dret al fitxer `php-main.zip` i tria **Extreu-ho tot** → **Extreu**.
4. Dins de la carpeta que s'ha creat hi ha una altra carpeta que també es diu `php-main`. Canvia-li el nom a **`phplab`**
   (botó dret → **Canvia el nom**).
5. Mou aquesta carpeta `phplab` a `C:\xampp\htdocs`. Per arribar-hi: obre l'Explorador de fitxers, fes clic a
   **Aquest ordinador**, després a **Disc local (C:)**, després a **xampp** i després a **htdocs**.

Al final has de tenir la carpeta `C:\xampp\htdocs\phplab`, i a dins hi has de veure fitxers com `index.php` i `README.md`.

> **Si saps fer servir Git**, en lloc dels passos anteriors pots fer `git clone https://github.com/jaanboote/php C:\xampp\htdocs\phplab`.
> Així, per actualitzar, només caldrà `git pull`.

## Pas 4 · Crea el fitxer de configuració

PHPLab necessita un fitxer anomenat `config.php`. El crearàs copiant la plantilla que ja porta.

1. Obre el **terminal** (tecla Windows, escriu `cmd`, Retorn).
2. Copia aquesta ordre, enganxa-la al terminal (clic amb el botó dret) i prem **Retorn**:
   ```bat
   copy C:\xampp\htdocs\phplab\config.example.php C:\xampp\htdocs\phplab\config.php
   ```
3. Ha de respondre `1 arxius copiats` (o `1 file(s) copied`). Ja pots tancar el terminal.

No cal canviar res d'aquest fitxer per començar. Només si vols entrar des d'altres aparells (pas 7) o fer servir el tutor amb IA (pas 8).

## Pas 5 · Entra per primera vegada

1. Al navegador, ves a <http://localhost/phplab>.
2. Sortirà una pantalla per **crear el teu usuari**. Tria un nom i una contrasenya d'**almenys 10 caràcters**, i apunta-la en un lloc segur.
   Aquesta pantalla només surt la primera vegada.
3. Ja ets a dins. Al menú de dalt, ves a **Diagnòstic**: totes les caselles han d'estar en **verd**.
   - La casella «Clau de l'API de Claude» pot quedar en gris: és opcional (pas 8).
   - «Dependències de Composer» en vermell vol dir que no llegirà els PDF del temari. Per arreglar-ho, mira el pas 6.
   - Qualsevol altra en vermell porta escrit a sota què has de fer.

**Ja pots fer servir PHPLab.** Els passos que queden són opcionals.

## Pas 6 (opcional) · Perquè llegeixi els PDF: Composer

Sense aquest pas PHPLab funciona igual, però no podrà llegir els PDF que pugis a **Temari** ni connectar-se amb el tutor amb IA.

1. Descarrega **Composer-Setup.exe** des de <https://getcomposer.org/download/> i obre'l.
2. Quan pregunti on és PHP, tria `C:\xampp\php\php.exe` i ves prement **Next** fins al final.
3. Obre un terminal **nou** (si en tenies un d'obert, tanca'l primer) i enganxa aquestes dues ordres, una darrere l'altra:
   ```bat
   cd C:\xampp\htdocs\phplab
   composer install
   ```
4. Quan acabi (pot trigar un minut), torna a **Diagnòstic**: «Dependències de Composer» ha de sortir en verd.

## Pas 7 (opcional) · Entrar des del mòbil o d'un altre ordinador

PHPLab només deixa entrar aparells de **la teva xarxa de casa** (la mateixa Wi-Fi). Des de fora de casa no s'hi pot entrar,
i és millor així.

**1. Busca l'adreça del teu ordinador.** Obre el terminal, escriu `ipconfig` i prem Retorn. Busca la línia
**Adreça IPv4** (o **IPv4 Address**) dins de l'apartat de la **Wi-Fi** o de l'**Ethernet**. Serà semblant a `192.168.1.72`.

> No facis cas de l'adreça `192.168.56.1`: és de VirtualBox, no de la teva xarxa.

**2. Obre-la des de l'altre aparell.** Connectat a la mateixa Wi-Fi, escriu al navegador `http://` seguit de l'adreça i `/phplab`.
Per exemple: `http://192.168.1.72/phplab`. Fes servir **la teva** adreça, la que t'ha sortit al punt anterior.

**3. Si diu «Forbidden» o «Accés denegat»:** la teva xarxa no comença per `192.168.1.`, que és la que PHPLab deixa entrar per defecte.
Mira els tres primers números de la teva adreça (per exemple, `192.168.0`) i canvia-ho a **dos llocs**:

- A `C:\xampp\htdocs\phplab\.htaccess` (obre'l amb el Bloc de notes), a la línia `Require ip 192.168.1.0/24`.
- A `C:\xampp\htdocs\phplab\config.php`, a la línia `'allowed_networks'`, on posa `'192.168.1.0/24'`.

En tots dos llocs, canvia només els tres primers números. Per exemple, `192.168.1.0/24` passa a ser `192.168.0.0/24`.
Desa els dos fitxers.

**4. Si no carrega gens (es queda pensant):** és el tallafoc de Windows. Ves a **Configuració** → **Privadesa i seguretat** →
**Seguretat del Windows** → **Tallafoc i protecció de xarxa** → **Permet una aplicació a través del tallafoc**. Busca
**Apache HTTP Server** i marca la casella **Privada**. Comprova també que la teva Wi-Fi està configurada com a xarxa **privada**
(Configuració → Xarxa i Internet → la teva Wi-Fi → Tipus de perfil de xarxa: **Privada**).

**5. Si un dia deixa de funcionar:** potser el teu ordinador ha canviat d'adreça (passa quan canvies de Wi-Fi o reinicies el
router). Torna a fer el punt 1 i fes servir l'adreça nova.

## Pas 8 (opcional) · Tutor amb intel·ligència artificial

Sense aquest pas, el tutor dels laboratoris funciona en mode **sense connexió**: et fa preguntes i et dona pistes, però no
entén el que li escrius. Amb una clau de l'API de Claude, respon de veritat a les teves preguntes. **És de pagament**
(la teva compte fa una petita despesa per cada pregunta) i necessita el pas 6.

1. Crea un compte a <https://console.anthropic.com/> i afegeix-hi crèdit.
2. A l'apartat **API Keys**, crea una clau i copia-la. Comença per `sk-ant-`.
3. Obre `C:\xampp\htdocs\phplab\config.php` amb el Bloc de notes, busca `'api_key' => ''` i enganxa la clau entre les
   dues cometes: `'api_key' => 'sk-ant-...'`. Desa el fitxer.
4. A **Diagnòstic**, «Clau de l'API de Claude» ha de sortir en verd.

> No comparteixis mai aquesta clau ni la pugis a Internet: qui la tingui pot gastar el teu crèdit.

---

## Cada dia: com engegar PHPLab

1. Obre el **XAMPP Control Panel** i prem **Start** a la fila **Apache**.
2. Al navegador, ves a <http://localhost/phplab> (o a l'adreça del pas 7 des d'un altre aparell).

Quan acabis, pots prémer **Stop**. El teu progrés es desa sol.

## Afegir els apunts de classe (temari)

1. A PHPLab, ves a **Temari** → **Pujar temari** i tria els fitxers (PDF, Word, ZIP...). Pots triar-ne molts alhora o un ZIP sencer.
2. PHPLab els ordena per temes tot sol. Després els pots llegir des de la mateixa pàgina, i el tutor els fa servir per respondre't.

Si un PDF surt com a «sense text», és un document escanejat (una foto): PHPLab només pot llegir PDF amb text.

## Actualitzar PHPLab a una versió nova

El teu progrés **no es perd**: es desa en una altra carpeta (`C:\xampp\phplab-data`).

**Si el vas descarregar en ZIP (pas 3):**

1. Copia el teu `config.php` (de `C:\xampp\htdocs\phplab`) a l'Escriptori, perquè no es perdi.
2. Esborra la carpeta `C:\xampp\htdocs\phplab`.
3. Torna a fer el **pas 3** amb la versió nova.
4. Torna a posar el teu `config.php` dins de la carpeta `C:\xampp\htdocs\phplab`.
5. Si havies fet el pas 6, torna a fer-ne el punt 3 (`composer install`).
6. Si havies canviat el `.htaccess` (pas 7), torna a fer aquest canvi.

**Si el vas descarregar amb Git:** obre el terminal i enganxa:

```bat
cd C:\xampp\htdocs\phplab
git pull
```

Després, al navegador, prem **Ctrl + F5** per carregar la versió nova.

## He oblidat la contrasenya

Obre el terminal i enganxa aquestes dues ordres, una darrere l'altra. Et demanarà la contrasenya nova:

```bat
cd C:\xampp\htdocs\phplab
C:\xampp\php\php.exe eines\canvia-contrasenya.php
```

## Si alguna cosa no funciona

| Què veus | Què vol dir i què has de fer |
|---|---|
| «No es pot accedir a aquest lloc» a `localhost/phplab` | Apache no està engegat. Obre el XAMPP Control Panel i prem **Start** a la fila Apache. |
| «Not Found» | La carpeta no és al lloc correcte o té un altre nom. Ha de ser exactament `C:\xampp\htdocs\phplab` (pas 3). |
| «Falta config.php» | No has fet el pas 4. |
| «Forbidden» des del mòbil o des d'un altre ordinador | La teva xarxa no és `192.168.1.x`. Mira el pas 7, punt 3. |
| Des d'un altre aparell es queda carregant i no obre res | El tallafoc de Windows. Mira el pas 7, punt 4. |
| «cal activar extension=zip» | No has fet el pas 2, o no has reiniciat Apache (Stop i Start). |
| Els PDF del temari surten com a «pendent» | Fes el pas 6 i després, a **Temari**, prem **Actualitzar temari**. |
| No veig les animacions | Windows té els efectes d'animació desactivats. A **Diagnòstic** hi ha un botó per veure-les igualment. |
| El tutor diu «problema de certificats SSL» | A `config.php`, la línia `'ca_bundle'` ha de ser `C:\\xampp\\apache\\bin\\curl-ca-bundle.crt`. |
| La pàgina es veu antiga després d'actualitzar | Prem **Ctrl + F5** al navegador. |

Si res d'això ho arregla, PHPLab apunta els errors en aquest fitxer, que pots obrir amb el Bloc de notes:
`C:\xampp\apache\logs\error.log`. Els errors més recents són al final.

---

### Una nota de seguretat

PHPLab executa el codi que hi escrius al teu ordinador. Està protegit perquè aquest codi no pugui tocar res més, però
**no l'obris mai a Internet** (no el publiquis en un servidor ni obris ports al router). Està pensat per fer-lo servir a casa,
a la teva xarxa. Tots els detalls són a [PHPLab per dins](TECNIC.md#2-seguretat-què-fa-la-app-i-per-què).
