<?php
/**
 * Alberione Advogados - Funções Auxiliares
 * config/helpers.php
 */

// ─── SANITIZAÇÃO ────────────────────────────────────────────────

/**
 * Escapa saída HTML
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitiza string para uso seguro
 */
function clean(string $str): string {
    return trim(strip_tags($str));
}

// Polyfills para compatibilidade com PHP 7.x em hospedagens compartilhadas.
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        if ($needle === '') return true;
        $length = strlen($needle);
        return substr($haystack, -$length) === $needle;
    }
}


/**
 * Sanitiza HTML vindo do editor de conteúdo do blog.
 *
 * Permite apenas tags/atributos editoriais seguros e remove vetores comuns de XSS:
 * <script>, iframes, eventos inline, style inline, javascript:, vbscript:, data: etc.
 *
 * Observação: foi implementado sem DOMDocument para funcionar também em hospedagens
 * compartilhadas onde a extensão php-xml não está ativa.
 */
function sanitize_post_html(string $html): string {
    $html = trim($html);
    if ($html === '') return '';

    $html = str_replace("\0", '', $html);
    $html = preg_replace('/<!--.*?-->/s', '', $html) ?? '';

    // Remove blocos inteiros de tags perigosas, inclusive seu conteúdo.
    $dangerousBlocks = [
        'script', 'style', 'iframe', 'object', 'embed', 'applet', 'svg', 'math',
        'form', 'input', 'button', 'textarea', 'select', 'option', 'link', 'meta',
        'base', 'frame', 'frameset', 'audio', 'video', 'source', 'track'
    ];
    foreach ($dangerousBlocks as $tag) {
        $html = preg_replace('/<' . $tag . '\b[^>]*>.*?<\/' . $tag . '>/is', '', $html) ?? '';
        $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/is', '', $html) ?? '';
    }

    $allowedTags = [
        'p' => [],
        'br' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'u' => [],
        's' => [],
        'h2' => [],
        'h3' => [],
        'h4' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'blockquote' => [],
        'pre' => [],
        'code' => [],
        'span' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
    ];

    $allowedTagString = '<' . implode('><', array_keys($allowedTags)) . '>';
    $html = strip_tags($html, $allowedTagString);

    $isSafeUrl = static function (string $url): bool {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $compact = preg_replace('/[\x00-\x20]+/', '', strtolower($url));

        if ($compact === '') return false;
        if (str_starts_with($compact, 'javascript:')) return false;
        if (str_starts_with($compact, 'vbscript:')) return false;
        if (str_starts_with($compact, 'data:')) return false;

        // URLs relativas, âncoras e caminhos absolutos locais são permitidos.
        if (str_starts_with($url, '/') || str_starts_with($url, '#') || str_starts_with($url, './') || str_starts_with($url, '../')) {
            return true;
        }

        if (preg_match('/^([a-z][a-z0-9+.-]*):/i', $url, $m)) {
            return in_array(strtolower($m[1]), ['http', 'https', 'mailto', 'tel'], true);
        }

        // Caminho relativo simples, exemplo: assets/img/imagem.webp
        return !preg_match('/^[a-z][a-z0-9+.-]*:/i', $url);
    };

    $parseAttributes = static function (string $rawAttrs): array {
        $attrs = [];
        preg_match_all('/([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'>]+)/', $rawAttrs, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $name = strtolower($match[1]);
            $value = trim($match[2]);
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            $attrs[$name] = $value;
        }
        return $attrs;
    };

    $html = preg_replace_callback('/<\s*(\/)?\s*([a-zA-Z][a-zA-Z0-9]*)\b([^>]*)>/i', function ($m) use ($allowedTags, $parseAttributes, $isSafeUrl) {
        $closing = $m[1] === '/';
        $tag = strtolower($m[2]);
        $rawAttrs = $m[3] ?? '';

        if (!array_key_exists($tag, $allowedTags)) {
            return '';
        }

        if ($closing) {
            return in_array($tag, ['br', 'img'], true) ? '' : '</' . $tag . '>';
        }

        $attrs = $parseAttributes($rawAttrs);
        $safeAttrs = [];

        foreach ($attrs as $name => $value) {
            if (str_starts_with($name, 'on')) continue;
            if (!in_array($name, $allowedTags[$tag], true)) continue;

            if (in_array($name, ['href', 'src'], true) && !$isSafeUrl($value)) {
                continue;
            }

            if (in_array($name, ['width', 'height'], true) && !preg_match('/^\d{1,5}$/', $value)) {
                continue;
            }

            if ($name === 'target' && !in_array($value, ['_blank', '_self'], true)) {
                continue;
            }

            if ($name === 'loading' && !in_array($value, ['lazy', 'eager'], true)) {
                continue;
            }

            $safeAttrs[$name] = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        if ($tag === 'a') {
            if (empty($safeAttrs['href'])) {
                $safeAttrs['href'] = '#';
            }
            if (preg_match('/^https?:\/\//i', html_entity_decode($safeAttrs['href'], ENT_QUOTES | ENT_HTML5, 'UTF-8'))) {
                $safeAttrs['target'] = '_blank';
                $safeAttrs['rel'] = 'noopener noreferrer';
            } else {
                unset($safeAttrs['target'], $safeAttrs['rel']);
            }
        }

        if ($tag === 'img') {
            if (empty($safeAttrs['src'])) {
                return '';
            }
            $safeAttrs['loading'] = $safeAttrs['loading'] ?? 'lazy';
        }

        $attrHtml = '';
        foreach ($safeAttrs as $name => $value) {
            $attrHtml .= ' ' . $name . '="' . $value . '"';
        }

        return '<' . $tag . $attrHtml . '>';
    }, $html) ?? '';

    // Remove protocolos perigosos que tenham sobrevivido por codificação incomum.
    $html = preg_replace('/(javascript|vbscript|data)\s*:/i', '', $html) ?? '';

    return trim($html);
}


// ─── SLUG ───────────────────────────────────────────────────────

function slugify(string $text): string {
    $text = mb_strtolower($text, 'UTF-8');
    $map = [
        'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
        'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
        'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
        'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
        'ç'=>'c','ñ'=>'n',
    ];
    $text = strtr($text, $map);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', trim($text));
    return trim($text, '-');
}

// ─── AUTENTICAÇÃO ───────────────────────────────────────────────

function is_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_auth(): void {
    if (!is_logged_in()) {
        $login_url = (defined('SITE_URL') ? rtrim(SITE_URL, '/') : '') . '/admin/login.php';
        header('Location: ' . $login_url);
        exit;
    }
}

function get_current_admin(): ?array {
    if (!is_logged_in()) return null;
    $stmt = db()->prepare('SELECT id, nome, email FROM admins WHERE id = ? AND ativo = 1');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

// ─── CSRF ───────────────────────────────────────────────────────

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(string $token): bool {
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ─── FLASH MESSAGES ─────────────────────────────────────────────

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ─── CONFIGURAÇÕES DO SITE ──────────────────────────────────────

function get_site_settings(): array {
    static $settings = null;
    if ($settings !== null) return $settings;
    try {
        $stmt = db()->query("SELECT * FROM site_settings WHERE config_key = 'default' LIMIT 1");
        $row = $stmt->fetch();
        $settings = $row ?: [];
    } catch (Exception $e) {
        $settings = [];
    }
    return $settings;
}

function setting(string $key, string $default = ''): string {
    $s = get_site_settings();
    return !empty($s[$key]) ? $s[$key] : $default;
}

// ─── POSTS / BLOG ───────────────────────────────────────────────

function get_posts(array $opts = []): array {
    $tipo   = $opts['tipo']   ?? null;
    $status = $opts['status'] ?? 'publicado';
    $limit  = (int)($opts['limit']  ?? BLOG_PER_PAGE);
    $offset = (int)($opts['offset'] ?? 0);
    $search = $opts['search'] ?? '';
    $categoria = $opts['categoria'] ?? '';

    $where = ['p.excluido_em IS NULL'];
    $params = [];

    if ($status !== 'todos') {
        $where[] = 'p.status = ?';
        $params[] = $status;
    }
    if ($tipo) {
        $where[] = 'p.tipo = ?';
        $params[] = $tipo;
    }
    if ($categoria) {
        $where[] = 'p.categoria = ?';
        $params[] = $categoria;
    }
    if ($search) {
        $where[] = '(p.titulo LIKE ? OR p.resumo LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql = "SELECT p.*, a.nome AS autor_nome
            FROM posts p
            LEFT JOIN admins a ON a.id = p.autor_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY p.publicado_em DESC, p.criado_em DESC
            LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function count_posts(array $opts = []): int {
    $tipo   = $opts['tipo']   ?? null;
    $status = $opts['status'] ?? 'publicado';
    $search = $opts['search'] ?? '';
    $categoria = $opts['categoria'] ?? '';

    $where = ['excluido_em IS NULL'];
    $params = [];

    if ($status !== 'todos') {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    if ($tipo) {
        $where[] = 'tipo = ?';
        $params[] = $tipo;
    }
    if ($categoria) {
        $where[] = 'categoria = ?';
        $params[] = $categoria;
    }
    if ($search) {
        $where[] = '(titulo LIKE ? OR resumo LIKE ?)';
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql = "SELECT COUNT(*) FROM posts WHERE " . implode(' AND ', $where);
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function get_post_by_slug(string $slug): ?array {
    $stmt = db()->prepare(
        "SELECT p.*, a.nome AS autor_nome
         FROM posts p
         LEFT JOIN admins a ON a.id = p.autor_id
         WHERE p.slug = ? AND p.status = 'publicado' AND p.excluido_em IS NULL
         LIMIT 1"
    );
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function get_post_by_id(int $id): ?array {
    $stmt = db()->prepare(
        "SELECT p.*, a.nome AS autor_nome
         FROM posts p
         LEFT JOIN admins a ON a.id = p.autor_id
         WHERE p.id = ? AND p.excluido_em IS NULL
         LIMIT 1"
    );
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function get_posts_destaques(int $limit = 3): array {
    $stmt = db()->prepare(
        "SELECT * FROM posts
         WHERE destaque = 1 AND status = 'publicado' AND excluido_em IS NULL
         ORDER BY publicado_em DESC
         LIMIT ?"
    );
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

function get_posts_recentes(int $limit = 4): array {
    return get_posts(['limit' => $limit, 'status' => 'publicado']);
}

function get_posts_relacionados(int $post_id, string $tipo, int $limit = 3, string $categoria = ''): array {
    if ($categoria !== '') {
        $stmt = db()->prepare(
            "SELECT * FROM posts
             WHERE categoria = ? AND status = 'publicado' AND excluido_em IS NULL AND id != ?
             ORDER BY publicado_em DESC
             LIMIT ?"
        );
        $stmt->execute([$categoria, $post_id, $limit]);
        $rows = $stmt->fetchAll();
        if (!empty($rows)) return $rows;
    }

    $stmt = db()->prepare(
        "SELECT * FROM posts
         WHERE tipo = ? AND status = 'publicado' AND excluido_em IS NULL AND id != ?
         ORDER BY publicado_em DESC
         LIMIT ?"
    );
    $stmt->execute([$tipo, $post_id, $limit]);
    return $stmt->fetchAll();
}

function increment_views(int $post_id): void {
    db()->prepare("UPDATE posts SET views_count = views_count + 1 WHERE id = ?")->execute([$post_id]);
}

function slug_exists(string $slug, int $exclude_id = 0): bool {
    $stmt = db()->prepare("SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ? AND excluido_em IS NULL");
    $stmt->execute([$slug, $exclude_id]);
    return (int)$stmt->fetchColumn() > 0;
}

function generate_unique_slug(string $base, int $exclude_id = 0): string {
    $slug = slugify($base);
    $original = $slug;
    $i = 1;
    while (slug_exists($slug, $exclude_id)) {
        $slug = $original . '-' . $i++;
    }
    return $slug;
}

// ─── MENSAGENS ──────────────────────────────────────────────────

function count_new_messages(): int {
    try {
        $stmt = db()->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'novo'");
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function save_contact_message(array $data) {
    $stmt = db()->prepare(
        "INSERT INTO contact_messages (nome, email, telefone, assunto, mensagem, origem_pagina, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $ok = $stmt->execute([
        clean($data['nome'] ?? ''),
        clean($data['email'] ?? ''),
        clean($data['telefone'] ?? ''),
        clean($data['assunto'] ?? ''),
        clean($data['mensagem'] ?? ''),
        $_SERVER['REQUEST_URI'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? '',
    ]);
    return $ok ? (int)db()->lastInsertId() : false;
}

// ─── UPLOAD ─────────────────────────────────────────────────────

function upload_image(array $file, string $subfolder = 'posts') {
    if ($file['error'] !== UPLOAD_ERR_OK) return false;
    if ($file['size'] > UPLOAD_MAX_SIZE) return false;

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, UPLOAD_ALLOWED_TYPES, true)) return false;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_EXT, true)) return false;

    $dir = UPLOADS_PATH . $subfolder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest     = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

    return UPLOADS_URL . $subfolder . '/' . $filename;
}

// ─── AUTH LOG ───────────────────────────────────────────────────

function log_auth(string $acao, ?int $admin_id, string $email_tentado = ''): void {
    try {
        db()->prepare(
            "INSERT INTO auth_logs (admin_id, email_tentado, ip_address, user_agent, acao)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([
            $admin_id,
            $email_tentado,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $acao,
        ]);
    } catch (Exception $e) {
        // silently fail
    }
}

// ─── FORMATAÇÃO ─────────────────────────────────────────────────

function format_date(string $datetime, string $format = 'd/m/Y'): string {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

function format_datetime(string $datetime): string {
    return format_date($datetime, 'd/m/Y H:i');
}

function reading_time(string $content): int {
    $words = str_word_count(strip_tags($content));
    return (int)max(1, round($words / 200));
}

function excerpt(string $text, int $length = 160): string {
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

function post_image_url(string $path, string $fallback = ''): string {
    if (empty($path)) return $fallback;
    // Se já for URL absoluta, retorna diretamente
    if (str_starts_with($path, 'http')) return $path;
    return rtrim(SITE_URL, '/') . $path;
}

function default_post_image_path(): string {
    return '/assets/images/default-post.svg';
}

function default_post_image_url(): string {
    return post_image_url(default_post_image_path());
}

function post_cover_url(array $post): string {
    $path = trim((string)($post['imagem_capa'] ?? ''));
    return $path !== '' ? post_image_url($path) : default_post_image_url();
}

function post_cover_alt(array $post, string $fallback = 'Imagem editorial da publicação'): string {
    $alt = trim((string)($post['imagem_alt'] ?? ''));
    if ($alt !== '') return $alt;

    $titulo = trim((string)($post['titulo'] ?? ''));
    return $titulo !== '' ? $titulo : $fallback;
}

function post_author_display(array $post, string $officeName = ''): string {
    $autor = trim((string)($post['autor_nome'] ?? ''));
    if ($autor !== '') return $autor;

    $officeName = trim($officeName);
    return $officeName !== '' ? 'Equipe Jurídica ' . $officeName : 'Equipe Jurídica';
}


function categoria_post_label(?string $categoria): string {
    $categoria = trim((string)$categoria);
    return $categoria !== '' ? $categoria : 'Geral';
}

function categorias_posts_publicadas(): array {
    $stmt = db()->query("SELECT DISTINCT categoria FROM posts WHERE categoria IS NOT NULL AND categoria != '' AND status = 'publicado' AND excluido_em IS NULL ORDER BY categoria ASC");
    return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

function build_url(string $path = ''): string {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

function post_permalink(string $slug): string {
    $slug = trim($slug);
    if ($slug === '') {
        return build_url('blog');
    }

    if (defined('USE_FRIENDLY_URLS') && USE_FRIENDLY_URLS) {
        return build_url('artigo/' . rawurlencode($slug));
    }

    return build_url('artigo.php?slug=' . rawurlencode($slug));
}

// ─── REDIRECT ───────────────────────────────────────────────────

function redirect(string $url): void {
    // Se URL relativa (começa com /), prefixa com SITE_URL para compatibilidade localhost
    if (str_starts_with($url, '/') && defined('SITE_URL')) {
        $url = rtrim(SITE_URL, '/') . $url;
    }
    header('Location: ' . $url);
    exit;
}

// ─── STATS PARA O DASHBOARD ─────────────────────────────────────

function get_dashboard_stats(): array {
    try {
        $stats = [];
        $pdo = db();
        $stats['total_posts']      = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE excluido_em IS NULL")->fetchColumn();
        $stats['posts_publicados'] = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='publicado' AND excluido_em IS NULL")->fetchColumn();
        $stats['posts_rascunho']   = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='rascunho' AND excluido_em IS NULL")->fetchColumn();
        $stats['msg_novas']        = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status='novo'")->fetchColumn();
        $stats['msg_total']        = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
        return $stats;
    } catch (Exception $e) {
        return ['total_posts'=>0,'posts_publicados'=>0,'posts_rascunho'=>0,'msg_novas'=>0,'msg_total'=>0];
    }
}
