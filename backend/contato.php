<?php
/**
 * Alberione Advogados — API de Contato
 * backend/contato.php
 * Aceita: POST JSON | Retorna: JSON
 */
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// ── Rate limiting simples por IP ─────────────────────────────────
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$key = 'rate_' . md5($ip);
$limit = 5;
if (!isset($_SESSION[$key])) $_SESSION[$key] = ['count' => 0, 'reset' => time() + 3600];
if (time() > $_SESSION[$key]['reset']) $_SESSION[$key] = ['count' => 0, 'reset' => time() + 3600];
if ($_SESSION[$key]['count'] >= $limit) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Muitas tentativas. Aguarde um momento.']);
    exit;
}
$_SESSION[$key]['count']++;

// ── Parse JSON Body ──────────────────────────────────────────────
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    exit;
}

// ── Honeypot ─────────────────────────────────────────────────────
if (!empty($data['honeypot'])) {
    echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    exit;
}

// ── Sanitização ──────────────────────────────────────────────────
$nome     = clean($data['nome']     ?? '');
$email    = filter_var(trim($data['email']    ?? ''), FILTER_SANITIZE_EMAIL);
$telefone = clean($data['telefone'] ?? '');
$assunto  = clean($data['assunto']  ?? '');
$mensagem = clean($data['mensagem'] ?? '');

// ── Validação ────────────────────────────────────────────────────
$errors = [];

if (strlen($nome) < 3) {
    $errors['nome'] = 'Informe seu nome completo.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Informe um e-mail válido.';
}
if (strlen($mensagem) < 15) {
    $errors['mensagem'] = 'Descreva sua mensagem com mais detalhes.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Corrija os campos indicados.', 'errors' => $errors]);
    exit;
}

// ── Salvar no banco ──────────────────────────────────────────────
$saved = false;
try {
    $saved = save_contact_message([
        'nome'     => $nome,
        'email'    => $email,
        'telefone' => $telefone,
        'assunto'  => $assunto,
        'mensagem' => $mensagem,
    ]);
} catch (Exception $e) {
    error_log('[CONTACT] Erro ao salvar: ' . $e->getMessage());
}

// ── Enviar E-mail ─────────────────────────────────────────────────
$emailSent = false;
$s = get_site_settings();
$dest = !empty($s['email_destino_form']) ? $s['email_destino_form'] : OFFICE_EMAIL;

if (!empty($dest) && filter_var($dest, FILTER_VALIDATE_EMAIL)) {
    $assunto_email = '[Site Alberione] ' . (!empty($assunto) ? $assunto : 'Nova mensagem de contato');
    $corpo = "Nova mensagem recebida pelo formulário do site.\n\n";
    $corpo .= "Nome: $nome\n";
    $corpo .= "E-mail: $email\n";
    if (!empty($telefone)) $corpo .= "Telefone: $telefone\n";
    if (!empty($assunto))  $corpo .= "Assunto: $assunto\n";
    $corpo .= "\nMensagem:\n$mensagem\n\n";
    $corpo .= "---\nEnviado em: " . date('d/m/Y H:i:s') . "\n";
    $corpo .= "IP: $ip\n";

    $headers  = "From: {$nome} <{$email}>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: AlberioneAdvogados\r\n";

    $emailSent = @mail($dest, $assunto_email, $corpo, $headers);
}

// ── Resposta ──────────────────────────────────────────────────────
if ($saved || $emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Mensagem enviada com sucesso! Nossa equipe entrará em contato em breve.',
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Não foi possível enviar sua mensagem agora. Por favor, tente novamente ou nos contate pelo WhatsApp.',
    ]);
}
