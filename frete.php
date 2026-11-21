<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
$pdo = new PDO("mysql:host=localhost;dbname=gerenciador_logistico;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll(PDO::FETCH_ASSOC);
$enderecos = $pdo->query("SELECT * FROM enderecos")->fetchAll(PDO::FETCH_ASSOC);
$transportadores = $pdo->query("SELECT * FROM transportadores")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Gerar Frete</title>
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
      <a class="nav-link active" href="frete.php">Gerar Frete</a>
      <a class="nav-link" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h2>💰 Gerar Frete</h2>

  <div class="card shadow p-4">
    <form method="post" action="historico.php">
      <div class="mb-3">
        <label class="form-label">📦 Produto</label>
        <select name="produto" class="form-select" required>
          <option value="">Selecione o produto</option>
          <?php foreach($produtos as $p){ echo "<option>".$p['nome']."</option>"; } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">📍 Endereço de entrega</label>
        <select name="endereco" class="form-select" required>
          <option value="">Selecione o endereço</option>
          <?php foreach($enderecos as $e){ echo "<option>".$e['rua']." - ".$e['cidade']."</option>"; } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">🚚 Transportadora</label>
        <select name="transportadora" class="form-select" required>
          <option value="">Selecione a transportadora</option>
          <?php foreach($transportadores as $t){ echo "<option>".$t['nome']." - ".$t['placa']."</option>"; } ?>
        </select>
      </div>

      <button type="submit" class="btn btn-info w-100 btn-lg">Gerar Frete 🚀</button>
    </form>
  </div>
</div>
</body>
</html>