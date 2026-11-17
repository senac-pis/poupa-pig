<?php
/** @var array $review */
// Antes do include: $review = ['user_name' => 'Ana', 'user_review_count' => 3, 'rating' => 5,
//                            'text' => '...', 'created_at' => '2026-09-18']
// A resenha é opcional: sem texto, mostra só a nota e a data
$reviewDate = DateTime::createFromFormat('!Y-m-d', (string) ($review['created_at'] ?? ''));
$reviewText = trim((string) ($review['text'] ?? ''));
$reviewUserCount = max((int) $review['user_review_count'], 0);
$rating = ['value' => $review['rating'], 'size' => 'size-4'];
?>
<article class="grid grid-cols-[minmax(0,2fr)_minmax(0,3fr)] items-center gap-6 border-b border-black-100 py-6 last:border-0">
    <div class="flex items-center gap-4">
        <div class="flex size-20 shrink-0 items-center justify-center rounded-full bg-gray-100">
            <img src="../assets/icons/person.svg" alt="" width="48" height="48" class="size-12">
        </div>
        <div class="flex min-w-0 flex-col gap-1">
            <span class="truncate text-base"><?= htmlspecialchars($review['user_name']) ?></span>
            <span class="text-sm text-black-300">Reviews <span class="font-bold text-primary"><?= $reviewUserCount ?></span></span>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-4">
            <?php include __DIR__ . '/../rating.php'; ?>
            <?php if ($reviewDate): ?>
                <time datetime="<?= $reviewDate->format('Y-m-d') ?>" class="text-base"><?= $reviewDate->format('d/m/y') ?></time>
            <?php endif; ?>
        </div>
        <?php if ($reviewText !== ''): ?>
            <!-- pre-line mantém as quebras de linha que a pessoa digitou -->
            <p class="whitespace-pre-line text-sm font-light"><?= htmlspecialchars($reviewText) ?></p>
        <?php endif; ?>
    </div>
</article>
