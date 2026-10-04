<?php
$base = $base ?? ''; $titulo = $titulo ?? 'Mi sitio';
require_once __DIR__ . '/auth.php';
$u = usuario_actual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?></title>
  <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= $base ?>index.php">
    <img src="<?= $base ?>assets/img/foto.png" alt="Foto de Yuleang" class="foto">
    <span><strong>Yuleang Ixchel</strong><small>UPVM</small></span>
  </a>
  <nav class="pill">
    <a href="<?= $base ?>index.php">Inicio</a>
    <a href="<?= $base ?>encuesta.php">Encuesta</a>
    <a href="<?= $base ?>crud/index.php">CRUD</a>
    <a href="<?= $base ?>index.php#ubicacion">Ubicación</a>
  </nav>
  <div class="sesion">
    <?php if ($u): ?>
      <span class="hola">Hola, <?= e($u['nombre']) ?></span>
      <a class="btn chico sec" href="<?= $base ?>logout.php">Salir</a>
    <?php else: ?>
      <a class="btn chico" href="<?= $base ?>login.php">Registrarme</a>
    <?php endif; ?>
  </div>
</header>
<main class="contenedor">
