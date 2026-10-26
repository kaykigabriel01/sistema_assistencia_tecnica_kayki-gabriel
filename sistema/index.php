<?php
require_once __DIR__ . '/includes/config.php'; require_login();
$page_title = 'Painel principal';
$clientes = (int)$pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
$equipamentos = (int)$pdo->query('SELECT COUNT(*) FROM equipamentos')->fetchColumn();
$ordens = (int)$pdo->query("SELECT COUNT(*) FROM ordens_servico WHERE status NOT IN ('Concluída','Cancelada')")->fetchColumn();
$recentes = $pdo->query("SELECT os.id,os.status,os.data_abertura,c.nome AS cliente,e.tipo,e.marca,e.modelo FROM ordens_servico os JOIN equipamentos e ON e.id=os.equipamento_id JOIN clientes c ON c.id=e.cliente_id ORDER BY os.data_abertura DESC LIMIT 5")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<h1>Olá, <?= e($_SESSION['user_name'] ?? 'usuário') ?>!</h1>
<p class="muted">Painel de controle da assistência técnica.</p>
<div class="cards">
 <div class="card"><div class="muted">Clientes cadastrados</div><div class="metric"><?= $clientes ?></div><a class="btn" href="clientes.php">Gerenciar clientes</a></div>
 <div class="card"><div class="muted">Equipamentos</div><div class="metric"><?= $equipamentos ?></div><a class="btn" href="equipamentos.php">Gerenciar equipamentos</a></div>
 <div class="card"><div class="muted">Ordens em aberto</div><div class="metric"><?= $ordens ?></div><a class="btn" href="ordens.php">Ver ordens</a></div>
</div>
<section class="card"><h2>Ordens recentes</h2><div class="table-wrap"><table><thead><tr><th>Nº</th><th>Cliente</th><th>Equipamento</th><th>Abertura</th><th>Status</th></tr></thead><tbody>
<?php foreach ($recentes as $r): ?><tr><td>#<?= (int)$r['id'] ?></td><td><?= e($r['cliente']) ?></td><td><?= e($r['tipo'].' '.$r['marca'].' '.$r['modelo']) ?></td><td><?= e($r['data_abertura']) ?></td><td><span class="pill"><?= e($r['status']) ?></span></td></tr><?php endforeach; ?>
</tbody></table></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
