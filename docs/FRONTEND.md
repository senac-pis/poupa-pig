# Front-end do Poupa Pig

Documentação do front-end: como `frontend/` se organiza, o que cada tela faz e espera do back-end,
os componentes reutilizáveis, o tema e a cor principal, como drawer e modal abrem pela URL, as
notificações e a escala de tamanhos do Figma.

> Tudo aqui descreve o código como ele está em `frontend/`. Se mudar o código, atualize este
> arquivo junto. O back-end está em [BACKEND.md](BACKEND.md).

## 1. Visão geral

- **PHP renderizando HTML no servidor.** Cada tela é um arquivo `.php` que monta os dados no topo
  e desenha o HTML embaixo. Não há framework de front nem build.
- **Tailwind CSS v4 pelo CDN**, com o tema do Figma em `components/theme.php` (ver seção 6).
- **Pouco JavaScript:** só onde HTML e CSS não resolvem: abrir os `<dialog>` (drawer e modal), o
  calendário do período, os seletores de ícone e cor e a troca da cor principal. Dropdowns simples
  usam `<details>`, e a notificação some só com CSS.
- **Estado na URL:** busca, filtros, página, mês, seção e o que está aberto (`?action=...`) vão
  como parâmetros GET. Recarregar ou compartilhar a URL abre a tela igual.
- **Leitura e escrita separadas:** as telas logadas leem o banco direto pelos models
  (`backend/models/`), e toda gravação é um POST para um controller em `backend/controllers/`,
  que volta para a tela com a mensagem em `$_SESSION['notification']`.
- **Toda entrada da URL é validada** antes de usar: números com `ctype_digit`, datas conferidas com
  `DateTime::createFromFormat('!Y-m-d', ...)`, textos cortados com `mb_substr(trim(...), 0, 100)`
  e valores fora de uma lista conhecida trocados pelo padrão. Tudo que é impresso passa por
  `htmlspecialchars`.
- **Banco fora do ar não quebra a tela:** as telas logadas buscam os dados dentro de um
  `try/catch`; no erro, o detalhe vai para o `error_log` e a tela abre vazia com o aviso genérico
  `UNKNOWN_ERROR`.

## 2. Organização de `frontend/`

```text
frontend/
├── index.php                 # landing page (pública)
├── pages/                    # uma tela por arquivo
│   ├── login.php
│   ├── register.php
│   ├── forgot-password.php   # recuperar acesso: e-mail
│   ├── verify-code.php       # recuperar acesso: código
│   ├── reset-password.php    # recuperar acesso: nova senha
│   ├── dashboard.php
│   ├── items.php
│   ├── categories.php
│   ├── review.php            # lista de produtos para avaliar
│   ├── product-reviews.php   # reviews de um produto
│   ├── settings.php
│   └── admin/                # vazia (.gitkeep)
├── components/               # pedaços de tela incluídos com include
│   ├── *.php                 # genéricos (header, tabela, drawer, modal, campos...)
│   ├── dashboard/            # blocos do Dashboard
│   ├── forms/                # conteúdo dos drawers de criar/editar
│   ├── reviews/              # blocos das telas de Review
│   ├── rows/                 # uma linha (<tr>) de cada tabela
│   └── settings/             # as seções de Configurações
├── assets/
│   ├── icons/                # SVGs, usados como máscara CSS
│   ├── images/               # porquinhos (pig-0 a pig-100, humores), hero.mp4, personagem
│   └── logos/                # logo.png e logo.svg
└── uploads/                  # fotos enviadas pelos usuários (fora do Git)
    ├── items/
    └── users/
```

- **`uploads/`:** quem grava é o back-end (`ItemService.php` e `AccountService.php`), com nome
  gerado por ele. O `.gitignore` ignora o conteúdo e mantém só os `.gitkeep`. Nas telas, o caminho
  chega pronto (`../uploads/items/<arquivo>` e `../uploads/users/<arquivo>`).
- **Caminhos relativos:** os componentes usam `../assets/...` porque são incluídos pelas telas de
  `pages/`. Os includes usam `__DIR__` (`include __DIR__ . '/../components/table.php';`).
- **Ícones como máscara:** os SVGs de `assets/icons/` são desenhados assim, para a cor vir de uma
  classe do Tailwind (e acompanhar a cor principal):

  ```php
  <span class="size-5 bg-secondary-strong"
      style="mask: url(../assets/icons/plus.svg) center / contain no-repeat; -webkit-mask: url(../assets/icons/plus.svg) center / contain no-repeat;"></span>
  ```

## 3. Páginas

Cada tela logada tem, no comentário do topo, um **contrato com o backend**: o que ela exige, de
onde vêm os dados, para onde os formulários enviam e como o back-end volta. Abaixo está o resumo
de cada um.

### Telas públicas

| Tela               | Arquivo                      | O que faz                                                         |
| ------------------ | ---------------------------- | ----------------------------------------------------------------- |
| Landing page       | `index.php`                  | Apresenta o Poupa Pig (hero com vídeo, problema, "Como funciona") e leva ao cadastro e ao login |
| Login              | `pages/login.php`            | Entrar com e-mail e senha                                         |
| Cadastro           | `pages/register.php`         | Criar conta com nome, e-mail e senha                              |
| Recuperar (e-mail) | `pages/forgot-password.php`  | Pede o e-mail para enviar o código                                |
| Recuperar (código) | `pages/verify-code.php`      | Pede o código de 6 dígitos                                        |
| Recuperar (senha)  | `pages/reset-password.php`   | Pede a nova senha e a confirmação                                 |

**Login** (`backend/controllers/login.php`)

- O form envia POST com `email` e `password`.
- No erro, volta para o login com `$_SESSION['notification']` (com `field` quando é erro de um
  campo; `INVALID_CREDENTIALS` aparece no aviso do canto) e `$_SESSION['old_input']['email']`.
- No sucesso, o back-end redireciona para a próxima tela.

**Cadastro** (`backend/controllers/register.php`)

- O form envia POST com `name`, `email` e `password`.
- No erro, volta com a notificação e `old_input` com `name` e `email` (a senha nunca volta).
- No sucesso, o back-end redireciona para o login.

**Recuperar acesso (três telas):** hoje têm **só o layout**. Os forms apenas passam de uma tela
para a outra (`forgot-password.php` → `verify-code.php` → `reset-password.php` → `login.php`), sem
back-end. As próximas etapas anotadas no topo de cada arquivo:

- **E-mail:** validar o e-mail no servidor; gerar um código de 6 dígitos com validade; enviar por
  e-mail; responder igual para e-mail cadastrado ou não (evita descobrir contas).
- **Código:** validar o código no servidor; conferir se é do e-mail e está na validade; limitar
  tentativas; mostrar a notificação "E-mail enviado!" ao chegar.
- **Nova senha:** só abrir depois do código validado; conferir se as duas senhas são iguais;
  salvar com `password_hash()`; invalidar o código e redirecionar para o login.

### Telas logadas

Todas chamam `requireLogin('login.php')` (de `backend/config/auth.php`) no topo: sem sessão, a
pessoa vai para o login. E todas só mostram dados do próprio usuário (o `id` da sessão vai em
toda consulta).

#### Dashboard — `pages/dashboard.php`

Mostra o mês: o porquinho (quanto a pessoa deixou de gastar), os números, os itens esperando
decisão e as últimas decisões.

| Parâmetro      | Valores                         | Efeito                                                       |
| -------------- | ------------------------------- | ------------------------------------------------------------ |
| `?month=`      | `2026-10`                       | Mês mostrado. Inválido, antes de 2000-01 ou no futuro: mês atual |
| `?action=`     | `summary`                       | Abre o drawer "Resumo de &lt;mês&gt;" (960px)                |
| `?status=`     | `gave_up`, `bought`, `thinking` | Aba do resumo (outro valor mostra todos)                     |
| `?page=`       | número                          | Página da tabela do resumo                                   |

Contrato com o back-end:

- `$items` vem de `listDashboardItems()` com **todos** os itens do usuário, no formato de
  `rows/item-row.php` mais `created_at` (data de inclusão) e `decided_at` (`Y-m-d` ou `null`).
- Todos os números saem de `$items` pela função `monthStats()` da própria página: o porquinho
  conta os itens **criados** no mês, a lista de decisões conta os **decididos** no mês. O % é
  `desistido ÷ desejado` no mês.
- "Comprei" e "Desisti" (em `dashboard/pending-list.php`) enviam POST para
  `backend/controllers/decide-item.php` com `id` e `decision` (`bought` ou `gave_up`). O back-end
  volta para o Dashboard com a mensagem na sessão.
- O dia de hoje vem do banco (`databaseToday()`), porque as datas de decisão são gravadas pelo
  MySQL.
- No mês atual aparece "Decida em breve" (`pending-list.php`); num mês que já passou, aparece o
  balanço do mês (`month-review.php`).

#### Itens — `pages/items.php`

Lista os desejos do usuário em tabela, com busca, filtros, paginação (10 por página) e
criar / editar / remover.

| Parâmetro                   | Efeito                                                           |
| --------------------------- | ---------------------------------------------------------------- |
| `?search=`                  | Busca pelo nome (até 100 caracteres)                             |
| `?category=`                | Filtra pela categoria (id de outro usuário é ignorado)           |
| `?start_date=` `?end_date=` | Filtra pelo período (`Y-m-d`; data inválida é ignorada)          |
| `?page=`                    | Página da lista                                                  |
| `?action=new`               | Abre o drawer "Novo item"                                        |
| `?action=edit&id=N`         | Abre o drawer "Editar item"                                      |
| `?action=remove&id=N`       | Abre o modal "Remover item"                                      |

Contrato com o back-end:

- `$items` e `$categories` vêm de `ItemModel`, já filtrados por busca, categoria, período e
  página. Cada item segue o formato de `components/rows/item-row.php`.
- O item do `?id=` é buscado direto no banco (pode estar em outra página da lista). Id inexistente
  ou de outro usuário: nada abre.
- Os forms enviam POST para `create-item.php`, `update-item.php` e `delete-item.php`.
- Depois de salvar, o back-end volta com a mensagem na sessão. No erro de formulário, volta em
  `?action=new` ou `?action=edit&id=N` com `field` na notificação e `old_input` com o que foi
  digitado (nunca a foto).
- No criar e editar, o nome sugere produtos (`listProductSuggestions()`): o catálogo, os que já
  têm review e os nomes dos itens da pessoa. As sugestões só são buscadas quando o drawer abre.

#### Categorias — `pages/categories.php`

Cards com os totais no topo, tabela das categorias com o % de itens de cada uma, busca,
paginação (10 por página) e criar / editar / remover. Não tem painel de filtros.

| Parâmetro             | Efeito                              |
| --------------------- | ----------------------------------- |
| `?search=`            | Busca pelo nome                     |
| `?page=`              | Página da lista                     |
| `?action=new`         | Drawer "Nova categoria"             |
| `?action=edit&id=N`   | Drawer "Editar categoria"           |
| `?action=remove&id=N` | Modal "Remover categoria"           |

Contrato com o back-end:

- `$categories` vem de `CategoryModel`, já filtrada por `?search` e `?page`. Cada categoria segue
  o formato de `components/rows/category-row.php`, com `item_count` e `item_percent` calculados
  (o % é sobre todos os itens do usuário, não só os da página).
- `$summary` traz os números dos cards (todas as categorias do usuário, sem a busca).
- Os forms enviam POST para `create-category.php`, `update-category.php` e
  `delete-category.php`. Erro de formulário volta como em Itens.
- **Categoria com itens não pode ser removida:** o modal troca a pergunta por um aviso ("tem N
  itens. Mova ou remova...") e mostra só o botão "Entendi". O `delete-category.php` também recusa
  (`CATEGORY_HAS_ITEMS`), porque o POST pode chegar direto.

#### Review — `pages/review.php`

Lista os produtos dos itens do usuário (com ou sem review), com a média e quantas pessoas
avaliaram. Cada linha leva à página do produto.

| Parâmetro  | Efeito                                                                |
| ---------- | --------------------------------------------------------------------- |
| `?search=` | Busca pelo nome do produto                                            |
| `?rating=` | 1 a 5: produtos com a média arredondada nessa nota (4 = de 3.5 até antes de 4.5) |
| `?page=`   | Página da lista (10 por página)                                       |

Contrato com o back-end:

- Antes de listar, `syncUserProducts()` cria os produtos dos itens que ainda não viraram produto
  (antigos ou renomeados).
- Cada produto segue o formato de `components/rows/review-row.php`.
- Os produtos são **compartilhados pelo nome do item**: as contagens e a média são das reviews de
  todos os usuários.
- O painel de filtros é o `components/reviews/rating-filters.php`, no lugar do `filters.php`.

#### Reviews do produto — `pages/product-reviews.php`

Resumo das notas do produto (média e distribuição) e as reviews de todos os usuários, paginadas.

| Parâmetro            | Efeito                                                     |
| -------------------- | ---------------------------------------------------------- |
| `?id=N`              | Produto (obrigatório)                                      |
| `?page=`             | Página das reviews (10 por página)                         |
| `?id=N&action=new`   | Abre o modal de nova review (só para quem pode avaliar)    |

Contrato com o back-end:

- `$ratingSummary` segue o formato de `components/reviews/rating-summary.php` e conta todas as
  reviews do produto. Cada review segue `components/reviews/review-card.php`.
- Quem não tem o item só abre produto do catálogo ou que já tem review. Os outros contam como
  inexistentes (não revelam o nome do item de ninguém) e voltam para `review.php`, assim como
  produto inexistente ou erro no banco.
- "Nova review" só aparece para quem tem um item com o nome do produto e ainda não avaliou.
- O modal envia POST para `backend/controllers/create-review.php` com `product_id`, `rating` e
  `text`. O back-end volta para esta tela com a mensagem na sessão.

#### Configurações — `pages/settings.php`

Menu de seções à esquerda (com busca pelo nome da seção) e a seção escolhida à direita.

| Parâmetro                            | Efeito                                         |
| ------------------------------------ | ---------------------------------------------- |
| `?section=`                          | `account` (padrão), `security` ou `appearance` |
| `?search=`                           | Filtra o menu de seções                        |
| `?section=account&action=edit-name`  | Troca o nome pelo campo de edição              |
| `?section=account&action=delete`     | Abre o modal "Excluir conta?"                  |

Contrato com o back-end:

- `$user` vem do banco (`findUserById()`) com `name`, `photo` (caminho pronto ou `''`) e
  `password_changed_days`. A tela também atualiza `name`, `photo` e `accent_color` na sessão,
  porque a sidebar e a cor principal leem de lá.
- Os forms enviam POST para `update-account.php` (nome), `update-password.php`,
  `update-photo.php` (enviado assim que o arquivo é escolhido) e `delete-account.php`.
- A cor principal vai por `fetch` para `update-accent-color.php` (ver seção 6).
- Erro de campo volta com `field` = `name`, `photo`, `current_password`, `new_password` ou
  `confirm_password`.
- Em Aparência, só o modo **Claro** funciona; **Escuro** e **Sistema** aparecem desabilitados
  ("Em breve").

## 4. Esqueleto das telas logadas

Todas as telas logadas seguem a mesma ordem de includes (exemplo de `pages/items.php`, resumido):

```php
<head>
    ...
    <?php include __DIR__ . '/../components/theme.php'; ?>
    <?php include __DIR__ . '/../components/accent-color.php'; ?>
</head>
<body class="flex gap-0.5 bg-background font-sans text-primary">
    <?php include __DIR__ . '/../components/notification.php'; ?>
    <?php include __DIR__ . '/../components/sidebar.php'; ?>

    <main class="flex h-screen min-w-0 flex-1 flex-col gap-[15px] rounded-2xl border border-black-200 px-5 py-2">
        <?php
        $pageTitle = 'Itens';
        $actionLabel = 'Novo item';
        $actionHref = '?' . http_build_query(array_merge($_GET, ['action' => 'new']));
        include __DIR__ . '/../components/page-header.php';

        $searchPlaceholder = 'Pesquisar itens...';
        include __DIR__ . '/../components/search-bar.php';

        $columns = ['Item', 'Categoria', 'Valor', 'Data de inclusão', 'Data final', 'Status', 'Ações'];
        $rows = $items;
        $rowTemplate = __DIR__ . '/../components/rows/item-row.php';
        $emptyMessage = 'Nenhum item cadastrado';
        include __DIR__ . '/../components/table.php';

        include __DIR__ . '/../components/pagination.php';
        ?>
    </main>

    <?php if ($showForm): ?> ... drawer.php ... <?php endif; ?>
    <?php if ($showRemove): ?> ... confirm-dialog.php ... <?php endif; ?>
</body>
```

- `theme.php` vem antes do `accent-color.php`, e os dois ficam no `<head>` para a cor certa já
  aparecer no primeiro desenho da tela.
- `notification.php` vem logo depois de abrir o `<body>`: além de mostrar o aviso, ele define o
  `$fieldError` que os formulários usam mais abaixo.
- As telas públicas incluem só `theme.php`, `notification.php` (login e cadastro), `logo.php` e
  `footer.php`.

## 5. Componentes reutilizáveis

Os componentes não são funções: são arquivos incluídos com `include`. **A tela define as variáveis
antes do include**, e o componente lê essas variáveis. Cada arquivo diz no topo o que precisa
(comentário "Antes do include: ..."). Componentes usados várias vezes na mesma tela (linhas,
campos, cards) recebem a variável montada de novo a cada uso, para não sobrar valor do anterior.

### Estrutura da tela

| Componente        | Antes do include                                                     | O que faz |
| ----------------- | -------------------------------------------------------------------- | --------- |
| `sidebar.php`     | `$activePage` = `dashboard`, `items`, `categories`, `review` ou `''` | Menu lateral (240px), logo, foto e nome do usuário (da sessão) e link para Configurações |
| `page-header.php` | `$pageTitle`; opcionais `$actionLabel` + `$actionHref` (botão) e `$backHref` (seta de voltar) | Título da tela (40px) e o botão de ação |
| `logo.php`        | opcional `$logoSize = 'small'` (sidebar); padrão é o grande          | "POUPA", o mascote e "PIG" (na cor principal) |
| `footer.php`      | —                                                                    | Rodapé das telas públicas |

### Lista, busca e filtros

| Componente                  | Antes do include                                                  | O que faz |
| --------------------------- | ----------------------------------------------------------------- | --------- |
| `search-bar.php`            | `$searchPlaceholder`; `$categories` (para o filtro); opcionais `$filtersEnabled = false` e `$filtersTemplate` | Busca pela URL (`?search=`, aplica com Enter) e botão de filtros ("Todos" ou "Filtros (N)"). Define `$searchTerm` |
| `filters.php`               | `$categories = [['id', 'name', 'count'], ...]`                    | Painel padrão: categoria e período (`?category`, `?start_date`, `?end_date`), aplica sozinho. Define `$activeFilters` |
| `reviews/rating-filters.php`| —                                                                 | Painel de Review: nota (`?rating=1..5`). Define `$activeFilters` |
| `table.php`                 | `$columns`, `$rows`, `$rowTemplate`, `$emptyMessage`              | Tabela com cabeçalho; inclui `$rowTemplate` para cada `$row`, ou mostra `$emptyMessage` |
| `pagination.php`            | `$currentPage`, `$totalPages`, `$shownItems`, `$totalItems`       | Paginação (primeira, última, vizinhas e "…"), mantendo os outros parâmetros da URL |
| `stat-card.php`             | `$statCard = ['label', 'value', 'icon', 'color']` (`pink`, `sky` ou `green`) | Card de número no topo |

Um painel de filtros novo precisa definir `$activeFilters` (quantos filtros estão aplicados),
porque o `search-bar.php` renderiza o painel antes e usa esse número no botão.

### Linhas de tabela (`rows/`)

Cada arquivo desenha um `<tr>` a partir do `$row` que o `table.php` passa, e escapa tudo o que
mostra. O formato do `$row` (que é o contrato com o back-end) está no topo de cada um:

| Arquivo            | Tela                 | Formato do `$row` |
| ------------------ | -------------------- | ----------------- |
| `item-row.php`     | Itens                | `id`, `name`, `image`, `price`, `link`, `start_date`, `end_date`, `status`, `category_id`, `category_name`, `category_icon`, `category_color` |
| `category-row.php` | Categorias           | `id`, `name`, `description`, `icon`, `color`, `item_count`, `item_percent` |
| `review-row.php`   | Review               | `id`, `name`, `user_count`, `review_count`, `average` |
| `decision-row.php` | Resumo do Dashboard  | o do `item-row.php` mais `created_at` e `decided_at` |

`status` chega como chave (`thinking`, `bought`, `gave_up`) e vira "Pensando", "Comprou" ou
"Desistiu". Datas chegam como `Y-m-d` e aparecem como `d/m/y`; valor inválido mostra "—".

Peças usadas dentro das linhas:

| Componente        | Antes do include                                                   | O que faz |
| ----------------- | ------------------------------------------------------------------ | --------- |
| `name-cell.php`   | `$cell = ['name', 'description', 'photo', 'icon', 'color']`        | Célula com foto (ou ícone da categoria na cor dela), nome e descrição, cortando nome longo |
| `row-actions.php` | `$rowActions = ['id', 'name']`                                     | Botões editar e remover: links para `?action=edit&id=N` e `?action=remove&id=N` |
| `rating.php`      | `$rating = ['value' => 3.6, 'size' => 'size-4']`                   | Nota em 5 "pillars", pintados pela porcentagem (3.6 = 3 cheios e o quarto em 60%) |

### Formulários e campos

| Componente               | Antes do include | O que faz |
| ------------------------ | ---------------- | --------- |
| `forms/item-form.php`    | `$itemForm = ['action', 'item' => $row ou null, 'categories', 'submitLabel']`; opcionais `old`, `error`, `productSuggestions` | Conteúdo do drawer de item. `item` null = novo (início já com hoje) |
| `forms/category-form.php`| `$categoryForm = ['action', 'category' => $row ou null, 'submitLabel']`; opcionais `old`, `error` | Conteúdo do drawer de categoria |
| `text-field.php`         | `$field = ['name', 'label', 'value', 'placeholder']`; opcionais `type`, `required`, `autofocus`, `attributes`, `error` | Label + input. `attributes` só aceita `maxlength`, `min`, `max`, `step`, `inputmode`, `autocomplete` e `list` |
| `date-period.php`        | `$datePeriod = ['start' => DateTime\|null, 'end' => DateTime\|null, 'background']`; opcionais `required`, `error` | Calendário de período (campos `start_date` e `end_date`); não deixa o fim antes do início |
| `icon-picker.php`        | `$iconPicker = ['value', 'label']`  | Seletor de ícone por grupos (campo `icon`) |
| `color-picker.php`       | `$colorPicker = ['value', 'label']` | Seletor de cor (campo `color`) |

Nos dois forms, voltando de um erro, o que foi digitado (`old`) vale mais que o valor salvo, e
`error` é o `$fieldError` do `notification.php`. O back-end valida tudo de novo.

### Drawer e modais

| Componente           | Antes do include | O que faz |
| -------------------- | ---------------- | --------- |
| `drawer.php`         | `$drawerTitle`, `$drawerTemplate` (arquivo com o conteúdo); opcional `$drawerWidth` (padrão `w-[480px]`) | Painel lateral à direita |
| `confirm-dialog.php` | `$confirmDialog = ['title', 'name', 'id', 'action']`; opcionais `blocked`, `message`, `confirmLabel` | Modal de confirmação. `blocked` troca a pergunta por um aviso com só "Entendi"; sem `id`, o POST vai sem id (excluir conta) |
| `review-dialog.php`  | `$reviewDialog = ['productId', 'action']` | Modal de nova review: nota de 1 a 5 obrigatória, resenha opcional |

Como eles abrem e fecham está na seção 7.

### Blocos de tela

- **`dashboard/`:** `month-picker.php` (troca de mês por links e `<details>`), `piggy-card.php`
  (imagem do porquinho pelo nível arredondado para baixo, de 20 em 20, e selo de humor),
  `month-stats.php` (cards Desejado, Pensando, Comprado...), `pending-list.php` (3 itens mais
  perto do prazo, com Comprei/Desisti), `month-review.php` (balanço de mês passado),
  `decision-list.php` (4 decisões mais novas) e `month-summary.php` (conteúdo do drawer de
  resumo: gráfico de 6 meses, desejos por categoria e tabela com abas por status). Recebem
  `$stats` da `monthStats()` e as variáveis de mês da página.
- **`reviews/`:** `rating-summary.php` (média e barras por nota, nos tons da cor principal),
  `review-card.php` (uma review) e `rating-filters.php` (painel de filtro).
- **`settings/`:** `account-section.php` (foto, nome, excluir conta), `security-section.php`
  (trocar senha; a nova segue a regra do cadastro, mínimo de 8) e `appearance-section.php` (modo
  e cor principal).

## 6. Tema e cor principal

### `components/theme.php`

Incluído no `<head>` de **todas** as telas. Carrega:

- a fonte **Google Sans Flex** (Google Fonts);
- o **Tailwind CSS v4 pelo CDN** (`@tailwindcss/browser@4`);
- o tema num `<style type="text/tailwindcss">` com `@theme static`.

O tema traz a paleta do Figma, com o mesmo nome do Figma (`pink/400` no Figma = `pink-400` aqui):
`pink`, `blue`, `sky`, `green`, `red`, `purple`, `gray`, `black` e `white`, cada uma de 100 a
500, mais `blue-alpha-500` (o fundo escurecido atrás do drawer e do modal).

Tokens da identidade visual:

| Token                         | Valor padrão  | Uso |
| ----------------------------- | ------------- | --- |
| `primary`                     | `blue-500`    | Textos, bordas e títulos |
| `secondary`                   | `pink-400`    | Cor principal: botão principal, menu ativo, títulos das telas, "pig" da logo |
| `secondary-100` a `secondary-500` | `pink-100` a `pink-500` | Tons claros e escuros da cor principal (hover, botões claros) |
| `secondary-strong`            | `pink-400`    | Texto e ícone dos botões claros (fundo `secondary-200`) |
| `background`                  | `white-200`   | Fundo da tela e texto dos botões |
| `font-sans`                   | Google Sans Flex | Fonte de tudo |
| `shadow-card`                 | `2px 2px 8px 0 rgba(9, 30, 66, 0.25)` | Sombra de cards e listas |

Use sempre `secondary` (e os tons dele) para o que deve seguir a cor do usuário. `pink-*` fixo só
onde a cor não deve mudar (como os cards de estatística).

O `static` no `@theme` faz o Tailwind gerar **todas** as cores, mesmo as que nenhuma classe usa:
o `accent-color.php` troca a cor principal por `var(--color-green-400)`, por exemplo, e essa
variável precisa existir.

### `components/accent-color.php`

Incluído no `<head>` das telas logadas, logo depois do `theme.php`. Aplica a cor escolhida em
Configurações > Aparência antes da tela aparecer.

- A cor vem de `users.accent_color`, guardada na sessão pelo login, pela tela de Configurações e
  pelo `update-accent-color.php`. Sem cor salva, fica o rosa padrão.
- As cores aceitas vêm de `accentColorOptions()` (`AccountService.php`): os tons **300, 400 e
  500** das famílias de `color_groups` em `backend/config/categories.php` (sky, pink, purple, green
  e red). Valor fora da lista é ignorado.
- A função JS `applyAccentColor('green-400')` troca as variáveis CSS no `<html>`:
  `--color-secondary` vira a cor escolhida, `--color-secondary-100` a `-500` viram os tons da
  família e `--color-secondary-strong` vira o mesmo tom, **menos no 300**, que usa o 500 da família
  (o 300 some no fundo 200).
- Em Aparência (`settings/appearance-section.php`), clicar numa cor já aplica e salva, sem botão
  de salvar (como no Figma): faz `fetch` POST para `update-accent-color.php`. Se o servidor
  recusar, volta para a cor anterior.

### Ícones e cores das categorias

Os ícones e as cores permitidos estão em `backend/config/categories.php`. O banco guarda só a
chave (`icon = 'smartphone'`, `color = 'sky-200'`), e o front transforma em visual:

- ícone: `assets/icons/<chave>.svg`;
- cor: o tom escolhido é o fundo; o tom do ícone vem de `icon_tones` (300 e 400 = ícone branco) e o
  da barra de % vem de `bar_tones`;
- valor fora da lista usa o primeiro (padrão): `box-seam` e `gray-200`, que não aparecem nos
  seletores.

Para adicionar um ícone, coloque o SVG em `assets/icons/` e a chave no grupo dele em
`categories.php`.

## 7. Drawer e modal pela URL

Drawer e modal **não abrem por JavaScript ao clicar**: o link muda a URL, a tela recarrega e o PHP
decide se inclui o `<dialog>`.

| URL                                  | Tela          | Abre |
| ------------------------------------ | ------------- | ---- |
| `?action=new`                        | Itens, Categorias | Drawer de criar |
| `?action=edit&id=N`                  | Itens, Categorias | Drawer de editar |
| `?action=remove&id=N`                | Itens, Categorias | Modal de remover |
| `?month=2026-10&action=summary`      | Dashboard     | Drawer do resumo do mês |
| `?id=N&action=new`                   | Reviews do produto | Modal de nova review |
| `?section=account&action=delete`     | Configurações | Modal de excluir conta |
| `?section=account&action=edit-name`  | Configurações | Campo de nome (não é dialog) |

Como funciona:

1. **Abrir:** os links montam a URL mantendo o que já está nela (busca, filtros, página), com
   `http_build_query(array_merge($_GET, ['action' => 'edit', 'id' => $id]))`. Assim a lista atrás
   continua igual.
2. **Decidir:** a tela valida `action` e `id` e busca o registro **direto no banco, do usuário
   logado**. Id inexistente ou de outro usuário: nada abre. Só então inclui `drawer.php`,
   `confirm-dialog.php` ou `review-dialog.php`.
3. **Mostrar:** o componente desenha um `<dialog>` e chama `dialog.showModal()`, que já dá o fundo
   escurecido, o foco preso dentro dele, o Esc e o resto da tela ignorado. Enquanto está aberto, a
   tela atrás não rola (`body:has(dialog[open]) { overflow: hidden; }`).
4. **Fechar:** o Esc, o clique no fundo, a seta Voltar e o Cancelar levam para a mesma URL **sem
   `action` e `id`** (`array_diff_key($_GET, ['action' => '', 'id' => ''])`), mantendo busca,
   filtros e página. No modal de review, só o `action` sai (o `id` é o do produto).
5. **Gravar:** os links só abrem. Quem cria, edita ou apaga é sempre o **POST do formulário** para
   o controller. No modal de remover, o foco começa no Cancelar, para um Enter sem querer não
   apagar nada.

## 8. Notificações e erros de formulário

O back-end avisa o resultado deixando na sessão:

```php
$_SESSION['notification'] = [
    'type'  => 'error',         // 'error' ou 'success'
    'code'  => 'INVALID_EMAIL', // chave do backend/config/errors.php ou success.php
    'field' => 'email',         // opcional: input com problema
];
```

O `components/notification.php` (incluído logo depois de abrir o `<body>`):

1. lê a notificação e **apaga da sessão**, para aparecer uma vez só;
2. busca a mensagem pelo `code` em `errors.php` ou `success.php`. Erro com código desconhecido
   mostra a mensagem de `UNKNOWN_ERROR`; sucesso com código desconhecido não mostra nada;
3. **sem `field`:** mostra o aviso no canto superior direito, com o porquinho feliz (sucesso,
   borda verde) ou bravo (erro, borda vermelha). Ele entra pela direita e sai sozinho depois de
   5 segundos, **só com CSS**. Quem pediu menos movimento no sistema (`prefers-reduced-motion`) vê
   o aviso aparecer e sumir sem deslizar;
4. **com `field`:** não mostra o aviso no canto. Define
   `$fieldError = ['field' => 'email', 'message' => '...']` para a tela mostrar a mensagem
   embaixo do input certo (os forms recebem como `'error' => $fieldError`). Sem `field`,
   `$fieldError` é `null`.

**O que foi digitado:** num erro de formulário, o back-end também deixa
`$_SESSION['old_input']` com os valores enviados. A tela lê uma vez e apaga
(`unset($_SESSION['old_input'])`), e o form preenche os campos com eles. Senha e foto nunca voltam.

As telas também usam a mesma notificação para o próprio erro: se o banco falhar ao montar a tela,
gravam `['type' => 'error', 'code' => 'UNKNOWN_ERROR', 'field' => '']`.

## 9. Tailwind e a escala do Figma

- **Tailwind v4 pelo CDN** (`@tailwindcss/browser@4`): o navegador gera o CSS na hora, a partir das
  classes da página. Não tem `npm`, build nem arquivo `.css` para compilar.
- Classes com valor fora da escala do Tailwind usam colchetes: `text-[40px]`, `w-[480px]`,
  `rounded-[48px]`.
- As cores do tema viram classes normais: `bg-secondary`, `text-primary`, `bg-secondary-200`,
  `text-secondary-strong`, `backdrop:bg-blue-alpha-500`.
- Estados de acessibilidade também ficam em classe: `focus-visible:ring-2`,
  `aria-pressed:border-[7px]`, `aria-disabled:opacity-50`, `has-[:checked]:text-secondary`.

**O Figma foi desenhado grande demais** e não vai ser refeito. Ao implementar qualquer tela,
**não copie os tamanhos do Figma**: converta para a escala abaixo, já aplicada nas telas
existentes.

| Elemento                    | Figma                  | Usar                                |
| --------------------------- | ---------------------- | ----------------------------------- |
| Título da página            | 64px                   | 40px (`text-[40px]`)                |
| Título de drawer            | 48px                   | 32px (`text-[32px]`)                |
| Título de modal             | 48px                   | 28px (`text-[28px]`)                |
| Menu da sidebar             | 32px, largura 307px    | 22px, largura 240px                 |
| Texto de botão / busca      | 20px                   | 16px (`text-base`)                  |
| Label e input de formulário | 24px                   | 16px (`text-base`)                  |
| Cabeçalho de tabela         | 20px, altura 50px      | 16px, altura 40px (`h-10`)          |
| Linhas de tabela            | 18px                   | 14px (`text-sm`)                    |
| Textos de filtro            | 24px                   | 18px (`text-lg`)                    |
| Botão principal             | 200 × 68px, texto 24px | 144 × 44px (`w-36 h-11`), `text-lg` |
| Ícones de botão             | 25px                   | 20px (`size-5`)                     |
| Ícone de item/categoria     | 50px                   | 40px (`size-10`)                    |
| Paddings e gaps de 30px     | 30px                   | 16–24px (`gap-4`, `p-6`)            |
| Paddings de 20px            | 20px                   | 10–16px (`py-2.5`, `px-4`)          |
| Drawer                      | 640px                  | 480px                               |
| Painel de filtros           | 571px                  | 400px                               |

Regra geral para o que não estiver na tabela: **~65–75% do tamanho do Figma**, arredondando para a
classe do Tailwind mais próxima. Na dúvida, compare com o componente equivalente que já existe em
`frontend/components/`.

## 10. Checklist para uma tela nova

1. Comentário no topo com o nome da tela, o frame do Figma e o **contrato com o backend**.
2. Tela logada: `requireLogin('login.php')` no topo e `$activePage` para a sidebar.
3. Validar cada parâmetro da URL antes de usar (seção 1).
4. Buscar os dados dentro de `try/catch`, com `error_log` e a notificação `UNKNOWN_ERROR` no erro.
5. Seguir o esqueleto da seção 4 e reaproveitar os componentes da seção 5, em vez de copiar HTML.
6. Drawer e modal abrindo pela URL (seção 7); gravação sempre por POST para um controller.
7. Escapar com `htmlspecialchars` tudo o que vem do banco ou da URL.
8. Usar `secondary` para o que segue a cor do usuário e a escala da seção 9 para os tamanhos.
