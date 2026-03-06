# Travel Orders Service

Microsserviço desenvolvido em **Laravel** para gerenciamento de pedidos de viagem corporativa.

A aplicação expõe uma **API REST** que permite criar, consultar, listar, atualizar status e cancelar pedidos de viagem, respeitando regras de negócio e controle de acesso por usuário.

---

# 🏗 Arquitetura e decisões técnicas

Algumas decisões tomadas durante o desenvolvimento:

- **Laravel 11** utilizado por oferecer estrutura moderna e robusta para APIs.
- **Laravel Sanctum** utilizado para autenticação via token.
- **Docker + Docker Compose** para facilitar execução local do projeto.
- **Eloquent ORM** para interação com banco de dados relacional.
- **Form Requests** para validação de dados de entrada.
- **Notifications** para envio de notificações quando pedidos são aprovados ou cancelados.
- **Testes automatizados com PHPUnit** para validar regras principais da aplicação.
- **Controle de autorização** garantindo que usuários visualizem apenas suas próprias ordens.

---

# 📦 Tecnologias utilizadas

- PHP 8+
- Laravel
- MySQL / SQLite
- Docker
- Laravel Sanctum
- PHPUnit

---

# 🚀 Como executar o projeto

## 1️⃣ Clonar o repositório

```bash
git clone https://github.com/seu-usuario/travel-orders-service.git
cd travel-orders-service
```

## 2️⃣ Subir os containers

```bash
docker compose up -d
```
## 3️⃣ Instalar dependências

```bash
docker compose exec app composer install
```

## 4️⃣ Rodar as migrations

```bash
docker compose exec app php artisan migrate
```

## 5️⃣ Acessar aplicação

A API estará disponível em:

```bash
http://localhost:8000
```

## 🔐 Autenticação

A API utiliza Laravel Sanctum com autenticação via token.

### Login

```bash
POST /api/login
```


Exemplo:

```bash
{
  "email": "user@test.com",
  "password": "123456"
}
```

Resposta:

```bash
{
  "token": "TOKEN_GERADO"
}
```


Utilize o token nas próximas requisições:

```bash
Authorization: Bearer "TOKEN"
```

## 📡 Endpoints da API

### Criar pedido de viagem

```bash
POST /api/travel-orders
```

Body:

```bash
{
  "requester_name": "Diego",
  "destination": "São Paulo",
  "departure_date": "2026-04-10",
  "return_date": "2026-04-15"
}
```

### Consultar pedido

```bash
GET /api/travel-orders/{id}
```
Retorna os dados de um pedido específico.


### Listar pedidos

```bash
GET /api/travel-orders
```

Filtros disponíveis:

```bash
status
destination
departure_date
return_date
```

Exemplo:

```bash
GET /api/travel-orders?status=approved
```

### Atualizar status do pedido

```bash
PATCH /api/travel-orders/{id}/status
```

Body:

```bash
{
  "status": "approved"
}
```

⚠️ Apenas *usuários administradores* podem alterar o status.

### Cancelar pedido

```bash
DELETE /api/travel-orders/{id}
```

Regras:

- Pedidos aprovados não podem ser cancelados

---


## 🔔 Notificações

Sempre que um pedido for:

- aprovado

- cancelado

Uma notificação é enviada para o usuário solicitante.

As notificações são armazenadas na tabela:

```bash
notifications
```

## 👥 Controle de acesso

A aplicação possui dois tipos de usuário:

| Tipo | Permissões |
|------|------------|
| Usuário comum | Criar e visualizar suas próprias ordens |
| Administrador | Aprovar ou cancelar pedidos |

Cada usuário só pode acessar **suas próprias ordens de viagem**.

---

### 🧪 Testes automatizados

Foram implementados testes utilizando PHPUnit para validar as principais regras do sistema.

### Executar testes

```bash
docker compose exec app php artisan test
```

Testes implementados:

- Criação de pedido

- Aprovação por administrador

- Bloqueio de aprovação por usuário comum

- Bloqueio de acesso a ordens de outros usuários

## 📁 Estrutura do projeto
```bash
app/
 ├── Http/
 │   ├── Controllers/
 │   │   └── TravelOrderController.php
 │   ├── Requests/
 │   │   └── StoreTravelOrderRequest.php
 │
 ├── Models/
 │   ├── User.php
 │   └── TravelOrder.php
 │
 ├── Notifications/
 │   └── TravelOrderStatusUpdated.php

database/
 ├── migrations/
 ├── factories/

routes/
 └── api.php

tests/
 └── Feature/
     └── TravelOrderTest.php
```

## 📌 Considerações finais

O objetivo deste projeto foi demonstrar:

Boas práticas com Laravel

Arquitetura de API REST

Controle de autenticação e autorização

Implementação de regras de negócio

Uso de Docker para execução simplificada

Testes automatizados para garantir confiabilidade

## 👨‍💻 Autor

Diego Santos