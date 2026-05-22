<?php
/**
 * Alberione Advogados — Blog
 * blog.php — Integrado ao banco MySQL via PDO
 */
require_once __DIR__ . '/config/config.php';

// ── Filtros da URL ───────────────────────────────────────────────
$tipo_allowed = ['artigo', 'informativo'];
$tipo    = in_array($_GET['tipo'] ?? '', $tipo_allowed) ? $_GET['tipo'] : '';
$busca   = clean(substr($_GET['q'] ?? '', 0, 100));
$page    = max(1, (int)($_GET['p'] ?? 1));
$limit   = BLOG_PER_PAGE;
$offset  = ($page - 1) * $limit;

// ── Consulta ao banco ─────────────────────────────────────────────
$opts = [
    'tipo'   => $tipo ?: null,
    'status' => 'publicado',
    'limit'  => $limit,
    'offset' => $offset,
    'search' => $busca,
];

$total = count_posts($opts);
$posts = get_posts($opts);
$pages = (int)ceil($total / $limit);

// Destaque: apenas na primeira página sem filtros
$post_destaque = null;
if ($page === 1 && !$tipo && !$busca) {
    $destaques = get_posts_destaques(1);
    $post_destaque = $destaques[0] ?? null;
    if ($post_destaque) {
        $posts = array_filter($posts, fn($p) => $p['id'] !== $post_destaque['id']);
    }
}

// ── Configurações do site ──────────────────────────────────────
$s = get_site_settings();
$office_name  = e(setting('office_name',   OFFICE_NAME));
$whatsapp_lnk = setting('whatsapp_link',   OFFICE_WHATSAPP_LINK);
$email_ctto   = setting('email_contato',   OFFICE_EMAIL);
$telefone     = setting('telefone',        '');
$instagram    = setting('instagram_url',   '');
$linkedin     = setting('linkedin_url',    '');
$facebook     = setting('facebook_url',    '');

// ── SEO ──────────────────────────────────────────────────────────
if ($tipo === 'artigo') {
    $seo_title = "Artigos | $office_name";
    $seo_desc  = "Artigos jurídicos sobre Direito Tributário, Societário e Empresarial publicados pelo escritório $office_name.";
} elseif ($tipo === 'informativo') {
    $seo_title = "Informativos | $office_name";
    $seo_desc  = "Informativos jurídicos e atualizações legislativas publicadas pelo escritório $office_name.";
} elseif ($busca) {
    $seo_title = "Busca: " . e($busca) . " | $office_name";
    $seo_desc  = "Resultados da busca por \"" . e($busca) . "\" no blog jurídico do escritório $office_name.";
} else {
    $seo_title = "Blog Jurídico | $office_name";
    $seo_desc  = "Publicações, artigos e informativos sobre Direito Tributário, Societário e Empresarial.";
}

// ── URL base para paginação ───────────────────────────────────────
function paginate_url(int $p, string $tipo = '', string $busca = ''): string {
    $params = ['p' => $p];
    if ($tipo)  $params['tipo'] = $tipo;
    if ($busca) $params['q']    = $busca;
    return '/blog?' . http_build_query($params);
}
$logo_path         = e(setting('logo_path', 'assets/images/alberione-advocacia.png'));
$header_logo_path  = e(setting('header_logo_path', 'assets/images/alberione-advocacia-horizontal.png'));
$footer_logo_path  = e(setting('footer_logo_path', 'assets/images/alberione-advocacia-white.png'));
$favicon_path      = e(setting('favicon_path', 'assets/images/apple-icon-180x180.png'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($seo_desc) ?>">
    <title><?= e($seo_title) ?></title>
    <meta property="og:title"       content="<?= e($seo_title) ?>">
    <meta property="og:description" content="<?= e($seo_desc) ?>">
    <meta property="og:type"        content="website">
    <link rel="icon" type="image/png" href="<?= $favicon_path ?>">
    <link rel="apple-touch-icon" href="assets/images/apple-icon-180x180.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/blog.css">
</head>
<?php $bp = rtrim(SITE_URL, '/'); ?>
<body>

<!-- HEADER -->
<header id="header" class="site-header">
    <div class="container header-inner">
        <a href="/" class="logo" aria-label="<?= $office_name ?>">
            <img src="<?= $header_logo_path ?>" alt="<?= $office_name ?>" height="64" width="379" decoding="async" class="header-logo">
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="/#inicio">Início</a></li>
                <li><a href="/#sobre">O Escritório</a></li>
                <li><a href="/#areas">Áreas de Atuação</a></li>
                <li><a href="<?= $bp ?>/blog" class="active">Blog</a></li>
                <li><a href="/#contato">Contato</a></li>
            </ul>
        </nav>
        <?php if (!empty($whatsapp_lnk)): ?>
        <a href="<?= e($whatsapp_lnk) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp-header">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <?php endif; ?>
    </div>
</header>

<!-- HERO BLOG -->
<section class="blog-hero">
    <div class="container">
        <div class="blog-hero-breadcrumb">
            <a href="<?= $bp ?>/">Início</a>
            <i class="fas fa-chevron-right"></i>
            <span>Blog</span>
            <?php if ($tipo): ?>
            <i class="fas fa-chevron-right"></i>
            <span><?= $tipo === 'artigo' ? 'Artigos' : 'Informativos' ?></span>
            <?php endif; ?>
        </div>
        <h1 class="blog-hero-title">
            <?php if ($tipo === 'artigo'): ?>
                Artigos Jurídicos
            <?php elseif ($tipo === 'informativo'): ?>
                Informativos
            <?php elseif ($busca): ?>
                Resultados para "<em><?= e($busca) ?></em>"
            <?php else: ?>
                Blog Jurídico
            <?php endif; ?>
        </h1>
        <p class="blog-hero-sub">Publicações sobre Direito Tributário, Societário e Empresarial.</p>
    </div>
</section>

<!-- BLOG MAIN -->
<main class="blog-main">
    <div class="container">

        <!-- Filtros -->
        <div class="blog-filters">
            <div class="blog-filter-tabs">
                <a href="<?= $bp ?>/blog" class="filter-tab <?= !$tipo ? 'active' : '' ?>">Todos</a>
                <a href="<?= $bp ?>/blog?tipo=artigo" class="filter-tab <?= $tipo === 'artigo' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> Artigos
                </a>
                <a href="<?= $bp ?>/blog?tipo=informativo" class="filter-tab <?= $tipo === 'informativo' ? 'active' : '' ?>">
                    <i class="fas fa-bell"></i> Informativos
                </a>
            </div>

            <form class="blog-search" method="get" action="/blog">
                <?php if ($tipo): ?><input type="hidden" name="tipo" value="<?= e($tipo) ?>"><?php endif; ?>
                <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Buscar publicações...">
                <button type="submit" aria-label="Buscar"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <!-- Post destaque -->
        <?php if ($post_destaque): ?>
        <article class="blog-featured">
            <a href="<?= $bp ?>/artigo/<?= e($post_destaque['slug']) ?>" class="blog-featured-image">
                <?php if (!empty($post_destaque['imagem_capa'])): ?>
                    <img src="<?= e(SITE_URL . $post_destaque['imagem_capa']) ?>"
                         alt="<?= e($post_destaque['imagem_alt'] ?: $post_destaque['titulo']) ?>"
                         loading="eager">
                <?php else: ?>
                    <div class="blog-featured-placeholder">
                        <i class="fas fa-star"></i>
                    </div>
                <?php endif; ?>
            </a>
            <div class="blog-featured-body">
                <div class="blog-featured-meta">
                    <span class="badge badge-<?= $post_destaque['tipo'] ?>">
                        <i class="fas fa-star"></i>
                        Destaque · <?= $post_destaque['tipo'] === 'artigo' ? 'Artigo' : 'Informativo' ?>
                    </span>
                    <time datetime="<?= e($post_destaque['publicado_em'] ?? $post_destaque['criado_em']) ?>">
                        <?= format_date($post_destaque['publicado_em'] ?? $post_destaque['criado_em']) ?>
                    </time>
                </div>
                <h2 class="blog-featured-title">
                    <a href="<?= $bp ?>/artigo/<?= e($post_destaque['slug']) ?>"><?= e($post_destaque['titulo']) ?></a>
                </h2>
                <?php if (!empty($post_destaque['resumo'])): ?>
                <p class="blog-featured-excerpt"><?= e(excerpt($post_destaque['resumo'], 220)) ?></p>
                <?php endif; ?>
                <a href="<?= $bp ?>/artigo/<?= e($post_destaque['slug']) ?>" class="btn btn-primary">
                    Ler artigo completo <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </article>
        <?php endif; ?>

        <!-- Grade de posts -->
        <?php if (!empty($posts)): ?>
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
            <article class="blog-card">
                <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>" class="blog-card-image">
                    <?php if (!empty($post['imagem_capa'])): ?>
                        <img src="<?= e(SITE_URL . $post['imagem_capa']) ?>"
                             alt="<?= e($post['imagem_alt'] ?: $post['titulo']) ?>"
                             loading="lazy">
                    <?php else: ?>
                        <div class="blog-card-image--placeholder">
                            <i class="fas fa-<?= $post['tipo'] === 'informativo' ? 'bell' : 'book-open' ?>"></i>
                        </div>
                    <?php endif; ?>
                </a>
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
                    <p class="blog-card-excerpt"><?= e(excerpt($post['resumo'], 130)) ?></p>
                    <?php endif; ?>
                    <a href="<?= $bp ?>/artigo/<?= e($post['slug']) ?>" class="blog-card-link">
                        Ler mais <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($pages > 1): ?>
        <nav class="blog-pagination" aria-label="Paginação">
            <?php if ($page > 1): ?>
            <a href="<?= paginate_url($page - 1, $tipo, $busca) ?>" class="page-btn page-prev">
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
            <?php endif; ?>

            <div class="page-numbers">
                <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
                <a href="<?= paginate_url($i, $tipo, $busca) ?>"
                   class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>

            <?php if ($page < $pages): ?>
            <a href="<?= paginate_url($page + 1, $tipo, $busca) ?>" class="page-btn page-next">
                Próxima <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>

        <?php else: ?>
        <div class="blog-empty">
            <i class="fas fa-search blog-empty-icon"></i>
            <?php if ($busca): ?>
                <h3>Nenhum resultado para "<?= e($busca) ?>"</h3>
                <p>Tente outras palavras-chave ou <a href="<?= $bp ?>/blog">veja todas as publicações</a>.</p>
            <?php else: ?>
                <h3>Ainda não há publicações nesta categoria</h3>
                <p>Em breve publicaremos novos conteúdos. <a href="/#contato">Entre em contato</a>.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div><!-- /.container -->
</main>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <img src="<?= $footer_logo_path ?>" alt="<?= $office_name ?>" height="102" width="220" decoding="async" class="footer-logo">
            <p>Soluções jurídicas estratégicas em Direito Tributário e Empresarial.</p>
        </div>
        <div class="footer-links">
            <h4>Navegação</h4>
            <ul>
                <li><a href="<?= $bp ?>/">Início</a></li>
                <li><a href="/#sobre">O Escritório</a></li>
                <li><a href="/#areas">Áreas de Atuação</a></li>
                <li><a href="<?= $bp ?>/blog">Blog</a></li>
                <li><a href="/#contato">Contato</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contato</h4>
            <?php if (!empty($email_ctto)): ?>
            <p><i class="fas fa-envelope"></i> <a href="mailto:<?= e($email_ctto) ?>"><?= e($email_ctto) ?></a></p>
            <?php endif; ?>
            <?php if (!empty($telefone)): ?>
            <p><i class="fas fa-phone"></i> <?= e($telefone) ?></p>
            <?php endif; ?>
            <?php if (!empty($instagram) || !empty($linkedin) || !empty($facebook)): ?>
            <div class="social-links footer-social" style="margin-top:12px">
                <?php if (!empty($instagram)): ?><a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a><?php endif; ?>
                <?php if (!empty($linkedin)): ?><a href="<?= e($linkedin) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a><?php endif; ?>
                <?php if (!empty($facebook)): ?><a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><?php endif; ?>
            </div>
            <?php endif; ?>
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

<script src="assets/js/main.js"></script>
</body>
</html>
