<?php
/**
 * Alberione Advogados — Admin: Editar Post
 * admin/post-edit.php
 */
require_once __DIR__ . '/../config/config.php';
require_auth();

$admin = get_current_admin();
$id    = (int)($_GET['id'] ?? 0);
if ($id < 1) { flash('error', 'Post não encontrado.'); redirect('/admin/posts.php'); }

$post = get_post_by_id($id);
if (!$post) { flash('error', 'Post não encontrado.'); redirect('/admin/posts.php'); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Requisição inválida.';
    } else {
        $titulo    = clean($_POST['titulo']   ?? '');
        $slug      = clean($_POST['slug']     ?? '');
        $tipo      = in_array($_POST['tipo']  ?? '', ['artigo','informativo']) ? $_POST['tipo'] : $post['tipo'];
        $categoria = clean(substr($_POST['categoria'] ?? '', 0, 100));
        $status    = in_array($_POST['status']?? '', ['rascunho','publicado']) ? $_POST['status'] : $post['status'];
        $destaque  = isset($_POST['destaque']) ? 1 : 0;
        $resumo    = clean($_POST['resumo']   ?? '');
        $conteudo  = sanitize_post_html($_POST['conteudo'] ?? '');
        $meta_title= clean($_POST['meta_title']   ?? '');
        $meta_desc = clean($_POST['meta_description'] ?? '');
        $imagem_alt= clean($_POST['imagem_alt'] ?? '');

        if (strlen($titulo) < 5)    $errors[] = 'O título deve ter ao menos 5 caracteres.';
        if (mb_strlen(trim(strip_tags($conteudo))) < 20) $errors[] = 'O conteúdo é muito curto.';

        if (empty($errors)) {
            // Slug
            if (empty($slug)) $slug = $post['slug'];
            else $slug = generate_unique_slug(slugify($slug), $id);

            // Upload imagem
            $imagem_capa = $post['imagem_capa'];
            if (!empty($_FILES['imagem_capa']['name'])) {
                $path = upload_image($_FILES['imagem_capa'], 'posts');
                if ($path) $imagem_capa = $path;
                else $errors[] = 'Erro no upload da imagem.';
            }
            if (!empty($_POST['remove_imagem'])) $imagem_capa = null;

            if (empty($errors)) {
                $publicado_em = $post['publicado_em'];
                if ($status === 'publicado' && empty($publicado_em)) {
                    $publicado_em = date('Y-m-d H:i:s');
                }

                db()->prepare(
                    "UPDATE posts SET tipo=?, categoria=?, titulo=?, slug=?, resumo=?, conteudo=?, imagem_capa=?, imagem_alt=?,
                     status=?, destaque=?, meta_title=?, meta_description=?, publicado_em=? WHERE id=?"
                )->execute([
                    $tipo, $categoria ?: null, $titulo, $slug, $resumo, $conteudo, $imagem_capa, $imagem_alt,
                    $status, $destaque, $meta_title, $meta_desc, $publicado_em, $id
                ]);

                flash('success', 'Post atualizado com sucesso!');
                redirect('/admin/post-edit.php?id=' . $id);
            }
        }
    }

    // Preencher para reexibição com valores do POST
    $post = array_merge($post, [
        'titulo'           => $_POST['titulo']           ?? $post['titulo'],
        'slug'             => $_POST['slug']             ?? $post['slug'],
        'tipo'             => $_POST['tipo']             ?? $post['tipo'],
        'categoria'        => $_POST['categoria']        ?? ($post['categoria'] ?? ''),
        'status'           => $_POST['status']           ?? $post['status'],
        'resumo'           => $_POST['resumo']           ?? $post['resumo'],
        'conteudo'         => sanitize_post_html($_POST['conteudo'] ?? $post['conteudo']),
        'meta_title'       => $_POST['meta_title']       ?? $post['meta_title'],
        'meta_description' => $_POST['meta_description'] ?? $post['meta_description'],
        'destaque'         => isset($_POST['destaque'])   ? 1 : 0,
    ]);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar: <?= e(mb_substr($post['titulo'], 0, 40)) ?> | Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">
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
                <h2>Editar Post</h2>
                <p>ID: <?= $post['id'] ?> · Criado em <?= format_datetime($post['criado_em']) ?></p>
            </div>
            <div style="display:flex;gap:8px">
                <?php if ($post['status'] === 'publicado'): ?>
                <a href="<?= e(post_permalink($post['slug'])) ?>" target="_blank" class="btn-sm-secondary">
                    <i class="fas fa-external-link-alt"></i> Ver no site
                </a>
                <?php endif; ?>
                <a href="<?= $bp ?>/admin/posts.php" class="btn-sm-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" id="postForm">
            <?= csrf_field() ?>

            <div class="post-editor-layout">
                <div class="post-editor-main">

                    <div class="admin-card">
                        <div class="admin-card-body">
                            <div class="form-group">
                                <label>Título <span class="req">*</span></label>
                                <input type="text" name="titulo" value="<?= e($post['titulo']) ?>" required maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Slug</label>
                                <div class="input-slug">
                                    <span class="slug-prefix"><?= (defined('USE_FRIENDLY_URLS') && USE_FRIENDLY_URLS) ? '/artigo/' : '/artigo.php?slug=' ?></span>
                                    <input type="text" name="slug" id="slugInput" value="<?= e($post['slug']) ?>" maxlength="255">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Resumo</h3></div>
                        <div class="admin-card-body">
                            <textarea name="resumo" rows="3" maxlength="320" placeholder="Breve descrição..."><?= e($post['resumo'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Conteúdo <span class="req">*</span></h3></div>
                        <div class="admin-card-body p-0">
                            <div id="quillEditor" style="min-height:400px"><?= sanitize_post_html($post['conteudo'] ?? '') ?></div>
                            <input type="hidden" name="conteudo" id="conteudoInput">
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header"><h3>SEO</h3></div>
                        <div class="admin-card-body">
                            <div class="form-group">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" value="<?= e($post['meta_title'] ?? '') ?>" maxlength="255">
                            </div>
                            <div class="form-group">
                                <label>Meta Description</label>
                                <textarea name="meta_description" rows="2" maxlength="320"><?= e($post['meta_description'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="post-editor-sidebar">

                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Publicar</h3></div>
                        <div class="admin-card-body">
                            <div class="post-meta-info">
                                <p><strong>Criado:</strong> <?= format_datetime($post['criado_em']) ?></p>
                                <?php if ($post['publicado_em']): ?>
                                <p><strong>Publicado:</strong> <?= format_datetime($post['publicado_em']) ?></p>
                                <?php endif; ?>
                                <p><strong>Views:</strong> <?= number_format($post['views_count'], 0, ',', '.') ?></p>
                            </div>
                            <div class="form-group">
                                <label>Tipo</label>
                                <select name="tipo">
                                    <option value="artigo"      <?= $post['tipo'] === 'artigo'      ? 'selected' : '' ?>>Artigo</option>
                                    <option value="informativo" <?= $post['tipo'] === 'informativo' ? 'selected' : '' ?>>Informativo</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Categoria</label>
                                <input type="text" name="categoria" value="<?= e($post['categoria'] ?? '') ?>"
                                       placeholder="Ex.: Direito Tributário" maxlength="100">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="rascunho"  <?= $post['status'] === 'rascunho'  ? 'selected' : '' ?>>Rascunho</option>
                                    <option value="publicado" <?= $post['status'] === 'publicado' ? 'selected' : '' ?>>Publicado</option>
                                </select>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="destaque" name="destaque" value="1" <?= $post['destaque'] ? 'checked' : '' ?>>
                                <label for="destaque"><i class="fas fa-star"></i> Destaque na home</label>
                            </div>
                            <div class="publish-actions">
                                <button type="submit" class="btn-admin-primary w-full">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header"><h3>Imagem Destaque</h3></div>
                        <div class="admin-card-body">
                            <?php if (!empty($post['imagem_capa'])): ?>
                            <div class="current-image">
                                <img src="<?= e(post_image_url($post['imagem_capa'])) ?>" alt="" class="current-img-preview">
                                <label class="form-check" style="margin-top:10px">
                                    <input type="checkbox" name="remove_imagem" value="1"> Remover imagem atual
                                </label>
                            </div>
                            <p style="font-size:.8rem;color:var(--admin-text-light);margin:8px 0">Envie nova imagem para substituir:</p>
                            <?php endif; ?>
                            <div class="upload-area" id="uploadArea">
                                <input type="file" id="imagem_capa" name="imagem_capa" accept="image/*" class="upload-input">
                                <div class="upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Clique ou arraste a imagem</p>
                                    <small>JPG, PNG, WebP — Máx 5MB. Se não enviar, será usada a imagem editorial padrão.</small>
                                </div>
                                <div class="upload-preview" id="uploadPreview" style="display:none">
                                    <img id="previewImg" src="" alt="">
                                    <button type="button" class="upload-remove" id="uploadRemove"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-top:12px">
                                <label>Alt da imagem</label>
                                <input type="text" name="imagem_alt" value="<?= e($post['imagem_alt'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="../assets/js/admin.js"></script>
<script>
const quill = new Quill('#quillEditor', {
    theme: 'snow',
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

document.getElementById('postForm').addEventListener('submit', function () {
    document.getElementById('conteudoInput').value = quill.root.innerHTML;
});

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
