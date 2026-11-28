# AGENTS.md

Instruções para agentes de IA (Claude Code, Codex, Copilot etc.) que trabalham neste repositório.
Pessoas da equipe também podem usar este arquivo como resumo das regras do projeto.

## O projeto

O **Poupa Pig** ajuda a evitar compra por impulso: a pessoa cadastra o que quer comprar, o item
fica numa quarentena de alguns dias e só depois ela decide se **comprou** ou **desistiu**. O app
tem itens, categorias, dashboard mensal, reviews de produtos e configurações da conta.

É o Projeto Integrador do Senac (repositório `senac-pis/poupa-pig`).

## Stack

- **PHP** puro, sem framework e sem Composer. Páginas renderizadas no servidor.
- **MySQL** (via XAMPP/phpMyAdmin), acessado com **PDO** e prepared statements.
- **Tailwind CSS v4 via CDN** (`@tailwindcss/browser`), com o tema em
  `frontend/components/theme.php`. Não há build, `package.json` nem `node_modules`.
- **JavaScript** puro, em `<script>` dentro dos componentes que precisam (drawer, diálogos,
  filtros, seletores).

## Como rodar localmente

1. Instale o XAMPP e inicie **Apache** e **MySQL**.
2. Coloque o repositório em `htdocs` (ex.: `C:\xampp\htdocs\poupa-pig`).
3. No phpMyAdmin (`http://localhost/phpmyadmin`), importe nesta ordem:
   1. `database/database.sql` (cria o banco `projeto_integrador` e as tabelas);
   2. `database/seeds/products.sql` (catálogo de produtos do autocompletar; pode rodar de novo).
4. Copie `backend/config/database.example.php` para `backend/config/database.php` e ajuste os
   dados. O nome do banco é `projeto_integrador`. O `database.php` está no `.gitignore`: nunca
   versione.
5. Acesse `http://localhost/poupa-pig/` (o `index.php` da raiz redireciona para `frontend/`).

Sem o Apache do XAMPP, dá para subir com o servidor embutido do PHP, rodando na raiz do projeto
(funciona em Windows, macOS e Linux com o PHP no PATH; no Windows, o do XAMPP fica em
`C:\xampp\php\php.exe`). O MySQL continua necessário. Depois, acesse `http://localhost:8000/`.

```bash
php -S localhost:8000
```

## Estrutura de pastas

```text
index.php              → redireciona para frontend/
frontend/
  index.php            → landing page
  pages/               → uma tela por arquivo (login, items, categories, dashboard, settings...)
  components/          → pedaços reutilizáveis incluídos com include (drawer, table, sidebar...)
    forms/ rows/ dashboard/ reviews/ settings/  → componentes agrupados por tela ou tipo
  assets/              → icons/ (SVG), images/, logos/
  uploads/             → fotos enviadas (items/, users/); só a pasta vai para o Git
backend/
  config/              → conexão, proteção de login, mapas de erros e de sucesso, listas fixas
  controllers/         → recebem os POST dos formulários (um arquivo por ação)
  services/            → regras de negócio, validação e redirects (sem SQL)
  models/              → só queries no banco (sem regra de negócio)
database/
  database.sql         → estrutura completa do banco
  seeds/               → dados iniciais
docs/                  → documentação do projeto
.github/               → templates de issue e de pull request
```

## Como o código está organizado

### Fluxo de uma ação

```text
página (frontend/pages)  --POST-->  controller  →  service (valida)  →  model (SQL)
          ^                                                                  |
          └-------------- redirect com $_SESSION['notification'] -----------┘
```

- O fluxo é sempre **POST → redirect → GET**. O controller nunca renderiza HTML.
- **Controller** (`backend/controllers/kebab-case.php`, ex.: `create-category.php`): exige login,
  aceita só `POST`, lê e valida a entrada com o service, chama o model dentro de `try/catch` e
  redireciona.
- **Service** (`backend/services/XxxService.php`): leitura do `$_POST`, validação, cálculos e
  funções de redirect. Não conversa com o banco.
- **Model** (`backend/models/XxxModel.php`): só queries. Toda função recebe `$pdo` e, quando o dado
  é de um usuário, `$userId`. Falha do banco vira exceção para o controller tratar.
- **Página** (`frontend/pages/`): exige login, valida os parâmetros da URL, busca os dados pelo
  model e inclui os componentes.
- **Componente** (`frontend/components/`): recebe variáveis definidas antes do `include`
  (documentadas no topo do arquivo, com `/** @var ... */`).

### Erros e mensagens

- Os erros ficam no mapa `backend/config/errors.php` e os sucessos em `backend/config/success.php`.
  O backend devolve só o **código** (`INVALID_NAME`, `ITEM_NOT_FOUND`...); o front mostra a
  mensagem pelo componente `notification.php`.
- Formato na sessão: `$_SESSION['notification'] = ['type' => 'error', 'code' => ..., 'field' => ...]`
  e `$_SESSION['old_input']` com o que foi digitado (nunca a senha).
- Um código combinado **não muda de nome**, só a mensagem. Erro novo entra no mapa com um
  comentário dizendo onde é usado.
- Exceção nunca chega ao usuário: `error_log('[nome-do-controller] ' . $e->getMessage())` e
  redirect com `UNKNOWN_ERROR`.

### Padrões de PHP

- Funções declaradas com `function`, em **camelCase** (`categoryInputError`, `findCategoryById`),
  com tipos nos parâmetros e no retorno.
- Constantes em `UPPER_SNAKE_CASE` com `const` (`CATEGORY_NAME_MAX_LENGTH`).
- Indentação de 4 espaços, aspas simples, arrays com `[]`.
- Arquivos só de PHP (backend) não fecham a tag `?>`.
- Todo arquivo começa com um docblock dizendo o que faz, o que recebe e para onde volta.
  Funções de model e service documentam `Recebe`, `Retorna` e `Lança` quando não é óbvio.
- `require_once __DIR__ . '/../...'` com caminho relativo ao próprio arquivo.

### Padrões de front

- Tailwind direto nas classes, usando as cores do tema (`bg-primary`, `text-secondary`,
  `pink-100`...). Não crie arquivo `.css` separado.
- Antes de criar um componente, procure um parecido em `frontend/components/` e reaproveite.
- Todo dado dinâmico impresso no HTML passa por `htmlspecialchars()`.
- Drawer e modal abrem pela URL (`?action=new`, `?action=edit&id=5`, `?action=remove&id=5`).
- Nome longo é cortado com `truncate` (e o texto completo no `title`).
- Não adicione botão, ícone, coluna ou texto que não esteja no Figma sem combinar com a equipe.

### Nomes e idioma

- Código (arquivos, funções, variáveis, tabelas, colunas) em **inglês**.
- Textos da interface, comentários, docs, commits, issues e PRs em **português**.

## Segurança (nunca cortar por simplicidade)

- Toda entrada externa (`$_POST`, `$_GET`, `$_FILES`, cookies) é tratada como maliciosa: confira o
  tipo (`is_string`, `ctype_digit`), faça `trim` e valide tamanho e formato antes de usar.
- SQL só com **prepared statements** e parâmetros nomeados. Nunca concatene entrada do usuário
  na query. Em `LIKE`, escape `%` e `_` com `addcslashes`.
- O id do usuário vem **sempre da sessão** (`requireLogin()` em `backend/config/auth.php`), nunca
  do formulário ou da URL. Toda query de dado do usuário filtra por `user_id`.
- Registro de outro usuário e registro inexistente devolvem o mesmo erro (`*_NOT_FOUND`).
- Senhas com `password_hash()` / `password_verify()`.
- Upload: tipo conferido pelo conteúdo do arquivo (não pela extensão), limite de tamanho, e a
  pasta `frontend/uploads/` bloqueia execução de scripts (`.htaccess`).
- Nada de credencial no repositório.

## Banco de dados

- `database/database.sql` é a fonte da estrutura. Mudou uma tabela ou coluna? Atualize o arquivo
  no mesmo PR e confira se os limites do service batem com o banco (ex.: `varchar(120)` ↔
  `CATEGORY_NAME_MAX_LENGTH = 120`).
- Dados iniciais ficam em `database/seeds/`, escritos para poder rodar mais de uma vez.
- Não exporte dados pessoais reais.

## Escala do Figma

O Figma do Poupa Pig foi desenhado grande demais e não vai ser refeito. Ao implementar qualquer
tela, **não copie os tamanhos do Figma**: converta para a escala abaixo, já aplicada nas telas
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

Para o que não estiver na tabela: **~65–75% do tamanho do Figma**, arredondando para a classe do
Tailwind mais próxima. Na dúvida, compare com o componente equivalente em `frontend/components/`.

## Git: branch, commit e PR

Fluxo da equipe (detalhes em `docs/GIT_WORKFLOW.md`):

```text
Issue → Branch → Desenvolvimento → Commit → Push → Pull Request → Review → Merge
```

- **Branch** a partir da `main` atualizada: `feature/<tela>-front`, `feature/<tela>-backend`,
  `fix/<assunto>` (ex.: `feature/reviews-backend`, `fix/long-names`).
- **Commits** no padrão Conventional Commits, em português, com o verbo no presente
  ("adiciona", "corrige") e o escopo da tela:
  - `feat(categories): adiciona model, regras e controllers de criar, editar e remover`
  - `fix(items): mostra os erros no drawer e corrige nome longo na lista`
  - tipos usados: `feat`, `fix`, `refactor`, `docs`, `chore`.
- Separe commits por parte (ex.: backend de um lado, front de outro).
- **PR** usando `.github/PULL_REQUEST_TEMPLATE.md`, com `Closes #N` da issue e o passo a passo para
  validar no XAMPP. Merge só depois do review.
- Nunca `git push --force` em branch compartilhada.

## Como um agente deve trabalhar aqui

1. **Leia antes de escrever.** Todo arquivo novo segue o padrão dos vizinhos (outro controller,
   outro model, outro componente). Na dúvida, abra o equivalente que já existe.
2. **Proponha antes de codar.** Mostre a abordagem, os arquivos afetados e as mudanças previstas e
   espere aprovação. Decisões de design ou de regra de negócio são da equipe.
3. **Código simples.** Prefira o código explícito e fácil de ler ao "esperto". Sem abstração
   especulativa: se algo vai precisar crescer, sugira em vez de já construir.
4. **Mudança pequena e focada.** Só o que foi pedido; problemas vistos no caminho viram sugestão.
5. **Commit, push e PR só com aprovação** de alguém da equipe.

## O que não fazer

- Não adicionar framework, Composer, npm, build de CSS ou biblioteca nova sem combinar.
- Não colocar SQL em página, controller ou service: SQL só em `backend/models/`.
- Não colocar regra de negócio no model nem HTML no controller.
- Não confiar em id vindo do formulário ou da URL para saber de quem é o dado.
- Não mostrar mensagem de exceção ou detalhe do banco para o usuário.
- Não renomear códigos de erro ou de sucesso já combinados.
- Não copiar tamanhos do Figma sem passar pela escala acima.
- Não versionar `backend/config/database.php`, fotos de `frontend/uploads/` nem arquivos de
  ambiente (`.env`, `.DS_Store`).
- Não mexer na estrutura do banco sem atualizar `database/database.sql`.
