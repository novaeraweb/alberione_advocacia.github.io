-- Migration: adiciona categoria às publicações existentes
ALTER TABLE posts
    ADD COLUMN categoria VARCHAR(100) NULL DEFAULT NULL AFTER tipo,
    ADD KEY idx_posts_categoria (categoria),
    ADD KEY idx_posts_categoria_pub (categoria, status, publicado_em, excluido_em);

UPDATE posts
SET categoria = CASE
    WHEN tipo = 'informativo' THEN 'Atualizações Legislativas'
    ELSE 'Direito Tributário'
END
WHERE categoria IS NULL OR categoria = '';
