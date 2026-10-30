<?php
/**
 * La mascota: un elefant de PHP dibuixat en SVG. El seu humor depèn de la ratxa de dies:
 *   0 dies → dorm · 1-2 → content · 3 o més → eufòric (amb espurnes).
 * També 'confos' (amb interrogants), per a les pàgines que no existeixen.
 * Opcionals: $humorForcat i $fraseForcada (per celebrar alguna cosa) i $mascotaClasse (on es col·loca).
 * @var int $ratxa
 */
$humor = $humorForcat ?? ($ratxa <= 0 ? 'dorm' : ($ratxa < 3 ? 'content' : 'eufòric'));
$frase = $fraseForcada ?? match ($humor) {
    'dorm' => 'Zzz... Fem una pregunta i em despertes?',
    'confos' => 'Mmm... aquí no hi ha res.',
    'content' => 'Som-hi! Una lliçó més i fem ratxa.',
    default => "$ratxa dies seguits! Imparable!",
};
?>
<figure class="mascota <?= e($mascotaClasse ?? '') ?> humor-<?= e($humor === 'eufòric' ? 'euforic' : $humor) ?>" aria-label="<?= e($frase) ?>">
  <figcaption class="mascota-bafarada"><?= e($frase) ?></figcaption>
  <svg viewBox="0 0 170 150" aria-hidden="true">
    <ellipse class="m-ombra" cx="96" cy="141" rx="58" ry="6"/>
    <g class="m-cos">
      <path class="m-cua" d="M150 92q12 2 10 16"/>
      <rect class="m-pota fosca" x="112" y="108" width="17" height="30" rx="7"/>
      <rect class="m-pota fosca" x="132" y="104" width="17" height="32" rx="7"/>
      <ellipse class="m-tronc" cx="104" cy="96" rx="52" ry="35"/>
      <rect class="m-pota" x="66" y="110" width="18" height="29" rx="7"/>
      <rect class="m-pota" x="88" y="112" width="18" height="27" rx="7"/>
      <text class="m-php" x="122" y="100" text-anchor="middle">php</text>
      <g class="m-cap">
        <ellipse class="m-orella" cx="90" cy="60" rx="24" ry="29"/>
        <ellipse class="m-orella-dins" cx="93" cy="61" rx="14" ry="19"/>
        <circle class="m-cara" cx="60" cy="62" r="31"/>
        <path class="m-trompa" d="M38 76c-14 8-18 24-9 34 5 5 11 3 12-2"/>
        <path class="m-ullal" d="M48 84q-5 7 0 13"/>
        <circle class="m-galta" cx="66" cy="76" r="5.5"/>
        <g class="m-ull">
          <?php if ($humor === 'dorm'): ?>
            <path d="M45 57q6 5 12 0" class="m-ull-tancat"/>
          <?php else: ?>
            <circle cx="51" cy="56" r="5.5" class="m-pupila"/>
            <circle cx="53" cy="54" r="1.8" class="m-brillantor"/>
          <?php endif; ?>
        </g>
      </g>
    </g>
    <?php if ($humor === 'dorm'): ?>
      <g class="m-zzz"><text x="22" y="40">z</text><text x="10" y="24">Z</text></g>
    <?php elseif ($humor === 'confos'): ?>
      <g class="m-preguntes"><text x="16" y="44">?</text><text x="34" y="26">?</text></g>
    <?php elseif ($humor === 'eufòric'): ?>
      <g class="m-espurnes">
        <path d="M20 30l3 7 7 3-7 3-3 7-3-7-7-3 7-3z"/>
        <path d="M150 20l2 5 5 2-5 2-2 5-2-5-5-2 5-2z"/>
        <path d="M30 108l2 4 4 2-4 2-2 4-2-4-4-2 4-2z"/>
      </g>
    <?php endif; ?>
  </svg>
</figure>
