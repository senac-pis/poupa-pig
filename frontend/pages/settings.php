<?php
/**
 * TELA DE CONFIGURAÇÕES
 *
 * Layout implementado a partir do Figma (Desktop - Light mode configuração).
 *
 * Contrato com o backend:
 * - exige usuário logado (requireLogin); $user vem do banco (UserModel) com name e password_changed_days;
 * - os forms enviam POST para backend/controllers/update-account.php, update-password.php
 *   e delete-account.php;
 * - depois de salvar, o backend volta para esta tela deixando a mensagem em $_SESSION['notification'];
 *   erro de campo volta com 'field' (name, current_password, new_password ou confirm_password).
 * A cor principal fica salva no navegador por enquanto (components/accent-color.php).
 */

require_once __DIR__ . '/../../backend/config/auth.php';

$sessionUser = requireLogin('login.php');

$activePage = '';

// Sem o banco, a tela abre com o nome da sessão e sem a data da senha
$user = [
    'name' => $sessionUser['name'],
    'password_changed_days' => null,
];

try {
    require_once __DIR__ . '/../../backend/config/connection.php';
    require_once __DIR__ . '/../../backend/models/UserModel.php';

    $dbUser = findUserById($pdo, $sessionUser['id']);
    if ($dbUser !== null) {
        $user = [
            'name' => $dbUser['name'],
            'password_changed_days' => $dbUser['password_changed_days'],
        ];
    }
} catch (Throwable $e) {
    // Detalhe interno só no log do servidor; a tela mostra o aviso genérico
    error_log('[settings] ' . $e->getMessage());
    $_SESSION['notification'] = ['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => ''];
}

$sections = [
    'account' => ['label' => 'Conta', 'icon' => 'person'],
    'security' => ['label' => 'Segurança', 'icon' => 'shield-plus'],
    'appearance' => ['label' => 'Aparência', 'icon' => 'palette'],
];

// Seção pela URL: ?section=security; qualquer outro valor abre Conta
$rawSection = $_GET['section'] ?? '';
$section = is_string($rawSection) && isset($sections[$rawSection]) ? $rawSection : 'account';
$action = $_GET['action'] ?? '';

$sectionTemplates = [
    'account' => __DIR__ . '/../components/settings/account-section.php',
    'security' => __DIR__ . '/../components/settings/security-section.php',
    'appearance' => __DIR__ . '/../components/settings/appearance-section.php',
];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurações — Poupa Pig</title>
    <?php include __DIR__ . '/../components/theme.php'; ?>
    <?php include __DIR__ . '/../components/accent-color.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = 'Configurações';
        include __DIR__ . '/../components/page-header.php';
        ?>

        <div class="flex min-h-0 flex-1 gap-2 pb-2">
            <section aria-labelledby="settings-menu-title" class="flex flex-1 flex-col gap-4 overflow-y-auto rounded-2xl bg-white-300/50 p-4">
                <?php
                $searchPlaceholder = 'Pesquisar configurações...';
                $filtersEnabled = false;
                include __DIR__ . '/../components/search-bar.php';
                /** @var string $searchTerm */

                // A busca filtra a lista de seções pelo nome
                $shownSections = array_filter($sections, fn($item) => $searchTerm === '' || mb_stripos($item['label'], $searchTerm) !== false);
                ?>

                <h2 id="settings-menu-title" class="text-[22px] font-bold">Configurações pessoais</h2>

                <?php if ($shownSections): ?>
                    <nav aria-labelledby="settings-menu-title">
                        <ul class="flex flex-col gap-1">
                            <?php foreach ($shownSections as $key => $item): ?>
                                <?php
                                $isActive = $key === $section;
                                // Mantém a busca ao trocar de seção, para a lista continuar filtrada
                                $sectionUrl = '?' . http_build_query(array_filter(['section' => $key, 'search' => $searchTerm]));
                                $iconMask = "url(../assets/icons/{$item['icon']}.svg) center / contain no-repeat";
                                ?>
                                <li>
                                    <a href="<?= htmlspecialchars($sectionUrl) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>
                                        class="flex items-center gap-2 rounded-lg px-4 py-1 text-lg transition focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary <?= $isActive ? 'bg-gray-100' : 'hover:bg-gray-100/60' ?>">
                                        <span class="size-4 shrink-0 bg-primary" style="mask: <?= $iconMask ?>; -webkit-mask: <?= $iconMask ?>;"></span>
                                        <?= $item['label'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <p class="px-4 text-base font-light">Nenhuma configuração encontrada</p>
                <?php endif; ?>
            </section>

            <section aria-label="<?= $sections[$section]['label'] ?>" class="flex-1 overflow-y-auto rounded-2xl bg-white-300/50">
                <?php include $sectionTemplates[$section]; ?>
            </section>
        </div>
    </main>

    <?php if ($section === 'account' && $action === 'delete'): ?>
        <?php
        $confirmDialog = [
            'title' => 'Excluir conta?',
            'message' => 'Tem certeza que deseja excluir sua conta?',
            'confirmLabel' => 'Excluir',
            'action' => '../../backend/controllers/delete-account.php',
        ];
        include __DIR__ . '/../components/confirm-dialog.php';
        ?>
    <?php endif; ?>
</body>
</html>
