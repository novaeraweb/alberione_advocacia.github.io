<?php
/**
 * Alberione Advogados — Verificação de Instalação
 * check.php
 *
 * Acesse: http://localhost:8888/AlberioneAdvocacia/check.php
 * ⚠️  REMOVA ou proteja este arquivo APÓS confirmar que tudo funciona!
 */

// Bloqueia em produção
$is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])
            || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost')
            || str_contains($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1');

// Permite acesso apenas com senha em prod, ou sempre em local
$senha_acesso = 'alberione_check_2025'; // Troque antes de subir para prod
if (!$is_local && ($_GET['key'] ?? '') !== $senha_acesso) {
    http_response_code(403);
    die('Acesso negado.');
}

$checks = [];

// ── PHP Version ──────────────────────────────────────────────────
$php_version = PHP_VERSION;
$php_ok = version_compare($php_version, '8.0.0', '>=');
$checks[] = [
    'label'  => 'Versão do PHP',
    'value'  => $php_version,
    'status' => $php_ok,
    'note'   => $php_ok ? 'OK' : 'Requer PHP 8.0 ou superior',
];

// ── Extensões ────────────────────────────────────────────────────
$exts = ['pdo', 'pdo_mysql', 'mbstring', 'fileinfo', 'json', 'openssl'];
foreach ($exts as $ext) {
    $ok = extension_loaded($ext);
    $checks[] = [
        'label'  => "Extensão: $ext",
        'value'  => $ok ? 'Carregada' : 'NÃO encontrada',
        'status' => $ok,
        'note'   => $ok ? '' : "Habilite a extensão $ext no php.ini",
    ];
}

// ── Banco de Dados ───────────────────────────────────────────────
$db_host    = 'localhost';
$db_name    = 'alberione_site';
$db_user    = 'root';
$db_pass    = '';

// Tenta ler do config se existir
if (file_exists(__DIR__ . '/config/config.php')) {
    // Detecta constantes definidas sem carregar o arquivo completo
    // (config.php inicia sessão, melhor só pegar credenciais)
    $config_content = file_get_contents(__DIR__ . '/config/config.php');
    foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $const) {
        if (preg_match("/define\('$const',\s*getenv\('[^']+'\)\s*\?:\s*'([^']*)'\)/", $config_content, $m)) {
            $${'db_' . strtolower(str_replace('DB_', '', $const))} = $m[1];
        }
    }
}

$db_ok = false;
$db_msg = '';
$db_tables = [];
$db_version = '';
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $db_ok  = true;
    $db_version = $pdo->query('SELECT VERSION()')->fetchColumn();

    // Verifica tabelas
    $tables_needed = ['admins', 'site_settings', 'posts', 'contact_messages', 'media_library', 'auth_logs'];
    $existing = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables_needed as $t) {
        $db_tables[$t] = in_array($t, $existing);
    }
    $db_msg = "Conectado! MySQL $db_version";
} catch (PDOException $e) {
    $db_msg = 'FALHA: ' . $e->getMessage();
}

$checks[] = [
    'label'  => 'Conexão MySQL',
    'value'  => $db_msg,
    'status' => $db_ok,
    'note'   => $db_ok ? '' : 'Verifique host, usuário e senha no config/config.php',
];

if ($db_ok) {
    $all_tables = array_filter($db_tables);
    $checks[] = [
        'label'  => 'Tabelas do banco',
        'value'  => count($all_tables) . '/' . count($db_tables) . ' tabelas encontradas',
        'status' => count($all_tables) === count($db_tables),
        'note'   => count($all_tables) < count($db_tables)
            ? 'Execute database/alberione.sql via phpMyAdmin'
            : 'Todas as tabelas OK',
    ];
}

// ── Diretório uploads ────────────────────────────────────────────
$uploads_path = __DIR__ . '/uploads/';
$uploads_exists = is_dir($uploads_path);
$uploads_write  = $uploads_exists && is_writable($uploads_path);
$checks[] = [
    'label'  => 'Pasta uploads/',
    'value'  => $uploads_exists ? ($uploads_write ? 'Existe e gravável' : 'Existe mas sem permissão de escrita') : 'NÃO existe',
    'status' => $uploads_write,
    'note'   => $uploads_write ? '' : ($uploads_exists ? 'Execute: chmod 755 uploads/' : 'Crie a pasta uploads/ manualmente'),
];

$uploads_posts_path = __DIR__ . '/uploads/posts/';
$checks[] = [
    'label'  => 'Pasta uploads/posts/',
    'value'  => is_dir($uploads_posts_path) ? (is_writable($uploads_posts_path) ? 'OK' : 'Sem permissão') : 'NÃO existe',
    'status' => is_dir($uploads_posts_path) && is_writable($uploads_posts_path),
    'note'   => is_dir($uploads_posts_path) ? '' : 'Crie a pasta uploads/posts/ manualmente',
];

// ── .htaccess / mod_rewrite ──────────────────────────────────────
$htaccess_ok = file_exists(__DIR__ . '/.htaccess');
$checks[] = [
    'label'  => '.htaccess',
    'value'  => $htaccess_ok ? 'Arquivo encontrado' : 'NÃO encontrado',
    'status' => $htaccess_ok,
    'note'   => $htaccess_ok ? 'Verifique se AllowOverride All está ativo no Apache' : 'Arquivo .htaccess ausente na raiz do projeto',
];

$rewrite_ok = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());
$checks[] = [
    'label'  => 'mod_rewrite Apache',
    'value'  => $rewrite_ok ? 'Ativo' : 'Não detectado via PHP',
    'status' => $rewrite_ok,
    'note'   => $rewrite_ok
        ? ''
        : 'Habilite no httpd.conf: LoadModule rewrite_module modules/mod_rewrite.so — e defina AllowOverride All',
];

// ── Arquivos principais ──────────────────────────────────────────
$required_files = [
    'index.php', 'blog.php', 'artigo.php', '404.php', '.htaccess',
    'config/config.php', 'config/database.php', 'config/helpers.php',
    'database/alberione.sql',
    'admin/login.php', 'admin/index.php', 'admin/posts.php',
    'admin/post-novo.php', 'admin/post-edit.php',
    'admin/mensagens.php', 'admin/configuracoes.php', 'admin/logout.php',
    'admin/partials/sidebar.php', 'admin/partials/topbar.php',
    'admin/partials/modal-confirm.php',
    'assets/css/style.css', 'assets/css/blog.css', 'assets/css/admin.css',
    'assets/js/main.js', 'assets/js/admin.js',
    'backend/contato.php', 'backend/sitemap.php',
];
$missing = [];
foreach ($required_files as $f) {
    if (!file_exists(__DIR__ . '/' . $f)) $missing[] = $f;
}
$checks[] = [
    'label'  => 'Arquivos do projeto',
    'value'  => empty($missing) ? 'Todos os ' . count($required_files) . ' arquivos presentes' : count($missing) . ' arquivo(s) faltando',
    'status' => empty($missing),
    'note'   => empty($missing) ? '' : 'Faltando: ' . implode(', ', $missing),
];

// ── Admin registrado ─────────────────────────────────────────────
$admin_ok = false;
$admin_msg = 'Banco indisponível';
if ($db_ok) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM admins WHERE ativo = 1")->fetchColumn();
        $admin_ok  = $count > 0;
        $admin_msg = $count . ' administrador(es) ativo(s)';
    } catch (Exception $e) {
        $admin_msg = 'Tabela admins não encontrada';
    }
}
$checks[] = [
    'label'  => 'Admin cadastrado',
    'value'  => $admin_msg,
    'status' => $admin_ok,
    'note'   => $admin_ok ? 'Login: admin@alberione.com.br / password' : 'Execute o SQL para criar o admin padrão',
];

// ── SITE_URL ─────────────────────────────────────────────────────
if (file_exists(__DIR__ . '/config/config.php')) {
    // Apenas detecta se config existe, não carregamos para evitar conflitos
    $checks[] = [
        'label'  => 'config/config.php',
        'value'  => 'Arquivo presente',
        'status' => true,
        'note'   => '',
    ];
}

// ── Resumo ───────────────────────────────────────────────────────
$total_ok = count(array_filter($checks, fn($c) => $c['status']));
$total    = count($checks);
$all_ok   = $total_ok === $total;

// ── Render HTML ──────────────────────────────────────────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script   = $_SERVER['SCRIPT_NAME'] ?? '/check.php';
$base_dir = rtrim(dirname($script), '/');
$site_url  = "$protocol://$host$base_dir";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico — Alberione Advogados</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f4f6f9; color: #2b2b2b; padding: 2rem 1rem; }
        .container { max-width: 860px; margin: 0 auto; }
        h1 { font-size: 1.6rem; margin-bottom: .4rem; color: #1F4E79; }
        .subtitle { color: #666; font-size: .9rem; margin-bottom: 2rem; }
        .summary { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .summary-card { padding: 1rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: 1.1rem; flex: 1; min-width: 140px; text-align: center; }
        .summary-ok  { background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .summary-fail { background: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
        th { background: #1F4E79; color: #fff; padding: .75rem 1rem; text-align: left; font-size: .85rem; text-transform: uppercase; letter-spacing: .05em; }
        td { padding: .75rem 1rem; border-bottom: 1px solid #f0f0f0; font-size: .9rem; vertical-align: top; }
        tr:last-child td { border-bottom: none; }
        .ok   { color: #2e7d32; font-weight: 600; }
        .fail { color: #c62828; font-weight: 600; }
        .badge { display: inline-block; width: 20px; height: 20px; border-radius: 50%; text-align: center; line-height: 20px; font-size: .75rem; font-weight: 700; }
        .badge-ok   { background: #e8f5e9; color: #2e7d32; }
        .badge-fail { background: #ffebee; color: #c62828; }
        .note { color: #888; font-size: .8rem; margin-top: .25rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: .95rem; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4caf50; }
        .alert-warning { background: #fff8e1; color: #e65100; border-left: 4px solid #ff9800; }
        .links { margin-top: 2rem; display: flex; gap: .75rem; flex-wrap: wrap; }
        .links a { background: #1F4E79; color: #fff; padding: .5rem 1.1rem; border-radius: 6px; text-decoration: none; font-size: .9rem; }
        .links a:hover { background: #163a5a; }
        .links a.gold { background: #B08A57; }
        .links a.gold:hover { background: #8a6a40; }
        footer { margin-top: 2rem; text-align: center; color: #aaa; font-size: .8rem; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 Diagnóstico do Sistema</h1>
    <p class="subtitle">Alberione Advogados — Verificação de instalação local</p>

    <div class="summary">
        <div class="summary-card summary-ok"><?= $total_ok ?> / <?= $total ?><br><small>Verificações OK</small></div>
        <?php if ($all_ok): ?>
        <div class="summary-card summary-ok">✅ Tudo pronto para rodar!</div>
        <?php else: ?>
        <div class="summary-card summary-fail">⚠️ <?= $total - $total_ok ?> item(ns) precisam de atenção</div>
        <?php endif; ?>
    </div>

    <?php if ($all_ok): ?>
    <div class="alert alert-success">
        <strong>✅ Sistema pronto!</strong> Todas as verificações passaram. Acesse o admin abaixo.
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <strong>⚠️ Atenção:</strong> Corrija os itens marcados em vermelho antes de usar o sistema.
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th style="width:32px"></th>
                <th>Verificação</th>
                <th>Resultado</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($checks as $c): ?>
            <tr>
                <td><span class="badge <?= $c['status'] ? 'badge-ok' : 'badge-fail' ?>"><?= $c['status'] ? '✓' : '✗' ?></span></td>
                <td><?= htmlspecialchars($c['label']) ?></td>
                <td class="<?= $c['status'] ? 'ok' : 'fail' ?>"><?= htmlspecialchars($c['value']) ?></td>
                <td><span class="note"><?= htmlspecialchars($c['note']) ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($db_ok && !empty($db_tables)): ?>
    <br>
    <table>
        <thead><tr><th>Tabela</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($db_tables as $t => $exists): ?>
            <tr>
                <td><?= $t ?></td>
                <td class="<?= $exists ? 'ok' : 'fail' ?>"><?= $exists ? '✓ Presente' : '✗ Ausente — execute alberione.sql' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="links">
        <a href="<?= htmlspecialchars($site_url) ?>/">🏠 Ver Site</a>
        <a href="<?= htmlspecialchars($site_url) ?>/admin/login.php" class="gold">🔐 Painel Admin</a>
        <a href="<?= htmlspecialchars($site_url) ?>/blog">📰 Blog</a>
    </div>

    <footer>
        ⚠️ <strong>Remova ou proteja este arquivo após a instalação!</strong>
        · check.php · <?= date('d/m/Y H:i') ?>
    </footer>
</div>
</body>
</html>
