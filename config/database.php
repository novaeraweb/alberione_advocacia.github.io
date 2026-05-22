<?php
/**
 * Alberione Advogados - Conexão PDO MySQL
 * config/database.php
 */

declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, time_zone='-03:00'",
                ]);
            } catch (PDOException $e) {
                error_log('[DB ERROR] ' . $e->getMessage());
                http_response_code(503);

                $is_local = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1'], true)
                    || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;

                if ($is_local || (defined('APP_DEBUG') && APP_DEBUG === true)) {
                    die(
                        'Erro de conexão com o banco de dados: ' .
                        htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') .
                        '<br><br>Verifique DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASS em config/config.php.'
                    );
                }

                die('Serviço temporariamente indisponível.');
            }
        }

        return self::$instance;
    }
}

/**
 * Atalho global para obter a instância do PDO
 */
function db(): PDO
{
    return Database::getInstance();
}
