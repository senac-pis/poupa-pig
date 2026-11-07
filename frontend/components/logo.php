<?php
// $logoSize = 'small' antes do include deixa a logo no tamanho da sidebar
$isSmallLogo = ($logoSize ?? 'large') === 'small';
$logoTextClass = $isSmallLogo ? 'text-[22px]' : 'text-3xl sm:text-[44px]';
$logoMascotClass = $isSmallLogo ? 'size-[64px]' : 'size-20 sm:size-[120px]';

unset($logoSize);
?>
<div class="flex items-center justify-center">
    <span class="<?= $logoTextClass ?> font-bold">POUPA</span>
    <div class="relative <?= $logoMascotClass ?> shrink-0 overflow-hidden">
        <img src="../assets/logos/logo.png" alt="Mascote Poupa Pig" class="absolute left-[-17.99%] top-[-12.36%] h-[150.43%] w-[140.51%] max-w-none">
    </div>
    <span class="<?= $logoTextClass ?> font-bold text-secondary">PIG</span>
</div>
