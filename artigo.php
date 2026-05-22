<?php
/**
 * Alberione Advogados — Artigo Interno
 * artigo.php — Integrado ao banco MySQL via PDO
 */
require_once __DIR__ . '/config/config.php';

// ── Busca pelo slug ──────────────────────────────────────────────
$slug = clean($_GET['slug'] ?? '');
if (empty($slug)) {
    redirect('/blog');
}

$post = get_post_by_slug($slug);
if (!$post) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// ── Incrementar visualizações ────────────────────────────────────
increment_views((int)$post['id']);

// ── Posts relacionados ────────────────────────────────────────────
$relacionados = get_posts_relacionados((int)$post['id'], $post['tipo'], 3);

// ── Configurações do site ─────────────────────────────────────────
$s = get_site_settings();
$office_name  = e(setting('office_name',  OFFICE_NAME));
$whatsapp_lnk = setting('whatsapp_link',  OFFICE_WHATSAPP_LINK);
$email_ctto   = setting('email_contato',  OFFICE_EMAIL);
$telefone     = setting('telefone',       '');
$instagram    = setting('instagram_url',  '');
$linkedin     = setting('linkedin_url',   '');

$bp = rtrim(SITE_URL, '/');
$asset_url = static function (?string $path) use ($bp): string {
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#', $path)) {
        return $path;
    }
    return $bp . '/' . ltrim($path, '/');
};

// ── SEO ──────────────────────────────────────────────────────────
$meta_title = !empty($post['meta_title'])
    ? e($post['meta_title'])
    : e($post['titulo'] . ' | ' . $office_name);

$meta_desc = !empty($post['meta_description'])
    ? e($post['meta_description'])
    : e(excerpt($post['resumo'] ?: $post['conteudo'], 160));

$post_url  = SITE_URL . '/artigo/' . rawurlencode($post['slug']);
$post_img  = !empty($post['imagem_capa']) ? $asset_url($post['imagem_capa']) : '';
$pub_date  = $post['publicado_em'] ?? $post['criado_em'];
$tipo_label = $post['tipo'] === 'artigo' ? 'Artigo' : 'Informativo';
$read_time  = reading_time($post['conteudo']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $meta_desc ?>">
    <title><?= $meta_title ?></title>

    <!-- Open Graph -->
    <meta property="og:title"       content="<?= $meta_title ?>">
    <meta property="og:description" content="<?= $meta_desc ?>">
    <meta property="og:type"        content="article">
    <meta property="og:url"         content="<?= e($post_url) ?>">
    <?php if ($post_img): ?>
    <meta property="og:image"       content="<?= e($post_img) ?>">
    <?php endif; ?>
    <meta property="article:published_time" content="<?= e($pub_date) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= $meta_title ?>">
    <meta name="twitter:description" content="<?= $meta_desc ?>">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "<?= addslashes($post['titulo']) ?>",
        "description": "<?= addslashes($meta_desc) ?>",
        "url": "<?= e($post_url) ?>",
        "datePublished": "<?= e($pub_date) ?>",
        "author": {
            "@type": "Organization",
            "name": "<?= addslashes($office_name) ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "<?= addslashes($office_name) ?>"
        }
    }
    </script>

    <link rel="icon" type="image/x-icon" href="<?= e($asset_url('assets/images/favicon.ico')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= e($asset_url('assets/css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e($asset_url('assets/css/blog.css')) ?>">

    <!-- Barra de progresso de leitura -->
    <style>
        #reading-progress {
            position: fixed; top: 0; left: 0; z-index: 9999;
            height: 3px; width: 0%;
            background: linear-gradient(to right, #1F4E79, #B08A57);
            transition: width .1s linear;
        }
    </style>
</head>
<body class="artigo-page">

<div id="reading-progress"></div>

<!-- HEADER -->
<header id="header" class="site-header">
    <div class="container header-inner">
        <a href="<?= $bp ?>/" class="logo" aria-label="<?= $office_name ?>">
            <?php if (!empty($s['logo_path'])): ?>
                <img src="<?= e($asset_url($s['logo_path'])) ?>" alt="<?= $office_name ?>" height="44">
            <?php else: ?>
                <span class="logo-text"><?= $office_name ?></span>
            <?php endif; ?>
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="<?= $bp ?>/#inicio">Início</a></li>
                <li><a href="<?= $bp ?>/#sobre">O Escritório</a></li>
                <li><a href="<?= $bp ?>/#areas">Áreas de Atuação</a></li>
                <li><a href="<?= $bp ?>/blog" class="active">Blog</a></li>
                <li><a href="<?= $bp ?>/#contato">Contato</a></li>
            </ul>
        </nav>
        <?php if (!empty($whatsapp_lnk)): ?>
        <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp-header">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <?php endif; ?>
    </div>
</header>

<!-- CONTEÚDO DO ARTIGO -->
<main class="artigo-main">
    <div class="container artigo-layout">

        <!-- ARTIGO -->
        <article class="artigo-content" id="artigo">

            <!-- Hero do artigo -->
            <header class="artigo-header">
                <div class="artigo-breadcrumb">
                    <a href="<?= $bp ?>/">Início</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="<?= $bp ?>/blog">Blog</a>
                    <i class="fas fa-chevron-right"></i>
                    <a href="<?= $bp ?>/blog?tipo=<?= $post['tipo'] ?>"><?= $tipo_label ?>s</a>
                </div>

                <div class="artigo-meta-top">
                    <span class="badge badge-<?= $post['tipo'] ?>"><?= $tipo_label ?></span>
                    <time datetime="<?= e($pub_date) ?>" class="artigo-date">
                        <i class="fas fa-calendar-alt"></i>
                        <?= format_date($pub_date, 'd \d\e F \d\e Y') ?>
                    </time>
                    <span class="artigo-read-time">
                        <i class="fas fa-clock"></i>
                        <?= $read_time ?> min de leitura
                    </span>
                </div>

                <h1 class="artigo-title"><?= e($post['titulo']) ?></h1>

                <?php if (!empty($post['resumo'])): ?>
                <p class="artigo-lead"><?= e($post['resumo']) ?></p>
                <?php endif; ?>

                <!-- Imagem capa -->
                <?php if (!empty($post['imagem_capa'])): ?>
                <figure class="artigo-cover">
                    <img src="<?= e($asset_url($post['imagem_capa'])) ?>"
                         alt="<?= e($post['imagem_alt'] ?: $post['titulo']) ?>"
                         loading="eager">
                </figure>
                <?php endif; ?>
            </header>

            <!-- Corpo do artigo -->
            <div class="artigo-body prose" id="artigo-body">
                <?= $post['conteudo'] // HTML já sanitizado no cadastro via admin ?>
            </div>

            <!-- Footer do artigo -->
            <footer class="artigo-footer">
                <div class="artigo-share">
                    <span>Compartilhar:</span>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= rawurlencode($post_url) ?>&title=<?= rawurlencode($post['titulo']) ?>"
                       target="_blank" rel="noopener" class="share-btn share-linkedin" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php if (!empty($whatsapp_lnk)): ?>
                    <a href="https://wa.me/?text=<?= rawurlencode($post['titulo'] . ' — ' . $post_url) ?>"
                       target="_blank" rel="noopener" class="share-btn share-wa" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <?php endif; ?>
                    <a href="https://twitter.com/intent/tweet?text=<?= rawurlencode($post['titulo']) ?>&url=<?= rawurlencode($post_url) ?>"
                       target="_blank" rel="noopener" class="share-btn share-twitter" aria-label="Twitter/X">
                        <i class="fab fa-x-twitter"></i>
                    </a>
                    <button class="share-btn share-copy" data-url="<?= e($post_url) ?>" aria-label="Copiar link">
                        <i class="fas fa-link"></i>
                    </button>
                </div>

                <a href="<?= $bp ?>/blog" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Voltar ao Blog
                </a>
            </footer>
        </article>

        <!-- SIDEBAR -->
        <aside class="artigo-sidebar">

            <!-- CTA Contato -->
            <div class="sidebar-cta">
                <h4>Precisa de assessoria?</h4>
                <p>Nossa equipe de especialistas está pronta para ajudar sua empresa.</p>
                <?php if (!empty($whatsapp_lnk)): ?>
                <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-primary btn-full">
                    <i class="fab fa-whatsapp"></i> Falar no WhatsApp
                </a>
                <?php endif; ?>
                <a href="<?= $bp ?>/#contato" class="btn btn-outline btn-full" style="margin-top:10px">
                    Enviar mensagem
                </a>
                <?php if (!empty($email_ctto)): ?>
                <p class="sidebar-email"><i class="fas fa-envelope"></i> <a href="mailto:<?= e($email_ctto) ?>"><?= e($email_ctto) ?></a></p>
                <?php endif; ?>
            </div>

            <!-- Relacionados -->
            <?php if (!empty($relacionados)): ?>
            <div class="sidebar-related">
                <h4><?= $tipo_label ?>s Relacionados</h4>
                <ul class="related-list">
                    <?php foreach ($relacionados as $rel): ?>
                    <li class="related-item">
                        <a href="<?= $bp ?>/artigo/<?= e($rel['slug']) ?>" class="related-link">
                            <div class="related-img">
                                <?php if (!empty($rel['imagem_capa'])): ?>
                                    <img src="<?= e($asset_url($rel['imagem_capa'])) ?>"
                                         alt="<?= e($rel['titulo']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="related-placeholder"><i class="fas fa-file-alt"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="related-info">
                                <p class="related-title"><?= e($rel['titulo']) ?></p>
                                <time><?= format_date($rel['publicado_em'] ?? $rel['criado_em']) ?></time>
                            </div>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Redes Sociais -->
            <?php if (!empty($instagram) || !empty($linkedin)): ?>
            <div class="sidebar-social">
                <h4>Siga-nos</h4>
                <div class="social-links">
                    <?php if (!empty($instagram)): ?>
                    <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($linkedin)): ?>
                    <a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </aside>
    </div><!-- /.artigo-layout -->

    <!-- Posts Relacionados (Mobile) -->
    <?php if (!empty($relacionados)): ?>
    <section class="related-mobile section-padding bg-light-section">
        <div class="container">
            <h3 class="section-title" style="font-size:1.4rem; margin-bottom:28px">
                Você também pode gostar
            </h3>
            <div class="blog-grid">
                <?php foreach ($relacionados as $rel): ?>
                <article class="blog-card">
                    <a href="<?= $bp ?>/artigo/<?= e($rel['slug']) ?>" class="blog-card-image">
                        <?php if (!empty($rel['imagem_capa'])): ?>
                            <img src="<?= e($asset_url($rel['imagem_capa'])) ?>"
                                 alt="<?= e($rel['titulo']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="blog-card-image--placeholder">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="blog-card-body">
                        <div class="blog-card-meta">
                            <span class="badge badge-<?= $rel['tipo'] ?>"><?= $rel['tipo'] === 'artigo' ? 'Artigo' : 'Informativo' ?></span>
                            <time><?= format_date($rel['publicado_em'] ?? $rel['criado_em']) ?></time>
                        </div>
                        <h4 class="blog-card-title">
                            <a href="<?= $bp ?>/artigo/<?= e($rel['slug']) ?>"><?= e($rel['titulo']) ?></a>
                        </h4>
                        <a href="<?= $bp ?>/artigo/<?= e($rel['slug']) ?>" class="blog-card-link">
                            Ler mais <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <?php if (!empty($s['logo_path'])): ?>
                <img src="<?= e($asset_url($s['logo_path'])) ?>" alt="<?= $office_name ?>" height="36" class="footer-logo">
            <?php else: ?>
                <span class="footer-logo-text"><?= $office_name ?></span>
            <?php endif; ?>
            <p>Soluções jurídicas estratégicas em Direito Tributário e Empresarial.</p>
        </div>
        <div class="footer-links">
            <h4>Navegação</h4>
            <ul>
                <li><a href="<?= $bp ?>/">Início</a></li>
                <li><a href="<?= $bp ?>/#areas">Áreas de Atuação</a></li>
                <li><a href="<?= $bp ?>/blog">Blog</a></li>
                <li><a href="<?= $bp ?>/#contato">Contato</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contato</h4>
            <?php if (!empty($email_ctto)): ?><p><i class="fas fa-envelope"></i> <a href="mailto:<?= e($email_ctto) ?>"><?= e($email_ctto) ?></a></p><?php endif; ?>
            <?php if (!empty($telefone)): ?><p><i class="fas fa-phone"></i> <?= e($telefone) ?></p><?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= $office_name ?> — Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<?php if (!empty($whatsapp_lnk)): ?>
<a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="whatsapp-float" aria-label="WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>
<?php endif; ?>

<script src="<?= e($asset_url('assets/js/main.js')) ?>"></script>
<script>
// Barra de progresso de leitura
(function () {
    const bar    = document.getElementById('reading-progress');
    const article = document.getElementById('artigo-body');
    if (!bar || !article) return;
    window.addEventListener('scroll', () => {
        const rect   = article.getBoundingClientRect();
        const total  = article.offsetHeight;
        const viewed = Math.min(Math.max(-rect.top, 0), total);
        bar.style.width = (viewed / total * 100) + '%';
    }, { passive: true });
})();

// Copiar link
document.querySelectorAll('.share-copy').forEach(btn => {
    btn.addEventListener('click', () => {
        navigator.clipboard.writeText(btn.dataset.url).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => { icon.className = 'fas fa-link'; }, 2000);
        });
    });
});
</script>
</body>
</html>
