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
    // Cadastro e trocar senha: senha com menos de 8 caracteres
    'WEAK_PASSWORD' => [
        'status'  => 422,
        'message' => 'Sua senha precisa ter pelo menos 8 caracteres.',
    ],
    // Login, cadastro, itens e categorias: campo obrigatório vazio (cadastro e itens informam o campo)
    'REQUIRED_FIELD' => [
        'status'  => 422,
        'message' => 'Esse campo é obrigatório.',
    ],
    // Qualquer exceção (banco fora do ar etc.), sem detalhes internos
    'UNKNOWN_ERROR' => [
        'status'  => 500,
        'message' => 'Algo deu errado do nosso lado. Tenta de novo daqui a pouco.',
    ],
    // Cadastro, configurações e categorias (nome com mais de 120 caracteres) e itens (nome com mais de 100)
    'INVALID_NAME' => [
        'status'  => 422,
        'message' => 'Esse nome está muito longo. Tenta encurtar um pouco.',
    ],

    // ---- Itens ----

    // Itens: category_id que não existe ou que não é do usuário logado
    'INVALID_CATEGORY' => [
        'status'  => 422,
        'message' => 'Escolha uma categoria da lista.',
    ],
    // Itens: valor que não é número, tem mais de 2 casas decimais ou não é maior que zero
    'INVALID_PRICE' => [
        'status'  => 422,
        'message' => 'Informe um valor maior que zero, com até 2 casas decimais.',
    ],
    // Itens: link que não é uma URL http ou https, ou passa de 255 caracteres
    'INVALID_URL' => [
        'status'  => 422,
        'message' => 'Informe o link completo, começando com http:// ou https://.',
    ],
    // Itens: data que não existe ou não está no formato AAAA-MM-DD
    'INVALID_DATE' => [
        'status'  => 422,
        'message' => 'Essa data não parece válida. Dá uma olhadinha.',
    ],
    // Itens: data final antes da data de início
    'INVALID_PERIOD' => [
        'status'  => 422,
        'message' => 'A data final precisa ser igual ou depois da data de início.',
    ],
    // Itens e foto do usuário: arquivo que não é PNG ou JPG de verdade (o tipo é conferido pelo conteúdo)
    'INVALID_IMAGE' => [
        'status'  => 422,
        'message' => 'Não consegui usar essa foto. Envie uma imagem PNG ou JPG.',
    ],
    // Itens e foto do usuário: foto acima de 5 MB (ou acima do limite do PHP)
    'IMAGE_TOO_LARGE' => [
        'status'  => 413,
        'message' => 'Essa foto passou de 5 MB. Escolha uma menor.',
    ],
    // Itens e Dashboard: id que não existe ou que é de outro usuário (os dois casos devolvem o mesmo)
    'ITEM_NOT_FOUND' => [
        'status'  => 404,
        'message' => 'Não encontramos esse item. Talvez ele já tenha sido removido.',
    ],

    // ---- Categorias ----

    // Categorias: ícone fora da lista de backend/config/categories.php
    'INVALID_ICON' => [
        'status'  => 422,
        'message' => 'Escolha um ícone da lista.',
    ],
    // Categorias: cor fora da lista de backend/config/categories.php
    'INVALID_COLOR' => [
        'status'  => 422,
        'message' => 'Escolha uma cor da lista.',
    ],
    // Categorias: descrição acima do limite do banco (65.535 bytes)
    'INVALID_DESCRIPTION' => [
        'status'  => 422,
        'message' => 'Essa descrição está muito longa. Tenta encurtar um pouco.',
    ],
    // Categorias: id que não existe ou que é de outro usuário (os dois casos devolvem o mesmo)
    'CATEGORY_NOT_FOUND' => [
        'status'  => 404,
        'message' => 'Não encontramos essa categoria. Talvez ela já tenha sido removida.',
    ],
    // Categorias: remover categoria que ainda tem itens
    'CATEGORY_HAS_ITEMS' => [
        'status'  => 409,
        'message' => 'Essa categoria ainda tem itens. Mova ou remova os itens antes de remover a categoria.',
    ],

    // ---- Configurações ----

    // Trocar senha: a senha atual não confere com a do banco
    'WRONG_PASSWORD' => [
        'status'  => 422,
        'message' => 'A senha atual não está certa. Confere e tenta de novo.',
    ],
    // Trocar senha: a confirmação é diferente da nova senha
    'PASSWORD_MISMATCH' => [
        'status'  => 422,
        'message' => 'As senhas não são iguais.',
    ],

    // ---- Dashboard ----

    // Comprei/Desisti: decisão que não é 'bought' nem 'gave_up'
    'INVALID_DECISION' => [
        'status'  => 422,
        'message' => 'Escolha entre Comprei e Desisti.',
    ],
    // Comprei/Desisti: item que já tinha sido decidido
    'ITEM_ALREADY_DECIDED' => [
        'status'  => 409,
        'message' => 'Esse item já foi decidido.',
    ],
];
