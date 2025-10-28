# Sistema de Gestão Agropecuária - ADAGRI

Sistema completo de gestão agropecuária com Laravel 12 (backend) e Vue 3 (frontend).

## 🎯 Sobre o Projeto

Sistema web para gestão de propriedades rurais, produtores, rebanhos e culturas agrícolas, com dashboard interativo e relatórios visuais em tempo real.

## 🛠️ Tecnologias

### Backend
- **Laravel 12** - Framework PHP
- **PostgreSQL 16** - Banco de dados
- **JWT Authentication** - Autenticação segura
- **PHP 8.3** - Linguagem

### Frontend
- **Vue 3** (Composition API) - Framework JavaScript
- **TypeScript** - Tipagem estática
- **Vite** - Build tool e dev server
- **PrimeVue** - Biblioteca de componentes UI
- **Tailwind CSS** - Framework CSS
- **ApexCharts** - Gráficos interativos
- **Pinia** - Gerenciamento de estado
- **Vue Router** - Roteamento
- **Axios** - Cliente HTTP

### DevOps
- **Docker & Docker Compose** - Containerização
- **Nginx** - Servidor web e proxy reverso
- **Multi-Stage Builds** - Otimização de imagens

## 💻 Requisitos

- Docker 20.10+
- Docker Compose 2.0+
- Make (opcional, para comandos facilitados)

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

**Pronto!** 🎉
- **Frontend**: http://localhost:5173
- **API**: http://localhost:8000/api

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

## 📊 Dashboard e Relatórios

O sistema inclui um **dashboard interativo** com gráficos em tempo real:

### Gráficos Disponíveis

1. **Propriedades por Município**
   - Gráfico de barras
   - Filtros multi-seleção por UF e Município
   - Visualização consolidada de propriedades

2. **Animais por Espécie**
   - Gráfico de rosca (donut)
   - Filtro multi-seleção de espécies
   - Total de animais por categoria

3. **Hectares por Cultura**
   - Gráfico de barras horizontal
   - Filtro multi-seleção de culturas
   - Área total cultivada

### Recursos dos Gráficos

- ✅ **Filtros multi-seleção** com checkboxes
- ✅ **Modo escuro** (dark mode) completo
- ✅ **Interatividade** (hover, zoom, exportação)
- ✅ **Responsivo** para mobile e desktop
- ✅ **Estados de loading e erro** tratados
- ✅ **Atualização em tempo real**

## 📮 Testando a API

1. Importe a Collection do Postman: `ADAGRI_API.postman_collection.json`
2. Importe o Environment: `ADAGRI_Environment.postman_environment.json`
3. Selecione o environment "ADAGRI - Development"
4. Execute "Login como Admin" (ou qualquer outro perfil)
5. Comece a testar!

**Guia completo:** [`docs/POSTMAN_GUIDE.md`](docs/POSTMAN_GUIDE.md)

## 🔧 Ambiente de Desenvolvimento

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

- **Frontend (Vue + Vite)**: http://localhost:5173
- **API (Laravel)**: http://localhost:8000/api
- **Nginx Gateway**: http://localhost:8000
- **Banco de Dados PostgreSQL**: localhost:5432
  - Database: `adagri_db`
  - Username: `adagri_user`
  - Password: `adagri_pass`

### Características do Ambiente Dev

- ✅ Hot reload (frontend e backend)
- ✅ Vite dev server (HMR ultra rápido)
- ✅ Dependências de desenvolvimento incluídas
- ✅ Ferramentas de debug
- ✅ Volume mount do código fonte
- ✅ Source maps habilitados
- ✅ Logs detalhados

## 🚀 Ambiente de Produção

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

- **Aplicação Completa**: http://localhost
- **API**: http://localhost/api
- **Banco de Dados PostgreSQL**: localhost:5432

### Características do Ambiente Prod

- 🚀 **Multi-stage builds** (imagens 75% menores)
- ⚡ **OPcache habilitado** (10x mais rápido)
- 📦 **Apenas dependências de produção** (sem pacotes -dev)
- 🗜️ **Gzip compression** ativado
- 🔒 **Security headers** configurados
- ❤️ **Health checks** automáticos
- 🔄 **Auto-restart** em caso de falha
- 🎯 **Frontend servido por Nginx** (otimizado)
- 🌐 **Gateway reverso** centralizado
- 💾 **Código embutido na imagem** (sem volumes)

### Otimizações de Produção

| Componente | Dev | Produção | Ganho |
|------------|-----|----------|-------|
| **API** | ~250-300 MB | ~80-100 MB | 70% menor |
| **Frontend** | ~400-500 MB | ~20-25 MB | 95% menor |
| **Performance** | Normal | OPcache + Cache | 10x mais rápido |
| **Autoloader** | PSR-4 | Classmap | 3x mais rápido |

## 📁 Estrutura do Projeto

```
adagri/
├── api/                          # Backend Laravel 12
│   ├── app/                      # Código da aplicação
│   ├── database/                 # Migrations e seeders
│   ├── routes/                   # Rotas da API
│   ├── Dockerfile                # Docker para desenvolvimento
│   ├── Dockerfile.prod           # Docker para produção (multi-stage)
│   ├── entrypoint.sh             # Script de inicialização (dev)
│   └── entrypoint.prod.sh        # Script de inicialização (prod)
│
├── frontend/                     # Frontend Vue 3
│   ├── src/
│   │   ├── components/           # Componentes reutilizáveis
│   │   │   ├── charts/           # Gráficos (ApexCharts)
│   │   │   ├── Header.vue
│   │   │   └── Sidebar.vue
│   │   ├── views/                # Views/Páginas
│   │   │   ├── DashboardView.vue
│   │   │   ├── LoginView.vue
│   │   │   └── ...
│   │   ├── services/             # Serviços de API
│   │   │   ├── reportService.ts
│   │   │   └── api.ts
│   │   ├── stores/               # Pinia stores
│   │   │   ├── auth.ts
│   │   │   └── dashboard.ts
│   │   ├── router/               # Vue Router
│   │   └── main.ts               # Entry point
│   ├── Dockerfile                # Docker para desenvolvimento
│   ├── Dockerfile.prod           # Docker para produção (multi-stage)
│   └── nginx.conf                # Nginx config para SPA
│
├── nginx/                        # Configurações Nginx
│   ├── nginx.conf                # Config para desenvolvimento
│   └── nginx.prod.conf           # Config para produção (gateway reverso)
│
├── docker-compose.yml            # Orquestração - Desenvolvimento
├── docker-compose.prod.yml       # Orquestração - Produção
└── Makefile                      # Comandos automatizados
```

## 🎮 Comandos Úteis

### Principais Comandos

```bash
make help          # Ver todos os comandos disponíveis
make setup         # Setup completo do projeto
make test          # Executar testes
make migrate       # Rodar migrations
make seed          # Popular banco de dados
make fresh         # Resetar DB (drop + migrate + seed)
```

### Docker - Desenvolvimento

```bash
make up            # Subir containers
make up-build      # Rebuild + subir
make down          # Parar containers
make restart       # Reiniciar containers
make logs          # Ver logs
make shell         # Acessar shell do container
make ps            # Listar containers
```

### Docker - Produção

```bash
make prod-up       # Subir containers de produção (build otimizado)
make prod-down     # Parar containers de produção
make prod-logs     # Ver logs de produção
make prod-ps       # Listar containers de produção
make prod-migrate  # Rodar migrations em produção
make prod-seed     # Popular banco em produção
```

### Desenvolvimento

```bash
make tinker        # Abrir Laravel Tinker
make routes        # Listar todas as rotas
make clear-cache   # Limpar todos os caches
make optimize      # Otimizar aplicação
make permissions   # Corrigir permissões
```

## 📦 Entidades

- **Produtor Rural**: Gestão de produtores rurais
  - Dados pessoais, contato e documentação

- **Propriedade**: Propriedades rurais vinculadas aos produtores
  - Localização, área total, coordenadas GPS

- **Unidade de Produção**: Culturas agrícolas nas propriedades
  - Tipo de cultura, área cultivada (hectares)

- **Rebanho**: Gestão de animais nas propriedades
  - Espécie, quantidade, finalidade

## ✨ Funcionalidades

### Backend (API)
- ✅ CRUD completo para todas as entidades
- ✅ Autenticação JWT com refresh tokens
- ✅ Sistema de permissões por perfil
- ✅ Exportação de propriedades em Excel
- ✅ Exportação de rebanhos em PDF
- ✅ Relatórios consolidados (JSON)
- ✅ Validação robusta de dados
- ✅ Tratamento de erros padronizado
- ✅ API RESTful documentada

### Frontend (Vue)
- ✅ Dashboard com gráficos interativos
- ✅ Filtros multi-seleção avançados
- ✅ Modo escuro (dark mode)
- ✅ Interface responsiva
- ✅ Autenticação com guards
- ✅ Gestão de estado com Pinia
- ✅ Componentes reutilizáveis
- ✅ TypeScript para type safety
- ✅ Loading states e tratamento de erros
- ✅ Navegação intuitiva

## 🧪 Testes

### Backend

```bash
# Executar todos os testes
make test

# Executar com coverage
./vendor/bin/pest --coverage
```

### Frontend

```bash
# Executar testes unitários
cd frontend
npm run test

# Executar com UI
npm run test:ui

# Coverage
npm run test:coverage
```

**Cobertura atual**: 64 testes passando (componentes, services, stores)

## 🔒 Segurança

- ✅ Autenticação JWT
- ✅ Refresh tokens
- ✅ Sistema de permissões por perfil
- ✅ Validação de entrada no backend
- ✅ CORS configurado
- ✅ Rate limiting
- ✅ Security headers (Nginx)
- ✅ Sanitização de dados
- ✅ HTTPS ready (configurar certificado SSL)

## 📝 Documentação Adicional

- [Guia do Postman](docs/POSTMAN_GUIDE.md)
- [Plano de Implementação CRUD](docs/CRUD_IMPLEMENTATION_PLAN.md)

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto foi desenvolvido como teste técnico.

## 👨‍💻 Autor

**Guilherme Rodrigues**
- GitHub: [@guilhermehub12](https://github.com/guilhermehub12)
- Email: guilhermedelmiro11@gmail.com

---

⭐ **Desenvolvido com Laravel 12, Vue 3 e muito ☕**
