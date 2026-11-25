<?php
/** @var array $row vem do foreach do table.php */
// Uma linha da tabela de Reviews; o table.php inclui este arquivo para cada $row
// $row vem do backend com as contagens e a média já calculadas:
// ['id', 'name', 'user_count', 'review_count', 'average']
// Ainda não tem foto de usuário: mostra até 3 avatares com o ícone e o resto vira "+N"
$avatarColors = [
    ['circle' => 'bg-pink-100', 'icon' => 'bg-pink-300', 'layer' => 'z-30'],
    ['circle' => 'bg-sky-100', 'icon' => 'bg-sky-300', 'layer' => 'z-20'],
    ['circle' => 'bg-green-100', 'icon' => 'bg-green-300', 'layer' => 'z-10'],
];
$rowUserCount = max((int) $row['user_count'], 0);
$rowAvatars = array_slice($avatarColors, 0, $rowUserCount);
$rowOtherUsers = $rowUserCount - count($rowAvatars);
$avatarMask = 'url(../assets/icons/person.svg) center / contain no-repeat';
$rowUrl = htmlspecialchars('product-reviews.php?' . http_build_query(['id' => (int) $row['id']]));
$rating = ['value' => $row['average'], 'size' => 'size-4'];
?>
<tr>
    <td>
        <!-- Nome longo corta com "..." e mostra o nome inteiro ao passar o mouse, como em Itens -->
        <a href="<?= $rowUrl ?>" title="<?= htmlspecialchars($row['name']) ?>"
            class="block max-w-60 truncate rounded font-light hover:text-secondary hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary">
            <?= htmlspecialchars($row['name']) ?>
        </a>
    </td>
    <td>
        <span class="sr-only"><?= $rowUserCount === 1 ? '1 usuário' : "$rowUserCount usuários" ?></span>
        <!-- Como no Figma, o primeiro avatar fica por cima e cada um cobre o começo do seguinte; o "+N" fica embaixo de todos -->
        <div aria-hidden="true" class="isolate flex items-center">
            <?php foreach ($rowAvatars as $avatarColor): ?>
                <span class="relative -ml-2.5 flex size-8 shrink-0 items-center justify-center rounded-full first:ml-0 <?= $avatarColor['circle'] ?> <?= $avatarColor['layer'] ?>">
                    <span class="size-4 <?= $avatarColor['icon'] ?>" style="mask: <?= $avatarMask ?>; -webkit-mask: <?= $avatarMask ?>;"></span>
                </span>
            <?php endforeach; ?>
            <?php if ($rowOtherUsers > 0): ?>
                <span class="-ml-4 flex h-8 items-center rounded-full bg-gray-100 pl-4 pr-2.5 text-sm font-extralight">+<?= $rowOtherUsers ?></span>
            <?php endif; ?>
        </div>
    </td>
    <td><?= (int) $row['review_count'] ?></td>
    <td><?php include __DIR__ . '/../rating.php'; ?></td>
</tr>
