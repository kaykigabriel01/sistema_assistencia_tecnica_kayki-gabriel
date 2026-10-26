<?php
declare(strict_types=1);
session_start();

const DB_HOST = '127.0.0.1';
const DB_PORT = 3307;
const DB_NAME = 'saep_db';
const DB_USER = 'root';
const DB_PASS = ''; // padrão comum do XAMPP; ajuste se definiu uma senha

// Em ambiente real, guarde a chave fora da pasta pública e use variável de ambiente.
const APP_KEY = 'troque-esta-chave-local-por-uma-chave-secreta-de-32-caracteres';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco. Confirme se o MySQL do XAMPP está ativo na porta 3307 e se o banco saep_db foi importado.');
}

const SESSION_TIMEOUT = 1800; 
if (isset($_SESSION['last_activity']) && (time() - (int)$_SESSION['last_activity']) > SESSION_TIMEOUT) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_start();
    $_SESSION['flash'] = 'Sua sessão expirou. Entre novamente.';
}
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}

function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sent = $_POST['csrf_token'] ?? '';
        if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
            http_response_code(403);
            exit('Solicitação inválida. Atualize a página e tente novamente.');
        }
    }
}
function encrypt_cpf(string $cpf): string {
    $key = hash('sha256', APP_KEY, true);
    $iv = random_bytes(16);
    $cipher = openssl_encrypt($cpf, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    if ($cipher === false) throw new RuntimeException('Falha ao proteger o CPF.');
    return base64_encode($iv . $cipher);
}
function decrypt_cpf(string $encrypted): string {
    if (str_starts_with($encrypted, 'SEED_DEMO_')) return '***.***.***-**';
    $raw = base64_decode($encrypted, true);
    if ($raw === false || strlen($raw) < 17) return 'CPF protegido';
    $plain = openssl_decrypt(substr($raw, 16), 'AES-256-CBC', hash('sha256', APP_KEY, true), OPENSSL_RAW_DATA, substr($raw, 0, 16));
    return $plain === false ? 'CPF protegido' : $plain;
}
