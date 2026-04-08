# 🌿 Portal de Notícias — Saúde & Bem-Estar

Sistema de portal de notícias desenvolvido em **PHP puro** com **MySQL**, focado em conteúdos sobre saúde, nutrição, fitness e qualidade de vida. Conta com painel de administração completo, sistema de aprovação de usuários e interface responsiva.

---

## 📋 Índice

- [Visão Geral](#-visão-geral)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Estrutura de Arquivos](#-estrutura-de-arquivos)
- [Banco de Dados](#-banco-de-dados)
- [Instalação](#-instalação)
- [Como Usar](#-como-usar)
- [Sistema de Permissões](#-sistema-de-permissões)
- [Painel Administrativo](#-painel-administrativo)

---

## 🌐 Visão Geral

O Portal Saúde & Bem-Estar é uma aplicação web onde autores cadastrados podem publicar e gerenciar notícias sobre saúde. Novos usuários precisam ter sua conta **aprovada por um administrador** antes de conseguirem fazer login, garantindo controle total sobre quem acessa o sistema.

---

## ✅ Funcionalidades

### Portal (público)
- Listagem de notícias com paginação
- Página completa de cada notícia
- Busca por título, conteúdo ou autor
- Interface responsiva para mobile e desktop

### Usuários
- Cadastro com senha criptografada (bcrypt)
- Login com verificação de aprovação pelo ADM
- Mensagem de "aguardando aprovação" ao tentar logar antes da liberação
- Edição de perfil (nome, e-mail, foto)
- Exclusão de conta

### Notícias
- Criar, editar e excluir notícias
- Upload de imagem por notícia (JPG, PNG, GIF — máx. 5MB)
- Dashboard pessoal com lista de notícias do autor

### Painel ADM
- Listagem de todos os usuários com filtros
- Aprovar ou revogar acesso de usuários
- Promover usuário a ADM ou rebaixar
- Criar usuários diretamente com permissões definidas
- Editar dados, senha e permissões de qualquer usuário
- Ativar/desativar contas sem excluir
- Excluir usuários permanentemente (com cascade nas notícias)
- Cards com estatísticas em tempo real (total, pendentes, aprovados, ADMs)

---

## 🛠 Tecnologias

| Camada | Tecnologia |
|--------|-----------|
| Back-end | PHP 8.2 |
| Banco de Dados | MySQL / MariaDB 10.4 |
| Estilização | CSS puro (variáveis, grid, flexbox) |
| Tipografia | Google Fonts — DM Serif Display, DM Sans, DM Mono |
| Servidor | Apache (XAMPP / WAMP / Laragon) |

> Sem frameworks, sem dependências externas de JavaScript. Tudo em PHP e CSS nativos.

---

## 📁 Estrutura de Arquivos

```
projeto-final/
│
├── index.php                        # Página inicial — lista de notícias
├── noticia.php                      # Página de notícia completa
├── login.php                        # Login de usuário
├── cadastro.php                     # Cadastro de novo usuário
├── logout.php                       # Encerra sessão
│
├── dashboard.php                    # Dashboard do usuário logado
├── nova_noticia.php                 # Criar nova notícia
├── editar_noticia.php               # Editar notícia existente
├── excluir_noticia.php              # Excluir notícia
├── editar_usuario.php               # Editar perfil do usuário
│
├── admin.php                        # Painel ADM — gerenciar usuários
├── admin_criar_usuario.php          # ADM — criar novo usuário
├── admin_editar_usuario.php         # ADM — editar usuário
│
├── conexao.php                      # Conexão com o banco + criação automática de tabelas
├── funcoes.php                      # Todas as funções do sistema
├── header.partial.php               # Header reutilizável (incluído em todas as páginas)
├── verifica_login.php               # Guard: redireciona se não estiver logado
├── verifica_adm.php                 # Guard: redireciona se não for ADM
│
├── style.css                        # Arquivo único de estilos (sem inline styles)
│
├── imagens/                         # Uploads de fotos de perfil e notícias
│
├── portal_saude_bem_estar_atualizado.sql   # SQL completo para instalação do zero
└── migration_adm.sql                       # Migration para bancos já existentes
```

---

## 🗄 Banco de Dados

### Banco: `portal_saude_bem_estar`

#### Tabela `usuarios`

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | INT AUTO_INCREMENT | Chave primária |
| `nome` | VARCHAR(100) | Nome completo |
| `email` | VARCHAR(100) UNIQUE | E-mail de acesso |
| `senha` | VARCHAR(255) | Hash bcrypt da senha |
| `data_criacao` | DATETIME | Data de cadastro |
| `ativo` | TINYINT(1) | Conta ativa (1) ou desativada (0) |
| `adm` | TINYINT(1) | É administrador (1) ou não (0) |
| `aprovado` | TINYINT(1) | Login liberado pelo ADM (1) ou pendente (0) |
| `foto` | VARCHAR(255) | Caminho da foto de perfil |

#### Tabela `noticias`

| Coluna | Tipo | Descrição |
|--------|------|-----------|
| `id` | INT AUTO_INCREMENT | Chave primária |
| `titulo` | VARCHAR(255) | Título da notícia |
| `noticia` | LONGTEXT | Conteúdo completo |
| `data` | DATETIME | Data de publicação |
| `autor` | INT | FK → `usuarios.id` (CASCADE DELETE) |
| `imagem` | VARCHAR(255) | Caminho da imagem da notícia |

---

## ⚙️ Instalação

### Pré-requisitos

- PHP 8.0 ou superior
- MySQL 5.7+ / MariaDB 10.4+
- Servidor Apache (XAMPP, WAMP, Laragon, etc.)

### Passo a passo

**1. Clone ou extraia o projeto**

Coloque a pasta `projeto-final` dentro de `htdocs` (XAMPP) ou `www` (WAMP/Laragon):

```
C:/xampp/htdocs/projeto-final/
```

**2. Configure o banco de dados**

Abra o phpMyAdmin e crie um banco chamado `portal_saude_bem_estar`.

Em seguida, importe o arquivo SQL conforme sua situação:

- **Instalação do zero:** importe `portal_saude_bem_estar_atualizado.sql`
- **Já tem o banco antigo:** execute apenas `migration_adm.sql`

**3. Configure a conexão**

Abra `conexao.php` e ajuste as credenciais se necessário:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // sua senha do MySQL
define('DB_NAME', 'portal_saude_bem_estar');
```

**4. Acesse no navegador**

```
http://localhost/projeto-final/
```

### Credenciais de teste (inclusas no SQL)

| Usuário | E-mail | Senha | Perfil |
|---------|--------|-------|--------|
| Gabriel Bandasz | gabrielprade15@gmail.com | *(hash no banco)* | ADM |
| Paulo Muzy | paulomuzy@gmail.com | *(hash no banco)* | Usuário aprovado |

> As senhas no SQL já estão em hash bcrypt. Para criar novos usuários com senha conhecida, use o Painel ADM após logar como Gabriel.

---

## 📖 Como Usar

### Fluxo de um novo usuário

```
1. Acessa /cadastro.php e cria sua conta
2. Vê a mensagem: "Aguarde a aprovação do administrador"
3. ADM acessa /admin.php e aprova o usuário
4. Usuário consegue fazer login normalmente
5. Acessa o dashboard e começa a publicar notícias
```

### Fluxo do Administrador

```
1. Faz login → redirecionado para o dashboard
2. Vê o botão "👑 Painel ADM" no header em todas as páginas
3. No painel: aprova/rejeita usuários, edita contas, promove ADMs
4. Pode criar usuários diretamente com permissões pré-definidas
```

---

## 🔐 Sistema de Permissões

O sistema possui três estados possíveis para um usuário:

| Estado | `ativo` | `adm` | `aprovado` | Pode logar? |
|--------|---------|-------|-----------|-------------|
| Pendente | 1 | 0 | 0 | ❌ Não — aguarda ADM |
| Aprovado | 1 | 0 | 1 | ✅ Sim |
| Administrador | 1 | 1 | 1 | ✅ Sim |
| Desativado | 0 | — | — | ❌ Não |

**Regras importantes:**
- Todo usuário criado pelo cadastro público começa com `aprovado = 0`
- Usuários ADM são automaticamente aprovados (`aprovado = 1`)
- Um ADM não pode remover seus próprios privilégios de ADM
- Um ADM não pode desativar ou excluir sua própria conta pelo painel
- Excluir um usuário também apaga todas as notícias dele (CASCADE)

---

## 👑 Painel Administrativo

Acessível em `/admin.php` apenas para usuários com `adm = 1`.

### Funcionalidades do painel

**Visão geral com cards de estatísticas:**
- Total de usuários cadastrados
- Quantidade aguardando aprovação
- Quantidade com acesso liberado
- Quantidade de administradores

**Tabela de usuários com filtros:**
- Filtrar por: Todos / Pendentes / Aprovados / ADMs
- Visualizar foto, nome, e-mail, data de cadastro, status e tipo
- Ações rápidas por linha: aprovar, revogar, promover a ADM, rebaixar, editar, ativar/desativar, excluir

**Criar usuário (`/admin_criar_usuario.php`):**
- Define nome, e-mail e senha
- Marca diretamente se será aprovado e/ou ADM

**Editar usuário (`/admin_editar_usuario.php`):**
- Altera nome e e-mail
- Redefine a senha (opcional)
- Alterna permissões: aprovado, ADM, ativo

---

## 🎨 Estilização

Todo o CSS está centralizado em um único arquivo `style.css`, organizado em seções:

- Variáveis CSS (paleta de cores, tipografia, sombras, transições)
- Reset & base
- Header e navegação
- Botões e alertas
- Formulários
- Cards de notícias e notícia completa
- Dashboard e tabelas
- Painel ADM (layout, sidebar, tabela, badges, botões de ação)
- Responsivo (breakpoints em 1024px, 900px, 768px e 480px)

**Nenhum arquivo PHP possui `style=` inline** — toda a estilização é feita exclusivamente via classes CSS.

---

## 👨‍💻 Desenvolvido por

**Gabriel Bandasz**  
Projeto Final — Portal de Notícias Saúde & Bem-Estar  
2026
