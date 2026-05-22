<?php
/**
 * Alberione Advogados — Admin: Configurações do Site
 * admin/configuracoes.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

$s      = get_site_settings();
$errors = [];
$saved  = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Requisição inválida. Tente novamente.';
    } else {
        // Campos permitidos para atualização
        $fields = [
            'office_name', 'hero_title', 'hero_subtitle', 'about_title', 'about_text',
            'email_contato', 'email_destino_form', 'telefone', 'whatsapp_numero', 'whatsapp_link',
            'endereco_linha1', 'endereco_linha2', 'cidade', 'estado', 'cep', 'horario_atendimento',
            'instagram_url', 'facebook_url', 'linkedin_url', 'youtube_url',
            'seo_home_title', 'seo_home_description',
            'smtp_host', 'smtp_port', 'smtp_user', 'smtp_from_name', 'smtp_from_email',
        ];

        // Campos de texto simples
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = clean($_POST[$field] ?? '');
        }

        // smtp_secure é enum
        $data['smtp_secure'] = in_array($_POST['smtp_secure'] ?? '', ['tls','ssl','none'])
            ? $_POST['smtp_secure']
            : 'tls';

        // smtp_pass — só atualizar se preenchido
        if (!empty($_POST['smtp_pass'])) {
            $data['smtp_pass'] = clean($_POST['smtp_pass']);
        }

        // Validações básicas
        if (strlen($data['office_name']) < 3) {
            $errors[] = 'O nome do escritório é obrigatório.';
        }
        if (!empty($data['email_contato']) && !filter_var($data['email_contato'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail de contato inválido.';
        }
        if (!empty($data['email_destino_form']) && !filter_var($data['email_destino_form'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail de destino do formulário inválido.';
        }

        if (empty($errors)) {
            // Verificar se já existe um registro
            $exists = db()->query("SELECT COUNT(*) FROM site_settings WHERE config_key='default'")->fetchColumn();

            if ($exists) {
                // Montar SET dinâmico
                $set    = [];
                $params = [];
                foreach ($data as $key => $val) {
                    $set[]    = "$key = ?";
                    $params[] = $val ?: null;
                }
                $params[] = 'default';
                db()->prepare("UPDATE site_settings SET " . implode(', ', $set) . " WHERE config_key = ?")->execute($params);
            } else {
                $data['config_key'] = 'default';
                $cols   = implode(', ', array_keys($data));
                $phs    = implode(', ', array_fill(0, count($data), '?'));
                db()->prepare("INSERT INTO site_settings ($cols) VALUES ($phs)")->execute(array_values($data));
            }

            // Limpar cache estático
            $s    = get_site_settings();
            $saved = true;
            flash('success', 'Configurações salvas com sucesso!');
            redirect('/admin/configuracoes.php');
        }
    }
}

// Tab ativa
$tab = in_array($_GET['tab'] ?? '', ['identidade','contato','endereco','redes','seo','smtp']) ? $_GET['tab'] : 'identidade';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações | Admin</title>
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
                <h2>Configurações do Site</h2>
                <p>Gerencie as informações institucionais e técnicas do site</p>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Tabs de navegação -->
        <div class="config-tabs">
            <a href="?tab=identidade" class="config-tab <?= $tab === 'identidade' ? 'active' : '' ?>">
                <i class="fas fa-building"></i> Identidade
            </a>
            <a href="?tab=contato" class="config-tab <?= $tab === 'contato' ? 'active' : '' ?>">
                <i class="fas fa-phone"></i> Contato
            </a>
            <a href="?tab=endereco" class="config-tab <?= $tab === 'endereco' ? 'active' : '' ?>">
                <i class="fas fa-map-marker-alt"></i> Endereço
            </a>
            <a href="?tab=redes" class="config-tab <?= $tab === 'redes' ? 'active' : '' ?>">
                <i class="fas fa-share-alt"></i> Redes Sociais
            </a>
            <a href="?tab=seo" class="config-tab <?= $tab === 'seo' ? 'active' : '' ?>">
                <i class="fas fa-search"></i> SEO
            </a>
            <a href="?tab=smtp" class="config-tab <?= $tab === 'smtp' ? 'active' : '' ?>">
                <i class="fas fa-envelope-open"></i> E-mail (SMTP)
            </a>
        </div>

        <form method="post" id="configForm">
            <?= csrf_field() ?>
            <input type="hidden" name="tab_active" value="<?= e($tab) ?>">

            <!-- ── TAB: IDENTIDADE ─────────────────────────────── -->
            <div class="config-panel <?= $tab === 'identidade' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-building"></i> Identidade Institucional</h3></div>
                    <div class="admin-card-body">
                        <div class="form-grid-2">
                            <div class="form-group form-group--full">
                                <label>Nome do Escritório <span class="req">*</span></label>
                                <input type="text" name="office_name" value="<?= e($s['office_name'] ?? OFFICE_NAME) ?>" required maxlength="150">
                            </div>
                            <div class="form-group form-group--full">
                                <label>Título Principal (Hero)</label>
                                <input type="text" name="hero_title" value="<?= e($s['hero_title'] ?? '') ?>" maxlength="255"
                                       placeholder="Ex: Soluções Jurídicas Estratégicas em Direito Tributário">
                            </div>
                            <div class="form-group form-group--full">
                                <label>Subtítulo do Hero</label>
                                <textarea name="hero_subtitle" rows="3" maxlength="400"
                                          placeholder="Frase de impacto exibida abaixo do título principal..."><?= e($s['hero_subtitle'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Título da Seção "Sobre"</label>
                                <input type="text" name="about_title" value="<?= e($s['about_title'] ?? '') ?>" maxlength="255"
                                       placeholder="Ex: Sobre o Escritório">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Texto do "Sobre o Escritório"</label>
                            <textarea name="about_text" rows="8"
                                      placeholder="Texto institucional exibido na seção Sobre. Use quebras de linha para separar parágrafos."><?= e($s['about_text'] ?? '') ?></textarea>
                            <small>Separe os parágrafos com uma linha em branco.</small>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar Identidade</button>
                </div>
            </div>

            <!-- ── TAB: CONTATO ─────────────────────────────────── -->
            <div class="config-panel <?= $tab === 'contato' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-phone"></i> Dados de Contato</h3></div>
                    <div class="admin-card-body">
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>E-mail de Contato (exibido no site)</label>
                                <input type="email" name="email_contato" value="<?= e($s['email_contato'] ?? '') ?>"
                                       placeholder="contato@alberione.com.br" maxlength="150">
                            </div>
                            <div class="form-group">
                                <label>E-mail Destino do Formulário</label>
                                <input type="email" name="email_destino_form" value="<?= e($s['email_destino_form'] ?? '') ?>"
                                       placeholder="Para onde as mensagens do formulário serão enviadas" maxlength="150">
                            </div>
                            <div class="form-group">
                                <label>Telefone</label>
                                <input type="text" name="telefone" value="<?= e($s['telefone'] ?? '') ?>"
                                       placeholder="(11) 3000-0000" maxlength="30">
                            </div>
                            <div class="form-group">
                                <label>Número do WhatsApp (só dígitos, com DDD e DDI)</label>
                                <input type="text" name="whatsapp_numero" value="<?= e($s['whatsapp_numero'] ?? '') ?>"
                                       placeholder="5511999999999" maxlength="30">
                                <small>Exemplo: 5511999999999 (55 + DDD + número)</small>
                            </div>
                            <div class="form-group form-group--full">
                                <label>Link completo do WhatsApp</label>
                                <input type="url" name="whatsapp_link" value="<?= e($s['whatsapp_link'] ?? '') ?>"
                                       placeholder="https://wa.me/5511999999999" maxlength="255">
                                <small>Gerado automaticamente por: https://wa.me/[numero]</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar Contato</button>
                </div>
            </div>

            <!-- ── TAB: ENDEREÇO ────────────────────────────────── -->
            <div class="config-panel <?= $tab === 'endereco' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-map-marker-alt"></i> Endereço</h3></div>
                    <div class="admin-card-body">
                        <div class="form-grid-2">
                            <div class="form-group form-group--full">
                                <label>Endereço Linha 1</label>
                                <input type="text" name="endereco_linha1" value="<?= e($s['endereco_linha1'] ?? '') ?>"
                                       placeholder="Rua, Avenida, número, complemento..." maxlength="255">
                            </div>
                            <div class="form-group form-group--full">
                                <label>Endereço Linha 2</label>
                                <input type="text" name="endereco_linha2" value="<?= e($s['endereco_linha2'] ?? '') ?>"
                                       placeholder="Bairro, andar, sala..." maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Cidade</label>
                                <input type="text" name="cidade" value="<?= e($s['cidade'] ?? '') ?>" maxlength="120" placeholder="São Paulo">
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <input type="text" name="estado" value="<?= e($s['estado'] ?? '') ?>" maxlength="60" placeholder="SP">
                            </div>
                            <div class="form-group">
                                <label>CEP</label>
                                <input type="text" name="cep" value="<?= e($s['cep'] ?? '') ?>" maxlength="20" placeholder="01310-000">
                            </div>
                            <div class="form-group">
                                <label>Horário de Atendimento</label>
                                <input type="text" name="horario_atendimento" value="<?= e($s['horario_atendimento'] ?? '') ?>"
                                       maxlength="120" placeholder="Segunda a Sexta, das 9h às 18h">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar Endereço</button>
                </div>
            </div>

            <!-- ── TAB: REDES SOCIAIS ───────────────────────────── -->
            <div class="config-panel <?= $tab === 'redes' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-share-alt"></i> Redes Sociais</h3></div>
                    <div class="admin-card-body">
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label><i class="fab fa-instagram" style="color:#e1306c"></i> Instagram</label>
                                <input type="url" name="instagram_url" value="<?= e($s['instagram_url'] ?? '') ?>"
                                       placeholder="https://instagram.com/alberioneavv" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label><i class="fab fa-linkedin-in" style="color:#0a66c2"></i> LinkedIn</label>
                                <input type="url" name="linkedin_url" value="<?= e($s['linkedin_url'] ?? '') ?>"
                                       placeholder="https://linkedin.com/in/alberioneavv" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label><i class="fab fa-facebook-f" style="color:#1877f2"></i> Facebook</label>
                                <input type="url" name="facebook_url" value="<?= e($s['facebook_url'] ?? '') ?>"
                                       placeholder="https://facebook.com/alberioneavv" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label><i class="fab fa-youtube" style="color:#ff0000"></i> YouTube</label>
                                <input type="url" name="youtube_url" value="<?= e($s['youtube_url'] ?? '') ?>"
                                       placeholder="https://youtube.com/@alberioneavv" maxlength="255">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar Redes Sociais</button>
                </div>
            </div>

            <!-- ── TAB: SEO ─────────────────────────────────────── -->
            <div class="config-panel <?= $tab === 'seo' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-search"></i> SEO da Home</h3></div>
                    <div class="admin-card-body">
                        <div class="form-group">
                            <label>Meta Title da Home</label>
                            <input type="text" name="seo_home_title" value="<?= e($s['seo_home_title'] ?? '') ?>"
                                   placeholder="Alberione Advogados | Direito Tributário em São Paulo" maxlength="255">
                            <small>Recomendado: até 70 caracteres.</small>
                        </div>
                        <div class="form-group">
                            <label>Meta Description da Home</label>
                            <textarea name="seo_home_description" rows="3" maxlength="320"
                                      placeholder="Escritório especializado em Direito Tributário, Societário e Empresarial..."><?= e($s['seo_home_description'] ?? '') ?></textarea>
                            <small>Recomendado: entre 120 e 160 caracteres.</small>
                        </div>
                        <div class="seo-preview">
                            <p class="seo-preview-label">Pré-visualização no Google:</p>
                            <div class="seo-preview-box">
                                <div class="seo-title" id="seoTitlePreview"><?= e($s['seo_home_title'] ?? OFFICE_NAME) ?></div>
                                <div class="seo-url">www.alberione.com.br</div>
                                <div class="seo-desc" id="seoDescPreview"><?= e($s['seo_home_description'] ?? '') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar SEO</button>
                </div>
            </div>

            <!-- ── TAB: SMTP ─────────────────────────────────────── -->
            <div class="config-panel <?= $tab === 'smtp' ? 'active' : '' ?>">
                <div class="admin-card">
                    <div class="admin-card-header"><h3><i class="fas fa-envelope-open"></i> Configurações de E-mail (SMTP)</h3></div>
                    <div class="admin-card-body">
                        <div class="alert alert-info" style="margin-bottom:20px">
                            <i class="fas fa-info-circle"></i>
                            Configure seu servidor SMTP para envio de e-mails pelo site. Sem configuração, os e-mails usarão a função <code>mail()</code> nativa do PHP.
                        </div>
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label>SMTP Host</label>
                                <input type="text" name="smtp_host" value="<?= e($s['smtp_host'] ?? '') ?>"
                                       placeholder="smtp.gmail.com" maxlength="150">
                            </div>
                            <div class="form-group">
                                <label>SMTP Porta</label>
                                <input type="number" name="smtp_port" value="<?= e($s['smtp_port'] ?? '587') ?>"
                                       placeholder="587" min="1" max="65535">
                            </div>
                            <div class="form-group">
                                <label>Usuário SMTP</label>
                                <input type="text" name="smtp_user" value="<?= e($s['smtp_user'] ?? '') ?>"
                                       placeholder="seu@email.com" maxlength="150">
                            </div>
                            <div class="form-group">
                                <label>Senha SMTP</label>
                                <input type="password" name="smtp_pass" placeholder="Deixe em branco para manter a atual">
                                <small>A senha atual não é exibida por segurança.</small>
                            </div>
                            <div class="form-group">
                                <label>Segurança</label>
                                <select name="smtp_secure">
                                    <option value="tls" <?= ($s['smtp_secure'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (recomendado)</option>
                                    <option value="ssl" <?= ($s['smtp_secure'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="none"<?= ($s['smtp_secure'] ?? '') === 'none'? 'selected' : '' ?>>Nenhuma</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nome do Remetente</label>
                                <input type="text" name="smtp_from_name" value="<?= e($s['smtp_from_name'] ?? OFFICE_NAME) ?>"
                                       placeholder="Alberione Advogados" maxlength="150">
                            </div>
                            <div class="form-group form-group--full">
                                <label>E-mail do Remetente</label>
                                <input type="email" name="smtp_from_email" value="<?= e($s['smtp_from_email'] ?? '') ?>"
                                       placeholder="noreply@alberione.com.br" maxlength="150">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="config-save-bar">
                    <button type="submit" class="btn-admin-primary"><i class="fas fa-save"></i> Salvar SMTP</button>
                </div>
            </div>

        </form>
    </main>
</div>

<script src="../assets/js/admin.js"></script>
<script>
// Preview SEO em tempo real
const titleInput = document.querySelector('[name="seo_home_title"]');
const descInput  = document.querySelector('[name="seo_home_description"]');
const titlePrev  = document.getElementById('seoTitlePreview');
const descPrev   = document.getElementById('seoDescPreview');

titleInput?.addEventListener('input', () => {
    if (titlePrev) titlePrev.textContent = titleInput.value || 'Alberione Advogados';
});
descInput?.addEventListener('input', () => {
    if (descPrev) descPrev.textContent = descInput.value;
});
</script>
</body>
</html>
