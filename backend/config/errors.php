<?php
/**
 * Mapa de erros da aplicação.
 *
 * O backend devolve o código (chave do array); o front usa a mensagem.
 * Depois de combinado, um código não muda de nome, só a mensagem.
 * Código que não estiver aqui cai em UNKNOWN_ERROR.
 *
 * Obs.: o fluxo é POST -> redirect -> GET, então o 'status' é só
 * referência/documentação (o redirect real é sempre 302).
 */

return [
    // Login: email não cadastrado OU senha errada (mesmo código nos dois casos)
    'INVALID_CREDENTIALS' => [
        'status'  => 401,
        'message' => 'Email ou senha incorretos. Confere e tenta de novo.',
    ],
    // Cadastro: já existe usuário com esse email
    'EMAIL_ALREADY_EXISTS' => [
        'status'  => 409,
        'message' => 'Esse email já tem uma conta. Que tal fazer login?',
    ],
    // Login e cadastro: email falha no filter_var(FILTER_VALIDATE_EMAIL)
    'INVALID_EMAIL' => [
        'status'  => 422,
        'message' => 'Esse email não parece válido. Dá uma olhadinha.',
    ],
    // Cadastro: senha com menos de 8 caracteres
    'WEAK_PASSWORD' => [
        'status'  => 422,
        'message' => 'Sua senha precisa ter pelo menos 8 caracteres.',
    ],
    // Login e cadastro: campo obrigatório vazio (cadastro informa o campo)
    'REQUIRED_FIELD' => [
        'status'  => 422,
        'message' => 'Esse campo é obrigatório.',
    ],
    // Qualquer exceção (banco fora do ar etc.), sem detalhes internos
    'UNKNOWN_ERROR' => [
        'status'  => 500,
        'message' => 'Algo deu errado do nosso lado. Tenta de novo daqui a pouco.',
    ],
];
