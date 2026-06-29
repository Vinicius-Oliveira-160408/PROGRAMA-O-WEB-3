<?php
// $paginaAtual deve ser definido na página antes de incluir este header
$paginaAtual = $paginaAtual ?? '';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRUD Mundo<?= isset($tituloPagina) ? ' · ' . htmlspecialchars($tituloPagina) : '' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="index.php">🌎 CRUD Mundo</a>
            <nav class="nav">
                <a href="index.php" class="<?= $paginaAtual === 'index' ? 'ativo' : '' ?>">Início</a>
                <a href="continentes.php" class="<?= $paginaAtual === 'continentes' ? 'ativo' : '' ?>">Continentes</a>
                <a href="paises.php" class="<?= $paginaAtual === 'paises' ? 'ativo' : '' ?>">Países</a>
                <a href="cidades.php" class="<?= $paginaAtual === 'cidades' ? 'ativo' : '' ?>">Cidades</a>
                <a href="governantes.php" class="<?= $paginaAtual === 'governantes' ? 'ativo' : '' ?>">Governantes</a>
            </nav>
        </div>
    </header>
    <main class="container">
