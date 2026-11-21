<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome'])) {
  $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");
  $stmt->execute([$_POST['nome'], $_POST['preco']]);
  header('Location: produtos.php'); exit;
}
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Produtos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🚚 Logístico</a>
    <div class="navbar-nav">
      <a class="nav-link" href="index.php">Início</a>
      <a class="nav-link active" href="produtos.php">Produtos</a>
      <a class="nav-link" href="enderecos.php">Endereços</a>
      <a class="nav-link" href="transportadoras.php">Transportadoras</a>
      <a class="nav-link" href="frete.php">Gerar Frete</a>
      <a class="nav-link" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2>📦 Produtos</h2>

  <div class="card shadow p-4 mb-4">
    <h5>Cadastrar produto novo</h5>
    <form method="post" class="row g-2">
      <div class="col-md-6"><input name="nome" class="form-control" placeholder="Nome do produto" required></div>
      <div class="col-md-4"><input name="preco" type="number" step="0.01" class="form-control" placeholder="Preço R$" required></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Cadastrar</button></div>
    </form>
  </div>

  <div class="card shadow p-4">
    <h5>Lista de produtos</h5>
    <table class="table table-striped">
      <tr><th>Nome</th><th>Preço</th></tr>
      <?php foreach($produtos as $p){ echo "<tr><td>".$p['nome']."</td><td>R$ ".number_format($p['preco'],2,',','.')."</td></tr>"; } ?>
    </table>
  </div>
</div>
</body>
</html>