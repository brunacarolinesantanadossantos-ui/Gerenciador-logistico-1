<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rua'])) {
  $stmt = $pdo->prepare("INSERT INTO enderecos (rua, cidade) VALUES (?, ?)");
  $stmt->execute([$_POST['rua'], $_POST['cidade']]);
  header('Location: enderecos.php'); exit;
}
$enderecos = $pdo->query("SELECT * FROM enderecos")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Endereços</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🚚 Logístico</a>
    <div class="navbar-nav">
      <a class="nav-link" href="index.php">Início</a>
      <a class="nav-link" href="produtos.php">Produtos</a>
      <a class="nav-link active" href="enderecos.php">Endereços</a>
      <a class="nav-link" href="transportadoras.php">Transportadoras</a>
      <a class="nav-link" href="frete.php">Gerar Frete</a>
      <a class="nav-link" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2>📍 Endereços</h2>

  <div class="card shadow p-4 mb-4">
    <h5>Cadastrar endereço novo</h5>
    <form method="post" class="row g-2">
      <div class="col-md-6"><input name="rua" class="form-control" placeholder="Rua / Endereço" required></div>
      <div class="col-md-4"><input name="cidade" class="form-control" placeholder="Cidade" required></div>
      <div class="col-md-2"><button class="btn btn-success w-100">Cadastrar</button></div>
    </form>
  </div>

  <div class="card shadow p-4">
    <h5>Lista de endereços</h5>
    <table class="table table-striped">
      <tr><th>Endereço</th><th>Cidade</th></tr>
      <?php foreach($enderecos as $e){ echo "<tr><td>".$e['rua']."</td><td>".$e['cidade']."</td></tr>"; } ?>
    </table>
  </div>
</div>
</body>
</html>