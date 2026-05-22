<?php
/**
 * Alberione Advogados — Admin: Mensagens
 * admin/mensagens.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

// ── Atualizar status ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? '')) {
    $id     = (int)($_POST['id'] ?? 0);
    $action = clean($_POST['action'] ?? '');
    $status_map = ['lido' => 'lido', 'respondido' => 'respondido', 'arquivado' => 'arquivado'];

    if ($id > 0 && isset($status_map[$action])) {
        $upd = "UPDATE contact_messages SET status=?";
        $params = [$status_map[$action]];
        if ($action === 'respondido') { $upd .= ', respondido_em=NOW()'; }
        $upd .= ' WHERE id=?';
        $params[] = $id;
        db()->prepare($upd)->execute($params);
        flash('success', 'Status atualizado com sucesso.');
    }
    redirect('/admin/mensagens.php' . ($id ? '?id=' . $id : ''));
}

// ── Listar mensagens ──────────────────────────────────────────────
$status_filter = in_array($_GET['status'] ?? '', ['novo','lido','respondido','arquivado']) ? $_GET['status'] : '';
$page   = max(1, (int)($_GET['p'] ?? 1));
$limit  = ADMIN_PER_PAGE;
$offset = ($page - 1) * $limit;

$where  = [];
$params = [];
if ($status_filter) {
    $where[]  = 'status = ?';
    $params[] = $status_filter;
}
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$total  = (int)db()->query("SELECT COUNT(*) FROM contact_messages $whereSQL")->fetchColumn();
$pages  = max(1, (int)ceil($total / $limit));

$stmt = db()->prepare("SELECT * FROM contact_messages $whereSQL ORDER BY criado_em DESC LIMIT ? OFFSET ?");
$stmt->execute(array_merge($params, [$limit, $offset]));
$mensagens = $stmt->fetchAll();

// Mensagem selecionada
$selected_id  = (int)($_GET['id'] ?? 0);
$selected_msg = null;
if ($selected_id) {
    $s = db()->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $s->execute([$selected_id]);
    $selected_msg = $s->fetch();
    // Marcar como lida automaticamente se for nova
    if ($selected_msg && $selected_msg['status'] === 'novo') {
        db()->prepare("UPDATE contact_messages SET status='lido' WHERE id=?")->execute([$selected_id]);
        $selected_msg['status'] = 'lido';
    }
}

// Contadores
$counts = [];
foreach (['novo','lido','respondido','arquivado'] as $st) {
    $counts[$st] = (int)db()->query("SELECT COUNT(*) FROM contact_messages WHERE status='$st'")->fetchColumn();
}

$s = get_site_settings();
$whatsapp_lnk = setting('whatsapp_link', OFFICE_WHATSAPP_LINK);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens | Admin</title>
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
                <h2>Mensagens de Contato</h2>
                <p><?= $total ?> mensagem<?= $total !== 1 ? 'ns' : '' ?> no total</p>
            </div>
        </div>

        <!-- Counters -->
        <div class="msg-counters">
            <a href="<?= $bp ?>/admin/mensagens.php" class="msg-counter <?= !$status_filter ? 'active' : '' ?>">
                <span class="msg-counter-num"><?= array_sum($counts) ?></span>
                <span>Todas</span>
            </a>
            <a href="?status=novo" class="msg-counter <?= $status_filter === 'novo' ? 'active' : '' ?>">
                <span class="msg-counter-num"><?= $counts['novo'] ?></span>
                <span>Novas</span>
            </a>
            <a href="?status=lido" class="msg-counter <?= $status_filter === 'lido' ? 'active' : '' ?>">
                <span class="msg-counter-num"><?= $counts['lido'] ?></span>
                <span>Lidas</span>
            </a>
            <a href="?status=respondido" class="msg-counter <?= $status_filter === 'respondido' ? 'active' : '' ?>">
                <span class="msg-counter-num"><?= $counts['respondido'] ?></span>
                <span>Respondidas</span>
            </a>
            <a href="?status=arquivado" class="msg-counter <?= $status_filter === 'arquivado' ? 'active' : '' ?>">
                <span class="msg-counter-num"><?= $counts['arquivado'] ?></span>
                <span>Arquivadas</span>
            </a>
        </div>

        <div class="msg-layout">
            <!-- Lista -->
            <div class="msg-list-panel">
                <?php if (!empty($mensagens)): ?>
                <ul class="msg-full-list">
                    <?php foreach ($mensagens as $m): ?>
                    <li class="msg-full-item <?= $m['status'] === 'novo' ? 'msg-new' : '' ?> <?= ($selected_id === (int)$m['id']) ? 'msg-selected' : '' ?>">
                        <a href="?id=<?= $m['id'] ?><?= $status_filter ? '&status=' . $status_filter : '' ?>" class="msg-full-link">
                            <div class="msg-full-avatar"><i class="fas fa-user-circle"></i></div>
                            <div class="msg-full-info">
                                <div class="msg-full-name">
                                    <?= e($m['nome']) ?>
                                    <?php if ($m['status'] === 'novo'): ?><span class="dot-new"></span><?php endif; ?>
                                </div>
                                <div class="msg-full-sub"><?= !empty($m['assunto']) ? e(mb_substr($m['assunto'],0,40)) : 'Sem assunto' ?></div>
                                <div class="msg-full-date"><?= format_datetime($m['criado_em']) ?></div>
                            </div>
                            <span class="msg-full-status status-msg-<?= $m['status'] ?>">
                                <?= ['novo'=>'Nova','lido'=>'Lida','respondido'=>'Respondida','arquivado'=>'Arquivada'][$m['status']] ?? $m['status'] ?>
                            </span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($pages > 1): ?>
                <div class="admin-pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <a href="?<?= http_build_query(array_filter(['p'=>$i,'status'=>$status_filter])) ?>"
                       class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state"><i class="fas fa-inbox"></i><p>Nenhuma mensagem encontrada.</p></div>
                <?php endif; ?>
            </div>

            <!-- Detalhe -->
            <div class="msg-detail-panel">
                <?php if ($selected_msg): ?>
                <div class="msg-detail">
                    <div class="msg-detail-header">
                        <div class="msg-detail-from">
                            <div class="msg-detail-avatar"><i class="fas fa-user-circle"></i></div>
                            <div>
                                <strong><?= e($selected_msg['nome']) ?></strong>
                                <span><?= e($selected_msg['email']) ?></span>
                                <?php if (!empty($selected_msg['telefone'])): ?>
                                <span><?= e($selected_msg['telefone']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="msg-detail-meta">
                            <time><?= format_datetime($selected_msg['criado_em']) ?></time>
                            <span class="status-msg-<?= $selected_msg['status'] ?>">
                                <?= ['novo'=>'Nova','lido'=>'Lida','respondido'=>'Respondida','arquivado'=>'Arquivada'][$selected_msg['status']] ?? '' ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($selected_msg['assunto'])): ?>
                    <div class="msg-detail-subject">
                        <strong>Assunto:</strong> <?= e($selected_msg['assunto']) ?>
                    </div>
                    <?php endif; ?>

                    <div class="msg-detail-body">
                        <?= nl2br(e($selected_msg['mensagem'])) ?>
                    </div>

                    <div class="msg-detail-actions">
                        <!-- Responder E-mail -->
                        <a href="mailto:<?= e($selected_msg['email']) ?>?subject=Re: <?= rawurlencode($selected_msg['assunto'] ?? 'Seu contato') ?>" class="btn-admin-primary">
                            <i class="fas fa-reply"></i> Responder por E-mail
                        </a>

                        <!-- WhatsApp (se tiver telefone) -->
                        <?php if (!empty($selected_msg['telefone'])): ?>
                        <?php $wa_num = preg_replace('/\D/', '', $selected_msg['telefone']); ?>
                        <?php if (strlen($wa_num) >= 10): ?>
                        <a href="https://wa.me/55<?= $wa_num ?>?text=<?= rawurlencode('Olá ' . $selected_msg['nome'] . ', tudo bem? Vi sua mensagem enviada pelo site. Podemos conversar?') ?>"
                           target="_blank" rel="noopener" class="btn-wa">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <?php endif; ?>
                        <?php endif; ?>

                        <!-- Ações de status -->
                        <form method="post" style="display:inline-flex;gap:8px">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= $selected_msg['id'] ?>">
                            <?php if ($selected_msg['status'] !== 'respondido'): ?>
                            <button type="submit" name="action" value="respondido" class="btn-sm-secondary">
                                <i class="fas fa-check-double"></i> Marcar respondido
                            </button>
                            <?php endif; ?>
                            <?php if ($selected_msg['status'] !== 'arquivado'): ?>
                            <button type="submit" name="action" value="arquivado" class="btn-sm-secondary"
                                    onclick="return confirm('Arquivar esta mensagem?')">
                                <i class="fas fa-archive"></i> Arquivar
                            </button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="empty-state msg-detail-empty">
                    <i class="fas fa-envelope-open-text"></i>
                    <p>Selecione uma mensagem para visualizar</p>
                </div>
                <?php endif; ?>
            </div>
        </div><!-- /.msg-layout -->

    </main>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
