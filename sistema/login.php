<?php
require_once __DIR__ . '/includes/config.php';
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string)($_POST['email'] ?? ''));
    $senha = (string)($_POST['senha'] ?? '');
    $stmt = $pdo->prepare('SELECT id,nome,email,senha_hash FROM usuarios WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($senha, $user['senha_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['nome'];
        $_SESSION['last_activity'] = time();
        header('Location: index.php'); exit;
    }
    $error = 'E-mail ou senha inválidos.';
}
$page_title = 'Entrar';
require __DIR__ . '/includes/header.php';
?>
<section class="card login">
<h1>Entrar no sistema</h1><p class="muted">Acesse para gerenciar clientes, equipamentos e ordens de serviço.</p>
<?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label for="email">E-mail</label><input id="email" name="email" type="email" required autocomplete="username">
<label for="senha">Senha</label><input id="senha" name="senha" type="password" required autocomplete="current-password">
<button class="btn" type="submit">Entrar</button>
</form>
<p class="muted">Acesso de demonstração: admin@local.test / Admin123!</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
