<?php
/**
 * NOTIFICAÇÃO
 *
 * Layout do Figma (notification-positive / notification-negative).
 * Mostra a mensagem que o backend deixou na sessão: entra pela direita
 * e sai sozinha depois de 5 segundos (só CSS, sem JS).
 * O caminho das imagens é relativo às páginas em frontend/pages/.
 *
 * Contrato com o backend:
 * $_SESSION['notification'] = [
 *     'type' => 'error',               // 'error' ou 'success'
 *     'code' => 'INVALID_CREDENTIALS', // chave do mapa de mensagens
 * ];
 *
 * Como usar:
 * 1. session_start() no topo da página, antes de qualquer HTML;
 * 2. logo depois de abrir o <body>:
 *    <?php include __DIR__ . '/../components/notification.php'; ?>
 */

$notification = $_SESSION['notification'] ?? null;

// Apaga da sessão para a mensagem aparecer uma vez só
unset($_SESSION['notification']);

if ($notification):
    // Por enquanto só existe o mapa de erros; o de sucesso entra quando precisar
    $messages = require __DIR__ . '/../../backend/config/errors.php';
    $code = $notification['code'] ?? '';
    $message = $messages[$code]['message'] ?? $messages['UNKNOWN_ERROR']['message'];

    $isSuccess = ($notification['type'] ?? '') === 'success';
    $image = $isSuccess ? 'pig-happy.png' : 'pig-angry.png';
    $borderColor = $isSuccess ? 'border-green-500' : 'border-red-400';
    $role = $isSuccess ? 'status' : 'alert';
?>

<style>
    @keyframes notification-slide {
        0%, 100% { transform: translateX(calc(100% + 16px)); visibility: hidden; }
        6%, 94% { transform: translateX(0); visibility: visible; }
    }

    .notification {
        animation: notification-slide 5s ease-in-out forwards;
    }

    /* Para quem pediu menos movimento no sistema: aparece e some sem deslizar */
    @media (prefers-reduced-motion: reduce) {
        .notification {
            animation-timing-function: step-end;
        }
    }
</style>

<div role="<?= $role ?>"
    class="notification fixed left-4 right-4 top-4 z-50 flex min-h-[100px] items-center gap-3.5 rounded-2xl border-t-2 bg-[#f5f5f5] py-4 pl-3.5 pr-6 text-base text-black-500 shadow-[0_2px_2px_0_rgba(0,0,0,0.25)] sm:left-auto sm:min-w-[314px] sm:max-w-[400px] <?= $borderColor ?>">
    <img src="../assets/images/<?= $image ?>" alt="" width="68" height="45" class="w-[68px] shrink-0">
    <p><?= htmlspecialchars($message) ?></p>
</div>

<?php endif; ?>