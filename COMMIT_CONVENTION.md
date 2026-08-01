# Padrão de Commits

Este projeto segue o padrão **Conventional Commits**.

## Estrutura

```
tipo(escopo opcional): descrição curta no imperativo
```

## Tipos

| Tipo       | Quando usar                                                        |
|------------|---------------------------------------------------------------------|
| `feat`     | nova funcionalidade                                                 |
| `fix`      | correção de bug                                                     |
| `refactor` | mudança de código que não é bug nem feature (reorganizar, renomear) |
| `chore`    | tarefas de manutenção (config, dependências, setup)                 |
| `docs`     | mudanças só em documentação                                         |
| `test`     | adicionar ou ajustar testes                                         |
| `style`    | formatação, espaços, ponto e vírgula — sem mudar lógica             |

## Regras

- Descrição no **imperativo**: "adiciona", "corrige", "remove" — não "adicionado", "corrigido".
- Minúsculo após o tipo, sem ponto final na primeira linha.
- Até ~50 caracteres na primeira linha. Se precisar de mais detalhe, pula uma
  linha em branco e escreve o corpo do commit.
- Um commit = uma mudança lógica. Evita misturar "adiciona feature X" com
  "corrige bug Y" no mesmo commit.

## Exemplos

```
chore: setup inicial do projeto Laravel com Sanctum
feat: adiciona migrations de categories e transactions
feat: implementa CRUD de categorias
feat: implementa CRUD de transacoes com filtros por data e categoria
chore: instala Laravel IDE Helper para autocomplete
fix: corrige imports faltantes de BelongsTo e HasMany nos models
```

## Exemplo com corpo (quando precisa explicar o "porquê")

```
fix: corrige delete de transacao sem autorizacao

Adiciona verificacao de ownership antes de deletar,
usuario nao podia deletar transacao de outro usuario.
```

## Escopo opcional

Indica qual parte do sistema mudou. Útil quando o projeto cresce e você
quer navegar pelo histórico filtrando por área.

```
feat(transactions): adiciona filtro por periodo de data
feat(auth): implementa endpoint de registro via Sanctum
```
