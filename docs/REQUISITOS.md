# Requisitos do Poupa Pig

Este documento lista os requisitos funcionais, os requisitos não funcionais e as regras de negócio
do Poupa Pig **como o sistema funciona hoje**. Tudo foi tirado do código: controllers, services e
models do backend, mapa de erros (`backend/config/errors.php`), banco (`database/database.sql`) e
telas (`frontend/pages/`). O que ainda não existe está em [Fora do escopo e a fazer](#4-fora-do-escopo-e-a-fazer).

## 1. Requisitos funcionais

| ID   | Requisito                                                                                                                                                                                                                       |
| ---- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| RF01 | O sistema deve permitir que o visitante crie uma conta com nome, e-mail e senha.                                                                                                                                                |
| RF02 | O sistema deve permitir que o usuário entre com e-mail e senha.                                                                                                                                                                 |
| RF03 | O sistema deve permitir que o usuário cadastre, edite e remova itens: foto (opcional), nome, categoria, valor, link (opcional), data de inclusão e data final da quarentena.                                                     |
| RF04 | O sistema deve listar os itens do usuário com busca por nome, filtro por categoria e filtro por período (data de inclusão), com 10 itens por página.                                                                            |
| RF05 | O sistema deve sugerir nomes de produto (autocompletar) no campo nome ao criar ou editar um item.                                                                                                                               |
| RF06 | O sistema deve permitir que o usuário cadastre, edite e remova categorias: nome, descrição (opcional), ícone e cor.                                                                                                             |
| RF07 | O sistema deve listar as categorias do usuário com busca por nome e 10 por página, mostrando a quantidade e o percentual de itens de cada uma e os cards de resumo (total de categorias, total de itens e categoria mais usada). |
| RF08 | O sistema deve permitir que o usuário decida um item que está pensando, escolhendo "Comprei" ou "Desisti" no Dashboard.                                                                                                         |
| RF09 | O sistema deve exibir o Dashboard mensal: porquinho com o percentual economizado, valores desejado, pensando e comprado, lista "Decida em breve", decisões do mês, troca de mês, resumo do mês e comparação com o mês anterior.  |
| RF10 | O sistema deve listar, na tela de Reviews, os produtos que estão nos itens do usuário, com busca por nome, filtro por nota e 10 por página.                                                                                      |
| RF11 | O sistema deve exibir a página do produto com média, distribuição das notas e as reviews de todos os usuários, 10 por página.                                                                                                   |
| RF12 | O sistema deve permitir que o usuário publique uma review com nota de 1 a 5 e resenha opcional.                                                                                                                                 |
| RF13 | O sistema deve permitir que o usuário, em Configurações, troque o nome, a foto, a senha e a cor principal, e exclua a conta.                                                                                                    |

## 2. Requisitos não funcionais

| ID    | Requisito                                                                                                                                                                                                          |
| ----- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| RNF01 | O sistema roda em PHP com MySQL, no ambiente XAMPP (Apache + MySQL), e é acessado pelo navegador.                                                                                                                  |
| RNF02 | O visual usa Tailwind CSS v4 (via CDN) e a fonte Google Sans Flex (Google Fonts). A landing page é responsiva; as telas logadas foram feitas para desktop.                                                          |
| RNF03 | A senha é guardada só como hash (`password_hash`) e conferida com `password_verify`. O id da sessão é renovado no login e na troca de senha.                                                                       |
| RNF04 | Um usuário nunca vê nem altera dados de outro: o id do usuário vem sempre da sessão, toda query filtra por `user_id`, e item ou categoria de outra pessoa responde como "não encontrado".                           |
| RNF05 | Todo acesso ao banco usa queries preparadas (PDO). Na busca, `%` e `_` são escapados para serem tratados como texto comum.                                                                                         |
| RNF06 | Toda entrada externa (formulário, URL) é validada no servidor, mesmo quando o formulário já limita o campo.                                                                                                        |
| RNF07 | Fotos são conferidas pelo conteúdo do arquivo (só PNG ou JPG, até 5 MB) e salvas com nome aleatório gerado pelo servidor. Arquivos só são apagados depois que o banco confirma a operação.                          |
| RNF08 | Erros e sucessos são devolvidos por código (`backend/config/errors.php` e `success.php`); o usuário vê só a mensagem amigável e o detalhe técnico vai apenas para o log do servidor.                                |
| RNF09 | Os controllers aceitam só POST e seguem o fluxo POST → redirect → GET; em erro de formulário, o que foi digitado volta preenchido (nunca senha nem foto).                                                          |
| RNF10 | O backend é organizado em camadas: controller (recebe o pedido) → service (regras e validação) → model (queries).                                                                                                  |

## 3. Regras de negócio

### Conta

| ID   | Regra                                                                                                               |
| ---- | ------------------------------------------------------------------------------------------------------------------- |
| RN01 | O nome do usuário é obrigatório e tem no máximo 120 caracteres.                                                     |
| RN02 | O e-mail é obrigatório, precisa ser válido, ter no máximo 180 caracteres e ser único no sistema.                   |
| RN03 | A senha tem no mínimo 8 caracteres e é usada exatamente como digitada (sem remover espaços).                        |
| RN04 | No login, e-mail não cadastrado e senha errada mostram a mesma mensagem, para não revelar quais e-mails existem.     |
| RN05 | As telas logadas exigem usuário na sessão; quem não está logado volta para o login.                                 |

### Itens

| ID   | Regra                                                                                                                                     |
| ---- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| RN06 | O nome do item é obrigatório e tem no máximo 100 caracteres.                                                                              |
| RN07 | O valor é obrigatório, maior que zero, com até 8 dígitos inteiros e até 2 casas decimais.                                                 |
| RN08 | O link é opcional; quando informado, precisa começar com `http://` ou `https://` e ter no máximo 255 caracteres.                          |
| RN09 | A data de inclusão e a data final são obrigatórias, precisam ser datas reais, e a data final precisa ser igual ou depois da data de início. |
| RN10 | A categoria é obrigatória e precisa ser uma categoria do próprio usuário.                                                                 |
| RN11 | Todo item novo começa com o status "pensando".                                                                                            |
| RN12 | Editar um item não muda o status; sem foto nova, a foto atual é mantida.                                                                  |

### Decisão (Comprei ou Desisti)

| ID   | Regra                                                                                                                                                                                      |
| ---- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| RN13 | Só um item "pensando" pode ser decidido, e só uma vez: a decisão é "comprei" ou "desisti" e grava a data da decisão. Um segundo clique no mesmo item é recusado.                             |
| RN14 | A decisão pode ser feita a qualquer momento, mesmo antes do fim da quarentena.                                                                                                             |
| RN15 | Item sem decisão continua como "ainda pensando". O prazo é mostrado como "faltam N dias", "vence amanhã", "vence hoje" ou "prazo passou".                                                   |
| RN16 | A lista "Decida em breve" mostra os 3 itens pensando com a data final mais próxima, de qualquer mês.                                                                                       |

### Dashboard

| ID   | Regra                                                                                                                                                                      |
| ---- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| RN17 | O mês de um item é o mês da data de inclusão. O valor desejado do mês é a soma dos itens incluídos nele; pensando, comprado e desistido são as partes dessa soma por status. |
| RN18 | O percentual do porquinho é **desistido ÷ desejado no mês × 100**, arredondado para baixo. Sem itens no mês, é 0%.                                                          |
| RN19 | A lista de decisões do mês conta os itens pela data da decisão, não pela data de inclusão.                                                                                 |
| RN20 | Só é possível abrir meses de janeiro de 2000 até o mês atual; mês inválido ou futuro abre o mês atual.                                                                     |
| RN21 | Em mês que já passou, o Dashboard mostra o resumo do mês (desistências, compras, maior desistência) e compara o percentual com o mês anterior.                             |

### Categorias

| ID   | Regra                                                                                                                                                         |
| ---- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| RN22 | O nome da categoria é obrigatório e tem no máximo 120 caracteres; a descrição é opcional.                                                                     |
| RN23 | Ícone e cor só podem ser escolhidos das listas de `backend/config/categories.php`; sem escolha, ficam o ícone `box-seam` e a cor `gray-200`.                   |
| RN24 | Categoria que ainda tem itens não pode ser removida.                                                                                                          |
| RN25 | O percentual da categoria é a quantidade de itens dela ÷ total de itens do usuário × 100, arredondado. A categoria mais usada é a com mais itens (no empate, a criada primeiro). |

### Reviews

| ID   | Regra                                                                                                                                                                         |
| ---- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| RN26 | O produto é identificado pelo nome do item, sem diferenciar maiúsculas nem acentos e com os espaços repetidos juntados ("Jaqueta  Adidas" e "jaqueta adidas" são o mesmo).    |
| RN27 | Só pode avaliar um produto quem tem um item com esse nome, e cada usuário avalia cada produto uma única vez.                                                                 |
| RN28 | A nota é obrigatória, de 1 a 5; a resenha é opcional e tem no máximo 1000 caracteres.                                                                                          |
| RN29 | Quem não tem o item só vê produtos do catálogo ou que já têm pelo menos uma review; produto que só existe nos itens de outra pessoa não aparece.                             |
| RN30 | O filtro por nota usa a média arredondada (nota 4 = média de 3,5 até antes de 4,5); produto sem review não entra no filtro.                                                  |

### Configurações

| ID   | Regra                                                                                                                                         |
| ---- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| RN31 | A troca de senha exige a senha atual correta, a nova com no mínimo 8 caracteres e a confirmação igual à nova.                                 |
| RN32 | A cor principal só pode ser um dos tons 300, 400 ou 500 da paleta das categorias (azul, rosa, roxo, verde, vermelho).                         |
| RN33 | A foto do usuário segue a mesma regra das fotos dos itens (PNG ou JPG, até 5 MB); a foto antiga é apagada ao trocar.                          |
| RN34 | Excluir a conta apaga itens, categorias e o usuário de uma vez (tudo ou nada), depois apaga as fotos e encerra a sessão.                      |

## 4. Fora do escopo e a fazer

Conforme o [README](../README.md), ainda não existem:

- **A fazer:** recuperação de senha por e-mail (as telas existem, mas são só layout, sem backend) e
  sair da conta (logout).
- **Motivo do item:** a coluna `reason` existe no banco, mas o campo ainda não está no formulário nem
  no backend.
- **Fora do escopo:** extensão do prazo da quarentena, lembrete do motivo ao vencer a quarentena,
  ranking de categorias e evolução semanal no dashboard, simulador de "e se tivesse comprado tudo",
  preço de referência via API do Mercado Livre e exportação do histórico em PDF/CSV.
