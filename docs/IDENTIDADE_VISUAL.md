# Manual de Identidade Visual

> Define a marca do Poupa Pig: conceito, logo, mascote, cores e tipografia. Como esses elementos
> viram tela (tamanhos, componentes, ícones) está no [Design Guide](DESIGN_GUIDE.md).

## Figma

Todo o visual nasceu no Figma: [Poupa Pig](https://www.figma.com/design/m9gI8kevph97sNSiFql9Ki/poupa-pig?node-id=0-1&t=AKTjgEf1Ks2EAlQu-1).

| Frame no Figma                   | O que tem                                                     |
| -------------------------------- | ------------------------------------------------------------- |
| `Style guide`                    | cores, tipografia, botões e formulário de referência          |
| `assets` e `icons`               | mascote, imagens e todos os ícones                            |
| `Porquinho` e `Selo de humor`    | os níveis do porquinho e os selos do dashboard                |
| `Landing page`                   | página pública inicial                                        |
| `Desktop - Light mode ...`       | cada tela do sistema (login, dashboard, itens, categorias...) |
| `filters`, `icon-picker`, etc.   | componentes soltos (filtros, seletores de ícone e de cor)     |

O Figma foi desenhado grande demais. O sistema usa as mesmas proporções numa escala menor
(~65–75%); a tabela de conversão está no [Design Guide](DESIGN_GUIDE.md#escala-do-figma).

## Conceito

O Poupa Pig ajuda a evitar a compra por impulso: o desejo de compra entra numa quarentena antes
da decisão. A marca é um **porquinho-cofrinho**, que representa guardar o dinheiro que não foi
gasto. O tom é leve e amigável, com cores claras e o mascote reagindo ao que o usuário faz.

## Logo

A logo é a palavra **POUPA**, o mascote e a palavra **PIG**, nessa ordem e na mesma linha:

- **POUPA** na cor do texto (`primary`, azul-escuro `#163261`);
- **PIG** na cor principal (`secondary`, rosa `#f18a9c` por padrão);
- as duas palavras em **Google Sans Flex Bold**.

| Arquivo                         | Uso                                                       |
| ------------------------------- | --------------------------------------------------------- |
| `frontend/assets/logos/logo.svg` | mascote em vetor (use quando precisar escalar)           |
| `frontend/assets/logos/logo.png` | mascote em PNG transparente (992 × 1059), usado nas telas |
| `docs/logo.jpg`                  | mascote com fundo, para documentos e apresentações       |
| `frontend/components/logo.php`   | a logo montada (texto + mascote) que as telas incluem    |

Tamanhos usados:

| Versão  | Onde                                | Texto                    | Mascote                |
| ------- | ----------------------------------- | ------------------------ | ---------------------- |
| Grande  | telas públicas (login, cadastro...) | 44px (30px no celular)   | 120px (80px no celular) |
| Pequena | sidebar das telas logadas           | 22px                     | 64px                   |

Como **não** usar a logo:

- não trocar a ordem POUPA / mascote / PIG nem separar as palavras em linhas;
- não deformar o mascote (sempre proporcional);
- não usar outras cores além das da paleta. O "PIG" acompanha a cor principal escolhida pelo usuário;
- não aplicar sobre fundos escuros ou com muita informação.

## Mascote

O porquinho aparece em vários estados. As imagens ficam em `frontend/assets/images/`.

| Imagem                       | Onde aparece                                                  |
| ---------------------------- | ------------------------------------------------------------- |
| `pig-happy.png`              | notificação de sucesso                                        |
| `pig-angry.png`              | notificação de erro                                           |
| `pig-smile.png`              | selo "Excelente!" do dashboard (67% ou mais)                  |
| `pig-neutral.png`            | selo "Melhore!" do dashboard (34% a 66%)                      |
| `pig-sad.png`                | selo "Ruim." do dashboard (menos de 34%)                      |
| `pig-0.png` a `pig-100.png`  | porquinho do dashboard enchendo de 20 em 20%                  |
| `hero.mp4`                   | vídeo do topo da landing page (`frontend/index.php`)          |

O porquinho do dashboard mostra quanto o usuário deixou de gastar no mês: o % é o valor
desistido dividido pelo valor desejado. A imagem é a do nível arredondado para baixo (70% usa a
`pig-60.png`).

## Paleta de cores

As cores vêm da coleção `color` do Figma e estão no `frontend/components/theme.php`. O nome no
Figma é o mesmo do código: `pink/400` no Figma é `pink-400` no Tailwind (`bg-pink-400`,
`text-pink-400`...).

### Cores de papel

São as cores que as telas usam no dia a dia. Use sempre o nome do papel, não o da cor.

| Token                | Cor padrão         | HEX       | Aplicação                                                   |
| -------------------- | ------------------ | --------- | ----------------------------------------------------------- |
| `primary`            | `blue-500`         | `#163261` | textos, bordas e contornos                                  |
| `secondary`          | `pink-400`         | `#f18a9c` | cor principal: botão principal, títulos, "PIG", menu ativo |
| `secondary-100…500`  | `pink-100…500`     | —         | tons claros da cor principal (cabeçalho de tabela, hover)  |
| `secondary-strong`   | `pink-400`         | `#f18a9c` | texto e ícone dos botões claros (fundo `secondary-200`)     |
| `background`         | `white-200`        | `#f2f2f2` | fundo das telas e texto dos botões cheios                   |
| Sucesso              | `green-400`/`500`  | `#0cbb00` | notificação de sucesso, selo "Excelente!"                   |
| Erro / perigo        | `red-400`          | `#ff0000` | erro de campo, notificação de erro, botão Remover           |
| Informação           | `sky-100`/`400`    | `#46a5ff` | botões neutros como "Selecionar", selo "Melhore!"           |

> No frame `Style guide` do Figma, "Primary" e "Secundary" estão os dois com `#F18A9C`. No
> sistema ficou assim: o rosa é a `secondary` (cor de destaque) e o azul-escuro `#163261` é a
> `primary` (texto).

### Cor principal trocável

Em **Configurações > Aparência** o usuário escolhe a cor principal do sistema. A escolha fica em
`users.accent_color` e é aplicada antes da página aparecer, pelo
`frontend/components/accent-color.php`.

- Opções: 5 famílias (**Azul** `sky`, **Rosa** `pink`, **Roxo** `purple`, **Verde** `green`,
  **Vermelho** `red`) nos tons **300, 400 e 500**, ou seja, 15 cores.
- Ao trocar, mudam juntos a `secondary`, os tons `secondary-100` a `secondary-500` da mesma família
  e a `secondary-strong`.
- No tom 300 a `secondary-strong` passa a usar o tom 500, porque o 300 some no fundo claro do botão.
- Sem cor salva, fica o rosa padrão (`pink-400`).

Por isso as telas **nunca** usam `pink-*` direto para destaque: usam `secondary-*`, para
acompanhar a cor do usuário. O tema escuro aparece na tela (Escuro / Sistema), mas ainda está
desativado.

### Paleta completa

| Família  | 100       | 200       | 300       | 400       | 500       |
| -------- | --------- | --------- | --------- | --------- | --------- |
| `pink`   | `#fce8eb` | `#f9d0d7` | `#f5adba` | `#f18a9c` | `#d97c8c` |
| `blue`   | `#d0d6df` | `#a2adc0` | `#7384a0` | `#455b81` | `#163261` |
| `sky`    | `#daedff` | `#b5dbff` | `#7ec0ff` | `#46a5ff` | `#3f95e6` |
| `green`  | `#cef1cc` | `#9ee499` | `#55cf4d` | `#0cbb00` | `#0ba800` |
| `red`    | `#ffcccc` | `#ff9999` | `#ff4d4d` | `#ff0000` | `#e60000` |
| `purple` | `#eee0ff` | `#ddc1ff` | `#c493ff` | `#aa65ff` | `#995be6` |
| `gray`   | `#e4e4e4` | `#bebebe` | `#969696` | `#6e6e6e` | `#464646` |
| `black`  | `#cccccc` | `#999999` | `#666666` | `#333333` | `#000000` |
| `white`  | `#ffffff` | `#f2f2f2` | `#e6e6e6` | `#d9d9d9` | `#cccccc` |

Extra: `blue-alpha-500` (`#16326133`, azul-escuro com 20% de opacidade) é o fundo escurecido
atrás do drawer e do modal.

As famílias `sky`, `pink`, `purple`, `green` e `red` também são as cores que o usuário pode dar
às categorias (os 5 tons de cada).

## Tipografia

Uma família só: **[Google Sans Flex](https://fonts.google.com/specimen/Google+Sans+Flex)**,
carregada do Google Fonts no `theme.php`. Se ela não carregar, entra a fonte do sistema
(`ui-sans-serif, system-ui, sans-serif`).

| Peso        | Classe        | Uso                                                         |
| ----------- | ------------- | ----------------------------------------------------------- |
| Bold (700)  | `font-bold`   | títulos, logo, menu, labels, botões claros, cabeçalho de tabela |
| Medium      | `font-medium` | linhas de tabela, rótulo dos cards de número                |
| Regular     | (padrão)      | textos corridos e botões cheios                             |
| Light (300) | `font-light`  | nomes nas tabelas, mensagens dos modais                     |
| Thin (200)  | `font-thin`   | números grandes dos cards, descrições                       |

Hierarquia do Figma e o tamanho usado no sistema:

| Figma        | Sistema                                |
| ------------ | -------------------------------------- |
| h1 — 64px    | 40px: título da página                 |
| h2 — 48px    | 32px no drawer / 28px no modal         |
| h3 — 36px    | 28px: números dos cards                |
| h4 — 32px    | 22px: menu da sidebar                  |
| h5 — 24px    | 16–18px: labels, inputs, filtros       |
| h6 — 20px    | 16px: botões, busca, cabeçalho de tabela |

Os títulos de página e de drawer ficam na cor principal (`text-secondary`). O título de modal
de remoção fica em vermelho (`text-red-400`).
