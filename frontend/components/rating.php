<?php
/** @var array $rating */
// Antes do include: $rating = ['value' => 3.6, 'size' => 'size-4'] (size é a classe de cada pillar)
// Cada pillar é pintado pela porcentagem da nota: 3.6 = 3 cheios, o quarto 60% e o quinto vazio
$ratingValue = min(max((float) $rating['value'], 0), 5);
$ratingSize = $rating['size'] ?? 'size-4';
$ratingLabel = number_format($ratingValue, 1, ',', '') . ' de 5 pillars';
?>
<span role="img" aria-label="<?= $ratingLabel ?>" class="inline-flex shrink-0 gap-0.5">
    <?php for ($pillar = 1; $pillar <= 5; $pillar++): ?>
        <?php $pillarFill = round(min(max($ratingValue - $pillar + 1, 0), 1) * 100); ?>
        <span class="relative shrink-0 <?= $ratingSize ?>">
            <img src="../assets/icons/pillar-empty.svg" alt="" class="absolute inset-0 size-full">
            <?php if ($pillarFill > 0): ?>
                <!-- A moeda cheia fica por cima da vazia, cortada na porcentagem da nota -->
                <img src="../assets/icons/pillar.svg" alt="" class="absolute inset-0 size-full"
                    style="clip-path: inset(0 <?= 100 - $pillarFill ?>% 0 0)">
            <?php endif; ?>
        </span>
    <?php endfor; ?>
</span>
