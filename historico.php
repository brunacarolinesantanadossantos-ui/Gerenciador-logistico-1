<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['produto'])) {
  $stmt = $pdo->prepare("INSERT INTO historico (produto, endereco, transportadora, valor) VALUES (?, ?, ?, ?)");
  $valor = rand(50, 300) + 0.99;
  $stmt->execute([$_POST['produto'], $_POST['endereco'], $_POST['transportadora'], $valor]);
  header('Location: historico.php'); exit;
}
$historico = $pdo->query("SELECT * FROM historico ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Histórico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🚚 Logístico</a>
    <div class="navbar-nav">
      <a class="nav-link" href="index.php">Início</a>
      <a class="nav-link" href="produtos.php">Produtos</a>
      <a class="nav-link" href="enderecos.php">Endereços</a>
      <a class="nav-link" href="transportadoras.php">Transportadoras</a>
      <a class="nav-link" href="frete.php">Gerar Frete</a>
      <a class="nav-link active" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2>📋 Histórico de Fretes</h2>

  <div class="card shadow p-4">
    <table class="table table-striped">
      <tr><th>Produto</th><th>Endereço</th><th>Transportadora</th><th>Valor</th></tr>
      <?php foreach($historico as $h){ 
        echo "<tr><td>".$h['produto']."</td><td>".$h['endereco']."</td><td>".$h['transportadora']."</td><td><span class='badge bg-success'>R$ ".number_format($h['valor'],2,',','.')."</span></td></tr>"; 
      } ?>
    </table>
    <?php if(count($historico)==0){ echo "<p class='text-center'>Nenhum frete ainda. <a href='frete.php'>Gerar o primeiro frete!</a></p>"; } ?>
  </div>

  <a href="frete.php" class="btn btn-primary mt-3 w-100">💰 Gerar Novo Frete</a>
</div>
</body>
</html>