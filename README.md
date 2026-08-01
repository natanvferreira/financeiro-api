# Financeiro API

API REST de controle financeiro pessoal, construída como projeto de treino em
PHP 8.3 + Laravel atual (vindo de uma base anterior em PHP 7.2 / Laravel 7).

## Stack

- PHP 8.3
- Laravel (última versão estável)
- Laravel Sanctum (autenticação via token)
- Pest 4 (testes automatizados)
- MySQL 8 (rodando em Docker)
- Composer

## Domínio

- **User** — dono das categorias e transações
- **Category** — categorias de entrada (`income`) ou saída (`expense`)
- **Transaction** — movimentações financeiras, vinculadas a uma categoria e a um usuário

## Setup local

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configura o `.env` apontando pro MySQL do Docker:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financeiro_api
DB_USERNAME=root
DB_PASSWORD=root
```

Roda as migrations e popula com dados de teste:

```bash
php artisan migrate:fresh --seed
```

Isso cria um usuário de teste (`natan@teste.com` / `senha123`) já com
categorias e transações de exemplo.

Sobe o servidor:

```bash
php artisan serve
```

> **Atenção:** evite rodar `php artisan optimize` em ambiente de
> desenvolvimento. Ele cacheia config/rotas e faz mudanças no `.env` ou nas
> rotas pararem de ter efeito até rodar `php artisan optimize:clear`.

## Autenticação

A API usa **Laravel Sanctum** com tokens opacos. Todo endpoint protegido exige
o header:

```
Authorization: Bearer {token}
```

Também é necessário mandar, em toda requisição:

```
Accept: application/json
```

As rotas de `register` e `login` têm **rate limiting** (máximo 6 tentativas
por minuto, por IP) para mitigar força bruta e spam de cadastro.

### Registro

```
POST /api/register
```

```json
{
    "name": "Nome",
    "email": "email@exemplo.com",
    "password": "senha123",
    "password_confirmation": "senha123"
}
```

Retorna o usuário criado e um token já pronto pra uso — não é necessário
fazer login em seguida.

### Login

```
POST /api/login
```

```json
{
    "email": "email@exemplo.com",
    "password": "senha123"
}
```

### Logout

```
POST /api/logout
```

Invalida o token atual.

## Endpoints

Listagens são **paginadas** (15 itens por página, parâmetro `?page=N`).

### Categorias

```
GET    /api/categories
POST   /api/categories
PUT    /api/categories/{id}
DELETE /api/categories/{id}
```

Body de criação/edição:

```json
{
    "name": "Alimentação",
    "type": "expense"
}
```

`type` aceita `income` ou `expense`.

Edição e exclusão só são permitidas para o dono do registro (ver
[Autorização](#autorização)).

### Transações

```
GET    /api/transactions
POST   /api/transactions
PUT    /api/transactions/{id}
DELETE /api/transactions/{id}
```

Body de criação/edição:

```json
{
    "description": "Supermercado",
    "amount": 350.50,
    "date": "2026-08-01",
    "category_id": 1
}
```

Filtros disponíveis na listagem (query params):

```
GET /api/transactions?category_id=1
GET /api/transactions?from=2026-08-01&to=2026-08-31
```

### Resumo mensal

```
GET /api/summary?month=2026-08
```

Se `month` não for informado, usa o mês atual. Retorna:

```json
{
    "month": "2026-08",
    "total_income": 5000.00,
    "total_expense": 350.50,
    "balance": 4649.50
}
```

## Arquitetura interna

- **Migrations** — definem a estrutura das tabelas (`categories`, `transactions`),
  incluindo chaves estrangeiras para `users`.
- **Models** — `Category` e `Transaction`, com relacionamentos Eloquent
  (`belongsTo`/`hasMany`), `HasFactory` e `$fillable` para mass assignment seguro.
- **Form Requests** — `StoreCategoryRequest` e `StoreTransactionRequest`
  centralizam as regras de validação, fora dos controllers.
- **API Resources** — `CategoryResource` e `TransactionResource` controlam
  o formato exato do JSON de resposta.
- **Controllers** — sempre filtram os dados por `$request->user()`, garantindo
  que cada usuário só acesse os próprios registros.
- **bootstrap/app.php** — configurado para responder erros de autenticação
  sempre em JSON (evita o comportamento padrão de redirecionar para uma
  rota `login` inexistente em uma API pura).

### Autorização

`CategoryPolicy` e `TransactionPolicy` centralizam a regra de que só o dono
do registro pode editá-lo ou excluí-lo. Os controllers chamam
`$this->authorize('update', $model)` / `$this->authorize('delete', $model)`,
que retornam automaticamente `403` quando a regra falha. O trait
`AuthorizesRequests` precisa estar presente em `app/Http/Controllers/Controller.php`
para que `$this->authorize()` funcione.

### Seeders

`DatabaseSeeder` chama `CategorySeeder` e `TransactionSeeder`, nessa ordem,
para popular um usuário de teste com dados de exemplo. Use
`php artisan migrate:fresh --seed` para recriar o banco do zero já populado.

## Testes

O projeto usa **Pest 4**. Cobertura atual:

- Registro e duplicidade de e-mail
- Rate limiting em login
- Listagem de categorias/transações restrita ao usuário autenticado
- Criação de categoria/transação
- Bloqueio de acesso a registros de outro usuário (via Policy)
- Validação de categoria inexistente ao criar transação
- Filtro de transações por categoria
- Paginação de categorias

Rodar toda a suíte:

```bash
php artisan test
```

Rodar um arquivo específico:

```bash
php artisan test --filter=CategoryTest
```

## Ferramentas de desenvolvimento

- **Laravel IDE Helper** (`barryvdh/laravel-ide-helper`) — gera hints para
  o Intelephense reconhecer métodos "mágicos" do Eloquent corretamente.
  Rodar após mudanças relevantes nos models:

  ```bash
  php artisan ide-helper:generate
  php artisan ide-helper:models
  ```

## Padrão de commits

Ver [COMMIT_CONVENTION.md](./COMMIT_CONVENTION.md).
