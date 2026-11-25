<?php
/** @var array $user */
/** @var array|null $fieldError */
// Seção Segurança da tela de Configurações (pages/settings.php)
// Erro de um campo volta do backend como no login: $_SESSION['notification'] com 'field'
$errorFor = $fieldError['field'] ?? '';

// Dias desde a última troca de senha (já calculados pelo banco).
// Sem o número (banco fora do ar), o aviso não aparece
$changedDays = $user['password_changed_days'];
$changedText = '';
if ($changedDays === 0) {
    $changedText = 'Sua senha foi alterada hoje';
} elseif ($changedDays === 1) {
    $changedText = 'Sua senha foi alterada há 1 dia';
} elseif ($changedDays !== null) {
    $changedText = "Sua senha foi alterada há $changedDays dias";
}

// A senha atual só precisa existir; as novas seguem a regra do cadastro (mínimo de 8)
$passwordFields = [
    ['name' => 'current_password', 'label' => 'Senha atual', 'autocomplete' => 'current-password', 'minlength' => ''],
    ['name' => 'new_password', 'label' => 'Nova senha', 'autocomplete' => 'new-password', 'minlength' => '8'],
    ['name' => 'confirm_password', 'label' => 'Confirmar nova senha', 'autocomplete' => 'new-password', 'minlength' => '8'],
];
?>
<div class="flex flex-col gap-4 p-6">
    <?php if ($changedText): ?>
        <p class="text-center text-sm text-green-500"><?= $changedText ?></p>
    <?php endif; ?>

    <form method="post" action="../../backend/controllers/update-password.php" class="flex flex-col gap-4">
        <?php foreach ($passwordFields as $passwordField): ?>
            <?php $hasError = $errorFor === $passwordField['name']; ?>
            <div class="flex flex-col gap-[5px]">
                <label for="<?= $passwordField['name'] ?>" class="text-base font-bold"><?= $passwordField['label'] ?></label>
                <input type="password" id="<?= $passwordField['name'] ?>" name="<?= $passwordField['name'] ?>"
                    autocomplete="<?= $passwordField['autocomplete'] ?>" required
                    <?= $passwordField['minlength'] ? 'minlength="' . $passwordField['minlength'] . '"' : '' ?>
                    <?= $hasError ? 'aria-invalid="true" aria-describedby="' . $passwordField['name'] . '-error" autofocus' : '' ?>
                    class="h-11 w-full rounded-[32px] border <?= $hasError ? 'border-red-400' : 'border-primary' ?> bg-transparent px-4 text-base outline-none focus:ring-2 focus:ring-secondary">
                <?php if ($hasError): ?>
                    <p id="<?= $passwordField['name'] ?>-error" class="px-4 text-sm text-red-400"><?= htmlspecialchars($fieldError['message']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit"
            class="h-11 w-36 cursor-pointer rounded-[48px] bg-secondary text-lg text-background transition hover:brightness-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary">
            Alterar
        </button>
    </form>

    <script>
        {
            // Avisa antes de enviar se a confirmação não bate com a nova senha.
            // O backend também precisa conferir, porque o POST pode chegar direto
            const form = document.currentScript.closest('div').querySelector('form');
            const newPassword = form.querySelector('[name="new_password"]');
            const confirmPassword = form.querySelector('[name="confirm_password"]');

            function checkMatch() {
                const matches = confirmPassword.value === newPassword.value;
                confirmPassword.setCustomValidity(matches ? '' : 'As senhas não são iguais');
            }

            newPassword.addEventListener('input', checkMatch);
            confirmPassword.addEventListener('input', checkMatch);
        }
    </script>
</div>
