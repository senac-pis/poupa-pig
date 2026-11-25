<?php
/**
 * Mapa de mensagens de sucesso da aplicação.
 *
 * O backend devolve o código (chave do array); o front usa a mensagem.
 * Depois de combinado, um código não muda de nome, só a mensagem.
 * Código que não estiver aqui não mostra notificação.
 */

return [
    // Cadastro: conta criada, o usuário vai para o login
    'ACCOUNT_CREATED' => [
        'message' => 'Conta criada! Agora é só entrar.',
    ],
    // Itens: item criado, editado e removido
    'ITEM_CREATED' => [
        'message' => 'Item adicionado!',
    ],
    'ITEM_UPDATED' => [
        'message' => 'Item atualizado!',
    ],
    'ITEM_DELETED' => [
        'message' => 'Item removido.',
    ],
    // Categorias: categoria criada, editada e removida
    'CATEGORY_CREATED' => [
        'message' => 'Categoria adicionada!',
    ],
    'CATEGORY_UPDATED' => [
        'message' => 'Categoria atualizada!',
    ],
    'CATEGORY_DELETED' => [
        'message' => 'Categoria removida.',
    ],
    // Configurações: nome e foto salvos, senha trocada e conta excluída (essa aparece no login)
    'ACCOUNT_UPDATED' => [
        'message' => 'Nome atualizado!',
    ],
    'PHOTO_UPDATED' => [
        'message' => 'Foto atualizada!',
    ],
    'PASSWORD_UPDATED' => [
        'message' => 'Senha alterada!',
    ],
    'ACCOUNT_DELETED' => [
        'message' => 'Sua conta foi excluída. Até a próxima!',
    ],
    // Dashboard: Comprei e Desisti
    'ITEM_GAVE_UP' => [
        'message' => 'Boa! Seu porquinho agradece.',
    ],
    'ITEM_BOUGHT' => [
        'message' => 'Compra registrada.',
    ],
];
