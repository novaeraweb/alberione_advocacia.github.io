<?php
/**
 * Alberione Advogados - Configurações Globais
 * config/config.php
 */

// Timezone Brasil
date_default_timezone_set('America/Sao_Paulo');

// Compatibilidade com PHP 7.x
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

// Modo de exibição de erros
// Em desenvolvimento local: display_errors = 1
// Em produção: display_errors = 0
$is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])
            || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');
ini_set('display_errors', $is_local ? 1 : 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Caminhos base
define('ROOT_PATH', dirname(__DIR__) . '/');
define('CONFIG_PATH', __DIR__ . '/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('UPLOADS_URL', '/uploads/');

// URL do site — detecta automaticamente local vs produção
$is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'])
            || str_contains($_SERVER['HTTP_HOST'] ?? '', 'localhost');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Detecta subpasta: se o projeto estiver em /AlberioneAdvocacia/, captura o basepath
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$base_path   = rtrim(dirname(str_replace('/admin', '', $script_name)), '/\\');
$base_path   = ($base_path === '/' || $base_path === '\\') ? '' : $base_path;
define('SITE_URL', $is_local ? "$protocol://$host$base_path" : 'https://www.alberione.com.br');
define('BASE_PATH', $base_path); // ex: /AlberioneAdvocacia ou vazio em produção
// Em localhost/subpasta, usa links com query string para não depender de .htaccess.
// Em produção, mantém URLs amigáveis. Pode ser forçado via USE_FRIENDLY_URLS=1/0.
$env_friendly = getenv('USE_FRIENDLY_URLS');
define('USE_FRIENDLY_URLS', $env_friendly !== false ? (bool)(int)$env_friendly : !$is_local);

// Informações do escritório (fallback se banco estiver indisponível)
define('OFFICE_NAME', 'Alberione Advogados');
define('OFFICE_EMAIL', 'contato@alberione.com.br');
define('OFFICE_PHONE', '');
define('OFFICE_WHATSAPP', '5511999999999');
define('OFFICE_WHATSAPP_LINK', 'https://wa.me/5511999999999');

// Blog
define('BLOG_PER_PAGE', 9);
define('ADMIN_PER_PAGE', 15);

// Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('UPLOAD_ALLOWED_EXT', ['jpg', 'jpeg', 'png', 'webp', 'gif']);

// Sessão segura
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Banco de dados (ajustar em produção via .env ou direto aqui)
// Em MAMP, normalmente o Apache roda em :8888 e o MySQL em :8889, com usuário root/root.
$is_mamp_local = $is_local && strpos($_SERVER['HTTP_HOST'] ?? '', ':8888') !== false;

define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: ($is_mamp_local ? '8889' : '3306'));
define('DB_NAME', getenv('DB_NAME') ?: 'alberione_site');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : ($is_mamp_local ? 'root' : ''));
define('DB_CHARSET', 'utf8mb4');

// Includes automáticos
require_once CONFIG_PATH . 'database.php';
require_once CONFIG_PATH . 'helpers.php';
