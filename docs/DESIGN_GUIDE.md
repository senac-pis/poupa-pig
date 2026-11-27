# Design Guide

> Explica como a identidade do Poupa Pig vira interface: escala de tamanhos, tokens, componentes
> e ícones. Logo, mascote, paleta e tipografia estão no
> [Manual de Identidade Visual](IDENTIDADE_VISUAL.md).

## Base técnica

- **Tailwind CSS v4** via CDN (`@tailwindcss/browser@4`), sem etapa de build.
- O tema (cores, fonte, sombra) fica num lugar só: `frontend/components/theme.php`, incluído no
  `<head>` de todas as páginas. As telas logadas incluem logo depois o `accent-color.php`, que
  aplica a cor principal do usuário.
- Os componentes são arquivos PHP em `frontend/components/`. A página prepara uma variável
  (ex.: `$field`, `$confirmDialog`) e faz `include`. O comentário no topo de cada arquivo diz o
  que ele espera receber.
- Referência visual: [Figma do Poupa Pig](https://www.figma.com/design/m9gI8kevph97sNSiFql9Ki/poupa-pig?node-id=0-1&t=AKTjgEf1Ks2EAlQu-1).

## Escala do Figma

O Figma foi desenhado grande demais e não vai ser refeito. Ao implementar uma tela, **não copie os
tamanhos do Figma**: converta para esta escala, já aplicada em todas as telas.

| Elemento                    | Figma                  | Sistema                             |
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

Regra geral para o que não estiver na tabela: **~65–75% do tamanho do Figma**, arredondando para
a classe do Tailwind mais próxima. Na dúvida, compare com o componente equivalente que já existe.

## Tokens

### Cores

Use os nomes de papel (`primary`, `secondary`, `background`...), descritos no
[Manual de Identidade Visual](IDENTIDADE_VISUAL.md#cores-de-papel). Destaque sempre com
`secondary-*`, nunca com `pink-*`, para acompanhar a cor principal que o usuário escolheu.

| Situação                    | Classes                                         |
| --------------------------- | ----------------------------------------------- |
| Fundo da tela               | `bg-background`                                 |
| Superfície (card, input)    | `bg-white-100`                                  |
| Texto e contorno            | `text-primary`, `border-primary`                |
| Borda de tabela/paginação   | `border-secondary`                              |
| Fundo atrás de drawer/modal | `backdrop:bg-blue-alpha-500`                    |
| Placeholder                 | `placeholder:text-black-200` (busca: `gray-200`) |

### Bordas arredondadas

| Raio                            | Onde                                              |
| ------------------------------- | ------------------------------------------------- |
| `rounded-[32px]` / `rounded-[48px]` | botões e barra de busca (formato de pílula)   |
| `rounded-2xl`                   | cards, tabela, modal, painéis, sidebar, notificação |
| `rounded-xl`                    | ícone e foto de item/categoria                    |
| `rounded-lg`                    | inputs, paginação, botões de ação da linha        |
| `rounded-full`                  | foto do usuário, botões redondos de ícone         |

### Sombra

- `shadow-card` (`2px 2px 8px rgba(9, 30, 66, 0.25)`): painéis que flutuam sobre a tela
  (filtros, seletor de cor, seletor de ícone).
- A notificação usa uma sombra própria, mais curta, como no Figma.

### Foco e estados

- **Foco visível em tudo que é clicável:** `focus-visible:ring-2` com `ring-secondary` ou
  `ring-primary` (o que contrastar com o fundo do elemento).
- **Hover:** botão cheio escurece (`hover:brightness-90`), botão claro escurece pouco
  (`hover:brightness-95`), botão de contorno ganha fundo `blue-100`.
- **Transição:** `transition` padrão do Tailwind.

## Componentes

Cada componente existe uma vez só em `frontend/components/`. Uma tela nova usa os que já
existem e não recria o visual.

### Botões

| Tipo              | Aparência                                                          | Exemplo                       |
| ----------------- | ------------------------------------------------------------------ | ----------------------------- |
| Principal         | `w-36 h-11`, `rounded-[48px]`, `bg-secondary`, texto `background`, `text-lg` | Entrar, Salvar     |
| Claro             | `rounded-[32px]`, `bg-secondary-200`, texto `secondary-strong` bold, ícone `size-5` | Novo item, filtros |
| Contorno          | mesmo tamanho do principal, `border-primary`, texto `primary`      | Cancelar                      |
| Perigo            | mesmo tamanho do principal, `bg-red-400`, `hover:bg-red-500`       | Remover                       |
| Neutro            | `h-9`, `rounded-[48px]`, `bg-sky-100`, texto `sky-400`             | Selecionar (seletores)        |
| Ação da linha     | quadrado `size-7`, `rounded-lg`, borda `primary` (editar) ou `red-400` (remover) | `row-actions.php` |
| Voltar            | ícone de seta `size-9` na cor `secondary`                          | drawer, telas de detalhe      |

Botão só com ícone sempre tem `aria-label` e `title`.

### Formulário

- **Campo de texto** (`text-field.php`): label `text-base` bold em cima, input `bg-white-100`,
  `rounded-lg`, `px-4 py-2`, foco com `ring-secondary`.
- **Erro no campo:** o input ganha `ring-1 ring-red-400` e a mensagem aparece embaixo em
  `text-sm text-red-400`, ligada por `aria-describedby`. O foco vai para o campo com erro.
- **Seletor de cor** (`color-picker.php`) e **seletor de ícone** (`icon-picker.php`): caixa com
  borda `primary` que abre um painel `shadow-card`. As opções aparecem em grupos e a escolhida
  ganha um check.
- Os formulários completos ficam em `components/forms/` (item e categoria).

### Busca e filtros

- **Barra de busca** (`search-bar.php`): pílula com borda `primary`, ícone de lupa e texto bold.
  Aplica com Enter.
- **Botão de filtros:** botão claro que mostra "Todos" ou "Filtros (n)" e abre um painel de 400px
  (`filters.php`) com os filtros de categoria e período.

### Tabela

- `table.php`: borda `secondary`, `rounded-2xl`, cabeçalho fixo `bg-secondary-200` (`h-10`,
  16px bold) e linhas `text-sm`.
- Com muitas linhas, a tabela rola por dentro e o cabeçalho fica parado.
- Sem dados, mostra uma mensagem centralizada (ex.: "Nenhum item cadastrado").
- Cada tela tem seu modelo de linha em `components/rows/`.
- **Célula de nome** (`name-cell.php`): foto ou ícone de 40px, nome e descrição. Nome longo é
  cortado com "..." e aparece inteiro ao passar o mouse.
- **Paginação** (`pagination.php`): barra com borda `secondary` embaixo da tabela, página atual
  na cor principal.

### Drawer

- `drawer.php`: painel lateral à direita, 480px, fundo `background`, título 32px na cor principal
  com a seta de voltar. Usado para criar e editar.
- Abre pela URL (`?action=new`, `?action=edit&id=5`), por isso o link pode ser compartilhado e o
  botão voltar do navegador funciona.
- Fecha com Esc, com a seta ou clicando no fundo. O foco fica preso dentro do drawer enquanto
  ele está aberto.

### Modal de confirmação

- `confirm-dialog.php`: caixa central de até 400px, título 28px em `red-400`, a pergunta e os
  botões Remover (perigo) e Cancelar (contorno).
- O foco começa no **Cancelar**, para um Enter sem querer não apagar nada.
- Quando a ação não é permitida, o modal mostra só o aviso e um botão **Entendi**.

### Notificação

- `notification.php`: aviso no canto superior direito, fundo `#f5f5f5`, borda de cima colorida
  (verde no sucesso, vermelha no erro) e o mascote (`pig-happy` ou `pig-angry`).
- Entra deslizando pela direita e some sozinha depois de 5 segundos. Para quem pediu menos
  movimento no sistema, aparece e some sem deslizar.
- Quando o erro é de um campo, não aparece no canto: a mensagem vai para baixo do campo.

### Estrutura das telas logadas

- **Sidebar** (`sidebar.php`): 240px, logo pequena, menu de 22px bold (o item ativo fica com
  `bg-secondary` e texto branco) e, embaixo, a foto e o nome do usuário com o link de
  Configurações.
- **Cabeçalho da página** (`page-header.php`): título de 40px na cor principal e, se a tela tiver,
  o botão claro de ação à direita.
- **Cards de número** (`stat-card.php`): cards de 112px de altura em `pink-100`, `sky-100` ou
  `green-100`, com o rótulo, o número em 28px thin e o ícone grande cortado no canto.
- **Dashboard** (`components/dashboard/`): porquinho do mês, selo de humor, resumo e listas.
- **Reviews** (`rating.php`): a nota é mostrada em 5 "pillars" (ícone de pilar), preenchidos
  pela porcentagem da nota.

## Ícones

- Todos os ícones são SVG em `frontend/assets/icons/`, tirados do frame `icons` do Figma.
- **Ícone em botão ou com cor:** o SVG é aplicado como máscara (`mask: url(...)`) num `<span>`
  com cor de fundo, assim o ícone pega a cor da classe (ex.: `bg-secondary-strong`) e acompanha a
  cor principal.
- **Ícone decorativo sem cor própria:** `<img alt="">` com `width` e `height`.
- Tamanhos: `size-5` (20px) nos botões, `size-10` (40px) no ícone de item/categoria e `size-24`
  nos cards de número.

### Ícones de categoria

O usuário escolhe o ícone e a cor de cada categoria. As opções ficam em
`backend/config/categories.php`, e o backend recusa qualquer valor fora da lista.

| Grupo             | Ícones                                                                                |
| ----------------- | ------------------------------------------------------------------------------------- |
| Alimentação       | grocery, restaurant, fastfood, local-cafe, local-pizza, bakery-dining, icecream, local-bar |
| Eletrônicos       | smartphone, laptop-windows, tv, headphones, sports-esports, photo-camera, tablet, earbuds |
| Moda e acessórios | apparel, checkroom, styler, wallet, eyeglasses, watch, diamond, backpack              |
| Casa              | weekend, bed, chair, table-lamp, kitchen, microwave-gen, blender, cleaning-services   |

O padrão, quando nada foi escolhido, é o ícone `box-seam` com a cor `gray-200`.

O tom do ícone depende do tom do fundo, para manter o contraste:

| Fundo | Ícone     |
| ----- | --------- |
| 100   | tom 400   |
| 200   | tom 500   |
| 300   | branco    |
| 400   | branco    |
| 500   | tom 200   |

## Acessibilidade e textos

- Todo campo tem `<label>`. Erros usam `aria-invalid` e `aria-describedby`.
- Drawer e modal usam `<dialog>`: Esc fecha e o foco fica dentro.
- O menu ativo usa `aria-current="page"`. Os botões de alternar usam `aria-pressed`.
- Imagem decorativa tem `alt=""`, e imagem com significado tem descrição (ex.: "Porquinho 60% cheio").
- Nomes longos são cortados com "..." e mostram o texto inteiro no `title`.
- A animação respeita `prefers-reduced-motion`.
- Os textos da interface são em português e no tom amigável do mascote
  (ex.: "Ruim. Segura mais um pouco.").

## Checklist para uma tela nova

1. Abrir o frame da tela no Figma e converter os tamanhos pela [escala](#escala-do-figma).
2. Incluir o `theme.php` (e o `accent-color.php`, se a tela for logada).
3. Montar a tela com os componentes que já existem em `frontend/components/`.
4. Usar `secondary-*` para destaque, nunca a cor fixa.
5. Conferir o foco pelo teclado, os labels e o `alt` das imagens.
