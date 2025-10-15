# Sistema de Gestão Agropecuária - ADAGRI

Sistema completo de gestão agropecuária com Laravel (backend) e Vue 3 (frontend).

## Requisitos

- Docker
- Docker Compose

## Ambiente de Desenvolvimento

### 1. Subir os containers

```bash
docker compose up -d --build
```

### 2. Executar migrations

```bash
docker compose exec app php artisan migrate
```

### 3. (Opcional) Executar seeders

```bash
docker compose exec app php artisan db:seed
```

### Acesso (Dev)

- **API**: http://localhost:8000
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
docker compose -f docker-compose.prod.yml up -d --build
```

### 2. Executar migrations

```bash
docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
```

### 3. Executar seeders

```bash
docker compose -f docker-compose.prod.yml exec app php artisan db:seed --force
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

### Desenvolvimento

```bash
docker compose up -d --build              # Subir containers
docker compose down                       # Parar containers
docker compose logs -f                    # Ver logs
docker compose exec app php artisan [cmd] # Executar artisan
docker compose exec app sh                # Acessar container
```

### Produção

```bash
docker compose -f docker-compose.prod.yml up -d --build              # Subir containers
docker compose -f docker-compose.prod.yml down                       # Parar containers
docker compose -f docker-compose.prod.yml logs -f                    # Ver logs
docker compose -f docker-compose.prod.yml exec app php artisan [cmd] # Executar artisan
docker compose -f docker-compose.prod.yml exec app sh                # Acessar container
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
