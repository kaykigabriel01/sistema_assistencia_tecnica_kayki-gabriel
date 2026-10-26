<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title ?? 'Assistência Técnica') ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand">Tecno<span>Assist</span></div>
  <?php if (!empty($_SESSION['user_id'])): ?>
  <nav>
    <a href="index.php">Início</a><a href="clientes.php">Clientes</a><a href="equipamentos.php">Equipamentos</a><a href="ordens.php">Ordens de serviço</a><a href="logout.php">Sair</a>
  </nav>
  <?php endif; ?>
</header>
<main class="container">
<?php if (!empty($_SESSION['flash'])): ?><div class="notice"><?= e($_SESSION['flash']) ?></div><?php unset($_SESSION['flash']); endif; ?>
