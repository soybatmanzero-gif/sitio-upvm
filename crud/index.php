<?php
require __DIR__ . '/../config.php';
$base = '../'; $titulo = 'CRUD - Sistema de ventas';
include __DIR__ . '/../includes/header.php';
?>
<h2>CRUD · Sistema de ventas</h2>
<div class="menu-crud">
  <a href="usuarios.php"><div class="card"><h3>👤 Usuarios</h3><p>Crear, ver, editar y eliminar</p></div></a>
  <a href="productos.php"><div class="card"><h3>📦 Productos</h3><p>Crear, ver, editar y eliminar</p></div></a>
  <a href="ventas.php"><div class="card"><h3>🧾 Ventas</h3><p>Crear, ver, editar y eliminar</p></div></a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
