<?php
/**
 * Alberione Advogados — Admin Dashboard
 * admin/index.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

$admin  = get_current_admin();
$stats  = get_dashboard_stats();
$recent_posts = get_posts(['limit' => 6, 'status' => 'todos']);

try {
    $recent_msgs = db()->query(
        "SELECT id, nome, email, assunto, status, criado_em FROM contact_messages ORDER BY criado_em DESC LIMIT 5"
    )->fetchAll();
} catch (Exception $e) {
    $recent_msgs = [];
}

$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin <?= OFFICE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<?php $bp = rtrim(SITE_URL, '/'); ?>
<body class="admin-body">
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/partials/topbar.php'; ?>

    <main class="admin-main">
        <div class="admin-page-header">
            <h2>Dashboard</h2>
            <p>Bem-vindo, <?= e($admin['nome'] ?? 'Administrador') ?>! Aqui está o resumo do seu site.</p>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= e($flash['message']) ?>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-widget">
                <div class="stat-widget-icon blue"><i class="fas fa-file-alt"></i></div>
                <div class="stat-widget-info">
                    <span class="stat-widget-num"><?= $stats['total_posts'] ?></span>
                    <span class="stat-widget-label">Total de Posts</span>
                </div>
            </div>
            <div class="stat-widget">
                <div class="stat-widget-icon green"><i class="fas fa-globe"></i></div>
                <div class="stat-widget-info">
                    <span class="stat-widget-num"><?= $stats['posts_publicados'] ?></span>
                    <span class="stat-widget-label">Publicados</span>
                </div>
            </div>
            <div class="stat-widget">
                <div class="stat-widget-icon orange"><i class="fas fa-edit"></i></div>
                <div class="stat-widget-info">
                    <span class="stat-widget-num"><?= $stats['posts_rascunho'] ?></span>
                    <span class="stat-widget-label">Rascunhos</span>
                </div>
            </div>
            <div class="stat-widget">
                <div class="stat-widget-icon red"><i class="fas fa-envelope"></i></div>
                <div class="stat-widget-info">
                    <span class="stat-widget-num"><?= $stats['msg_novas'] ?></span>
                    <span class="stat-widget-label">Mensagens Novas</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="<?= $bp ?>/admin/post-novo.php" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i> Novo Artigo
            </a>
            <a href="<?= $bp ?>/admin/post-novo.php?tipo=informativo" class="quick-action-btn">
                <i class="fas fa-bell"></i> Novo Informativo
            </a>
            <a href="<?= $bp ?>/admin/mensagens.php" class="quick-action-btn">
                <i class="fas fa-inbox"></i> Ver Mensagens
                <?php if ($stats['msg_novas'] > 0): ?>
                <span class="badge-count"><?= $stats['msg_novas'] ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $bp ?>/admin/configuracoes.php" class="quick-action-btn">
                <i class="fas fa-cog"></i> Configurações
            </a>
        </div>

        <div class="dashboard-grid">
            <!-- Posts recentes -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Posts Recentes</h3>
                    <a href="<?= $bp ?>/admin/posts.php" class="admin-card-link">Ver todos</a>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($recent_posts)): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $p): ?>
                                <tr>
                                    <td class="td-title"><?= e(mb_substr($p['titulo'], 0, 50)) ?><?= mb_strlen($p['titulo']) > 50 ? '…' : '' ?></td>
                                    <td><span class="badge badge-<?= $p['tipo'] ?>"><?= $p['tipo'] === 'artigo' ? 'Artigo' : 'Informativo' ?></span></td>
                                    <td><span class="status-badge status-<?= $p['status'] ?>"><?= $p['status'] === 'publicado' ? 'Publicado' : 'Rascunho' ?></span></td>
                                    <td class="td-date"><?= format_date($p['publicado_em'] ?? $p['criado_em']) ?></td>
                                    <td><a href="<?= $bp ?>/admin/post-edit.php?id=<?= $p['id'] ?>" class="table-action"><i class="fas fa-edit"></i></a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state"><i class="fas fa-file-alt"></i><p>Nenhum post cadastrado ainda.</p><a href="<?= $bp ?>/admin/post-novo.php" class="btn-sm-primary">Criar primeiro post</a></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mensagens recentes -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>Mensagens Recentes</h3>
                    <a href="<?= $bp ?>/admin/mensagens.php" class="admin-card-link">Ver todas</a>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($recent_msgs)): ?>
                    <ul class="msg-list">
                        <?php foreach ($recent_msgs as $msg): ?>
                        <li class="msg-item <?= $msg['status'] === 'novo' ? 'msg-new' : '' ?>">
                            <div class="msg-avatar"><i class="fas fa-user"></i></div>
                            <div class="msg-info">
                                <strong><?= e($msg['nome']) ?></strong>
                                <span><?= !empty($msg['assunto']) ? e(mb_substr($msg['assunto'], 0, 40)) : 'Sem assunto' ?></span>
                                <time><?= format_datetime($msg['criado_em']) ?></time>
                            </div>
                            <div class="msg-badge">
                                <?php if ($msg['status'] === 'novo'): ?>
                                <span class="dot-new"></span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= $bp ?>/admin/mensagens.php?id=<?= $msg['id'] ?>" class="msg-link"></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="empty-state"><i class="fas fa-inbox"></i><p>Nenhuma mensagem recebida.</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
