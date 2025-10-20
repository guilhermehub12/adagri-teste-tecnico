# Sistema de Gestão Agropecuária - ADAGRI

Sistema completo de gestão agropecuária com Laravel 12 (backend) e Vue 3 (frontend).

## Requisitos

- Docker
- Docker Compose

## 🚀 Instalação Rápida

### Setup Inicial (Primeira vez)

```bash
# 1. Clone o repositório
git clone https://github.com/guilhermehub12/adagri-teste-tecnico
cd adagri-teste-tecnico

# 2. Subir containers
make up-build

# 3. Executar setup automático
make setup
```

**Pronto!** 🎉 A API está disponível em `http://localhost:8000/api`

### Ver todos os comandos disponíveis

```bash
make help
```

### Problema de Permissões?

Se encontrar erros relacionados a permissões:

```bash
make permissions
```

## 🔐 Contas de Teste

Após rodar o seeder, você terá 4 contas disponíveis:

| Perfil | Email | Senha | Permissões |
|--------|-------|-------|------------|
| **Admin** | admin@adagri.ce.gov.br | password123 | Acesso total + gerenciar usuários |
| **Gestor** | gestor@adagri.ce.gov.br | password123 | Criar, editar, visualizar |
| **Técnico** | tecnico@adagri.ce.gov.br | password123 | Criar e visualizar |
| **Extensionista** | extensionista@adagri.ce.gov.br | password123 | Somente visualizar + relatórios |

## 📮 Testando a API

1. Importe a Collection do Postman: `ADAGRI_API.postman_collection.json`
2. Importe o Environment: `ADAGRI_Environment.postman_environment.json`
3. Selecione o environment "ADAGRI - Development"
4. Execute "Login como Admin" (ou qualquer outro perfil)
5. Comece a testar!

**Guia completo:** [`docs/POSTMAN_GUIDE.md`](docs/POSTMAN_GUIDE.md)

## Ambiente de Desenvolvimento

### 1. Subir os containers

```bash
make up-build
```

### 2. Executar migrations

```bash
make migrate
```

### 3. (Opcional) Executar seeders

```bash
make seed
```

**Ou execute tudo de uma vez:**

```bash
make setup
```

### Acesso (Dev)

- **API**: http://localhost:8000/api
- **Banco de Dados PostgreSQL**: localhost:5432
  - Database: `adagri_db`
  - Username: `adagri_user`
  - Password: `adagri_pass`

### Características do Ambiente Dev

- Hot reload (alterações no código refletem instantaneamente)
- Dependências de desenvolvimento (PHPUnit, etc.)
- Ferramentas de debug
- Volume mount do código fonte

## Ambiente de Produção

### 1. Build e subir os containers

```bash
make prod-up
```

### 2. Executar migrations

```bash
make prod-migrate
```

### 3. Executar seeders

```bash
make prod-seed
```

### Acesso (Prod)

- **API**: http://localhost
- **Banco de Dados PostgreSQL**: localhost:5432

### Características do Ambiente Prod

- Imagem otimizada (~50% menor)
- Apenas dependências de produção
- Código embutido na imagem (sem volumes)
- Auto-restart em caso de falha

## Estrutura do Projeto

```
/
├── api/                      # Backend Laravel
├── nginx/                    # Configuração Nginx
├── docker-compose.yml        # Dev
├── docker-compose.prod.yml   # Prod
├── Dockerfile                # Dev
└── Dockerfile.prod           # Prod
```

## Comandos Úteis

### Principais Comandos

```bash
make help          # Ver todos os comandos disponíveis
make setup         # Setup completo do projeto
make test          # Executar testes
make migrate       # Rodar migrations
make seed          # Popular banco de dados
make fresh         # Resetar DB (drop + migrate + seed)
```

### Docker

```bash
make up            # Subir containers
make down          # Parar containers
make restart       # Reiniciar containers
make logs          # Ver logs
make shell         # Acessar shell do container
make ps            # Listar containers
```

### Desenvolvimento

```bash
make tinker        # Abrir Laravel Tinker
make routes        # Listar todas as rotas
make clear-cache   # Limpar todos os caches
make optimize      # Otimizar aplicação
make permissions   # Corrigir permissões
```

### Produção

```bash
make prod-up       # Subir containers de produção
make prod-down     # Parar containers de produção
make prod-logs     # Ver logs de produção
make prod-migrate  # Rodar migrations em produção
make prod-seed     # Popular banco em produção
```

## Entidades

- **Produtor Rural**: Gestão de produtores
- **Propriedade**: Propriedades rurais vinculadas aos produtores
- **Unidade de Produção**: Culturas agrícolas nas propriedades
- **Rebanho**: Animais nas propriedades

## Funcionalidades

- CRUD completo para todas as entidades
- Exportação de propriedades em Excel
- Exportação de rebanhos em PDF
- Relatórios consolidados
- API RESTful documentada
