<?php
require_once __DIR__ . '/includes/config.php'; require_login(); verify_csrf();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $cliente_id=(int)($_POST['cliente_id']??0); $tipo=trim((string)($_POST['tipo']??'')); $marca=trim((string)($_POST['marca']??'')); $modelo=trim((string)($_POST['modelo']??'')); $serie=trim((string)($_POST['numero_serie']??'')); $defeito=trim((string)($_POST['defeito_relatado']??''));
  if(!$cliente_id||$tipo===''||$marca===''||$modelo===''||$defeito==='') $error='Preencha os campos obrigatórios e selecione um cliente.';
  else { $s=$pdo->prepare('INSERT INTO equipamentos(cliente_id,tipo,marca,modelo,numero_serie,defeito_relatado) VALUES(?,?,?,?,?,?)'); $s->execute([$cliente_id,$tipo,$marca,$modelo,$serie?:null,$defeito]); $_SESSION['flash']='Equipamento cadastrado.'; header('Location: equipamentos.php'); exit; }
}
$clientes=$pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$lista=$pdo->query('SELECT e.*,c.nome cliente FROM equipamentos e JOIN clientes c ON c.id=e.cliente_id ORDER BY e.criado_em DESC')->fetchAll();
$page_title='Equipamentos'; require __DIR__ . '/includes/header.php';
?>
<h1>Equipamentos</h1><section class="card"><h2>Cadastrar equipamento</h2><?php if($error): ?><div class="error"><?=e($error)?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="form-grid">
<div><label>Cliente responsável</label><select name="cliente_id" required><option value="">Selecione...</option><?php foreach($clientes as $c): ?><option value="<?=(int)$c['id']?>"><?=e($c['nome'])?></option><?php endforeach;?></select></div>
<div><label>Tipo</label><input name="tipo" required placeholder="Notebook, desktop, impressora..."></div><div><label>Marca</label><input name="marca" required></div><div><label>Modelo</label><input name="modelo" required></div><div><label>Número de série</label><input name="numero_serie"></div><div><label>Defeito relatado</label><input name="defeito_relatado" required></div></div><button class="btn">Salvar equipamento</button></form></section>
<section class="card"><h2>Equipamentos cadastrados</h2><div class="table-wrap"><table><thead><tr><th>Cliente</th><th>Tipo</th><th>Marca/modelo</th><th>Série</th><th>Defeito relatado</th></tr></thead><tbody><?php foreach($lista as $e): ?><tr><td><?=e($e['cliente'])?></td><td><?=e($e['tipo'])?></td><td><?=e($e['marca'].' '.$e['modelo'])?></td><td><?=e($e['numero_serie'])?></td><td><?=e($e['defeito_relatado'])?></td></tr><?php endforeach;?></tbody></table></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
