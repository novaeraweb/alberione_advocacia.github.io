<?php
/**
 * Topbar do painel
 * admin/partials/topbar.php
 */
$flash        = get_flash();
$new_messages = count_new_messages();
$bp           = rtrim(SITE_URL, '/');
?>
<header class="admin-topbar">
    <div class="topbar-left">
        <button class="topbar-menu-toggle" id="menuToggleAdmin" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-title" id="pageTitle"></span>
    </div>
    <div class="topbar-right">
        <?php if ($new_messages > 0): ?>
        <a href="<?= $bp ?>/admin/mensagens.php" class="topbar-icon-btn" title="<?= $new_messages ?> mensagem(ns) nova(s)">
            <i class="fas fa-envelope"></i>
            <span class="topbar-badge"><?= $new_messages ?></span>
        </a>
        <?php endif; ?>
        <a href="<?= $bp ?>/" target="_blank" class="topbar-icon-btn" title="Ver site">
            <i class="fas fa-external-link-alt"></i>
        </a>
        <a href="<?= $bp ?>/admin/logout.php" class="topbar-icon-btn topbar-logout" title="Sair">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</header>

<?php if ($flash): ?>
<div class="alert alert-<?= e($flash['type']) ?> alert-topbar" role="alert">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'warning' ? 'exclamation-triangle' : 'exclamation-circle') ?>"></i>
    <?= e($flash['message']) ?>
    <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Fechar"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>
