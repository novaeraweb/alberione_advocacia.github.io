<?php
require_once __DIR__ . '/../config/helpers.php';

function expect_content(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$html = '<p>Conforme o art. 3º, § 1º, a cobrança deve observar a lei. <strong>Ação e restituição.</strong></p>';
$decoded = decode_post_content(['conteudo_b64' => base64_encode($html), 'conteudo' => 'legado']);
expect_content($decoded === $html, 'Conteúdo UTF-8 e formatação devem ser preservados integralmente.');
expect_content(decode_post_content(['conteudo' => $html]) === $html, 'Formulários antigos devem continuar aceitos.');
expect_content(decode_post_content(['conteudo_b64' => '', 'conteudo' => $html]) === $html, 'Campo codificado vazio deve aceitar o formato legado.');

foreach ([
    ['conteudo_b64' => '%%%invalido%%%', 'conteudo' => $html],
    ['conteudo_b64' => base64_encode("\xff")],
    ['conteudo_b64' => ['valor']],
    ['conteudo' => ['valor']],
] as $fields) {
    try {
        decode_post_content($fields);
        throw new RuntimeException('Conteúdo malformado deve ser rejeitado.');
    } catch (InvalidArgumentException $expected) {
        // Erro de entrada controlado, sem substituir silenciosamente por outro campo.
    }
}

$unsafe = '<p>Conteúdo editorial suficientemente longo para o teste.</p><script>alert(1)</script>';
$safe = sanitize_post_html(decode_post_content(['conteudo_b64' => base64_encode($unsafe)]));
expect_content(strpos($safe, '<script') === false && strpos($safe, 'alert(1)') === false, 'O HTML decodificado deve continuar sendo sanitizado.');
echo "PASS: conteúdo UTF-8, compatibilidade, rejeição de entradas inválidas e sanitização.\n";
