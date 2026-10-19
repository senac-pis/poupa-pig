<?php
// $logoSize = 'small' antes do include deixa a logo no tamanho da sidebar
$isSmallLogo = ($logoSize ?? 'large') === 'small';
$logoTextClass = $isSmallLogo ? 'text-[28px]' : 'text-4xl sm:text-[64px]';
$logoMascotClass = $isSmallLogo ? 'size-[90px]' : 'size-24 sm:size-[173px]';

unset($logoSize);
?>
<div class="flex items-center justify-center">
    <span class="<?= $logoTextClass ?> font-bold">POUPA</span>
    <div class="relative <?= $logoMascotClass ?> shrink-0 overflow-hidden">
        <img src="../assets/logos/logo.png" alt="Mascote Poupa Pig" class="absolute left-[-17.99%] top-[-12.36%] h-[150.43%] w-[140.51%] max-w-none">
    </div>
    <span class="<?= $logoTextClass ?> font-bold text-secondary">PIG</span>
</div>
