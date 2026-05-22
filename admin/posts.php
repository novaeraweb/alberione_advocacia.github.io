<?php
/**
 * Alberione Advogados — Admin: Gerenciar Posts
 * admin/posts.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

// ── Ações ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        flash('error', 'Requisição inválida.');
        redirect('/admin/posts.php');
    }

    $id = (int)($_POST['id'] ?? 0);
    if ($id < 1) { flash('error', 'ID inválido.'); redirect('/admin/posts.php'); }

    switch ($_POST['action']) {
        case 'excluir':
            db()->prepare("UPDATE posts SET excluido_em = NOW() WHERE id = ?")->execute([$id]);
            flash('success', 'Post excluído com sucesso.');
            break;
        case 'publicar':
            db()->prepare("UPDATE posts SET status='publicado', publicado_em=COALESCE(publicado_em, NOW()) WHERE id=?")->execute([$id]);
            flash('success', 'Post publicado.');
            break;
        case 'rascunho':
            db()->prepare("UPDATE posts SET status='rascunho' WHERE id=?")->execute([$id]);
            flash('success', 'Post movido para rascunho.');
            break;
    }
    redirect('/admin/posts.php?' . http_build_query(array_filter([
        'tipo'  => $_GET['tipo']  ?? '',
        'status'=> $_GET['status']?? '',
        'categoria' => $_GET['categoria'] ?? '',
        'q'         => $_GET['q'] ?? '',
    ])));
}

// ── Filtros ───────────────────────────────────────────────────────
$tipo_filter   = in_array($_GET['tipo']   ?? '', ['artigo','informativo']) ? $_GET['tipo']   : '';
$status_filter = in_array($_GET['status'] ?? '', ['publicado','rascunho']) ? $_GET['status'] : '';
$busca         = clean(substr($_GET['q'] ?? '', 0, 100));
$categoria_filter = clean(substr($_GET['categoria'] ?? '', 0, 100));
$page          = max(1, (int)($_GET['p'] ?? 1));
$limit         = ADMIN_PER_PAGE;
$offset        = ($page - 1) * $limit;

$opts = [
    'tipo'   => $tipo_filter ?: null,
    'status' => $status_filter ?: 'todos',
    'limit'  => $limit,
    'offset' => $offset,
    'search' => $busca,
    'categoria' => $categoria_filter ?: null,
];
$total = count_posts($opts);
$posts = get_posts($opts);
$pages = max(1, (int)ceil($total / $limit));
$categorias = categorias_posts_publicadas();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Posts | Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
            <div>
                <h2>Gerenciar Posts</h2>
                <p><?= $total ?> post<?= $total !== 1 ? 's' : '' ?> encontrado<?= $total !== 1 ? 's' : '' ?></p>
            </div>
            <a href="<?= $bp ?>/admin/post-novo.php" class="btn-admin-primary">
                <i class="fas fa-plus"></i> Novo Post
            </a>
        </div>

        <!-- Filtros -->
        <form class="admin-filters" method="get">
            <div class="filter-group">
                <select name="tipo" onchange="this.form.submit()">
                    <option value="">Todos os tipos</option>
                    <option value="artigo"      <?= $tipo_filter === 'artigo'      ? 'selected' : '' ?>>Artigos</option>
                    <option value="informativo" <?= $tipo_filter === 'informativo' ? 'selected' : '' ?>>Informativos</option>
                </select>

                <select name="categoria" onchange="this.form.submit()">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categorias as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $categoria_filter === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" onchange="this.form.submit()">
                    <option value="">Todos os status</option>
                    <option value="publicado" <?= $status_filter === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                    <option value="rascunho"  <?= $status_filter === 'rascunho'  ? 'selected' : '' ?>>Rascunho</option>
                </select>
            </div>
            <div class="admin-search">
                <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Buscar por título...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
            <?php if ($tipo_filter || $categoria_filter || $status_filter || $busca): ?>
            <a href="<?= $bp ?>/admin/posts.php" class="btn-sm-secondary"><i class="fas fa-times"></i> Limpar filtros</a>
            <?php endif; ?>
        </form>

        <div class="admin-card">
            <div class="admin-card-body p-0">
                <?php if (!empty($posts)): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Capa</th>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Categoria</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $p): ?>
                            <tr>
                                <td class="td-thumb">
                                    <?php if (!empty($p['imagem_capa'])): ?>
                                    <img src="<?= e(post_image_url($p['imagem_capa'])) ?>" alt="" loading="lazy" class="post-thumb">
                                    <?php else: ?>
                                    <div class="post-thumb-placeholder"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td class="td-title">
                                    <a href="<?= $bp ?>/admin/post-edit.php?id=<?= $p['id'] ?>">
                                        <?= e(mb_substr($p['titulo'], 0, 60)) ?><?= mb_strlen($p['titulo']) > 60 ? '…' : '' ?>
                                    </a>
                                    <?php if ($p['destaque']): ?>
                                    <span class="badge-star"><i class="fas fa-star"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge badge-<?= $p['tipo'] ?>"><?= $p['tipo'] === 'artigo' ? 'Artigo' : 'Informativo' ?></span></td>
                                <td><?= !empty($p['categoria']) ? e($p['categoria']) : '<span style="color:#999">—</span>' ?></td>
                                <td><span class="status-badge status-<?= $p['status'] ?>"><?= $p['status'] === 'publicado' ? 'Publicado' : 'Rascunho' ?></span></td>
                                <td class="td-views"><?= number_format($p['views_count'], 0, ',', '.') ?></td>
                                <td class="td-date"><?= format_date($p['publicado_em'] ?? $p['criado_em']) ?></td>
                                <td class="td-actions">
                                    <a href="<?= $bp ?>/admin/post-edit.php?id=<?= $p['id'] ?>" class="table-action" title="Editar"><i class="fas fa-edit"></i></a>
                                    <?php if ($p['status'] === 'publicado'): ?>
                                    <a href="<?= e(post_permalink($p['slug'])) ?>" target="_blank" class="table-action" title="Ver no site"><i class="fas fa-external-link-alt"></i></a>
                                    <?php endif; ?>

                                    <!-- Toggle Status -->
                                    <form method="post" style="display:inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="<?= $p['status'] === 'publicado' ? 'rascunho' : 'publicar' ?>">
                                        <button type="submit" class="table-action" title="<?= $p['status'] === 'publicado' ? 'Mover para rascunho' : 'Publicar' ?>">
                                            <i class="fas fa-<?= $p['status'] === 'publicado' ? 'eye-slash' : 'eye' ?>"></i>
                                        </button>
                                    </form>

                                    <!-- Excluir -->
                                    <form method="post" style="display:inline" onsubmit="return confirm('Confirmar exclusão de \"<?= addslashes(mb_substr($p['titulo'],0,40)) ?>\"?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="excluir">
                                        <button type="submit" class="table-action table-action-danger" title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($pages > 1): ?>
                <div class="admin-pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>"
                       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <p><?= $busca ? 'Nenhum resultado para "' . e($busca) . '"' : 'Nenhum post encontrado.' ?></p>
                    <a href="<?= $bp ?>/admin/post-novo.php" class="btn-sm-primary">Criar novo post</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
