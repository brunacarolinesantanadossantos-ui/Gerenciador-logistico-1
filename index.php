<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8mb4","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$msg = "";

// BAIXAR RELATORIO DO DIA
if(isset($_GET['baixar_relatorio']) && !empty($_GET['data_relatorio'])){
    $data = $_GET['data_relatorio'];
    $s = $pdo->prepare("SELECT f.*, p.nome AS produto_nome, e.cliente AS cliente_nome, e.cidade, e.estado FROM fretes f LEFT JOIN produtos p ON p.id=f.produto_id LEFT JOIN enderecos e ON e.id=f.endereco_id WHERE DATE(f.data_frete)=?");
    $s->execute([$data]);
    $dados = $s->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_fretes_'.$data.'.csv"');
    $out = fopen('php://output','w');
    fputcsv($out, ['Data','Produto','Cliente','Cidade','Estado','Transportadora','Valor','Prazo'], ';');
    foreach($dados as $f){
        fputcsv($out, [$f['data_frete'], $f['produto_nome'], $f['cliente_nome'], $f['cidade'], $f['estado'], $f['transportadora'], $f['valor'], $f['prazo']], ';');
    }
    fclose($out);
    exit;
}

// CADASTRAR TRANSPORTADOR
if(isset($_POST['cadastrar_transportador'])){
    $s = $pdo->prepare("INSERT INTO transportadores (nome,cpf_cnpj,veiculo,placa) VALUES (?,?,?,?)");
    $s->execute([$_POST['nome'],$_POST['cpf_cnpj'],$_POST['veiculo'],$_POST['placa']]);
    $msg = "Transportador cadastrado!";
}

// EDITAR PRODUTO
$produto_editar = null;
if(isset($_GET['editar_produto'])){
  $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
  $stmt->execute([$_GET['editar_produto']]);
  $produto_editar = $stmt->fetch();
}

if(isset($_POST['salvar_edicao_produto'])){
  $stmt = $pdo->prepare("UPDATE produtos SET nome=?, codigo=?, peso=?, preco=? WHERE id=?");
  $stmt->execute([$_POST['nome'], $_POST['codigo'], $_POST['peso'], $_POST['preco'], $_POST['id']]);
  header('Location: index.php');
  exit;
}

// CADASTRAR PRODUTO
if(isset($_POST['cadastrar_produto'])){
    $s = $pdo->prepare("INSERT INTO produtos (nome,codigo,peso,preco) VALUES (?,?,?,?)");
    $s->execute([$_POST['nome'],$_POST['codigo'],$_POST['peso'],$_POST['preco']]);
    $msg = "Produto cadastrado!";
}
// EDITAR TRANSPORTADOR
$transportador_editar = null;
if(isset($_GET['editar_transportador'])){
  $stmt = $pdo->prepare("SELECT * FROM transportadores WHERE id=?");
  $stmt->execute([$_GET['editar_transportador']]);
  $transportador_editar = $stmt->fetch();
}
if(isset($_POST['salvar_edicao_transportador'])){
  $stmt = $pdo->prepare("UPDATE transportadores SET nome=?, cpf_cnpj=?, veiculo=?, placa=? WHERE id=?");
  $stmt->execute([$_POST['nome'], $_POST['cpf_cnpj'], $_POST['veiculo'], $_POST['placa'], $_POST['id']]);
  header("Location: index.php");
  exit;
}
// EDITAR ENDERECO
$endereco_editar = null;
if(isset($_GET['editar_endereco'])){
  $stmt = $pdo->prepare("SELECT * FROM enderecos WHERE id=?");
  $stmt->execute([$_GET['editar_endereco']]);
  $endereco_editar = $stmt->fetch();
}
if(isset($_POST['salvar_edicao_endereco'])){
  $stmt = $pdo->prepare("UPDATE enderecos SET cliente=?, cep=?, cidade=?, estado=?, rua=?, tipo=?, numero=?, bairro=? WHERE id=?");
$stmt->execute([$_POST['cliente'], $_POST['cep'], $_POST['cidade'], $_POST['estado'], $_POST['rua'], $_POST['tipo'], $_POST['numero'], $_POST['bairro'], $_POST['id']]);
  header('Location: index.php');
  exit;
}
// CADASTRAR ENDERECO
if(isset($_POST['cadastrar_endereco'])){
    $s = $pdo->prepare("INSERT INTO enderecos (cliente,cep,cidade,estado,rua,tipo,numero,bairro) VALUES (?,?,?,?,?,?,?,?)");
    $s->execute([$_POST['cliente'],$_POST['cep'],$_POST['cidade'],$_POST['estado'],$_POST['rua'],$_POST['tipo'],$_POST['numero'],$_POST['bairro']]);
    $msg = "Endereco cadastrado!";
}
// GERAR FRETE - 1 transportador leva VARIOS produtos
if(isset($_POST['gerar_frete'])){
  $s = $pdo->prepare("INSERT INTO fretes (produto_id,endereco_id,transportadora,valor) VALUES (?,?,?,?)");
  foreach($_POST['produto_ids'] as $pid){
    $s->execute([$pid,$_POST['endereco_id'],$_POST['transportadora'],$_POST['valor']]);
  }
  $msg = "Frete gerado! ".count($_POST['produto_ids'])." produtos com o mesmo transportador!";
}
$transportadores = $pdo->query("SELECT * FROM transportadores")->fetchAll(PDO::FETCH_ASSOC);
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
$enderecos = $pdo->query("SELECT * FROM enderecos")->fetchAll(PDO::FETCH_ASSOC);
$fretes = $pdo->query("SELECT f.*, p.nome AS produto_nome, e.cliente AS cliente_nome, e.cidade FROM fretes f LEFT JOIN produtos p ON p.id=f.produto_id LEFT JOIN enderecos e ON e.id=f.endereco_id ORDER BY f.data_frete DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Gerenciador Logístico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🚚 Gerenciador Logístico</a>
    <div>
      <a class="text-white mx-2 text-decoration-none" href="index.php">Início</a>
      <a class="text-white mx-2 text-decoration-none" href="index.php">Transportadores</a>
      <a class="text-white mx-2 text-decoration-none" href="index.php">Fretes</a>
      <a class="text-white mx-2 text-decoration-none" href="index.php">Relatório</a>
      <a class="text-white mx-2 text-decoration-none" href="logout.php">Sair</a>
    </div>
  </div>
</nav>
<div class="container">

<style>body{font-family:Arial;max-width:900px;margin:20px auto;padding:10px}input,select{padding:8px;margin:4px;width:95%}button{padding:10px;background:#2563eb;color:#fff;border:0;border-radius:6px}h2{border-bottom:2px solid #2563eb}.card{border:1px solid #ddd;padding:12px;border-radius:8px;margin:12px 0}</style>
 <?php if($msg) echo "<b style='color:green'>$msg</b>"; ?>

<div class="card"><h2>📊 Relatorio de Fretes do Dia</h2>
<form method="get">
<input type="date" name="data_relatorio" required>
<button name="baixar_relatorio" value="1">Baixar Relatorio (Excel / CSV)</button>
</form>
<p>Escolha o dia e baixe todos os fretes daquele dia.</p>
</div>

<div class="card"><h2><?= $transportador_editar ? 'Editar Transportador' : 'Transportador' ?></h2><form method="post">
<?php if($transportador_editar): ?><input type="hidden" name="id" value="<?= $transportador_editar['id'] ?>"><?php endif; ?>
<input name="nome" placeholder="Nome" required value="<?= $transportador_editar['nome'] ?? '' ?>">
<input name="cpf_cnpj" placeholder="CPF / CNPJ" required value="<?= $transportador_editar['cpf_cnpj'] ?? '' ?>">
<input name="veiculo" placeholder="Veiculo - ex: Caminhao" value="<?= $transportador_editar['veiculo'] ?? '' ?>">
<input name="placa" placeholder="Placa" value="<?= $transportador_editar['placa'] ?? '' ?>">
<button name="<?= $transportador_editar ? 'salvar_edicao_transportador' : 'cadastrar_transportador' ?>"><?= $transportador_editar ? 'Salvar Edicao' : 'Cadastrar Transportador' ?></button>
</form>
<?php foreach($transportadores as $t) echo "🚚 <b>".$t['nome']."</b> - ".$t['veiculo']." - ".$t['placa']." <a href='?editar_transportador=".$t['id']."'>Editar</a><br>"; ?></div>
<div class="card"><h2><?= $produto_editar ? 'Editar Produto' : 'Produto' ?></h2><form method="post">
<?php if($produto_editar): ?><input type="hidden" name="id" value="<?= $produto_editar['id'] ?>"><?php endif; ?>
<input name="nome" placeholder="Nome do produto" required value="<?= $produto_editar['nome'] ?? '' ?>">
<input name="codigo" placeholder="Codigo" value="<?= $produto_editar['codigo'] ?? '' ?>">
<input name="peso" placeholder="Peso" type="number" step="0.01" value="<?= $produto_editar['peso'] ?? '' ?>">
<input name="preco" placeholder="Preco" type="number" step="0.01" value="<?= $produto_editar['preco'] ?? '' ?>">
<button name="<?= $produto_editar ? 'salvar_edicao_produto' : 'cadastrar_produto' ?>"><?= $produto_editar ? 'Salvar Edição' : 'Cadastrar Produto' ?></button></form>
<?php foreach($produtos as $p) echo "📦 <b>".$p['nome']."</b> - Cod: ".$p['codigo']." - Peso: ".$p['peso']." - R$ ".$p['preco']." <a href='?editar_produto=".$p['id']."'>✏️ Editar</a><br>"; ?>
<div class="card"><h2><?= $endereco_editar ? 'Editar Endereco' : 'Endereco' ?></h2><form method="post">
<?php if($endereco_editar): ?><input type="hidden" name="id" value="<?= $endereco_editar['id'] ?>"><?php endif; ?>
<input name="cliente" placeholder="Cliente" required value="<?= $endereco_editar['cliente'] ?? '' ?>">
<input name="cep" placeholder="CEP" value="<?= $endereco_editar['cep'] ?? '' ?>">
<input name="cidade" placeholder="Cidade" value="<?= $endereco_editar['cidade'] ?? '' ?>">
<input name="estado" placeholder="Estado" value="<?= $endereco_editar['estado'] ?? '' ?>">
<input name="rua" placeholder="Rua" value="<?= $endereco_editar['rua'] ?? '' ?>">
<input name="numero" placeholder="Numero" value="<?= $endereco_editar['numero'] ?? '' ?>">
<input name="bairro" placeholder="Bairro" value="<?= $endereco_editar['bairro'] ?? '' ?>">
<select name="tipo"><option value="Origem" <?= ($endereco_editar['tipo'] ?? '')=='Origem'?'selected':'' ?>>Origem</option><option value="Destino" <?= ($endereco_editar['tipo'] ?? '')=='Destino'?'selected':'' ?>>Destino</option></select>
<button name="<?= $endereco_editar ? 'salvar_edicao_endereco' : 'cadastrar_endereco' ?>"><?= $endereco_editar ? 'Salvar Edicao' : 'Cadastrar Endereco' ?></button></form>
<?php foreach($enderecos as $e) echo "📍 <b>".$e['cliente']."</b> - ".$e['cidade']." - CEP: ".$e['cep']." <a href='?editar_endereco=".$e['id']."'>Editar</a><br>"; ?>
</div></div>

<div class="card"><h2>Gerar Frete</h2><form method="post">
<p><b>Marque os produtos (pode marcar vários):</b></p>
<?php foreach($produtos as $p){ ?>

<select name="endereco_id" required>
<option value="">Escolha o endereço</option>
<?php foreach($enderecos as $e){ ?>
<option value="<?=$e['id']?>"><?=$e['cliente']?> - <?=$e['cidade']?></option>
<?php } ?>
</select>
<select name="transportadora" required>
<option value="">Escolha a transportadora</option>
<?php foreach($transportadores as $t){ ?>
<option value="<?=$t['nome']?>"><?=$t['nome']?> - <?=$t['veiculo']?></option>
<?php } ?>
</select>
<input name="valor" placeholder="Valor do frete" type="number" step="0.01" required>
<input name="prazo" placeholder="Prazo - ex: 3 dias" required>
<button name="gerar_frete">Gerar Frete</button>
</form><h3>Fretes gerados:</h3>
<?php foreach($fretes as $f) echo "✅ ".($f['data_frete'] ?? '')." | Produto: <b>".$f['produto_nome']."</b> | Cliente: <b>".$f['cliente_nome']." - ".$f['cidade']."</b> | Transportadora: <b>".$f['transportadora']."</b> | Valor: R$ ".$f['valor']." | Prazo: ".$f['prazo']."<br>"; ?>
</div>
<div id="paradas-extras" style="margin-top:10px;"></div>
<button type="button" id="btn-parada" style="margin-top:5px; background:#28a745; color:white; padding:8px; border:none; border-radius:5px; cursor:pointer;">+ Adicionar Parada Extra</button>
<script>
document.getElementById('btn-parada').addEventListener('click', function(){
  var div = document.createElement('div');
  div.style.marginTop = '5px';
  div.innerHTML = '<input type="text" name="paradas_extras[]" placeholder="Endereço da parada extra" style="width:80%; padding:5px;"> <button type="button" onclick="this.parentElement.remove()">X</button>';
  document.getElementById('paradas-extras').appendChild(div);
});
</script></body></html>