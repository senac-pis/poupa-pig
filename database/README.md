# Banco de Dados

> Documente nesta pasta a estrutura MySQL do projeto e a forma correta de versioná-la.

## Para que serve

Use esta pasta como referência para o modelo de dados da equipe, para a exportação do SQL e para a evolução do banco ao longo da disciplina.

Utilize esta pasta para versionar a estrutura do banco MySQL do projeto.

## Arquivo principal

```text
database.sql
```

Ele deve permitir que outro integrante da equipe consiga criar a estrutura básica do banco utilizando o phpMyAdmin.

## Regras

- Não exporte dados pessoais reais;
- Não adicione senhas em texto puro;
- Utilize chaves primárias;
- Utilize chaves estrangeiras nos relacionamentos quando aplicável;
- Mantenha o SQL atualizado quando a estrutura do banco mudar.

## Arquivos desta pasta

| Arquivo                  | Tipo       | O que faz                                                        |
| ------------------------ | ---------- | ---------------------------------------------------------------- |
| `database.sql`           | DDL        | Cria o banco e as tabelas (importar primeiro)                    |
| `seeds/products.sql`     | DML        | Catálogo de produtos (importar depois do `database.sql`)         |
| `seeds/demo.sql`         | DML        | Dados fictícios para demonstração (opcional)                     |
| `queries/dml.sql`        | DML        | Escritas dos models em SQL puro (roda com `ROLLBACK`)            |
| `queries/dql.sql`        | DQL        | Consultas dos models em SQL puro                                 |
| `migrations/`            | Migrations | Evolução do schema, `001` a `006`, para rodar em ordem           |
| `dump/poupa_pig.sql`     | Dump       | Estrutura + catálogo + demonstração em um arquivo só             |

O schema, o dicionário de dados e o MER/DER estão em [`docs/BANCO.md`](../docs/BANCO.md).
