<?php
require __DIR__ . '/../config.php';
$pdo = db(DB_SISTEMA); $msg = ''; $err = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $a = $_POST['accion'] ?? '';
        if ($a === 'crear') {                                   // CREATE
            $pdo->prepare('INSERT INTO usuarios (nombre,email,telefono) VALUES (?,?,?)')
                ->execute([trim($_POST['nombre']), trim($_POST['email']), trim($_POST['telefono'])]);
            $msg = 'Usuario creado correctamente.';
        } elseif ($a === 'actualizar') {                        // UPDATE
            $pdo->prepare('UPDATE usuarios SET nombre=?, email=?, telefono=?, activo=? WHERE id_usuario=?')
                ->execute([trim($_POST['nombre']), trim($_POST['email']), trim($_POST['telefono']),
                           isset($_POST['activo']) ? 1 : 0, (int)$_POST['id']]);
            $msg = 'Usuario actualizado.';
        } elseif ($a === 'eliminar') {                          // DELETE
            $pdo->prepare('DELETE FROM usuarios WHERE id_usuario=?')->execute([(int)$_POST['id']]);
            $msg = 'Usuario eliminado.';
        }
    }
} catch (PDOException $ex) {
    $err = $ex->getCode() == 23000
        ? 'No se pudo completar: correo duplicado o el usuario tiene ventas registradas.'
        : 'Error: ' . $ex->getMessage();
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM usuarios WHERE id_usuario=?');
    $st->execute([(int)$_GET['editar']]);
    $editar = $st->fetch() ?: null;
}
$filas = $pdo->query('SELECT * FROM usuarios ORDER BY id_usuario')->fetchAll();   // READ

$base = '../'; $titulo = 'Usuarios';
include __DIR__ . '/../includes/header.php';
?>
<h2>Usuarios</h2>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>

<section class="card">
  <h3><?= $editar ? 'Editar usuario #' . e($editar['id_usuario']) : 'Nuevo usuario' ?></h3>
  <form method="post">
    <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">
    <?php if ($editar): ?><input type="hidden" name="id" value="<?= e($editar['id_usuario']) ?>"><?php endif; ?>
    <div class="grid2">
      <div><label>Nombre</label><input type="text" name="nombre" required maxlength="100" value="<?= e($editar['nombre'] ?? '') ?>"></div>
      <div><label>Email</label><input type="email" name="email" required maxlength="150" value="<?= e($editar['email'] ?? '') ?>"></div>
      <div><label>Teléfono</label><input type="text" name="telefono" maxlength="20" value="<?= e($editar['telefono'] ?? '') ?>"></div>
      <?php if ($editar): ?>
      <div class="opciones"><label style="margin-top:2rem"><input type="checkbox" name="activo" <?= $editar['activo'] ? 'checked' : '' ?>> Activo</label></div>
      <?php endif; ?>
    </div>
    <p style="margin-top:1rem">
      <button class="btn" type="submit"><?= $editar ? 'Guardar cambios' : 'Crear usuario' ?></button>
      <?php if ($editar): ?><a class="btn sec" href="usuarios.php">Cancelar</a><?php endif; ?>
    </p>
  </form>
</section>

<section class="card tabla-wrap">
  <table>
    <tr><th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Activo</th><th>Acciones</th></tr>
    <?php foreach ($filas as $f): ?>
    <tr>
      <td><?= e($f['id_usuario']) ?></td><td><?= e($f['nombre']) ?></td><td><?= e($f['email']) ?></td>
      <td><?= e($f['telefono']) ?></td><td><?= $f['activo'] ? 'Sí' : 'No' ?></td>
      <td class="acciones">
        <a class="btn chico" href="?editar=<?= $f['id_usuario'] ?>">Editar</a>
        <form method="post" onsubmit="return confirm('¿Eliminar este usuario?')">
          <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $f['id_usuario'] ?>">
          <button class="btn chico sec" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<p><a class="btn sec" href="index.php">← Volver al menú CRUD</a></p>
<?php include __DIR__ . '/../includes/footer.php'; ?>
