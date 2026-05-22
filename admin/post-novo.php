<?php
/**
 * Alberione Advogados — Admin: Novo Post
 * admin/post-novo.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

$admin  = get_current_admin();
$errors = [];
$tipo_default = in_array($_GET['tipo'] ?? '', ['artigo','informativo']) ? $_GET['tipo'] : 'artigo';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Requisição inválida.';
    } else {
        $titulo   = clean($_POST['titulo']   ?? '');
        $slug     = clean($_POST['slug']     ?? '');
        $tipo     = in_array($_POST['tipo']  ?? '', ['artigo','informativo']) ? $_POST['tipo'] : 'artigo';
        $categoria = clean(substr($_POST['categoria'] ?? '', 0, 100));
        $status   = in_array($_POST['status']?? '', ['rascunho','publicado']) ? $_POST['status'] : 'rascunho';
        $destaque = isset($_POST['destaque']) ? 1 : 0;
        $resumo   = clean($_POST['resumo']   ?? '');
        $conteudo = sanitize_post_html($_POST['conteudo'] ?? ''); // HTML do editor sanitizado antes de salvar
        $meta_title= clean($_POST['meta_title']   ?? '');
        $meta_desc = clean($_POST['meta_description'] ?? '');

        // Validação
        if (strlen($titulo) < 5)   $errors[] = 'O título deve ter ao menos 5 caracteres.';
        if (mb_strlen(trim(strip_tags($conteudo))) < 20) $errors[] = 'O conteúdo é muito curto.';

        if (empty($errors)) {
            // Slug
            if (empty($slug)) $slug = generate_unique_slug($titulo);
            else $slug = generate_unique_slug(slugify($slug));

            // Upload de imagem
            $imagem_capa = null;
            $imagem_alt  = clean($_POST['imagem_alt'] ?? '');
            if (!empty($_FILES['imagem_capa']['name'])) {
                $path = upload_image($_FILES['imagem_capa'], 'posts');
                if ($path) {
                    $imagem_capa = $path;
                } else {
                    $errors[] = 'Erro no upload da imagem. Verifique o tipo e tamanho (máx 5MB).';
                }
            }

            if (empty($errors)) {
                $publicado_em = ($status === 'publicado') ? date('Y-m-d H:i:s') : null;

                db()->prepare(
                    "INSERT INTO posts (autor_id, tipo, categoria, titulo, slug, resumo, conteudo, imagem_capa, imagem_alt, status, destaque, meta_title, meta_description, publicado_em)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                )->execute([
                    $admin['id'], $tipo, $categoria ?: null, $titulo, $slug, $resumo, $conteudo,
                    $imagem_capa, $imagem_alt, $status, $destaque,
                    $meta_title, $meta_desc, $publicado_em
                ]);

                flash('success', $status === 'publicado' ? 'Post publicado com sucesso!' : 'Rascunho salvo com sucesso!');
                redirect('/admin/posts.php');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Post | Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
    <!-- Quill Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
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
                <h2>Novo Post</h2>
                <p>Crie um novo artigo ou informativo</p>
            </div>
            <a href="<?= $bp ?>/admin/posts.php" class="btn-sm-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="postForm" novalidate>
            <?= csrf_field() ?>

            <div class="post-editor-layout">
                <!-- Principal -->
                <div class="post-editor-main">

                    <!-- Título -->
                    <div class="admin-card">
                        <div class="admin-card-body">
                            <div class="form-group">
                                <label for="titulo">Título <span class="req">*</span></label>
                                <input type="text" id="titulo" name="titulo" value="<?= e($_POST['titulo'] ?? '') ?>"
                                       placeholder="Título do artigo ou informativo..." required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label for="slug">Slug (URL amigável)</label>
                                <div class="input-slug">
                                    <span class="slug-prefix"><?= (defined('USE_FRIENDLY_URLS') && USE_FRIENDLY_URLS) ? '/artigo/' : '/artigo.php?slug=' ?></span>
                                    <input type="text" id="slug" name="slug" value="<?= e($_POST['slug'] ?? '') ?>"
                                           placeholder="gerado-automaticamente" maxlength="255">
                                </div>
                                <small>Deixe em branco para gerar automaticamente.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Resumo -->
                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Resumo</h3></div>
                        <div class="admin-card-body">
                            <div class="form-group">
                                <textarea id="resumo" name="resumo" rows="3" maxlength="320"
                                          placeholder="Breve descrição para listagem e SEO (até 320 caracteres)..."><?= e($_POST['resumo'] ?? '') ?></textarea>
                                <small class="char-count" data-max="320" data-field="resumo">0/320</small>
                            </div>
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Conteúdo <span class="req">*</span></h3></div>
                        <div class="admin-card-body p-0">
                            <div id="quillEditor" style="min-height:400px"><?= sanitize_post_html($_POST['conteudo'] ?? '') ?></div>
                            <input type="hidden" name="conteudo" id="conteudoInput">
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="admin-card">
                        <div class="admin-card-header"><h3>SEO</h3></div>
                        <div class="admin-card-body">
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" value="<?= e($_POST['meta_title'] ?? '') ?>"
                                       placeholder="Título para mecanismos de busca (máx 70 chars)" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Meta Description</label>
                                <textarea name="meta_description" rows="2" maxlength="320"
                                          placeholder="Descrição para mecanismos de busca (máx 160 chars)..."><?= e($_POST['meta_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar do editor -->
                <div class="post-editor-sidebar">

                    <!-- Publicar -->
                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Publicar</h3></div>
                        <div class="admin-card-body">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select name="tipo" id="tipoSelect">
                                    <option value="artigo"      <?= ($tipo_default === 'artigo') ? 'selected' : '' ?>>Artigo</option>
                                    <option value="informativo" <?= ($tipo_default === 'informativo') ? 'selected' : '' ?>>Informativo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Categoria</label>
                                <input type="text" name="categoria" value="<?= e($_POST['categoria'] ?? '') ?>"
                                       placeholder="Ex.: Direito Tributário" maxlength="100">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="rascunho"  <?= ($_POST['status'] ?? 'rascunho') === 'rascunho'  ? 'selected' : '' ?>>Rascunho</option>
                                    <option value="publicado" <?= ($_POST['status'] ?? '')          === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                                </select>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="destaque" name="destaque" value="1" <?= !empty($_POST['destaque']) ? 'checked' : '' ?>>
                                <label for="destaque"><i class="fas fa-star"></i> Destaque na home</label>
                            </div>
                            <div class="publish-actions">
                                <button type="submit" name="status" value="rascunho" class="btn-sm-secondary w-full">
                                    <i class="fas fa-save"></i> Salvar Rascunho
                                </button>
                                <button type="submit" name="status" value="publicado" class="btn-admin-primary w-full">
                                    <i class="fas fa-globe"></i> Publicar Agora
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Imagem Capa -->
                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Imagem Destaque</h3></div>
                        <div class="admin-card-body">
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="imagem_capa" name="imagem_capa" accept="image/*" class="upload-input">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Clique ou arraste a imagem</p>
                                    <small>JPG, PNG, WebP — Máx 5MB. Se não enviar, será usada a imagem editorial padrão.</small>
                                </div>
                                <div class="upload-preview" id="uploadPreview" style="display:none">
                                    <img id="previewImg" src="" alt="Pré-visualização">
                                    <button type="button" class="upload-remove" id="uploadRemove"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:12px">
                                <label>Alt da imagem</label>
                                <input type="text" name="imagem_alt" value="<?= e($_POST['imagem_alt'] ?? '') ?>"
                                       placeholder="Descrição da imagem para acessibilidade">
                            </div>
                        </div>
                    </div>

                </div>
            </div><!-- /.post-editor-layout -->
        </form>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="../assets/js/admin.js"></script>
<script>
// Init Quill
const quill = new Quill('#quillEditor', {
    theme: 'snow',
    placeholder: 'Escreva o conteúdo do post aqui...',
    modules: {
        toolbar: [
            [{ header: [2, 3, false] }],
            ['bold','italic','underline','strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['blockquote','link','image'],
            [{ align: [] }],
            ['clean']
        ]
    }
});

// Slug automático
const tituloInput = document.getElementById('titulo');
const slugInput   = document.getElementById('slug');
tituloInput?.addEventListener('input', function () {
    if (!slugInput.dataset.manual) {
        slugInput.value = slugify(this.value);
    }
});
slugInput?.addEventListener('input', function () {
    this.dataset.manual = 'true';
});

function slugify(text) {
    const map = { á:'a',à:'a',ã:'a',â:'a',é:'e',ê:'e',í:'i',ó:'o',õ:'o',ô:'o',ú:'u',ç:'c',ñ:'n' };
    return text.toLowerCase()
        .replace(/[áàãâäéèêëíìîïóòõôöúùûüçñ]/g, m => map[m] || m)
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s-]+/g, '-').trim('-');
}

// Preencher conteúdo antes de submit
document.getElementById('postForm').addEventListener('submit', function () {
    document.getElementById('conteudoInput').value = quill.root.innerHTML;
});

// Upload preview
const fileInput = document.getElementById('imagem_capa');
fileInput?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('uploadPreview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});
document.getElementById('uploadRemove')?.addEventListener('click', function () {
    fileInput.value = '';
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';
});
</script>
</body>
</html>
