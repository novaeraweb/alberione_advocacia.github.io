<?php
/**
 * Sidebar do painel
 * admin/partials/sidebar.php
 */
$current  = basename($_SERVER['PHP_SELF']);
$new_messages = count_new_messages();
$bp = rtrim(SITE_URL, '/'); // ex: http://localhost:8888/AlberioneAdvocacia ou https://www.alberione.com.br
?>
<nav class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo"><i class="fas fa-balance-scale"></i></div>
        <div class="sidebar-title">
            <span><?= e(setting('office_name', OFFICE_NAME)) ?></span>
            <small>Painel Administrativo</small>
        </div>
        <button class="sidebar-close" id="sidebarClose" aria-label="Fechar menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <ul class="sidebar-nav">
        <li class="nav-section">Conteúdo</li>
        <li>
            <a href="<?= $bp ?>/admin/" class="<?= $current === 'index.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= $bp ?>/admin/posts.php" class="<?= $current === 'posts.php' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> Gerenciar Posts
            </a>
        </li>
        <li>
            <a href="<?= $bp ?>/admin/post-novo.php" class="<?= $current === 'post-novo.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Novo Post
            </a>
        </li>

        <li class="nav-section">Atendimento</li>
        <li>
            <a href="<?= $bp ?>/admin/mensagens.php" class="<?= $current === 'mensagens.php' ? 'active' : '' ?>">
                <i class="fas fa-inbox"></i> Mensagens
                <?php if ($new_messages > 0): ?>
                <span class="nav-badge"><?= $new_messages ?></span>
                <?php endif; ?>
            </a>
        </li>

        <li class="nav-section">Sistema</li>
        <li>
            <a href="<?= $bp ?>/admin/configuracoes.php" class="<?= $current === 'configuracoes.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Configurações
            </a>
        </li>
        <li>
            <a href="<?= $bp ?>/" target="_blank">
                <i class="fas fa-external-link-alt"></i> Ver Site
            </a>
        </li>
        <li>
            <a href="<?= $bp ?>/admin/logout.php" class="nav-danger">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </li>
    </ul>

    <div class="sidebar-user">
        <div class="sidebar-user-avatar"><i class="fas fa-user-circle"></i></div>
        <div class="sidebar-user-info">
            <span><?= e($_SESSION['admin_nome'] ?? 'Admin') ?></span>
            <small><?= e($_SESSION['admin_email'] ?? '') ?></small>
        </div>
    </div>
</nav>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
