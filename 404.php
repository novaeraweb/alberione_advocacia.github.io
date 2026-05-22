<?php
/**
 * Alberione Advogados — Página 404
 * 404.php
 */
$autoload = __DIR__ . '/config/config.php';
if (file_exists($autoload)) {
    require_once $autoload;
    $office_name  = e(setting('office_name',  OFFICE_NAME));
    $whatsapp_lnk = setting('whatsapp_link',  OFFICE_WHATSAPP_LINK);
} else {
    $office_name  = 'Alberione Advogados';
    $whatsapp_lnk = '';
}

http_response_code(404);
$bp = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada | <?= $office_name ?></title>
    <meta name="robots" content="noindex">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy:    #1F4E79;
            --navy-dk: #163856;
            --gold:    #B08A57;
            --ivory:   #F5F0E8;
            --gray:    #6B7280;
            --charcoal:#2B2B2B;
            --white:   #FFFFFF;
        }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, var(--navy-dk) 0%, var(--navy) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
        }
        .error-card {
            background: var(--white);
            border-radius: 16px;
            padding: 56px 48px;
            max-width: 560px;
            width: 100%;
            box-shadow: 0 24px 80px rgba(0,0,0,.3);
        }
        .error-icon {
            font-size: 4rem;
            color: var(--gold);
            margin-bottom: 20px;
        }
        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            font-weight: 700;
            color: var(--navy-dk);
            line-height: 1;
            margin-bottom: 8px;
        }
        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 12px;
        }
        .error-desc {
            font-size: .95rem;
            color: var(--gray);
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .error-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--navy);
            color: var(--white);
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: .2s ease;
        }
        .btn-primary:hover { background: var(--navy-dk); transform: translateY(-2px); }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: .2s ease;
        }
        .btn-outline:hover { background: var(--navy); color: var(--white); }
        .brand {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #F3F4F6;
            font-size: .82rem;
            color: var(--gray);
        }
        .brand strong { color: var(--charcoal); }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon"><i class="fas fa-balance-scale"></i></div>
        <div class="error-code">404</div>
        <h1 class="error-title">Página não encontrada</h1>
        <p class="error-desc">
            A página que você está procurando não existe ou foi movida.<br>
            Verifique o endereço digitado ou utilize a navegação abaixo.
        </p>
        <div class="error-actions">
            <a href="<?= $bp ?>/" class="btn-primary">
                <i class="fas fa-home"></i> Voltar ao início
            </a>
            <a href="<?= $bp ?>/blog" class="btn-outline">
                <i class="fas fa-book-open"></i> Ver Blog
            </a>
        </div>
        <?php if (!empty($whatsapp_lnk)): ?>
        <div class="error-actions" style="margin-top:12px">
            <a href="<?= htmlspecialchars($whatsapp_lnk, ENT_QUOTES) ?>" target="_blank" rel="noopener"
               style="font-size:.85rem;color:#25D366;display:inline-flex;align-items:center;gap:6px;font-weight:600">
                <i class="fab fa-whatsapp"></i> Fale conosco pelo WhatsApp
            </a>
        </div>
        <?php endif; ?>
        <div class="brand">
            <strong><?= htmlspecialchars($office_name, ENT_QUOTES) ?></strong><br>
            Direito Tributário &amp; Empresarial
        </div>
    </div>
</body>
</html>
