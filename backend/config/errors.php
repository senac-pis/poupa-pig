<?php 
/**
 * Mapa de erros da aplicação.
 *
 * O backend devolve o código (chave do array); o front usa a mensagem.
 * Depois de combinado, um código não muda de nome — só a mensagem.
 * Código que não estiver aqui cai em UNKNOWN_ERROR.
 */

return [
    'INVALID_CREDENTIALS' => [
        'status' => 401,
        'message' => 'Email ou senha incorretos. Confere e tenta de novo.'
    ],
    'EMAIL_ALREADY_EXISTS' => [
        'status' => 409,
        'message' => 'Esse email já tem uma conta. Que tal fazer login?'
    ],
    'INVALID_EMAIL' => [
        'status'  => 422,
        'message' => 'Esse email não parece válido. Dá uma olhadinha.',
    ],
    'WEAK_PASSWORD' => [
        'status'  => 422,
        'message' => 'Sua senha precisa ter pelo menos 8 caracteres.', // tem que definir isso 
    ],
    'REQUIRED_FIELD' => [
        'status'  => 422,
        'message' => 'Esse campo é obrigatório.',
    ],
    'UNKNOWN_ERROR' => [
        'status'  => 500,
        'message' => 'Algo deu errado do nosso lado. Tenta de novo daqui a pouco.',
    ],
];
?>
