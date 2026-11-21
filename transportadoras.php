<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome'])) {
  $stmt = $pdo->prepare("INSERT INTO transportadores (nome, placa) VALUES (?, ?)");
  $stmt->execute([$_POST['nome'], $_POST['placa']]);
  header('Location: transportadoras.php'); exit;
}
$transportadores = $pdo->query("SELECT * FROM transportadores")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Transportadoras</title>
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
      <a class="nav-link active" href="transportadoras.php">Transportadoras</a>
      <a class="nav-link" href="frete.php">Gerar Frete</a>
      <a class="nav-link" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2>🚚 Transportadoras</h2>

  <div class="card shadow p-4 mb-4">
    <h5>Cadastrar transportadora nova</h5>
    <form method="post" class="row g-2">
      <div class="col-md-6"><input name="nome" class="form-control" placeholder="Nome da transportadora" required></div>
      <div class="col-md-4"><input name="placa" class="form-control" placeholder="Placa do veículo" required></div>
      <div class="col-md-2"><button class="btn btn-warning w-100">Cadastrar</button></div>
    </form>
  </div>

  <div class="card shadow p-4">
    <h5>Lista de transportadoras</h5>
    <table class="table table-striped">
      <tr><th>Nome</th><th>Placa</th></tr>
      <?php foreach($transportadores as $t){ echo "<tr><td>".$t['nome']."</td><td>".$t['placa']."</td></tr>"; } ?>
    </table>
  </div>
</div>
</body>
</html>