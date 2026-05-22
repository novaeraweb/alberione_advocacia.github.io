<?php
/**
 * Alberione Advogados — Home Institucional (One Page)
 * index.php — Integrado ao banco MySQL via PDO
 */
require_once __DIR__ . '/config/config.php';

// ── Dados do banco ──────────────────────────────────────────────
$s = get_site_settings();
$posts_destaque = get_posts_destaques(3);
$posts_recentes = get_posts_recentes(3);

$office_name  = e(setting('office_name',    OFFICE_NAME));
$hero_title   = e(setting('hero_title',     'Soluções Jurídicas Estratégicas em Direito Tributário'));
$hero_sub     = e(setting('hero_subtitle',  'Atuação consultiva, preventiva e contenciosa para empresas e contribuintes que buscam segurança jurídica e eficiência fiscal.'));
$about_title  = e(setting('about_title',    'Sobre o Escritório'));
$about_text   =   setting('about_text',     'O escritório Alberione Advogados atua com foco em Direito Tributário, Direito Societário e Direito Empresarial, oferecendo soluções jurídicas estratégicas e seguras para empresas e contribuintes.');
$whatsapp_num = setting('whatsapp_numero', OFFICE_WHATSAPP);
$whatsapp_lnk = setting('whatsapp_link',  OFFICE_WHATSAPP_LINK);
$email_ctto   = setting('email_contato',  OFFICE_EMAIL);
$telefone     = setting('telefone',       '');
$horario      = setting('horario_atendimento', 'Segunda a Sexta, das 9h às 18h');
$endereco1    = setting('endereco_linha1', '');
$endereco2    = setting('endereco_linha2', '');
$cidade_uf    = trim(setting('cidade','') . ' – ' . setting('estado',''), ' –');
$cep          = setting('cep', '');
$instagram    = setting('instagram_url', '');
$linkedin     = setting('linkedin_url',  '');
$facebook     = setting('facebook_url',  '');
$youtube      = setting('youtube_url',   '');
$seo_title    = e(setting('seo_home_title',       $office_name . ' | Direito Tributário'));
$seo_desc     = e(setting('seo_home_description', 'Escritório especializado em Direito Tributário, Societário e Empresarial. Consultoria estratégica, preventiva e contenciosa.'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $seo_desc ?>">
    <meta name="robots" content="index, follow">
    <title><?= $seo_title ?></title>

    <!-- Open Graph -->
    <meta property="og:title"       content="<?= $seo_title ?>">
    <meta property="og:description" content="<?= $seo_desc ?>">
    <meta property="og:type"        content="website">
    <meta property="og:url"         content="<?= SITE_URL ?>">
    <meta property="og:locale"      content="pt_BR">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">

    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LegalService",
      "name": "<?= $office_name ?>",
      "description": "<?= $seo_desc ?>",
      "url": "<?= SITE_URL ?>",
      "telephone": "<?= e($telefone) ?>",
      "email": "<?= e($email_ctto) ?>"
    }
    </script>
</head>
<?php $bp = rtrim(SITE_URL, '/'); ?>
<body>

<!-- ══════════════════════════════════════════
     HEADER / NAVEGAÇÃO
══════════════════════════════════════════ -->
<header id="header" class="site-header">
    <div class="container header-inner">
        <a href="<?= $bp ?>/" class="logo" aria-label="<?= $office_name ?> – Página Inicial">
            <?php if (!empty($s['logo_path'])): ?>
                <img src="<?= e($s['logo_path']) ?>" alt="<?= $office_name ?>" height="48">
            <?php else: ?>
                <span class="logo-text"><?= $office_name ?></span>
            <?php endif; ?>
        </a>

        <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="#inicio">Início</a></li>
                <li><a href="#sobre">O Escritório</a></li>
                <li><a href="#areas">Áreas de Atuação</a></li>
                <li><a href="#diferenciais">Diferenciais</a></li>
                <li><a href="#blog">Informativo</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
        </nav>

        <?php if (!empty($whatsapp_lnk)): ?>
        <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp-header">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <?php endif; ?>
    </div>
</header>

<!-- ══════════════════════════════════════════
     HERO
══════════════════════════════════════════ -->
<section id="inicio" class="hero-section hero-section-video">
    <!-- Vídeo de fundo da Home.
         Coloque o arquivo em assets/videos/banner-inicial.mp4.
         Opcional: assets/videos/banner-inicial.webm para navegadores compatíveis. -->
    <video class="hero-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
        <source src="<?= $bp ?>/assets/videos/banner-inicial.webm" type="video/webm">
        <source src="<?= $bp ?>/assets/videos/banner-inicial.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="hero-tag">Direito Tributário &amp; Empresarial</div>
        <h1 class="hero-title"><?= $hero_title ?></h1>
        <p class="hero-subtitle"><?= $hero_sub ?></p>
        <div class="hero-actions">
            <a href="#contato" class="btn btn-primary">Fale com um advogado</a>
            <?php if (!empty($whatsapp_lnk)): ?>
            <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-outline-light">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-scroll">
        <a href="#sobre" aria-label="Rolar para baixo">
            <i class="fas fa-chevron-down"></i>
        </a>
    </div>
</section>

<!-- ══════════════════════════════════════════
     INDICADORES
══════════════════════════════════════════ -->
<section class="home-stats-strip" aria-label="Indicadores do escritório">
    <div class="container">
        <div class="home-stats-grid" data-aos="fade-up">
            <div class="stat-card">
                <div class="stat-number" data-count="15">0</div>
                <div class="stat-label">Anos de experiência</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" data-count="500">0</div>
                <div class="stat-label">Clientes atendidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" data-count="1200">0</div>
                <div class="stat-label">Casos resolvidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" data-count="20º">0</div>
                <div class="stat-label">Áreas atendidas</div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     SOBRE O ESCRITÓRIO
══════════════════════════════════════════ -->
<section id="sobre" class="about-section about-section-split">
    <div class="about-grid about-grid-full">
        <div class="about-content-wrap" data-aos="fade-right">
            <div class="about-content about-content-inner">
                <div class="section-tag">O Escritório</div>
                <h2 class="section-title"><?= $about_title ?></h2>
                <div class="about-text" style="text-align:justify">
                    <?php
                    $paragraphs = explode("\n\n", $about_text);
                    foreach ($paragraphs as $p):
                        $p = trim($p);
                        if ($p):
                    ?>
                    <p><?= e($p) ?></p>
                    <?php endif; endforeach; ?>
                     <div class="section-tag">O Advogado</div>
                     <h2 class="section-title">Alberione Araújo</h2>
                     <div class="about-text" style="text-align:justify;">
                        <p>Alberione Araujo é advogado graduado pela Faculdade de Direito de Bauru, com experiência nas áreas de Direito Tributário e Direito do Trabalho. Foi Procurador Municipal, é Especialista em Direito do Trabalho e Processo do Trabalho pela ESA, Especialista em Direito Tributário pela USP/FDRP e Graduado em Ciências Contábeis pela UNIP.</p>
                        <p>Também possui experiência como Professor Universitário, unindo prática jurídica, formação acadêmica sólida e visão estratégica para oferecer soluções eficientes e seguras aos seus clientes.</p>
                     </div>
                </div>
                <div class="about-actions">
                    <a href="#areas" class="btn btn-primary">Nossas Áreas de Atuação</a>
                    <a href="#contato" class="btn btn-outline">Agendar Consulta</a>
                </div>
            </div>
        </div>
        <figure class="about-lawyer-card about-lawyer-card-full" data-aos="fade-left">
            <img src="<?= $bp ?>/assets/images/alberione-sobre-direita.webp"
                 alt="Advogado Alberione em ambiente institucional do escritório"
                 width="1000"
                 height="1200"
                 loading="lazy"
                 decoding="async">
        </figure>
    </div>
</section>

<!-- ══════════════════════════════════════════
     ÁREAS DE ATUAÇÃO
══════════════════════════════════════════ -->
<section id="areas" class="areas-section section-padding bg-dark-section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <div class="section-tag light">Especialidades</div>
            <h2 class="section-title light">Áreas de Atuação</h2>
            <p class="section-lead light">Atuação especializada nas principais demandas jurídicas de empresas e contribuintes.</p>
        </div>

        <div class="areas-grid" data-aos="fade-up" data-aos-delay="100">
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-balance-scale"></i></div>
                <h3>Direito Tributário</h3>
                <p>Consultoria, planejamento e defesa em questões fiscais e tributárias nas esferas federal, estadual e municipal.</p>
            </div>
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-building"></i></div>
                <h3>Direito Societário</h3>
                <p>Constituição, reestruturação e dissolução de sociedades empresariais. Due diligence e M&A.</p>
            </div>
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-briefcase"></i></div>
                <h3>Direito Empresarial</h3>
                <p>Assessoria jurídica empresarial completa: contratos, recuperação judicial, compliance e governança.</p>
            </div>
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-gavel"></i></div>
                <h3>Contencioso Tributário</h3>
                <p>Representação em processos administrativos e judiciais, embargos à execução fiscal e ações anulatórias.</p>
            </div>
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Planejamento Fiscal</h3>
                <p>Estratégias legais para redução da carga tributária, aproveitamento de créditos e escolha do regime ideal.</p>
            </div>
            <div class="area-card">
                <div class="area-icon"><i class="fas fa-handshake"></i></div>
                <h3>Reforma Tributária</h3>
                <p>Análise de impactos, adequação aos novos tributos (IBS, CBS, Imposto Seletivo) e planejamento da transição.</p>
            </div>
        </div>
    </div>
</section>


<!-- ══════════════════════════════════════════
     DIFERENCIAIS
══════════════════════════════════════════ -->
<section id="diferenciais" class="diff-section section-padding">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <div class="section-tag">Por que nos escolher</div>
            <h2 class="section-title">Nossos Diferenciais</h2>
        </div>

        <div class="diff-grid" data-aos="fade-up" data-aos-delay="100">
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-award"></i></div>
                <h4>Excelência Técnica</h4>
                <p>Equipe altamente qualificada com especialização nas mais complexas questões do Direito Tributário.</p>
            </div>
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-shield-alt"></i></div>
                <h4>Segurança Jurídica</h4>
                <p>Orientações claras e embasadas na legislação vigente, garantindo que nossos clientes tomem as melhores decisões.</p>
            </div>
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-users"></i></div>
                <h4>Atendimento Personalizado</h4>
                <p>Cada cliente recebe atenção individualizada e soluções adaptadas às suas necessidades específicas.</p>
            </div>
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-sync-alt"></i></div>
                <h4>Atualização Constante</h4>
                <p>Acompanhamento contínuo das mudanças legislativas, especialmente no contexto da Reforma Tributária.</p>
            </div>
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-eye"></i></div>
                <h4>Transparência</h4>
                <p>Comunicação clara sobre estratégias, prazos e resultados em todas as etapas do atendimento.</p>
            </div>
            <div class="diff-item">
                <div class="diff-icon"><i class="fas fa-lightbulb"></i></div>
                <h4>Visão Estratégica</h4>
                <p>Abordagem preventiva que identifica riscos e oportunidades antes que se tornem problemas.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     BLOG / PUBLICAÇÕES
══════════════════════════════════════════ -->
<section id="blog" class="blog-section section-padding bg-light-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-tag">Conhecimento</div>
            <h2 class="section-title">Artigos e Informativos</h2>
            <p class="section-lead">Publicações jurídicas para manter você informado sobre as principais mudanças e oportunidades no Direito Tributário.</p>
        </div>

        <?php if (!empty($posts_recentes)): ?>
        <div class="blog-grid" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($posts_recentes as $post): ?>
            <article class="blog-card">
                <?php if (!empty($post['imagem_capa'])): ?>
                <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>" class="blog-card-image">
                    <img src="<?= e(post_image_url($post['imagem_capa'])) ?>"
                         alt="<?= e($post['imagem_alt'] ?: $post['titulo']) ?>"
                         loading="lazy">
                </a>
                <?php else: ?>
                <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>" class="blog-card-image blog-card-image--placeholder">
                    <div class="blog-placeholder-icon">
                        <i class="fas fa-<?= $post['tipo'] === 'informativo' ? 'bell' : 'book-open' ?>"></i>
                    </div>
                </a>
                <?php endif; ?>

                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        <span class="badge badge-<?= $post['tipo'] ?>">
                            <?= $post['tipo'] === 'artigo' ? 'Artigo' : 'Informativo' ?>
                        </span>
                        <time datetime="<?= e($post['publicado_em'] ?? $post['criado_em']) ?>">
                            <?= format_date($post['publicado_em'] ?? $post['criado_em']) ?>
                        </time>
                    </div>
                    <h3 class="blog-card-title">
                        <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>"><?= e($post['titulo']) ?></a>
                    </h3>
                    <?php if (!empty($post['resumo'])): ?>
                    <p class="blog-card-excerpt"><?= e(excerpt($post['resumo'], 140)) ?></p>
                    <?php endif; ?>
                    <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>" class="blog-card-link">
                        Ler mais <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="blog-cta text-center" data-aos="fade-up">
            <a href="<?= $bp ?>/blog" class="btn btn-outline">
                Ver todas as publicações <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <?php else: ?>
        <div class="blog-empty text-center" data-aos="fade-up">
            <i class="fas fa-book-open blog-empty-icon"></i>
            <p>Em breve, publicaremos artigos e informativos sobre Direito Tributário.</p>
            <a href="#contato" class="btn btn-primary">Entre em contato</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ══════════════════════════════════════════
     CTA PRINCIPAL
══════════════════════════════════════════ -->
<section class="cta-section">
    <div class="container cta-inner">
        <div class="cta-content" data-aos="fade-right">
            <h2>Precisa de assessoria tributária?</h2>
            <p>Nossa equipe está pronta para analisar seu caso e apresentar as melhores soluções jurídicas para sua empresa.</p>
        </div>
        <div class="cta-actions" data-aos="fade-left">
            <a href="#contato" class="btn btn-primary">Fale com nossa equipe</a>
            <?php if (!empty($whatsapp_lnk)): ?>
            <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-outline-light">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     CONTATO
══════════════════════════════════════════ -->
<section id="contato" class="contact-section section-padding">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <div class="section-tag">Fale Conosco</div>
            <h2 class="section-title">Entre em Contato</h2>
            <p class="section-lead">Estamos prontos para atender sua demanda com agilidade e dedicação.</p>
        </div>

        <div class="contact-grid" data-aos="fade-up" data-aos-delay="100">
            <!-- Formulário -->
            <div class="contact-form-wrap">
                <form id="contactForm" class="contact-form" novalidate>
                    <input type="text" name="honeypot" class="hp-field" tabindex="-1" autocomplete="off">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nome">Nome completo <span class="req">*</span></label>
                            <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required maxlength="120">
                            <span class="form-error" data-field="nome"></span>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail <span class="req">*</span></label>
                            <input type="email" id="email" name="email" placeholder="seu@email.com.br" required maxlength="150">
                            <span class="form-error" data-field="email"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telefone">Telefone / WhatsApp</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(11) 99999-9999" maxlength="30" data-mask="phone">
                        </div>
                        <div class="form-group">
                            <label for="assunto">Assunto</label>
                            <select id="assunto" name="assunto">
                                <option value="">Selecione...</option>
                                <option value="Direito Tributário">Direito Tributário</option>
                                <option value="Planejamento Fiscal">Planejamento Fiscal</option>
                                <option value="Contencioso Tributário">Contencioso Tributário</option>
                                <option value="Direito Societário">Direito Societário</option>
                                <option value="Reforma Tributária">Reforma Tributária</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group form-group--full">
                        <label for="mensagem">Mensagem <span class="req">*</span></label>
                        <textarea id="mensagem" name="mensagem" placeholder="Descreva sua demanda ou dúvida..." rows="5" required maxlength="2000"></textarea>
                        <span class="form-error" data-field="mensagem"></span>
                    </div>

                    <div class="form-lgpd">
                        <p>Ao enviar este formulário, você concorda com nossa <a href="#" target="_blank">Política de Privacidade</a>. Suas informações serão utilizadas exclusivamente para retorno ao seu contato.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-full" id="formSubmit">
                            <span class="btn-text">Enviar mensagem</span>
                            <span class="btn-loading" style="display:none">
                                <i class="fas fa-spinner fa-spin"></i> Enviando...
                            </span>
                        </button>
                    </div>

                    <div id="formAlert" class="form-alert" style="display:none"></div>
                </form>
            </div>

            <!-- Dados de contato -->
            <div class="contact-info">
                <?php if (!empty($endereco1)): ?>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <strong>Endereço</strong>
                        <p><?= e($endereco1) ?></p>
                        <?php if (!empty($endereco2)): ?>
                        <p><?= e($endereco2) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($cidade_uf)): ?>
                        <p><?= e($cidade_uf) ?> <?= !empty($cep) ? ' — CEP ' . e($cep) : '' ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($telefone)): ?>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <strong>Telefone</strong>
                        <p><a href="tel:<?= preg_replace('/\D/', '', $telefone) ?>"><?= e($telefone) ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($whatsapp_lnk)): ?>
                <div class="contact-info-item">
                    <div class="contact-info-icon contact-info-icon--wa"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <strong>WhatsApp</strong>
                        <p><a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener">Conversar agora</a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($email_ctto)): ?>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <strong>E-mail</strong>
                        <p><a href="mailto:<?= e($email_ctto) ?>"><?= e($email_ctto) ?></a></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($horario)): ?>
                <div class="contact-info-item">
                    <div class="contact-info-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <strong>Horário de Atendimento</strong>
                        <p><?= e($horario) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Redes Sociais -->
                <?php if (!empty($instagram) || !empty($linkedin) || !empty($facebook) || !empty($youtube)): ?>
                <div class="contact-social">
                    <strong>Redes Sociais</strong>
                    <div class="social-links">
                        <?php if (!empty($instagram)): ?>
                        <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($linkedin)): ?>
                        <a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($facebook)): ?>
                        <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($youtube)): ?>
                        <a href="<?= e($youtube) ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <?php if (!empty($s['logo_path'])): ?>
                <img src="assets/images/alberione-advocacia-white.png" alt="<?= $office_name ?>" height="40" class="footer-logo">
            <?php else: ?>
                <span class="footer-logo-text"><?= $office_name ?></span>
            <?php endif; ?>
            <p>Soluções jurídicas estratégicas em Direito Tributário e Empresarial.</p>
        </div>

        <div class="footer-links">
            <h4>Navegação</h4>
            <ul>
                <li><a href="#inicio">Início</a></li>
                <li><a href="#sobre">O Escritório</a></li>
                <li><a href="#areas">Áreas de Atuação</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
        </div>

        <div class="footer-links">
            <h4>Áreas de Atuação</h4>
            <ul>
                <li><a href="#areas">Direito Tributário</a></li>
                <li><a href="#areas">Direito Societário</a></li>
                <li><a href="#areas">Direito Empresarial</a></li>
                <li><a href="#areas">Contencioso Tributário</a></li>
                <li><a href="#areas">Reforma Tributária</a></li>
            </ul>
        </div>

        <div class="footer-contact">
            <h4>Contato</h4>
            <?php if (!empty($email_ctto)): ?>
            <p><i class="fas fa-envelope"></i> <a href="mailto:<?= e($email_ctto) ?>" rel="noopener noreferrer"><?= e($email_ctto) ?></a></p>
            <?php endif; ?>
            <?php if (!empty($telefone)): ?>
            <p><i class="fas fa-phone"></i> <?= e($telefone) ?></p>
            <p><i class="fas fa-phone"></i> 14 3361.0024</p>
            <?php endif; ?>
            <?php if (!empty($endereco1)): ?>
            <p><i class="fas fa-map-marker-alt"></i> <?= e($endereco1) ?><?= !empty($cidade_uf) ? ', ' . e($cidade_uf) : '' ?></p>
            <?php endif; ?>

            <?php if (!empty($instagram) || !empty($linkedin) || !empty($facebook)): ?>
            <div class="social-links footer-social">
                <?php if (!empty($instagram)): ?>
                <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($linkedin)): ?>
                <a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <?php endif; ?>
                <?php if (!empty($facebook)): ?>
                <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>
                &copy; <?= date('Y') ?> <?= $office_name ?> — Todos os direitos reservados.
                | OAB/SP
            </p>
            <p class="footer-disclaimer">
                As informações deste site têm caráter meramente informativo e não constituem consulta jurídica.
            </p>
        </div>
    </div>
</footer>

<!-- WhatsApp Flutuante -->
<?php if (!empty($whatsapp_lnk)): ?>
<a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener"
   class="whatsapp-float" aria-label="Fale por WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>
<!-- JavaScript -->
<script>const WHATSAPP_LINK = '<?= e($whatsapp_lnk) ?>';</script>
<script src="assets/js/main.js"></script>
</body>
</html>