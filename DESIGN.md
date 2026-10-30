# PHPLab · Sistema de disseny

**Direcció: «laboratori de nit».** Una eina de desenvolupament seriosa i tranquil·la, com un bon editor de codi a les
fosques: fons negre profund, superfícies que es distingeixen per la llum de la vora i no per colors, un sol color propi
(el lila de l'elefant de PHP) per a les accions, i el groc reservat a les recompenses. La personalitat surt del terminal
(lletra monoespai, finestres amb els tres punts, cursor que parpelleja) i de la mascota, no de degradats ni efectes.

> Aquesta direcció parteix del que ja tenia el projecte (tipografies, lila, colors dels temes) i de les pautes de les
> eines per a desenvolupadors en mode fosc. No s'ha comparat amb webs concretes.

Els valors són a `assets/css/app.css` com a variables (`:root`); les plantilles i el CSS fan servir els noms, mai valors
directes. El mode fosc és el de per defecte; el clar s'activa amb el botó de la barra superior i es recorda al navegador.

## 1. Color

Cada color té **un sol rol**. Contrast de text ≥ 4,5:1 (comprovat sobre `surface`).

| Token | Fosc | Clar | Rol |
|---|---|---|---|
| `--paper` | `#000000` | `#f6f6f9` | Fons de la pàgina |
| `--surface` | `#0b0b10` | `#ffffff` | Targetes, panells, barra superior |
| `--surface-2` | `#15151d` | `#f0f0f5` | Superfícies dins de targetes, hover de files, codi en línia |
| `--sunken` | `#050507` | `#e9e9f1` | Camps enfonsats (barres de progrés, respostes) |
| `--text` | `#ededf3` | `#15161f` | Text principal i títols |
| `--muted` | `#8d8ea4` | `#5d5f73` | Text secundari, metadades, etiquetes |
| `--border` | `#1d1d27` | `#e3e3ec` | Vores de targetes i separadors |
| `--border-strong` | `#2c2c3a` | `#cfd0dc` | Vores de camps i botons secundaris |
| `--accent` | `#8f93f5` | `#5b5fc7` | **Primari**: botons principals, enllaços actius, focus, progrés |
| `--accent-2` | `#b4b7ff` | `#4347b0` | Text en color primari (enllaços, etiquetes actives) |
| `--accent-soft` | `#191a38` | `#ecedfc` | Fons de l'element actiu (menú, pastilles) |
| `--spark` | `#ffcf4d` | `#d99a0b` | **Accent únic**: XP, ratxa, rècords. Mai per a accions |
| `--ok` / `--warn` / `--err` | `#3fd39a` / `#f0b24a` / `#ff6b7a` | `#0a7d57` / `#b26a00` / `#d93848` | Correcte, avís, error (sempre amb icona, mai només color) |
| `--t1` … `--t5` | blau, lila, verd, taronja, rosa | versions més fosques | Identifiquen cada tema (vora superior, icona). Mai per a text llarg |
| `--code-bg` | `#040406` | `#14152a` | Fons de l'editor i de la terminal (fosc també en mode clar) |

## 2. Tipografia

Tres famílies, locals a `assets/vendor/fonts`:

- **Bricolage Grotesque** (`--display`): títols i xifres grans. Pes 600–800, interlletratge negatiu.
- **Lexend** (`--sans`): tot el text. Pes 350 el cos, 500–600 les etiquetes i els botons.
- **JetBrains Mono** (`--mono`): codi, terminals, rutes i ordres. Sense lligadures (`=>` s'ha de veure tal com s'escriu).

Escala (8 mides, base 16 px):

| Token | Mida | Interlínia | Ús |
|---|---|---|---|
| `--fs-xs` | 12 px | 1.4 | Llegendes, comptadors petits |
| `--fs-sm` | 13 px | 1.45 | Metadades, pastilles, peu |
| `--fs-md` | 14 px | 1.5 | Text secundari, botons petits, codi |
| `--fs-base` | 16 px | 1.65 | Cos de text |
| `--fs-lg` | 18 px | 1.5 | Títols de targeta (h3), entradetes |
| `--fs-xl` | 22 px | 1.3 | Títols de secció (h2) |
| `--fs-2xl` | 28 px | 1.2 | Títols destacats |
| `--fs-3xl` | 28–40 px (fluid) | 1.1 | Títol de pàgina (h1) |

Línies de text de 60–75 caràcters (`max-width: 68ch` als paràgrafs llargs). Números que canvien: `tabular-nums`.

## 3. Espai

Escala de 4 px: `--sp-1` 4 · `--sp-2` 8 · `--sp-3` 12 · `--sp-4` 16 · `--sp-5` 20 · `--sp-6` 24 · `--sp-8` 32 · `--sp-12` 48 · `--sp-16` 64.

- Dins d'una targeta: 20–24 px. Entre targetes: 16–20 px. Entre seccions: 48 px.
- Amplada màxima del contingut: 1440 px; text llarg (lliçons): 860 px. Marge lateral: 24 px (16 px al mòbil).

## 4. Forma i profunditat

- Radis: `--r-xs` 4 px (codi en línia) · `--r-sm` 8 px (botons petits, camps) · `--r-md` 12 px (botons, blocs interns) ·
  `--r-lg` 18 px (targetes) · `--r-full` (pastilles). Els elements niats segueixen *radi exterior = interior + marge*.
- Ombres, només dues: `--shadow` (repòs) i `--shadow-lift` (hover o elevat). En fosc, la profunditat ve sobretot d'una
  **llum fina a la vora superior** (`inset 0 1px 0`), no d'ombres grans.
- Cap glassmorphism excepte la barra superior (desenfocament lleuger perquè el contingut no hi xoqui).

## 5. Components

- **Botó primari**: fons `--accent`, text `--on-accent`, relleu de tecla (`0 3px 0 --accent-deep`) que s'enfonsa en prémer.
  Un per pantalla com a molt.
- **Botó secundari**: fons `--surface`, vora `--border-strong`; en hover, vora `--accent`.
- **Botó discret** (`.btn-link`): sense vora; en hover, fons `--surface-2`.
- Tots: focus visible amb anell de 3 px `--accent`, desactivat amb opacitat 0,5, alçada mínima 40 px en pantalles tàctils.
- **Enllaços**: `--accent-2`, subratllat fi separat del text.
- **Camps**: fons `--surface`, vora `--border-strong`, focus amb anell `--accent-soft` i vora `--accent`.
- **Targetes**: `--surface`, vora `--border`, `--r-lg`, `--shadow` + llum a la vora. Les d'un tema porten la vora superior del color del tema.
- **Pastilles** (`.pill`): `--fs-sm`, `--r-full`; neutres, `ok`, `doing` (lila) i `err`.
- **Capçalera de pàgina** (`.pagina-cap`): h1 + entradeta en `--muted` (`--fs-lg`, 72 caràcters com a molt), alineades a l'esquerra. Sense etiquetes en majúscules espaiades a sobre.
- **Navegació**: barra superior fixa amb el menú i el jugador (nivell, XP, ratxa); la pàgina activa porta la marca lila que llisca.
- **Peu**: una línia discreta amb el nom del mòdul i enllaços a Temari i Diagnòstic.

## 6. Imatge i icones

- Icones: el joc SVG propi (`icones()` a `src/helpers.php`), traç de 1,9 px, hereten el color del text. Res d'emojis a la
  interfície (només a les insígnies, on fan de medalla).
- Il·lustracions: la mascota (elefant de PHP) i les il·lustracions SVG dels modes de repte. Cap foto d'estoc.
- Captures i codi sempre dins de finestres de terminal (`.term-window`) o de l'editor.

## 7. Moviment

- Corbes: `--ease-out-quint` per entrar i sortir; `--ease-in-out-cubic` per al que es mou dins de la pantalla; `ease` per
  als canvis de color. Rebot (`--ease-spring`) només en celebracions.
- Durades: microinteraccions 100–160 ms; panells i desplegables 200–260 ms; entrades d'elements 300–450 ms.
- Només `transform` i `opacity`. Tot es desactiva amb `prefers-reduced-motion`.
