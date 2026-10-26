<?php
require_once __DIR__ . '/includes/config.php'; require_login(); verify_csrf();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim((string)($_POST['nome'] ?? '')); $cpf = preg_replace('/\D+/', '', (string)($_POST['cpf'] ?? ''));
    $telefone = trim((string)($_POST['telefone'] ?? '')); $email = trim((string)($_POST['email'] ?? ''));
    if ($nome === '' || strlen($cpf) !== 11 || $telefone === '') $error = 'Informe nome, CPF com 11 dígitos e telefone.';
    else {
      try {
        $stmt = $pdo->prepare('INSERT INTO clientes (nome,cpf_criptografado,telefone,email) VALUES (?,?,?,?)');
        $stmt->execute([$nome, encrypt_cpf($cpf), $telefone, $email ?: null]);
        $_SESSION['flash'] = 'Cliente cadastrado com sucesso.'; header('Location: clientes.php'); exit;
      } catch (PDOException $ex) { $error = 'Não foi possível cadastrar. Verifique os dados informados.'; }
    }
}
$termo = trim((string)($_GET['q'] ?? ''));
if ($termo !== '') {
  $stmt = $pdo->prepare('SELECT id,nome,telefone,email,cpf_criptografado FROM clientes WHERE nome LIKE ? OR telefone LIKE ? OR email LIKE ? ORDER BY nome');
  $like = "%$termo%"; $stmt->execute([$like,$like,$like]); $lista = $stmt->fetchAll();
} else $lista = $pdo->query('SELECT id,nome,telefone,email,cpf_criptografado FROM clientes ORDER BY nome')->fetchAll();
$page_title = 'Clientes'; require __DIR__ . '/includes/header.php';
?>
<h1>Clientes</h1>
<section class="card"><h2>Novo cliente</h2><?php if($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="form-grid"><div><label>Nome completo</label><input name="nome" required maxlength="120"></div><div><label>CPF (11 dígitos)</label><input name="cpf" inputmode="numeric" required maxlength="14" placeholder="Somente números ou com pontuação"></div><div><label>Telefone</label><input name="telefone" required maxlength="30"></div><div><label>E-mail</label><input name="email" type="email" maxlength="120"></div></div><button class="btn" type="submit">Salvar cliente</button></form></section>
<section class="card"><h2>Pesquisar clientes</h2><form class="search" method="get"><div><label>Nome, telefone ou e-mail</label><input name="q" value="<?= e($termo) ?>" placeholder="Digite um termo e confirme"></div><button class="btn" type="submit">Buscar</button><a class="btn secondary" href="clientes.php">Limpar</a></form></section>
<section class="card"><h2>Lista de clientes (<?= count($lista) ?>)</h2><div class="table-wrap"><table><thead><tr><th>Nome</th><th>CPF armazenado</th><th>Telefone</th><th>E-mail</th></tr></thead><tbody>
<?php foreach($lista as $c): ?><tr><td><?= e($c['nome']) ?></td><td><?= e(decrypt_cpf($c['cpf_criptografado'])) ?></td><td><?= e($c['telefone']) ?></td><td><?= e($c['email']) ?></td></tr><?php endforeach; ?>
<?php if(!$lista): ?><tr><td colspan="4">Nenhum cliente encontrado.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
