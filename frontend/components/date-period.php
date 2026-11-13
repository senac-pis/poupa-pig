<?php
/** @var array $datePeriod */
// Antes do include: $datePeriod = ['start' => DateTime|null, 'end' => DateTime|null, 'background' => 'bg-white-100']
// Opcional: 'required' => true. Os campos se chamam start_date e end_date, como no banco.
// As datas chegam já validadas por quem usa (filtro ou página)
$periodRequired = !empty($datePeriod['required']);
?>
<div class="flex gap-2.5">
    <?php foreach (['start' => 'Início', 'end' => 'Fim'] as $periodField => $periodBoxLabel): ?>
        <?php
        $periodDate = $datePeriod[$periodField];
        // Obrigatório: fica cinza enquanto vazio (:invalid) e muda sozinho ao escolher a data.
        // Opcional (filtro): o filtro recarrega ao escolher, então basta decidir no PHP
        $emptyClass = $periodRequired ? 'invalid:text-black-200' : ($periodDate ? '' : 'text-black-200');
        ?>
        <label class="relative flex min-w-0 flex-1 flex-col gap-2 rounded-2xl p-3 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-secondary <?= $datePeriod['background'] ?>">
            <span class="text-base font-medium"><?= $periodBoxLabel ?></span>
            <span class="flex items-center gap-2.5">
                <img src="../assets/icons/calendar-range.svg" alt="" width="24" height="24" class="size-6">
                <!-- O ícone nativo do calendário cobre a caixa toda, invisível: clicar em qualquer lugar abre o seletor -->
                <input type="date" name="<?= $periodField ?>_date" value="<?= $periodDate?->format('Y-m-d') ?>" <?= $periodRequired ? 'required' : '' ?>
                    class="w-full min-w-0 bg-transparent text-lg font-medium outline-none <?= $emptyClass ?> [&::-webkit-calendar-picker-indicator]:absolute [&::-webkit-calendar-picker-indicator]:inset-0 [&::-webkit-calendar-picker-indicator]:size-full [&::-webkit-calendar-picker-indicator]:cursor-pointer [&::-webkit-calendar-picker-indicator]:opacity-0">
            </span>
        </label>
    <?php endforeach; ?>
</div>
