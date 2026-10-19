<?php
// Antes do include: $activePage = 'dashboard' | 'items' | 'categories' | 'review'
$menuItems = [
    'dashboard' => ['label' => 'Dashboard', 'href' => 'dashboard.php'],
    'items' => ['label' => 'Itens', 'href' => 'items.php'],
    'categories' => ['label' => 'Categorias', 'href' => 'categories.php'],
    'review' => ['label' => 'Review', 'href' => 'review.php'],
];
$activePage = $activePage ?? '';
$logoSize = 'small';
?>
<aside class="sticky top-0 flex h-screen w-[307px] shrink-0 flex-col items-center overflow-y-auto rounded-2xl border border-black-200 bg-background py-10">
    <div class="flex w-full flex-col items-center gap-2.5">
        <?php include __DIR__ . '/logo.php'; ?>
        <hr class="w-full border-black-200">
    </div>

    <nav aria-label="Menu principal" class="flex w-full flex-1 flex-col items-center justify-between py-5">
        <ul class="flex w-full flex-col gap-5 px-5">
            <?php foreach ($menuItems as $page => $item): ?>
                <?php $isActive = $page === $activePage; ?>
                <li>
                    <a href="<?= $item['href'] ?>" <?= $isActive ? 'aria-current="page"' : '' ?>
                        class="block rounded-2xl px-5 py-2.5 text-center text-[32px] font-bold transition focus:outline-none focus:ring-2 focus:ring-primary <?= $isActive ? 'bg-secondary text-white-100' : 'hover:bg-pink-100' ?>">
                        <?= $item['label'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="flex items-center gap-2.5 px-5">
            <div class="flex size-[65px] shrink-0 items-center justify-center rounded-full bg-gray-100">
                <img src="../assets/icons/person.svg" alt="" width="34" height="34" class="size-[34px]">
            </div>
            <div class="flex flex-col text-base">
                <span class="font-bold text-secondary">Usuário</span>
                <a href="settings.php" class="hover:underline">Configurações</a>
            </div>
        </div>
    </nav>
</aside>
