-- ============================================================
-- Alberione Advogados — Banco de Dados Final (Produção)
-- Arquivo: database/alberione.sql
-- Versão: 2.0 | Charset: utf8mb4 | Engine: InnoDB
-- ============================================================

SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
SET time_zone = '-03:00';
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS alberione_site
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE alberione_site;

-- ============================================================
-- TABELA 1: ADMINISTRADORES
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    nome          VARCHAR(120)   NOT NULL,
    email         VARCHAR(150)   NOT NULL,
    senha_hash    VARCHAR(255)   NOT NULL,
    ativo         TINYINT(1)     NOT NULL DEFAULT 1,
    ultimo_login_em DATETIME     NULL     DEFAULT NULL,
    criado_em     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuários administradores do painel';

-- ============================================================
-- TABELA 2: CONFIGURAÇÕES DO SITE
-- ============================================================
CREATE TABLE IF NOT EXISTS site_settings (
    id                    INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    config_key            VARCHAR(50)   NOT NULL DEFAULT 'default',

    -- Identidade
    office_name           VARCHAR(150)  NOT NULL DEFAULT 'Alberione Advogados',
    hero_title            VARCHAR(255)  NULL DEFAULT NULL,
    hero_subtitle         TEXT          NULL DEFAULT NULL,
    about_title           VARCHAR(255)  NULL DEFAULT NULL,
    about_text            LONGTEXT      NULL DEFAULT NULL,

    -- Contato
    email_contato         VARCHAR(150)  NULL DEFAULT NULL,
    email_destino_form    VARCHAR(150)  NULL DEFAULT NULL,
    telefone              VARCHAR(30)   NULL DEFAULT NULL,
    whatsapp_numero       VARCHAR(30)   NULL DEFAULT NULL,
    whatsapp_link         VARCHAR(255)  NULL DEFAULT NULL,

    -- Endereço
    endereco_linha1       VARCHAR(255)  NULL DEFAULT NULL,
    endereco_linha2       VARCHAR(255)  NULL DEFAULT NULL,
    cidade                VARCHAR(120)  NULL DEFAULT NULL,
    estado                VARCHAR(60)   NULL DEFAULT NULL,
    cep                   VARCHAR(20)   NULL DEFAULT NULL,
    horario_atendimento   VARCHAR(120)  NULL DEFAULT NULL,

    -- Redes Sociais
    instagram_url         VARCHAR(255)  NULL DEFAULT NULL,
    facebook_url          VARCHAR(255)  NULL DEFAULT NULL,
    linkedin_url          VARCHAR(255)  NULL DEFAULT NULL,
    youtube_url           VARCHAR(255)  NULL DEFAULT NULL,

    -- SEO
    seo_home_title        VARCHAR(255)  NULL DEFAULT NULL,
    seo_home_description  VARCHAR(320)  NULL DEFAULT NULL,

    -- Visual
    logo_path             VARCHAR(255)  NULL DEFAULT NULL,
    favicon_path          VARCHAR(255)  NULL DEFAULT NULL,

    -- SMTP
    smtp_host             VARCHAR(150)  NULL DEFAULT NULL,
    smtp_port             SMALLINT      NULL DEFAULT 587,
    smtp_user             VARCHAR(150)  NULL DEFAULT NULL,
    smtp_pass             VARCHAR(255)  NULL DEFAULT NULL,
    smtp_secure           ENUM('tls','ssl','none') NOT NULL DEFAULT 'tls',
    smtp_from_name        VARCHAR(150)  NULL DEFAULT NULL,
    smtp_from_email       VARCHAR(150)  NULL DEFAULT NULL,

    criado_em             DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em         DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_site_settings_key (config_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Configurações gerais e institucionais do site';

-- ============================================================
-- TABELA 3: POSTS DO BLOG
-- ============================================================
CREATE TABLE IF NOT EXISTS posts (
    id                INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    autor_id          INT UNSIGNED   NOT NULL,
    tipo              ENUM('artigo','informativo') NOT NULL DEFAULT 'artigo',
    categoria         VARCHAR(100)   NULL DEFAULT NULL,
    titulo            VARCHAR(255)   NOT NULL,
    slug              VARCHAR(255)   NOT NULL,
    resumo            TEXT           NULL DEFAULT NULL,
    conteudo          LONGTEXT       NOT NULL,
    imagem_capa       VARCHAR(255)   NULL DEFAULT NULL,
    imagem_alt        VARCHAR(255)   NULL DEFAULT NULL,
    status            ENUM('rascunho','publicado') NOT NULL DEFAULT 'rascunho',
    destaque          TINYINT(1)     NOT NULL DEFAULT 0,
    meta_title        VARCHAR(255)   NULL DEFAULT NULL,
    meta_description  VARCHAR(320)   NULL DEFAULT NULL,
    views_count       INT UNSIGNED   NOT NULL DEFAULT 0,
    publicado_em      DATETIME       NULL DEFAULT NULL,
    criado_em         DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    excluido_em       DATETIME       NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY uq_posts_slug (slug),
    KEY idx_posts_tipo (tipo),
    KEY idx_posts_categoria (categoria),
    KEY idx_posts_status (status),
    KEY idx_posts_destaque (destaque),
    KEY idx_posts_autor_id (autor_id),
    KEY idx_posts_publicado_em (publicado_em),
    KEY idx_posts_blog_query (tipo, status, publicado_em, excluido_em),
    KEY idx_posts_categoria_pub (categoria, status, publicado_em, excluido_em),
    KEY idx_posts_destaque_pub (destaque, status, publicado_em, excluido_em),

    CONSTRAINT fk_posts_autor
        FOREIGN KEY (autor_id) REFERENCES admins(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Artigos e informativos do blog';

-- ============================================================
-- TABELA 4: MENSAGENS DE CONTATO
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    nome          VARCHAR(120)   NOT NULL,
    email         VARCHAR(150)   NOT NULL,
    telefone      VARCHAR(30)    NULL DEFAULT NULL,
    assunto       VARCHAR(180)   NULL DEFAULT NULL,
    mensagem      TEXT           NOT NULL,
    origem_pagina VARCHAR(255)   NULL DEFAULT NULL,
    ip_address    VARCHAR(45)    NULL DEFAULT NULL,
    user_agent    TEXT           NULL DEFAULT NULL,
    status        ENUM('novo','lido','respondido','arquivado') NOT NULL DEFAULT 'novo',
    respondido_em DATETIME       NULL DEFAULT NULL,
    criado_em     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_contact_status (status),
    KEY idx_contact_email (email),
    KEY idx_contact_criado_em (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Mensagens recebidas pelo formulário de contato';

-- ============================================================
-- TABELA 5: BIBLIOTECA DE MÍDIA
-- ============================================================
CREATE TABLE IF NOT EXISTS media_library (
    id               INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    admin_id         INT UNSIGNED   NULL DEFAULT NULL,
    arquivo_original VARCHAR(255)   NOT NULL,
    arquivo_nome     VARCHAR(255)   NOT NULL,
    arquivo_path     VARCHAR(255)   NOT NULL,
    mime_type        VARCHAR(100)   NULL DEFAULT NULL,
    extensao         VARCHAR(20)    NULL DEFAULT NULL,
    tamanho_bytes    BIGINT UNSIGNED NULL DEFAULT NULL,
    largura          INT            NULL DEFAULT NULL,
    altura           INT            NULL DEFAULT NULL,
    criado_em        DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_media_admin_id (admin_id),
    KEY idx_media_criado_em (criado_em),

    CONSTRAINT fk_media_admin
        FOREIGN KEY (admin_id) REFERENCES admins(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Imagens e arquivos enviados pelo painel';

-- ============================================================
-- TABELA 6: LOGS DE AUTENTICAÇÃO
-- ============================================================
CREATE TABLE IF NOT EXISTS auth_logs (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id      INT UNSIGNED    NULL DEFAULT NULL,
    email_tentado VARCHAR(150)    NULL DEFAULT NULL,
    ip_address    VARCHAR(45)     NULL DEFAULT NULL,
    user_agent    TEXT            NULL DEFAULT NULL,
    acao          ENUM('login_sucesso','login_falha','logout') NOT NULL,
    criado_em     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_auth_admin_id (admin_id),
    KEY idx_auth_acao (acao),
    KEY idx_auth_criado_em (criado_em),

    CONSTRAINT fk_auth_admin
        FOREIGN KEY (admin_id) REFERENCES admins(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Log de tentativas e eventos de autenticação';

-- ============================================================
-- VIEWS AUXILIARES
-- ============================================================

CREATE OR REPLACE VIEW v_posts_publicados AS
    SELECT p.*, a.nome AS autor_nome
    FROM posts p
    LEFT JOIN admins a ON a.id = p.autor_id
    WHERE p.status = 'publicado' AND p.excluido_em IS NULL
    ORDER BY p.publicado_em DESC;

CREATE OR REPLACE VIEW v_mensagens_ativas AS
    SELECT * FROM contact_messages
    WHERE status != 'arquivado'
    ORDER BY criado_em DESC;

CREATE OR REPLACE VIEW v_dashboard_stats AS
    SELECT
        (SELECT COUNT(*) FROM posts WHERE excluido_em IS NULL) AS total_posts,
        (SELECT COUNT(*) FROM posts WHERE status = 'publicado' AND excluido_em IS NULL) AS posts_publicados,
        (SELECT COUNT(*) FROM posts WHERE status = 'rascunho' AND excluido_em IS NULL) AS posts_rascunho,
        (SELECT COUNT(*) FROM contact_messages WHERE status = 'novo') AS mensagens_novas,
        (SELECT COUNT(*) FROM contact_messages) AS mensagens_total;

-- ============================================================
-- DADOS INICIAIS
-- ============================================================

INSERT INTO site_settings (
    config_key, office_name,
    hero_title, hero_subtitle,
    about_title, about_text,
    email_contato, email_destino_form,
    telefone, whatsapp_numero, whatsapp_link,
    endereco_linha1, cidade, estado, cep,
    horario_atendimento,
    seo_home_title, seo_home_description
) VALUES (
    'default', 'Alberione Advogados',
    'Soluções Jurídicas Estratégicas em Direito Tributário',
    'Atuação consultiva, preventiva e contenciosa para empresas e contribuintes que buscam segurança jurídica e eficiência fiscal.',
    'Sobre o Escritório',
    'O Escritório Alberione Advogados atua com foco em Direito Tributário, Direito Societário e Direito Empresarial, oferecendo soluções jurídicas estratégicas e seguras para empresas e contribuintes.\n\nNosso compromisso é garantir suporte completo na gestão legal e fiscal dos nossos clientes, com orientações claras, atuação preventiva e defesa técnica em casos administrativos e judiciais.\n\nCom profissionais especializados nas áreas Tributária e Direito Público, atendemos pessoas físicas e jurídicas de forma personalizada e eficaz.',
    'contato@alberione.com.br', 'contato@alberione.com.br',
    '(11) 3000-0000', '5511900000000', 'https://wa.me/5511900000000',
    'Rua Example, 100 – Sala 1001', 'São Paulo', 'SP', '01310-000',
    'Segunda a Sexta, das 9h às 18h',
    'Alberione Advogados | Direito Tributário em São Paulo',
    'Escritório especializado em Direito Tributário, Societário e Empresarial. Atendimento estratégico, consultivo e contencioso para empresas e contribuintes.'
);

-- Admin padrão (senha: Admin@2025 — TROQUE IMEDIATAMENTE após instalar)
INSERT INTO admins (nome, email, senha_hash, ativo) VALUES
(
    'Administrador',
    'admin@alberione.com.br',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
);

-- Posts de exemplo
INSERT INTO posts (autor_id, tipo, categoria, titulo, slug, resumo, conteudo, status, destaque, publicado_em, meta_title, meta_description) VALUES
(
    1, 'artigo', 'Direito Tributário',
    'Reforma Tributária: O que muda para sua empresa em 2025?',
    'reforma-tributaria-o-que-muda-para-sua-empresa-em-2025',
    'Com a aprovação da Reforma Tributária, empresas de todos os segmentos precisam entender as mudanças na tributação do consumo e se preparar para a transição.',
    '<p>A Reforma Tributária, aprovada em 2023 e agora em fase de regulamentação, representa a maior mudança no sistema tributário brasileiro das últimas décadas. Para as empresas, entender o impacto é fundamental.</p><h2>As principais mudanças</h2><p>O IBS (Imposto sobre Bens e Serviços) e a CBS (Contribuição sobre Bens e Serviços) substituirão vários tributos atuais, simplificando a estrutura e criando um sistema de crédito amplo.</p><p>O Imposto Seletivo incidirá sobre bens e serviços considerados prejudiciais à saúde ou ao meio ambiente, como cigarros e bebidas alcoólicas.</p><h2>Período de transição</h2><p>A transição ocorrerá de forma gradual entre 2026 e 2033, permitindo que empresas e contribuintes se adaptem às novas regras.</p><p>Durante este período, é essencial que as empresas revisem seus processos fiscais e se planejem para a nova realidade tributária.</p><h2>Como o Alberione Advogados pode ajudar</h2><p>Nosso escritório oferece consultoria especializada para auxiliar sua empresa na análise de impactos, planejamento tributário e adequação à nova legislação.</p>',
    'publicado', 1, NOW(),
    'Reforma Tributária 2025: Impactos para Empresas | Alberione Advogados',
    'Entenda as principais mudanças da Reforma Tributária e como elas afetam sua empresa. Consultoria especializada em Direito Tributário.'
),
(
    1, 'artigo', 'Planejamento Tributário',
    'Planejamento Tributário: Estratégias legais para reduzir a carga fiscal',
    'planejamento-tributario-estrategias-legais',
    'O planejamento tributário é uma ferramenta legal e essencial para qualquer empresa que busca eficiência fiscal e redução lícita da carga tributária.',
    '<p>O planejamento tributário é o conjunto de ações legais que visam reduzir a carga tributária de uma empresa, aproveitando lacunas e benefícios previstos na legislação fiscal.</p><h2>O que é planejamento tributário?</h2><p>Diferente da sonegação fiscal, que é ilegal, o planejamento tributário consiste em organizar as atividades empresariais de forma a minimizar legalmente os tributos devidos.</p><h2>Principais estratégias</h2><ul><li>Escolha do regime tributário adequado (Simples Nacional, Lucro Presumido ou Lucro Real)</li><li>Aproveitamento de créditos fiscais</li><li>Utilização de incentivos fiscais e benefícios regionais</li><li>Estruturação societária eficiente</li></ul><h2>A importância da consultoria jurídica</h2><p>Um advogado tributarista especializado pode identificar oportunidades que passam despercebidas, garantindo que sua empresa pague apenas o que é devido por lei.</p>',
    'publicado', 0, NOW(),
    'Planejamento Tributário: Estratégias Legais | Alberione Advogados',
    'Saiba como reduzir legalmente a carga tributária da sua empresa com planejamento tributário especializado.'
),
(
    1, 'informativo', 'Atualizações Legislativas',
    'Prazo para entrega da DIRPF 2025 encerra em 30 de maio',
    'prazo-dirpf-2025',
    'A Receita Federal estabeleceu o prazo final de 30 de maio de 2025 para a entrega da Declaração de Imposto de Renda Pessoa Física. Confira as principais novidades.',
    '<p>A Receita Federal publicou as regras para a declaração do Imposto de Renda Pessoa Física 2025. O prazo para entrega vai de 17 de março a 30 de maio de 2025.</p><h2>Quem deve declarar?</h2><p>Devem declarar os contribuintes que em 2024:</p><ul><li>Receberam rendimentos tributáveis acima de R$ 30.639,90</li><li>Receberam rendimentos isentos, não tributáveis ou tributados exclusivamente na fonte acima de R$ 200.000,00</li><li>Realizaram operações na bolsa de valores</li><li>Obtiveram ganho de capital na alienação de bens ou direitos</li></ul><h2>Novidades para 2025</h2><p>A declaração pré-preenchida foi aprimorada e agora inclui mais informações automaticamente, facilitando o processo para a maioria dos contribuintes.</p>',
    'publicado', 0, NOW(),
    'DIRPF 2025: Prazo e Novidades | Alberione Advogados',
    'Prazo para entrega da Declaração do Imposto de Renda 2025 encerra em 30 de maio. Veja quem deve declarar e as novidades.'
);

SET FOREIGN_KEY_CHECKS = 1;
