<?php
require_once __DIR__ . '/includes/config.php'; require_login(); verify_csrf();
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $equipamento_id=(int)($_POST['equipamento_id']??0); $descricao=trim((string)($_POST['descricao_servico']??'')); $status=(string)($_POST['status']??'Aberta'); $valor=(float)str_replace(',','.',(string)($_POST['valor']??'0'));
  $valid=['Aberta','Em andamento','Aguardando peça','Concluída','Cancelada'];
  if(!$equipamento_id||$descricao===''||!in_array($status,$valid,true)||$valor<0) $error='Confira os campos da ordem de serviço.';
  else { $s=$pdo->prepare('INSERT INTO ordens_servico(equipamento_id,descricao_servico,status,valor,data_conclusao) VALUES(?,?,?,?,?)'); $s->execute([$equipamento_id,$descricao,$status,$valor,$status==='Concluída'?date('Y-m-d H:i:s'):null]); $_SESSION['flash']='Ordem de serviço cadastrada.'; header('Location: ordens.php'); exit; }
}
$equipamentos=$pdo->query('SELECT e.id,e.tipo,e.marca,e.modelo,c.nome cliente FROM equipamentos e JOIN clientes c ON c.id=e.cliente_id ORDER BY c.nome')->fetchAll();
$lista=$pdo->query('SELECT os.*,c.nome cliente,e.tipo,e.marca,e.modelo FROM ordens_servico os JOIN equipamentos e ON e.id=os.equipamento_id JOIN clientes c ON c.id=e.cliente_id ORDER BY os.data_abertura DESC')->fetchAll();
$page_title='Ordens de serviço'; require __DIR__ . '/includes/header.php';
?>
<h1>Ordens de serviço</h1><section class="card"><h2>Abrir ordem de serviço</h2><?php if($error): ?><div class="error"><?=e($error)?></div><?php endif;?>
<form method="post"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><div class="form-grid">
<div><label>Equipamento / cliente</label><select name="equipamento_id" required><option value="">Selecione...</option><?php foreach($equipamentos as $eq): ?><option value="<?=(int)$eq['id']?>"><?=e($eq['cliente'].' — '.$eq['tipo'].' '.$eq['marca'].' '.$eq['modelo'])?></option><?php endforeach;?></select></div>
<div><label>Status</label><select name="status"><?php foreach(['Aberta','Em andamento','Aguardando peça','Concluída','Cancelada'] as $st): ?><option><?=e($st)?></option><?php endforeach;?></select></div>
<div><label>Valor estimado (R$)</label><input type="number" name="valor" min="0" step="0.01" value="0.00"></div><div><label>Descrição do serviço</label><input name="descricao_servico" required></div></div><button class="btn">Salvar ordem</button></form></section>
<section class="card"><h2>Ordens registradas</h2><div class="table-wrap"><table><thead><tr><th>Nº</th><th>Abertura</th><th>Cliente</th><th>Equipamento</th><th>Serviço</th><th>Status</th><th>Valor</th></tr></thead><tbody>
<?php foreach($lista as $os): ?><tr><td>#<?=(int)$os['id']?></td><td><?=e($os['data_abertura'])?></td><td><?=e($os['cliente'])?></td><td><?=e($os['tipo'].' '.$os['marca'].' '.$os['modelo'])?></td><td><?=e($os['descricao_servico'])?></td><td><span class="pill"><?=e($os['status'])?></span></td><td>R$ <?=number_format((float)$os['valor'],2,',','.')?></td></tr><?php endforeach;?>
<?php if(!$lista): ?><tr><td colspan="7">Nenhuma ordem cadastrada.</td></tr><?php endif;?></tbody></table></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
