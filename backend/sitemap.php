<?php
/**
 * Alberione Advogados — Sitemap XML dinâmico
 * backend/sitemap.php
 * Acesso: /sitemap.xml  (reescrito pelo .htaccess)
 */
if (!defined('ROOT_PATH')) require_once __DIR__ . '/../config/config.php';

// Cache simples: gera o sitemap no máximo 1x por hora
$cache_file = sys_get_temp_dir() . '/alberione_sitemap.xml';
$cache_ttl  = 3600; // 1 hora

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
    header('Content-Type: application/xml; charset=UTF-8');
    readfile($cache_file);
    exit;
}

$base = rtrim(SITE_URL, '/');

// Páginas estáticas
$static_pages = [
    ['loc' => $base . '/',       'priority' => '1.0', 'changefreq' => 'weekly'],
    ['loc' => $base . '/blog',   'priority' => '0.9', 'changefreq' => 'daily'],
];

// Posts publicados
$posts = [];
try {
    $stmt = db()->query(
        "SELECT slug, atualizado_em, publicado_em
         FROM posts
         WHERE status = 'publicado' AND excluido_em IS NULL
         ORDER BY publicado_em DESC
         LIMIT 500"
    );
    $posts = $stmt->fetchAll();
} catch (Exception $e) {
    // banco indisponível — exibe só páginas estáticas
}

// Monta XML
ob_start();
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($static_pages as $page) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($page['loc']) . "</loc>\n";
    echo "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $page['priority'] . "</priority>\n";
    echo "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
    echo "  </url>\n";
}

foreach ($posts as $post) {
    $lastmod = !empty($post['atualizado_em']) ? date('Y-m-d', strtotime($post['atualizado_em'])) : date('Y-m-d');
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars((defined('USE_FRIENDLY_URLS') && USE_FRIENDLY_URLS) ? ($base . '/artigo/' . $post['slug']) : ($base . '/artigo.php?slug=' . rawurlencode($post['slug']))) . "</loc>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "    <lastmod>" . $lastmod . "</lastmod>\n";
    echo "  </url>\n";
}

echo '</urlset>' . "\n";

$xml = ob_get_clean();

// Salva cache
@file_put_contents($cache_file, $xml);

header('Content-Type: application/xml; charset=UTF-8');
echo $xml;
