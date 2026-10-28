<?php
require_once __DIR__ . '/../config/helpers.php';

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE posts (id INTEGER PRIMARY KEY, slug TEXT UNIQUE, excluido_em TEXT)');
        $pdo->exec("INSERT INTO posts VALUES (1, 'conteudo-juridico', '2026-01-01'), (2, 'conteudo-juridico-1', NULL)");
    }
    return $pdo;
}

$slug = generate_unique_slug('Conteúdo Jurídico');
if ($slug !== 'conteudo-juridico-2') {
    throw new RuntimeException('O novo slug deve considerar registros ativos e excluídos.');
}
db()->prepare('INSERT INTO posts (id, slug) VALUES (?, ?)')->execute([3, $slug]);

if (generate_unique_slug('conteudo-juridico-1', 2) !== 'conteudo-juridico-1') {
    throw new RuntimeException('A edição deve poder manter o slug do próprio registro.');
}
echo "PASS: slug único considera posts excluídos e preserva o endereço do próprio post na edição.\n";
