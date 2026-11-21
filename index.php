<?php
session_start();
if (!isset($_SESSION['logado'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Início - Gerenciador Logístico</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">🚚 Logístico</a>
    <div class="navbar-nav">
      <a class="nav-link active" href="index.php">Início</a>
      <a class="nav-link" href="produtos.php">Produtos</a>
      <a class="nav-link" href="enderecos.php">Endereços</a>
      <a class="nav-link" href="transportadoras.php">Transportadoras</a>
      <a class="nav-link" href="frete.php">Gerar Frete</a>
      <a class="nav-link" href="historico.php">Histórico</a>
      <a class="nav-link" href="logout.php">Sair</a>
    </div>
  </div>
</nav>

<div class="container">
  <h1>Bem-vindo ao Gerenciador Logístico!</h1>
  <p>Escolha uma opção no menu acima para começar.</p>

  <div class="row mt-4">
    <div class="col-md-4"><a href="produtos.php" class="btn btn-primary w-100">📦 Produtos</a></div>
    <div class="col-md-4"><a href="enderecos.php" class="btn btn-success w-100">📍 Endereços</a></div>
    <div class="col-md-4"><a href="transportadoras.php" class="btn btn-warning w-100">🚚 Transportadoras</a></div>
  </div>
  <div class="row mt-3">
    <div class="col-md-6"><a href="frete.php" class="btn btn-info w-100">💰 Gerar Frete</a></div>
    <div class="col-md-6"><a href="historico.php" class="btn btn-secondary w-100">📋 Histórico</a></div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>