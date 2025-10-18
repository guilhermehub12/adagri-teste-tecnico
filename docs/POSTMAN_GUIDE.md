# 📮 Guia de Uso - ADAGRI Postman Collection

Este guia explica como usar a Collection do Postman para testar a API ADAGRI.

## 📥 Importando a Collection e Environment

### 1. Importar no Postman

1. Abra o Postman
2. Clique em **Import** (canto superior esquerdo)
3. Selecione os arquivos:
   - `ADAGRI_API.postman_collection.json`
   - `ADAGRI_Environment.postman_environment.json`
4. Clique em **Import**

### 2. Ativar o Environment

1. No canto superior direito, clique no dropdown de environments
2. Selecione **ADAGRI - Development**
3. O environment está ativo quando aparecer em destaque

## 🎯 Estrutura da Collection

A collection está organizada em 8 pastas:

### 1️⃣ Autenticação
- **Register** - Registrar novo usuário (perfil padrão: extensionista)
- **Login** - Login com qualquer usuário (salva token automaticamente)
- **Login como Admin** - Login rápido como admin
- **Login como Gestor** - Login rápido como gestor
- **Login como Técnico** - Login rápido como técnico
- **Login como Extensionista** - Login rápido como extensionista
- **Me** - Ver perfil do usuário autenticado
- **Logout** - Fazer logout (revoga token)

### 2️⃣ Gerenciamento de Usuários (Admin Only)
- Listar, criar, ver, atualizar e deletar usuários
- **Requer perfil:** Admin

### 3️⃣ Produtores Rurais
- CRUD completo de produtores rurais
- **Criar/Ler:** Admin, Gestor, Técnico
- **Atualizar:** Admin, Gestor
- **Deletar:** Admin

### 4️⃣ Propriedades
- CRUD completo de propriedades
- **Criar/Ler:** Admin, Gestor, Técnico
- **Atualizar:** Admin, Gestor
- **Deletar:** Admin

### 5️⃣ Unidades de Produção
- CRUD completo de unidades de produção
- **Criar/Ler:** Admin, Gestor, Técnico
- **Atualizar:** Admin, Gestor
- **Deletar:** Admin

### 6️⃣ Rebanhos
- CRUD completo de rebanhos
- **Criar/Ler:** Admin, Gestor, Técnico
- **Atualizar:** Admin, Gestor
- **Deletar:** Admin

### 7️⃣ Relatórios
- Propriedades por Município
- Animais por Espécie
- Hectares por Cultura
- **Acesso:** Todos os perfis (incluindo Extensionista)

### 8️⃣ Exportações
- Exportar Propriedades (Excel)
- Exportar Rebanhos (PDF)
- **Acesso:** Todos os perfis (incluindo Extensionista)

## 🔐 Sistema de Perfis

A API possui 4 perfis com permissões diferentes:

| Perfil | Criar | Editar | Deletar | Visualizar | Gerenciar Usuários |
|--------|-------|--------|---------|------------|-------------------|
| **Admin** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Gestor** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Técnico** | ✅ | ❌ | ❌ | ✅ | ❌ |
| **Extensionista** | ❌ | ❌ | ❌ | ✅ | ❌ |

## 🚀 Fluxo de Teste Recomendado

### Passo 1: Autenticação

Escolha um dos logins rápidos ou faça login manual:

```
1. Pasta "1. Autenticação"
2. Executar "Login como Admin" (ou outro perfil)
3. O token será salvo automaticamente
```

### Passo 2: Criar Dados Básicos

Execute as requisições nesta ordem:

```
1. "Criar Produtor" (pasta Produtores Rurais)
   → O ID será salvo em {{produtor_id}}

2. "Criar Propriedade" (pasta Propriedades)
   → Usa {{produtor_id}} automaticamente
   → O ID será salvo em {{propriedade_id}}

3. "Criar Unidade" (pasta Unidades de Produção)
   → Usa {{propriedade_id}} automaticamente
   → O ID será salvo em {{unidade_id}}

4. "Criar Rebanho" (pasta Rebanhos)
   → Usa {{propriedade_id}} automaticamente
   → O ID será salvo em {{rebanho_id}}
```

### Passo 3: Testar Operações

Agora você pode testar:
- Listagens
- Visualizações individuais
- Atualizações
- Exclusões (precisa de perfil Admin)
- Relatórios
- Exportações

## 📝 Variáveis Disponíveis

### Variáveis de Environment

| Variável | Descrição | Valor Padrão |
|----------|-----------|--------------|
| `base_url` | URL base da API | `http://localhost:8001/api` |
| `token` | Token de autenticação | *(auto-preenchido)* |
| `admin_email` | Email do admin | `admin@adagri.ce.gov.br` |
| `admin_password` | Senha do admin | `password123` |
| `gestor_email` | Email do gestor | `gestor@adagri.ce.gov.br` |
| `gestor_password` | Senha do gestor | `password123` |
| `tecnico_email` | Email do técnico | `tecnico@adagri.ce.gov.br` |
| `tecnico_password` | Senha do técnico | `password123` |
| `extensionista_email` | Email do extensionista | `extensionista@adagri.ce.gov.br` |
| `extensionista_password` | Senha do extensionista | `password123` |

### Variáveis de Collection (Auto-preenchidas)

| Variável | Descrição |
|----------|-----------|
| `produtor_id` | ID do último produtor criado |
| `propriedade_id` | ID da última propriedade criada |
| `unidade_id` | ID da última unidade criada |
| `rebanho_id` | ID do último rebanho criado |
| `user_id` | ID do usuário autenticado |
| `created_user_id` | ID do último usuário criado (admin) |

## 🔧 Configurações Avançadas

### Alterar URL Base

Se sua API estiver rodando em outra porta ou host:

1. Clique no ícone de olho (👁️) no canto superior direito
2. Clique em **Edit** no environment "ADAGRI - Development"
3. Altere o valor de `base_url`
4. Salve

### Scripts Automáticos

A collection possui scripts que executam automaticamente:

**Após Login:**
```javascript
// Salva o token automaticamente
pm.environment.set('token', response.token);
```

**Após Criar Recursos:**
```javascript
// Salva o ID do recurso criado
pm.collectionVariables.set('produtor_id', response.data.id);
```

## ⚠️ Troubleshooting

### Token não está sendo salvo

1. Verifique se o environment está ativo
2. Execute uma requisição de login novamente
3. Verifique no console (View → Show Postman Console)

### Erro 401 (Unauthenticated)

1. Faça login novamente
2. Verifique se o token está presente no environment (👁️)
3. Certifique-se de que a autenticação está habilitada na collection

### Erro 403 (Unauthorized)

Você não tem permissão para esta ação. Verifique:
- Perfil do usuário autenticado (use "Me")
- Faça login com um perfil adequado (Admin para deletar, Gestor para editar, etc.)

### IDs não estão sendo substituídos

1. Certifique-se de executar as requisições de criação primeiro
2. Verifique se os scripts de teste estão habilitados
3. Execute "Criar Produtor" antes de "Criar Propriedade"

## 📊 Testando Relatórios e Exportações

### Relatórios

Os relatórios aceitam filtros opcionais via query parameters:

**Propriedades por Município:**
```
GET /relatorios/propriedades-por-municipio?uf=CE&municipio=Fortaleza
```

**Animais por Espécie:**
```
GET /relatorios/animais-por-especie?especie=Bovino
```

**Hectares por Cultura:**
```
GET /relatorios/hectares-por-cultura?cultura=Milho
```

### Exportações

**Excel (Propriedades):**
```
GET /propriedades/export/excel?municipio=Fortaleza&uf=CE
```

**PDF (Rebanhos):**
```
GET /rebanhos/export/pdf?especie=Bovino&propriedade_id=1
```

Para ativar/desativar filtros:
1. Abra a requisição
2. Vá na aba **Params**
3. Marque/desmarque os checkboxes dos parâmetros


## 📚 Recursos Adicionais

- **Documentação da API:** Veja os comentários em cada requisição
- **Testes Automatizados:** Cada requisição tem scripts de teste
- **Variáveis Dinâmicas:** IDs são salvos automaticamente após criação
- **Filtros de Exemplo:** Todos os query params documentados
