<?php
/**
 * Alberione Advogados — Admin Login
 * admin/login.php
 */
if (!defined('ROOT_PATH')) require_once __DIR__ . '/../config/config.php';

if (is_logged_in()) {
    redirect('/admin/');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $error = 'Informe e-mail e senha.';
    } else {
        try {
            $stmt = db()->prepare("SELECT id, nome, senha_hash, ativo FROM admins WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && $admin['ativo'] && password_verify($senha, $admin['senha_hash'])) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_nome'] = $admin['nome'];
                $_SESSION['admin_email']= $email;

                db()->prepare("UPDATE admins SET ultimo_login_em = NOW() WHERE id = ?")->execute([$admin['id']]);
                log_auth('login_sucesso', $admin['id'], $email);

                redirect('/admin/');
            } else {
                $error = 'Credenciais inválidas.';
                log_auth('login_falha', null, $email);
            }
        } catch (Exception $e) {
            $error = 'Erro interno. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Administrativo | <?= OFFICE_NAME ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-container">
    <div class="login-card">
        <div class="login-brand">
            <div class="login-logo"><i class="fas fa-balance-scale"></i></div>
            <h1><?= OFFICE_NAME ?></h1>
            <p>Painel Administrativo</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="login-form" novalidate>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>"
                       placeholder="admin@alberione.com.br" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha"><i class="fas fa-lock"></i> Senha</label>
                <div class="input-password">
                    <input type="password" id="senha" name="senha" placeholder="Sua senha" required>
                    <button type="button" class="toggle-pass" aria-label="Mostrar senha">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>

        <div class="login-footer">
        <a href="<?= rtrim(SITE_URL, '/') ?>/"><i class="fas fa-arrow-left"></i> Voltar ao site</a>
        </div>
    </div>
</div>
<script>
document.querySelector('.toggle-pass').addEventListener('click', function () {
    const input = document.getElementById('senha');
    const icon  = document.getElementById('eyeIcon');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
});
</script>
</body>
</html>
