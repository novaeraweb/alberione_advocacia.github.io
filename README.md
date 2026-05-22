# Alberione Advogados — Site Institucional
> Site one page institucional com blog, dashboard administrativo e painel de mensagens.

---

## 🏛️ Sobre o Projeto

Site completo do **Alberione Advogados**, escritório especializado em Direito Tributário, Societário e Empresarial.

**Tecnologias:** PHP 8+, MySQL, HTML5, CSS3, JavaScript ES6+  
**Design:** Paleta premium Navy `#1F4E79` + Gold `#B08A57` + Ivory `#F5F0E8`  
**Tipografia:** Playfair Display + Inter

---

## 📁 Estrutura de Arquivos

```
alberione/
├── index.php                  → Home institucional One Page
├── blog.php                   → Blog (Artigos + Informativos)
├── artigo.php                 → Página interna do artigo
├── 404.php                    → Página de erro personalizada
├── check.php                  → 🔍 Diagnóstico de instalação (REMOVER em produção)
├── .htaccess                  → URLs amigáveis + segurança + cache
├── .env.example               → Modelo de configuração
│
├── config/
│   ├── config.php             → Constantes, sessão, conexão (auto-detecta localhost)
│   ├── database.php           → PDO Singleton (função db())
│   └── helpers.php            → Funções: auth, posts, CSRF, upload, settings...
│
├── database/
│   └── alberione.sql          → SQL completo: 6 tabelas + views + dados iniciais
│
├── backend/
│   ├── contato.php            → API JSON do formulário de contato
│   └── sitemap.php            → Sitemap XML dinâmico
│
├── admin/
│   ├── login.php              → Login seguro com bcrypt
│   ├── index.php              → Dashboard com estatísticas
│   ├── posts.php              → Gerenciar posts (CRUD + filtros)
│   ├── post-novo.php          → Criar artigo/informativo (Quill + upload)
│   ├── post-edit.php          → Editar post existente
│   ├── mensagens.php          → Mensagens do formulário (split-panel)
│   ├── configuracoes.php      → 6 abas de configurações do site
│   ├── logout.php             → Logout seguro
│   └── partials/
│       ├── sidebar.php        → Sidebar com navegação dinâmica
│       ├── topbar.php         → Barra superior com flash messages
│       └── modal-confirm.php  → Modal JS de confirmação genérico
│
├── assets/
│   ├── css/
│   │   ├── style.css          → CSS da home (Navy/Gold/Ivory)
│   │   ├── blog.css           → CSS do blog e artigo
│   │   └── admin.css          → CSS do painel administrativo
│   └── js/
│       ├── main.js            → JS do site público
│       └── admin.js           → JS do painel admin
│
└── uploads/                   → Imagens enviadas pelo admin
    └── posts/                 → Capas dos posts
```

---

## 🗄️ Banco de Dados

### Tabelas

| Tabela | Descrição |
|---|---|
| `admins` | Usuário administrador do painel |
| `site_settings` | Configurações institucionais e técnicas |
| `posts` | Artigos e informativos do blog |
| `contact_messages` | Mensagens recebidas pelo formulário |
| `media_library` | Arquivos enviados pelo admin |
| `auth_logs` | Log de autenticação |

### Views

| View | Descrição |
|---|---|
| `v_posts_publicados` | Posts publicados com autor |
| `v_mensagens_ativas` | Mensagens não arquivadas |
| `v_dashboard_stats` | Contadores para o dashboard |

---

## 🚀 Instalação — Passo a Passo

### Para XAMPP (desenvolvimento local)

#### 1. Copiar arquivos
```
Copie a pasta do projeto para:
C:\xampp\htdocs\AlberioneAdvocacia\
```
> ⚠️ Certifique-se de que o `index.php` está diretamente em `AlberioneAdvocacia/`, não em subpastas.

#### 2. Habilitar mod_rewrite
Abra `C:\xampp\apache\conf\httpd.conf` e localize a linha:
```
#LoadModule rewrite_module modules/mod_rewrite.so
```
Remova o `#`:
```
LoadModule rewrite_module modules/mod_rewrite.so
```
E verifique se há `AllowOverride All` no bloco `<Directory "C:/xampp/htdocs">`:
```apache
<Directory "C:/xampp/htdocs">
    AllowOverride All
    ...
</Directory>
```

#### 3. Importar banco de dados
1. Abra `http://localhost:8888/phpmyadmin`
2. Crie o banco `alberione_site` (ou deixe o SQL criar automaticamente)
3. Clique em **Importar** e selecione `database/alberione.sql`

#### 4. Verificar instalação
Acesse: `http://localhost:8888/AlberioneAdvocacia/check.php`

O diagnóstico mostrará todos os itens em verde se tudo estiver correto.

#### 5. Acessar o sistema
- **Site:** `http://localhost:8888/AlberioneAdvocacia/`
- **Admin:** `http://localhost:8888/AlberioneAdvocacia/admin/login.php`
- **Login padrão:** `admin@alberione.com.br` / `password`

> ⚠️ **Troque a senha imediatamente após o primeiro acesso!**

---

### Para servidor de produção (cPanel/hospedagem)

#### 1. Fazer upload
- Envie todos os arquivos para a raiz do domínio (`public_html/`) via FTP ou gerenciador de arquivos
- Crie a pasta `uploads/posts/` com permissão `755`

#### 2. Criar banco de dados
Via cPanel → MySQL Databases:
1. Crie o banco `alberione_site`
2. Crie um usuário e associe ao banco com todos os privilégios
3. Importe `database/alberione.sql`

#### 3. Configurar credenciais
Edite `config/config.php`, seção "Banco de dados":
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'seu_banco');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

#### 4. Ativar HTTPS no .htaccess
Abra `.htaccess` e descomente o bloco de HTTPS:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteCond %{HTTP_HOST} !^localhost [NC]
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

#### 5. Trocar senha do admin
```php
# Gera hash no terminal PHP:
php -r "echo password_hash('SuaSenhaSegura@2025', PASSWORD_DEFAULT);"
```
```sql
UPDATE admins SET senha_hash='SEU_HASH_AQUI' WHERE email='admin@alberione.com.br';
```

#### 6. Remover check.php
```bash
rm check.php
```

---

## 🌐 URLs do sistema

| URL | Descrição |
|---|---|
| `/` | Home institucional one page |
| `/blog` | Listagem do blog (todos os tipos) |
| `/blog?tipo=artigo` | Filtro somente artigos |
| `/blog?tipo=informativo` | Filtro somente informativos |
| `/blog?q=busca` | Pesquisa no blog |
| `/artigo/[slug]` | Leitura do artigo |
| `/admin/` | Dashboard administrativo |
| `/admin/login.php` | Login do painel |
| `/admin/posts.php` | Gerenciar posts |
| `/admin/post-novo.php` | Criar novo post |
| `/admin/mensagens.php` | Mensagens de contato |
| `/admin/configuracoes.php` | Configurações do site |
| `/backend/contato.php` | API do formulário (POST JSON) |
| `/sitemap.xml` | Sitemap dinâmico |
| `/check.php` | Diagnóstico de instalação (remover em produção) |

---

## 🔐 Segurança implementada

- ✅ Senhas com `password_hash()` bcrypt (custo 12)
- ✅ CSRF token em todos os formulários POST
- ✅ PDO com prepared statements em 100% das queries
- ✅ Soft delete para posts (`excluido_em`)
- ✅ Validação MIME + extensão em uploads
- ✅ Rate limiting (5 req/hora por IP) no formulário de contato
- ✅ Honeypot anti-spam
- ✅ Sessão com `httpOnly`, `SameSite=Lax` e `secure` em HTTPS
- ✅ Bloqueio de acesso a arquivos sensíveis via `.htaccess`
- ✅ Headers de segurança (X-Frame-Options, X-XSS-Protection, etc.)
- ✅ `display_errors` desabilitado em produção, ativo em localhost
- ✅ `SITE_URL` detectado automaticamente (localhost vs produção)
- ✅ HTTPS force-redirect comentado por padrão (ativar em produção)

---

## ✨ Funcionalidades do Site Público

### Home One Page
- Seção Hero com CTA e link WhatsApp
- Sobre o escritório (texto do banco)
- Contadores animados (anos, clientes, casos)
- 6 Áreas de Atuação em grid
- 6 Diferenciais com ícones
- Blog — últimos posts do banco com destaque
- Formulário de contato AJAX com validação
- Dados de contato do banco (WhatsApp, email, endereço, redes sociais)
- Rodapé com redes sociais do banco
- WhatsApp flutuante

### Blog
- Listagem com paginação (9 por página)
- Filtro por Artigo / Informativo
- Busca por título e resumo
- Post em destaque no topo
- Badges por tipo (Artigo / Informativo)
- SEO dinâmico por filtro/busca

### Artigo
- Barra de progresso de leitura
- Compartilhamento: LinkedIn, WhatsApp, Twitter/X, copiar link
- Posts relacionados na sidebar
- CTA de contato
- Schema.org Article
- Open Graph e Twitter Card
- Contador de views automático

---

## 🖥️ Funcionalidades do Painel Admin

### Dashboard
- 4 cards de estatísticas reais (total posts, publicados, rascunhos, mensagens novas)
- Atalhos rápidos (novo artigo, novo informativo, ver mensagens, configurações)
- Últimos 6 posts com status
- Últimas 5 mensagens com destaque para novas

### Gerenciar Posts
- Listagem com filtros combinados (tipo, status, busca)
- Publicar / Despublicar inline sem recarregar
- Exclusão com confirmação (soft delete)
- Paginação configurável

### Novo / Editar Post
- Editor Quill WYSIWYG completo
- Upload de imagem com preview e drag & drop (até 5 MB)
- Slug auto-gerado e editável
- SEO por post (meta title + description com contador de caracteres)
- Status: rascunho / publicado
- Destaque na home
- Atalho Ctrl+S para salvar

### Mensagens
- Layout split-panel: lista lateral + detalhe completo
- Marcar como lida / respondida / arquivada
- Resposta direta por e-mail (mailto:)
- Resposta via WhatsApp (se telefone informado)
- Contadores por status (novas, lidas, respondidas, arquivadas)

### Configurações (6 abas)
1. **Identidade** — Nome, Hero title/subtitle, Sobre
2. **Contato** — E-mail, telefone, WhatsApp
3. **Endereço** — Rua, cidade, estado, CEP, horário
4. **Redes Sociais** — Instagram, Facebook, LinkedIn, YouTube
5. **SEO** — Meta title e description da home com preview em tempo real
6. **SMTP** — Configurações de e-mail transacional

---

## 🔧 Requisitos de Servidor

- **PHP** 8.0+ (recomendado 8.2)
- **MySQL** 5.7+ ou **MariaDB** 10.4+
- **Extensões PHP:** `pdo`, `pdo_mysql`, `mbstring`, `fileinfo`, `json`, `openssl`
- **Apache** com `mod_rewrite` habilitado e `AllowOverride All`
- **HTTPS** recomendado em produção

---

## 🐛 Troubleshooting

### Problema: Página em branco ou "uploads" apenas
**Causa:** Diretório listado porque `mod_rewrite` não está ativo ou `AllowOverride All` não configurado.
**Solução:**
1. Habilitar `mod_rewrite` no `httpd.conf`
2. Configurar `AllowOverride All` para a pasta `htdocs`
3. Reiniciar o Apache no XAMPP

### Problema: Erro PHP na tela (localhost)
**Causa:** `display_errors` está ativo em localhost — excelente para debug!
**Solução:** Leia o erro e consulte a seção de instalação acima. Os erros mais comuns são:
- `Connection refused` → MySQL não está rodando (inicie no painel XAMPP)
- `Unknown database` → Banco não foi importado ainda
- `No such file or directory` → Arquivo em subpasta errada

### Problema: Redirecionamento infinito (HTTPS loop)
**Causa:** HTTPS force-redirect ativo em localhost.
**Solução:** O `.htaccess` já tem o bloco HTTPS comentado por padrão. Não descomente em localhost.

### Problema: URL com subpasta não funciona
**Causa:** `RewriteBase /` está configurado mas o projeto está em `/AlberioneAdvocacia/`.
**Solução:** No `.htaccess`, altere:
```apache
# De:
RewriteBase /
# Para:
RewriteBase /AlberioneAdvocacia/
```

### Problema: Uploads não funcionam
**Causa:** Pasta `uploads/` sem permissão de escrita.
**Solução:**
```bash
# Linux/Mac:
chmod 755 uploads/ uploads/posts/
# Windows XAMPP: pasta já deve ter permissão automática
```

---

## 👤 Credenciais padrão de instalação

| Campo | Valor |
|---|---|
| E-mail | `admin@alberione.com.br` |
| Senha | `password` |
| URL Admin | `/admin/login.php` |

> ⚠️ **IMPORTANTE:** Troque a senha imediatamente após a primeira instalação.

---

## 📝 Próximas melhorias sugeridas

- [ ] Biblioteca de mídia com galeria
- [ ] Agendamento de publicação de posts
- [ ] Categorias e tags no blog
- [ ] Newsletter / captação de e-mails
- [ ] Integração PHPMailer para SMTP configurável
- [ ] Painel de SEO com análise por post
- [ ] Editor de áreas de atuação pelo painel
- [ ] Upload de logo e favicon pelo painel
- [ ] Backup automático do banco
- [ ] Logs de acesso do site público

---

*© Alberione Advogados — Desenvolvido por Nova Era Web*
