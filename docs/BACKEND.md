# Back-end do Poupa Pig

Documentação do back-end em PHP: como as camadas se organizam, o que cada controller recebe e
devolve, a configuração, o upload de fotos e os cuidados de segurança que o código já tem.

> Tudo aqui descreve o código como ele está em `backend/`. Se mudar o código, atualize este
> arquivo junto.

## 1. Visão geral

- **PHP puro**, sem framework, rodando no XAMPP (Apache + MySQL). Banco acessado por **PDO**.
- **Fluxo POST → redirect → GET:** o formulário faz POST para um controller, o controller grava o
  resultado na sessão (`$_SESSION['notification']`) e redireciona (302) para uma página do front,
  que mostra a mensagem uma vez e a apaga.
- **Quem lê e quem grava:**
  - **Leitura (GET):** as páginas de `frontend/pages/` chamam `requireLogin()` e os models
    diretamente para montar a tela (listas, filtros, paginação, Dashboard).
  - **Escrita (POST):** só os controllers de `backend/controllers/` alteram o banco.

```text
backend/
├── config/
│   ├── auth.php                  # requireLogin()
│   ├── categories.php            # ícones e cores permitidos
│   ├── connection.php            # cria o $pdo
│   ├── connection.example.php    # exemplo didático da conexão
│   ├── database.example.php      # modelo das credenciais
│   ├── database.php              # credenciais locais (fora do Git)
│   ├── errors.php                # mapa de códigos de erro
│   └── success.php               # mapa de códigos de sucesso
├── controllers/                  # um arquivo por ação (POST)
├── services/                     # regras, validação, upload e redirects
└── models/                       # queries
```

## 2. Camadas: controller → service → model

| Camada         | Onde                   | Responsabilidade                                                                                                                       |
| -------------- | ---------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| **Controller** | `backend/controllers/` | Porta de entrada. Exige login, aceita só POST, lê a entrada, chama a validação, chama o model dentro de `try/catch` e redireciona.     |
| **Service**    | `backend/services/`    | Regras da tela: leitura e validação dos campos, upload de foto, redirects com notificação. **Não conversa com o banco.**              |
| **Model**      | `backend/models/`      | Só queries (prepared statements). Recebe o `$pdo` como parâmetro. Sem regra de negócio; em falha, a `PDOException` sobe para quem chamou. |

Roteiro comum de um controller (ex.: `create-item.php`):

1. `require_once` do `auth.php` e do service; `$user = requireLogin('../../frontend/pages/login.php');`.
2. Se `REQUEST_METHOD !== 'POST'`, redireciona para a tela sem fazer nada.
3. Lê e valida a entrada com o service; no primeiro erro, redireciona com o código.
4. Dentro do `try`: `require_once` do `connection.php` e do model, checagens que precisam do
   banco (dono do registro, duplicidade) e a gravação.
5. `catch`: grava o detalhe com `error_log('[nome-do-controller] ...')` e redireciona com
   `UNKNOWN_ERROR`.
6. Sucesso: redireciona com o código de sucesso.

O `connection.php` é incluído **dentro do `try`** de propósito: se o banco estiver fora do ar, a
exceção vira `UNKNOWN_ERROR` em vez de uma tela de erro do PHP.

`login.php` e `register.php` são os mais antigos e não usam service: as funções de redirect e as
constantes ficam no próprio controller.

## 3. Configuração (`backend/config/`)

### `auth.php` — `requireLogin()`

```php
$user = requireLogin('../../frontend/pages/login.php'); // em backend/controllers
$user = requireLogin('login.php');                      // em frontend/pages
```

- Inicia a sessão se ainda não estiver ativa.
- Lê `$_SESSION['user']['id']`; se não for um inteiro positivo, redireciona para o login e encerra.
- Retorna `['id' => int, 'name' => string]`.
- **O id do usuário vem sempre da sessão**, nunca de formulário ou URL. É esse id que todos os
  controllers passam para os models.

### `database.example.php`, `database.php` e `connection.php`

- `database.example.php` é o modelo: copie para `database.php` e ajuste `host`, `port`,
  `database`, `username`, `password` e `charset` (`utf8mb4`).
- `database.php` está no `.gitignore` e **não vai para o Git**.
- `connection.php` monta o DSN `mysql:host=...;port=...;dbname=...;charset=...` e cria o `$pdo` com:
  - `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` (erro do banco vira exceção);
  - `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`.
- Se a conexão falhar, grava a mensagem no `error_log` e lança `RuntimeException` com uma mensagem
  genérica, que o controller transforma em `UNKNOWN_ERROR`.
- `connection.example.php` é o exemplo didático original: em vez de lançar exceção, ele faz
  `exit` com a mensagem. **O que o sistema usa é o `connection.php`.**

### `errors.php` e `success.php` — mapas de códigos

O back-end nunca escreve mensagem para o usuário: ele devolve um **código** (chave do array) e o
front busca a mensagem no mapa.

- `errors.php`: `'CODIGO' => ['status' => 422, 'message' => '...']`.
  - O `status` é só **referência**: como o fluxo é POST → redirect, a resposta real é sempre 302.
  - Código que não estiver no mapa é exibido como `UNKNOWN_ERROR`.
- `success.php`: `'CODIGO' => ['message' => '...']`. Código desconhecido não mostra notificação.
- Depois de combinado, **um código não muda de nome**, só a mensagem.

Códigos de erro, por área:

| Área            | Códigos                                                                                                                                                  |
| --------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Geral           | `REQUIRED_FIELD` (422), `INVALID_NAME` (422), `UNKNOWN_ERROR` (500)                                                                                       |
| Login/cadastro  | `INVALID_CREDENTIALS` (401), `EMAIL_ALREADY_EXISTS` (409), `INVALID_EMAIL` (422), `WEAK_PASSWORD` (422)                                                   |
| Itens           | `INVALID_CATEGORY`, `INVALID_PRICE`, `INVALID_URL`, `INVALID_DATE`, `INVALID_PERIOD`, `INVALID_IMAGE` (422), `IMAGE_TOO_LARGE` (413), `ITEM_NOT_FOUND` (404) |
| Categorias      | `INVALID_ICON`, `INVALID_COLOR`, `INVALID_DESCRIPTION` (422), `CATEGORY_NOT_FOUND` (404), `CATEGORY_HAS_ITEMS` (409)                                       |
| Configurações   | `WRONG_PASSWORD` (422), `PASSWORD_MISMATCH` (422)                                                                                                        |
| Dashboard       | `INVALID_DECISION` (422), `ITEM_ALREADY_DECIDED` (409)                                                                                                    |
| Reviews         | `INVALID_RATING`, `INVALID_REVIEW_TEXT` (422), `PRODUCT_NOT_FOUND` (404), `REVIEW_NOT_ALLOWED` (403), `REVIEW_ALREADY_EXISTS` (409)                         |

Códigos de sucesso: `ACCOUNT_CREATED`, `ITEM_CREATED`, `ITEM_UPDATED`, `ITEM_DELETED`,
`CATEGORY_CREATED`, `CATEGORY_UPDATED`, `CATEGORY_DELETED`, `ACCOUNT_UPDATED`, `PHOTO_UPDATED`,
`PASSWORD_UPDATED`, `ACCOUNT_DELETED`, `ITEM_GAVE_UP`, `ITEM_BOUGHT`, `REVIEW_CREATED`.

### `categories.php` — ícones e cores permitidos

Retorna as listas que o back-end usa para **recusar qualquer valor fora delas** e que o front usa
para montar os seletores:

| Chave         | Conteúdo                                                                                       |
| ------------- | ---------------------------------------------------------------------------------------------- |
| `icon_groups` | Ícones por grupo (Alimentação, Eletrônicos, Moda e acessórios, Casa), 8 em cada.               |
| `icons`       | Lista plana: `box-seam` (padrão, fora do seletor) + todos os ícones dos grupos.                |
| `color_groups` | Rótulo → família da paleta: Azul `sky`, Rosa `pink`, Roxo `purple`, Verde `green`, Vermelho `red`. |
| `color_tones` | `100` a `500`.                                                                                 |
| `colors`      | `gray-200` (padrão, fora do seletor) + `família-tom` para cada combinação (ex.: `sky-200`).     |
| `icon_tones`  | Tom do ícone para cada tom de fundo (`white` = ícone branco).                                  |
| `bar_tones`   | Tom da barra de percentual para cada tom de fundo.                                             |

O primeiro item de `icons` e de `colors` é o padrão quando a categoria é salva sem ícone ou cor.
O banco guarda só a chave (ex.: `smartphone`, `sky-200`).

## 4. Padrão de notificação e `old_input`

Todo controller que redireciona deixa na sessão:

```php
$_SESSION['notification'] = [
    'type'  => 'error',          // 'error' ou 'success'
    'code'  => 'INVALID_PRICE',  // chave do errors.php ou do success.php
    'field' => 'price',          // só em erro: nome do input com problema ('' = aviso geral)
];

$_SESSION['old_input'] = [ /* campos de texto como foram enviados */ ];
```

- **`field`:** com valor, o front mostra a mensagem embaixo do input; vazio, mostra o aviso no
  canto da tela.
- **`old_input`:** devolve os campos de texto para o formulário reabrir preenchido. **Nunca guarda
  senha nem foto.** No sucesso dos itens e das categorias ele é apagado.
- **Consumo no front:** `frontend/components/notification.php` lê a notificação, apaga da sessão
  (aparece uma vez só) e busca a mensagem no mapa. Com `field`, deixa `$fieldError` para a página.
- Os erros são validados **um por vez, na ordem dos campos do formulário**: a pessoa vê o primeiro
  problema, corrige e reenvia.

## 5. Controllers

Todos ficam em `backend/controllers/`. Os caminhos de redirect abaixo são relativos a
`frontend/pages/`. Salvo onde indicado, todos exigem login (`requireLogin`) e, se a requisição não
for POST, redirecionam para a tela de origem sem fazer nada.

### `login.php`

| Item        | Detalhe                                                                                                    |
| ----------- | ---------------------------------------------------------------------------------------------------------- |
| Login?      | Não exige (é a entrada).                                                                                   |
| Recebe      | `email` (com `trim`), `password` (sem `trim`).                                                             |
| Validações  | `email` vazio → `REQUIRED_FIELD`; `password` vazio → `REQUIRED_FIELD`; email inválido → `INVALID_EMAIL`.   |
| Banco       | `findUserByEmail`; email inexistente **ou** senha errada (`password_verify`) → `INVALID_CREDENTIALS`.      |
| Erro        | Volta para `login.php` com `old_input = ['email']`.                                                        |
| Sucesso     | `session_regenerate_id(true)`, grava `$_SESSION['user']` (`id`, `name`, `photo`, `accent_color`) e vai para `items.php`. |

### `register.php`

| Item        | Detalhe                                                                                                                                                       |
| ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Login?      | Não exige.                                                                                                                                                    |
| Recebe      | `name` e `email` (com `trim`), `password` (sem `trim`).                                                                                                       |
| Validações  | `name` vazio → `REQUIRED_FIELD`; mais de 120 caracteres → `INVALID_NAME`; `email` vazio → `REQUIRED_FIELD`; mais de 180 ou inválido → `INVALID_EMAIL`; `password` vazio → `REQUIRED_FIELD`; menos de 8 → `WEAK_PASSWORD`. |
| Banco       | Email já cadastrado → `EMAIL_ALREADY_EXISTS`. Grava com `password_hash(..., PASSWORD_DEFAULT)`. Erro MySQL 1062 (dois cadastros ao mesmo tempo) também vira `EMAIL_ALREADY_EXISTS`. |
| Erro        | Volta para `register.php` com `old_input = ['name', 'email']`.                                                                                                |
| Sucesso     | `ACCOUNT_CREATED` e vai para `login.php` com o email já preenchido (`old_input`).                                                                            |

### `create-item.php` e `update-item.php`

| Item        | Detalhe                                                                                                                                                                         |
| ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Recebe      | POST multipart: `name`, `category_id`, `price`, `link` (opcional), `start_date`, `end_date`, `image` (opcional). O update recebe também `id`.                                   |
| Validações  | Ver `itemInputError` (seção 6): nome, categoria, preço, link, datas e período; depois a foto (`inspectItemImage`).                                                             |
| Banco       | Update: `findItemById` (inexistente ou de outro usuário → `ITEM_NOT_FOUND`). Ambos: `categoryBelongsToUser` (falso → `INVALID_CATEGORY`).                                       |
| Foto        | Create: salva se veio. Update: sem foto nova mantém a atual; com foto nova, a antiga é apagada **só depois** que o banco aceitou. Se o banco falhar, a foto recém-salva é apagada. |
| Erro        | Volta para `items.php?action=new` ou `items.php?action=edit&id=N` com `old_input` (sem a foto).                                                                                  |
| Sucesso     | `ITEM_CREATED` / `ITEM_UPDATED` e volta para `items.php`.                                                                                                                       |

Detalhes:

- Pedido maior que o `post_max_size` do PHP chega sem `$_POST` e sem `$_FILES`;
  `itemRequestTooLarge()` detecta isso e devolve `IMAGE_TOO_LARGE` (no update, como aviso geral na
  lista, porque sem o `id` não dá para reabrir o drawer).
- O update não muda o `status` do item.

### `delete-item.php`

| Item     | Detalhe                                                                                               |
| -------- | ----------------------------------------------------------------------------------------------------- |
| Recebe   | `id` (só dígitos).                                                                                    |
| Erros    | `id` inválido, inexistente ou de outro usuário → `ITEM_NOT_FOUND`; falha → `UNKNOWN_ERROR`.            |
| Sucesso  | Apaga o item e depois a foto do disco; `ITEM_DELETED` e volta para `items.php`.                       |

### `decide-item.php` (Comprei / Desisti do Dashboard)

| Item     | Detalhe                                                                                                                                   |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Recebe   | `id` e `decision` (`bought` ou `gave_up`).                                                                                                |
| Erros    | `id` inválido / inexistente / de outro usuário → `ITEM_NOT_FOUND`; decisão fora da lista → `INVALID_DECISION`; item que não está `thinking` → `ITEM_ALREADY_DECIDED`. |
| Banco    | `decideItem` grava status e data da decisão com `WHERE status = 'thinking'`: dois cliques seguidos não decidem duas vezes.               |
| Sucesso  | `ITEM_BOUGHT` ou `ITEM_GAVE_UP` e volta para `dashboard.php`.                                                                            |

### `create-category.php` e `update-category.php`

| Item        | Detalhe                                                                                                                                   |
| ----------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Recebe      | `name`, `description` (opcional), `icon` e `color` (opcionais: vazios viram `box-seam` e `gray-200`). O update recebe também `id`.        |
| Validações  | Ver `categoryInputError` (seção 6): ícone e cor da lista, nome obrigatório até 120 caracteres, descrição até 65.535 bytes.               |
| Banco       | Update: `findCategoryById` (inexistente ou de outro usuário → `CATEGORY_NOT_FOUND`).                                                      |
| Erro        | Volta para `categories.php?action=new` ou `?action=edit&id=N` com `old_input`.                                                            |
| Sucesso     | `CATEGORY_CREATED` / `CATEGORY_UPDATED` e volta para `categories.php`.                                                                    |

### `delete-category.php`

| Item     | Detalhe                                                                                                                                                                  |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Recebe   | `id`.                                                                                                                                                                    |
| Erros    | Inexistente ou de outro usuário → `CATEGORY_NOT_FOUND`; com itens → `CATEGORY_HAS_ITEMS` (a tela esconde o botão, mas o pedido pode chegar direto); falha → `UNKNOWN_ERROR`. |
| Banco    | Se um item for criado entre a contagem e o DELETE, a chave estrangeira (`ON DELETE RESTRICT`) recusa com SQLSTATE `23000`, que também vira `CATEGORY_HAS_ITEMS`.        |
| Sucesso  | `CATEGORY_DELETED` e volta para `categories.php`.                                                                                                                        |

### `create-review.php`

| Item        | Detalhe                                                                                                                                                                                          |
| ----------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Recebe      | `product_id`, `rating` (1 a 5) e `text` (opcional, até 1000 caracteres).                                                                                                                         |
| Validações  | `product_id` inválido → `PRODUCT_NOT_FOUND`; nota vazia → `REQUIRED_FIELD`; nota fora de 1–5 → `INVALID_RATING`; texto longo → `INVALID_REVIEW_TEXT`.                                            |
| Banco       | Produto inexistente → `PRODUCT_NOT_FOUND`. Usuário sem item com o nome do produto → `REVIEW_NOT_ALLOWED`, ou `PRODUCT_NOT_FOUND` se o produto não é público (não revela itens alheios). Já avaliou → `REVIEW_ALREADY_EXISTS`; SQLSTATE `23000` (UNIQUE `product_id + user_id`) também. |
| Redirect    | `product-reviews.php?id=N`; sem produto válido, `review.php`.                                                                                                                                    |
| Sucesso     | `REVIEW_CREATED`.                                                                                                                                                                                |

### `update-account.php` (nome)

| Item     | Detalhe                                                                                                      |
| -------- | ------------------------------------------------------------------------------------------------------------ |
| Recebe   | `name`.                                                                                                      |
| Erros    | Vazio → `REQUIRED_FIELD`; mais de 120 → `INVALID_NAME`; volta para `settings.php?section=account&action=edit-name`. |
| Sucesso  | Atualiza banco e `$_SESSION['user']['name']` (a sidebar usa); `ACCOUNT_UPDATED` em `settings.php?section=account`. |

### `update-password.php`

| Item     | Detalhe                                                                                                                                                                               |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Recebe   | `current_password`, `new_password`, `confirm_password` (nenhum passa por `trim`).                                                                                                     |
| Erros    | Campo vazio → `REQUIRED_FIELD`; nova com menos de 8 → `WEAK_PASSWORD`; confirmação diferente → `PASSWORD_MISMATCH`; senha atual errada → `WRONG_PASSWORD`. Volta para `settings.php?section=security`. |
| Banco    | Usuário da sessão que não existe mais → encerra a sessão e vai para o login com `UNKNOWN_ERROR`. Grava o novo hash e a data da troca.                                                 |
| Sucesso  | `session_regenerate_id(true)` e `PASSWORD_UPDATED`. As senhas nunca vão para a sessão.                                                                                               |

### `update-photo.php`

| Item     | Detalhe                                                                                                                                        |
| -------- | ---------------------------------------------------------------------------------------------------------------------------------------------- |
| Recebe   | POST multipart: `photo`.                                                                                                                       |
| Erros    | Acima do limite → `IMAGE_TOO_LARGE`; tipo inválido → `INVALID_IMAGE`; sem arquivo → `REQUIRED_FIELD` (todos com `field = 'photo'`).          |
| Sucesso  | Salva a nova, grava no banco, apaga a antiga, atualiza `$_SESSION['user']['photo']` e grava `PHOTO_UPDATED` em `settings.php?section=account`. |

### `update-accent-color.php` (exceção: chamado por `fetch`)

A cor muda no clique, sem recarregar a página, então este controller **não redireciona nem grava
notificação**: responde só com o status HTTP.

| Status | Quando                                                     |
| ------ | ---------------------------------------------------------- |
| 204    | Salvou no banco e em `$_SESSION['user']['accent_color']`.  |
| 405    | Não é POST.                                                |
| 422    | `accent_color` fora de `accentColorOptions()`.             |
| 500    | Falha do banco.                                            |

### `delete-account.php`

| Item     | Detalhe                                                                                                                             |
| -------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| Recebe   | Só o POST de confirmação do modal; o usuário é sempre o da sessão.                                                                  |
| Banco    | `deleteUserAccount`: itens, categorias e usuário numa **transação** (tudo ou nada). As reviews saem pelo `ON DELETE CASCADE`.      |
| Erro     | `UNKNOWN_ERROR` em `settings.php` (nada foi apagado).                                                                               |
| Sucesso  | Apaga as fotos dos itens e a do usuário (só depois do commit), limpa a sessão e vai para o login com `ACCOUNT_DELETED`.            |

## 6. Services

| Arquivo               | Usado por                                    | Principais funções                                                                                                                                                                                                 |
| --------------------- | -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `ItemService.php`     | controllers de item, `update-photo`, `delete-account` | `itemInputFromPost`, `itemInputError`, `isHttpUrl`, `isValidItemDate`, `itemRequestTooLarge`, `inspectItemImage`, `storeItemImage`, `deleteItemImageFile`, `redirectItems*`                                     |
| `CategoryService.php` | controllers de categoria, `categories.php`   | `categoryInputFromPost`, `categoryInputError`, `categoryIcon`, `categoryColor`, `categoryItemPercent`, `redirectCategories*`                                                                                      |
| `DashboardService.php` | `decide-item`                               | `decisionItemId`, `decisionStatus`, `redirectDashboard*`                                                                                                                                                          |
| `ReviewService.php`   | `create-review`                              | `reviewInputFromPost`, `redirectProductReviews*`                                                                                                                                                                  |
| `AccountService.php`  | controllers de configurações                 | `accountField`, `accountNameError`, `passwordInputError`, `accentColorOptions`, `storeUserPhoto`, `deleteUserPhotoFile`, `userPhotoUrl`, `redirectSettings*`, `endSessionAndRedirectToLogin`                     |

Regras de validação que valem a pena saber:

- **Item** (`itemInputError`, na ordem):
  - `name`: obrigatório, até 100 caracteres (o formulário limita a 100; a coluna aceita 180).
  - `category_id`: obrigatório, só dígitos e maior que zero.
  - `price`: obrigatório, `^\d{1,8}(\.\d{1,2})?$` (cabe no `DECIMAL(10,2)`) e maior que zero.
  - `link`: opcional; até 255 caracteres e URL `http` ou `https` (`filter_var` sozinho aceitaria
    `javascript:`).
  - `start_date` e `end_date`: obrigatórias, data real no formato `Y-m-d`; `end_date` não pode
    ser antes de `start_date` (`INVALID_PERIOD`).
- **Categoria** (`categoryInputError`): ícone e cor vazios ou dentro de `categories.php`; nome
  obrigatório até 120 caracteres; descrição até 65.535 **bytes** (limite do `TEXT`).
- **Senha** (`passwordInputError`): atual obrigatória; nova obrigatória com pelo menos 8; confirmação
  obrigatória e igual à nova (`hash_equals`).
- **Cor principal** (`accentColorOptions`): a paleta das categorias só nos tons 300 a 500 (os mais
  claros não dão contraste com texto branco).

## 7. Models

| Arquivo             | Tabelas                      | Funções                                                                                                                                                                                                                                       |
| ------------------- | ---------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `UserModel.php`     | `users` (+ itens e categorias na exclusão) | `findUserByEmail`, `createUser`, `findUserById` (com dias desde a troca de senha, calculados no MySQL), `updateUserName`, `updateUserPassword`, `updateUserPhoto`, `updateUserAccentColor`, `deleteUserAccount`                 |
| `ItemModel.php`     | `items`, `categories`        | `listItems`, `countItems` (filtros: busca, categoria, período pela data de inclusão), `findItemById`, `listCategoriesWithCount`, `categoryBelongsToUser`, `createItem`, `updateItem`, `deleteItem`, `listDashboardItems`, `decideItem`, `databaseToday` |
| `CategoryModel.php` | `categories`, `items`        | `listCategories`, `countCategories`, `categorySummary` (cards do topo), `findCategoryById`, `createCategory`, `updateCategory`, `deleteCategory`                                                                                              |
| `ReviewModel.php`   | `products`, `reviews`, `items` | `syncUserProducts`, `listUserProducts`, `countUserProducts`, `findProductById`, `userHasProductItem`, `productIsPublic`, `listProductSuggestions`, `userReviewedProduct`, `productRatingSummary`, `listProductReviews`, `createReview` |

Observações:

- **Dono do registro:** em `ItemModel` e `CategoryModel`, toda query filtra por `user_id`. Em
  `UserModel`, o `id` é sempre o da sessão.
- **Produtos são compartilhados:** um produto é o nome de um item, comparado pela collation do
  banco (sem diferenciar maiúsculas nem acentos) e com os espaços juntados (`REGEXP_REPLACE`).
  `syncUserProducts` cria os produtos que faltam antes de a lista de Reviews abrir.
- **Privacidade dos produtos:** `productIsPublic` só considera visível o produto do catálogo
  (`is_public`) ou que já tem review. Assim, trocar o `?id=` na URL não mostra nomes de itens de
  outras pessoas. `listProductSuggestions` segue a mesma regra.
- **Data de hoje:** `databaseToday` usa o relógio do MySQL, porque o PHP local roda em UTC e as
  datas de decisão são gravadas pelo MySQL.

## 8. Upload de fotos

| Tipo          | Pasta                     | Coluna        | Controllers                                  |
| ------------- | ------------------------- | ------------- | -------------------------------------------- |
| Foto do item  | `frontend/uploads/items/` | `items.image` | `create-item`, `update-item`, `delete-item`  |
| Foto do usuário | `frontend/uploads/users/` | `users.photo` | `update-photo`, `delete-account`             |

- **Fora do Git:** as pastas vão versionadas só com o `.gitkeep`; as fotos estão no `.gitignore`.
- **No banco vai só o nome do arquivo**, nunca o caminho.
- **Conferência** (`inspectItemImage`, usada também na foto do usuário):
  - sem arquivo enviado: a foto é opcional (no item) ou `REQUIRED_FIELD` (na foto do usuário);
  - `UPLOAD_ERR_INI_SIZE` / `UPLOAD_ERR_FORM_SIZE` ou mais de 5 MB → `IMAGE_TOO_LARGE`;
  - `is_uploaded_file` garante que o arquivo veio do upload;
  - o tipo é conferido **pelo conteúdo** (`finfo` + `getimagesize`), nunca pelo nome nem pelo tipo
    que o navegador informa: só `image/jpeg` e `image/png`.
- **Nome gerado pelo servidor:** `bin2hex(random_bytes(16))` + `.jpg` ou `.png`, salvo com
  `move_uploaded_file`. O nome enviado pelo usuário é ignorado.
- **Ordem segura:** a foto nova é apagada se o banco falhar; a antiga só é apagada depois que o
  banco aceitou a nova (ou confirmou a exclusão).
- **Apagar com segurança:** `deleteItemImageFile` e `deleteUserPhotoFile` só apagam nomes no
  formato `^[a-f0-9]{32}\.(jpg|png)$`, para que um valor estranho no banco nunca aponte para outro
  arquivo. `userPhotoUrl` aplica a mesma regra ao montar o caminho da imagem.
- **Limite do PHP:** no XAMPP o `upload_max_filesize` padrão é 2 MB; acima do `post_max_size`, o
  PHP descarta o pedido inteiro, e `itemRequestTooLarge()` transforma isso em `IMAGE_TOO_LARGE`.

## 9. Segurança que o código já tem

**Senhas e sessão**

- Senha salva só como hash (`password_hash(..., PASSWORD_DEFAULT)`) e conferida com `password_verify`.
- Senha nunca passa por `trim`, nunca vai para `old_input` e nunca vai para a sessão; o hash
  também não entra em `$_SESSION['user']`.
- Login com email inexistente e com senha errada devolve o mesmo código (`INVALID_CREDENTIALS`),
  para não revelar quais emails têm conta.
- `session_regenerate_id(true)` no login, na troca de senha e ao encerrar a sessão na exclusão da
  conta.
- Trocar a senha exige a senha atual.

**Acesso aos dados**

- Todas as queries usam **prepared statements** com parâmetros nomeados (nada de concatenar
  entrada do usuário no SQL).
- O id do usuário vem sempre da sessão (`requireLogin`), nunca do formulário.
- Toda query de itens e categorias filtra pelo `user_id`; um registro de outro usuário dá o mesmo
  erro que um registro inexistente (`ITEM_NOT_FOUND`, `CATEGORY_NOT_FOUND`), sem revelar que ele
  existe.
- A categoria escolhida num item é conferida como do usuário (`categoryBelongsToUser`).

**Validação da entrada**

- Toda entrada é checada no servidor, mesmo que o front já valide: tipo (`is_string`), `trim`,
  tamanho máximo pelo limite da coluna, formato (email, data, preço, URL).
- Ids aceitos só com `ctype_digit` e maiores que zero.
- Valores fechados por lista permitida: ícone e cor da categoria, decisão do item, cor principal.
- Links só `http`/`https`.
- Controllers de escrita aceitam só POST.

**Banco como segunda barreira**

- UNIQUE de email e de `product_id + user_id` (reviews): duplicidade em requisições simultâneas
  vira `EMAIL_ALREADY_EXISTS` / `REVIEW_ALREADY_EXISTS`.
- `ON DELETE RESTRICT` em `items.category_id`: categoria com itens não é apagada mesmo numa corrida.
- `WHERE status = 'thinking'` no `decideItem`: um item não é decidido duas vezes.
- Exclusão da conta numa transação: ou apaga tudo, ou nada.

**Erros**

- Detalhes de exceção vão só para o `error_log`, com o nome do controller (`[create-item] ...`).
  O usuário vê só o código genérico `UNKNOWN_ERROR`.

## 10. Limitações conhecidas

- **Recuperação de senha:** ainda não existe no back-end.
- **Logout:** ainda não existe controller de logout.
- **CSRF:** os formulários não enviam token CSRF; a proteção hoje é só exigir login e POST.
