# ==================================================================================
# ADAGRI - Sistema de Gestão Agropecuária
# ==================================================================================
# Makefile para automação de tarefas do projeto
# Use 'make help' para ver todos os comandos disponíveis
# ==================================================================================

# Cores para output
GREEN  := \033[0;32m
YELLOW := \033[1;33m
RED    := \033[0;31m
BLUE   := \033[0;34m
NC     := \033[0m # No Color

# Docker Compose command
DOCKER_COMPOSE := docker compose
DOCKER_COMPOSE_PROD := docker compose -f docker-compose.prod.yml

# Container name
APP_CONTAINER := adagri_app

# ==================================================================================
# Default target - mostra ajuda
# ==================================================================================
.DEFAULT_GOAL := help

.PHONY: help
help: ## Mostra esta mensagem de ajuda
	@echo "$(BLUE)╔════════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BLUE)║           ADAGRI - Comandos Disponíveis                    ║$(NC)"
	@echo "$(BLUE)╚════════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@awk 'BEGIN {FS = ":.*##"; printf "Use: $(YELLOW)make <target>$(NC)\n\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2 } /^##@/ { printf "\n$(BLUE)%s$(NC)\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
	@echo ""

##@ 🚀 Setup e Instalação

.PHONY: setup
setup: ## Setup completo do projeto (primeira vez)
	@echo "$(YELLOW)🚀 Setting up ADAGRI project...$(NC)"
	@$(MAKE) check-docker
	@echo "$(YELLOW)📦 Installing dependencies...$(NC)"
	@$(DOCKER_COMPOSE) exec app composer install
	@$(MAKE) env
	@$(MAKE) key
	@$(MAKE) permissions
	@$(MAKE) migrate
	@$(MAKE) seed
	@$(MAKE) clear-cache
	@echo ""
	@echo "$(GREEN)✅ Setup complete!$(NC)"
	@echo ""
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo "$(GREEN)📍 API available at: http://localhost:8000/api$(NC)"
	@echo "$(GREEN)📮 Import Postman collection: ADAGRI_API.postman_collection.json$(NC)"
	@echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
	@echo ""
	@echo "$(YELLOW)🔐 Test accounts:$(NC)"
	@echo "  Admin:         admin@adagri.ce.gov.br / password123"
	@echo "  Gestor:        gestor@adagri.ce.gov.br / password123"
	@echo "  Técnico:       tecnico@adagri.ce.gov.br / password123"
	@echo "  Extensionista: extensionista@adagri.ce.gov.br / password123"
	@echo ""

.PHONY: install
install: ## Instala dependências do Composer
	@echo "$(YELLOW)📦 Installing Composer dependencies...$(NC)"
	@$(DOCKER_COMPOSE) exec app composer install
	@echo "$(GREEN)✅ Dependencies installed!$(NC)"

.PHONY: env
env: ## Cria arquivo .env a partir do .env.example
	@echo "$(YELLOW)⚙️  Setting up environment...$(NC)"
	@if [ ! -f api/.env ]; then \
		cp api/.env.example api/.env; \
		echo "$(GREEN)✅ .env file created$(NC)"; \
	else \
		echo "$(GREEN)✅ .env file already exists$(NC)"; \
	fi

.PHONY: key
key: ## Gera chave de aplicação Laravel
	@echo "$(YELLOW)🔑 Generating application key...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan key:generate
	@echo "$(GREEN)✅ Application key generated!$(NC)"

.PHONY: permissions
permissions: ## Corrige permissões de arquivos e diretórios
	@echo "$(YELLOW)🔐 Fixing file permissions...$(NC)"
	@echo "$(YELLOW)📁 Fixing directory permissions (755)...$(NC)"
	@find ./api/app -type d -exec chmod 755 {} \;
	@echo "$(YELLOW)📄 Fixing file permissions (644)...$(NC)"
	@find ./api/app -type f -exec chmod 644 {} \;
	@echo "$(YELLOW)📝 Making storage and cache writable (775)...$(NC)"
	@chmod -R 775 api/storage api/bootstrap/cache
	@echo "$(GREEN)✅ Permissions fixed!$(NC)"
	@echo ""
	@echo "Permission breakdown:"
	@echo "  Directories (app/):     755 (rwxr-xr-x)"
	@echo "  Files (app/):           644 (rw-r--r--)"
	@echo "  Storage/Cache:          775 (rwxrwxr-x)"
	@echo ""
	@echo "$(YELLOW)💡 If you still have permission issues, run: make clear-cache$(NC)"

##@ 🗄️  Database

.PHONY: migrate
migrate: ## Executa migrations do banco de dados
	@echo "$(YELLOW)🗄️  Running migrations...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan migrate
	@echo "$(GREEN)✅ Migrations completed!$(NC)"

.PHONY: migrate-fresh
migrate-fresh: ## Reseta o banco e executa migrations (APAGA TUDO!)
	@echo "$(RED)⚠️  WARNING: This will DROP all tables and data!$(NC)"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		echo "$(YELLOW)🗄️  Dropping all tables and running migrations...$(NC)"; \
		$(DOCKER_COMPOSE) exec app php artisan migrate:fresh; \
		echo "$(GREEN)✅ Database reset complete!$(NC)"; \
	else \
		echo "$(BLUE)Cancelled.$(NC)"; \
	fi

.PHONY: migrate-rollback
migrate-rollback: ## Desfaz última migration
	@echo "$(YELLOW)⏪ Rolling back last migration...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan migrate:rollback
	@echo "$(GREEN)✅ Rollback completed!$(NC)"

.PHONY: seed
seed: ## Popula banco de dados com dados de teste
	@echo "$(YELLOW)🌱 Seeding database...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan db:seed
	@echo "$(GREEN)✅ Database seeded!$(NC)"

.PHONY: fresh
fresh: migrate-fresh seed ## Reseta DB + Migrations + Seeders (APAGA TUDO!)

##@ 🐳 Docker

.PHONY: up
up: ## Sobe os containers em modo development
	@echo "$(YELLOW)🐳 Starting Docker containers...$(NC)"
	@$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✅ Containers are running!$(NC)"
	@echo "$(BLUE)API: http://localhost:8000$(NC)"

.PHONY: up-build
up-build: ## Sobe os containers fazendo rebuild das imagens
	@echo "$(YELLOW)🐳 Building and starting Docker containers...$(NC)"
	@$(DOCKER_COMPOSE) up -d --build
	@echo "$(GREEN)✅ Containers built and running!$(NC)"

.PHONY: down
down: ## Para os containers
	@echo "$(YELLOW)🛑 Stopping Docker containers...$(NC)"
	@$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✅ Containers stopped!$(NC)"

.PHONY: restart
restart: down up ## Reinicia os containers

.PHONY: logs
logs: ## Mostra logs dos containers
	@$(DOCKER_COMPOSE) logs -f

.PHONY: ps
ps: ## Lista containers em execução
	@$(DOCKER_COMPOSE) ps

##@ 🚀 Produção

.PHONY: prod-up
prod-up: ## Sobe containers em modo PRODUÇÃO
	@echo "$(YELLOW)🚀 Starting PRODUCTION containers...$(NC)"
	@$(DOCKER_COMPOSE_PROD) up -d --build
	@echo "$(GREEN)✅ Production containers running!$(NC)"
	@echo "$(BLUE)Acessível em: http://localhost$(NC)"

.PHONY: prod-down
prod-down: ## Para containers de produção
	@echo "$(YELLOW)🛑 Stopping PRODUCTION containers...$(NC)"
	@$(DOCKER_COMPOSE_PROD) down
	@echo "$(GREEN)✅ Production containers stopped!$(NC)"

.PHONY: prod-logs
prod-logs: ## Mostra logs dos containers de produção
	@$(DOCKER_COMPOSE_PROD) logs -f

.PHONY: prod-ps
prod-ps: ## Lista containers de produção
	@$(DOCKER_COMPOSE_PROD) ps

##@ 🔧 Desenvolvimento

.PHONY: shell
shell: ## Acessa shell do container da aplicação
	@echo "$(BLUE)Opening shell in app container...$(NC)"
	@$(DOCKER_COMPOSE) exec app sh

.PHONY: tinker
tinker: ## Abre Laravel Tinker
	@$(DOCKER_COMPOSE) exec app php artisan tinker

.PHONY: test
test: ## Executa todos os testes
	@echo "$(YELLOW)🧪 Running tests...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan test
	@echo "$(GREEN)✅ Tests completed!$(NC)"

.PHONY: test-coverage
test-coverage: ## Executa testes com cobertura
	@echo "$(YELLOW)🧪 Running tests with coverage...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan test --coverage

.PHONY: clear-cache
clear-cache: ## Limpa todos os caches do Laravel
	@echo "$(YELLOW)🧹 Clearing all caches...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan optimize:clear
	@$(DOCKER_COMPOSE) exec app composer dump-autoload
	@echo "$(GREEN)✅ All caches cleared!$(NC)"

.PHONY: optimize
optimize: ## Otimiza a aplicação (cache de rotas, config, views)
	@echo "$(YELLOW)⚡ Optimizing application...$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan optimize
	@echo "$(GREEN)✅ Application optimized!$(NC)"

.PHONY: routes
routes: ## Lista todas as rotas da API
	@$(DOCKER_COMPOSE) exec app php artisan route:list

##@ 📦 Produção

.PHONY: prod-up
prod-up: ## Sobe containers em modo produção
	@echo "$(YELLOW)🚀 Starting production containers...$(NC)"
	@$(DOCKER_COMPOSE_PROD) up -d --build
	@echo "$(GREEN)✅ Production containers running!$(NC)"

.PHONY: prod-down
prod-down: ## Para containers de produção
	@$(DOCKER_COMPOSE_PROD) down

.PHONY: prod-logs
prod-logs: ## Mostra logs dos containers de produção
	@$(DOCKER_COMPOSE_PROD) logs -f

.PHONY: prod-migrate
prod-migrate: ## Executa migrations em produção
	@echo "$(YELLOW)🗄️  Running production migrations...$(NC)"
	@$(DOCKER_COMPOSE_PROD) exec app php artisan migrate --force
	@echo "$(GREEN)✅ Production migrations completed!$(NC)"

.PHONY: prod-seed
prod-seed: ## Popula banco em produção
	@echo "$(YELLOW)🌱 Seeding production database...$(NC)"
	@$(DOCKER_COMPOSE_PROD) exec app php artisan db:seed --force
	@echo "$(GREEN)✅ Production database seeded!$(NC)"

##@ 🧹 Limpeza

.PHONY: clean
clean: ## Remove containers, volumes e imagens
	@echo "$(RED)⚠️  This will remove all containers, volumes, and images!$(NC)"
	@read -p "Are you sure? [y/N] " -n 1 -r; \
	echo; \
	if [[ $$REPLY =~ ^[Yy]$$ ]]; then \
		echo "$(YELLOW)🧹 Cleaning up...$(NC)"; \
		$(DOCKER_COMPOSE) down -v --rmi all; \
		echo "$(GREEN)✅ Cleanup complete!$(NC)"; \
	else \
		echo "$(BLUE)Cancelled.$(NC)"; \
	fi

##@ 🔍 Utilities

.PHONY: check-docker
check-docker: ## Verifica se Docker está rodando
	@if ! docker info > /dev/null 2>&1; then \
		echo "$(RED)❌ Docker is not running. Please start Docker first.$(NC)"; \
		exit 1; \
	fi
	@echo "$(GREEN)✅ Docker is running$(NC)"

.PHONY: status
status: ## Mostra status do projeto
	@echo "$(BLUE)╔════════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BLUE)║                   ADAGRI Status                            ║$(NC)"
	@echo "$(BLUE)╚════════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo "$(YELLOW)Docker Containers:$(NC)"
	@$(DOCKER_COMPOSE) ps
	@echo ""
	@echo "$(YELLOW)Database Connection:$(NC)"
	@$(DOCKER_COMPOSE) exec app php artisan db:show 2>/dev/null || echo "$(RED)❌ Cannot connect to database$(NC)"
	@echo ""
