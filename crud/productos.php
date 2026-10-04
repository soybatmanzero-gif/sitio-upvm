<?php
require __DIR__ . '/../config.php';
$pdo = db(DB_SISTEMA); $msg = ''; $err = '';
$carpeta = __DIR__ . '/../imagenes_productos/';

// Guarda la imagen subida y devuelve el nombre del archivo (o null si no se subió)
function subirImagen(string $carpeta): ?string {
    if (empty($_FILES['imagen']['name']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) return null;
    $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) throw new RuntimeException('Formato de imagen no permitido.');
    $nombre = uniqid('prod_') . '.' . $ext;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombre);
    return $nombre;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $a = $_POST['accion'] ?? '';
        if ($a === 'crear') {                                   // CREATE
            $img = subirImagen($carpeta);
            $pdo->prepare('INSERT INTO productos (nombre,descripcion,precio,stock,imagen) VALUES (?,?,?,?,?)')
                ->execute([trim($_POST['nombre']), trim($_POST['descripcion']), $_POST['precio'], (int)$_POST['stock'], $img]);
            $msg = 'Producto creado.';
        } elseif ($a === 'actualizar') {                        // UPDATE
            $img = subirImagen($carpeta);
            $sql = 'UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, activo=?' . ($img ? ', imagen=?' : '') . ' WHERE id_producto=?';
            $p = [trim($_POST['nombre']), trim($_POST['descripcion']), $_POST['precio'], (int)$_POST['stock'], isset($_POST['activo']) ? 1 : 0];
            if ($img) $p[] = $img;
            $p[] = (int)$_POST['id'];
            $pdo->prepare($sql)->execute($p);
            $msg = 'Producto actualizado.';
        } elseif ($a === 'eliminar') {                          // DELETE
            $pdo->prepare('DELETE FROM productos WHERE id_producto=?')->execute([(int)$_POST['id']]);
            $msg = 'Producto eliminado.';
        }
    }
} catch (RuntimeException $ex) {
    $err = $ex->getMessage();
} catch (PDOException $ex) {
    $err = $ex->getCode() == 23000
        ? 'No se puede eliminar: el producto tiene ventas registradas.'
        : 'Error: ' . $ex->getMessage();
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM productos WHERE id_producto=?');
    $st->execute([(int)$_GET['editar']]);
    $editar = $st->fetch() ?: null;
}
$filas = $pdo->query('SELECT * FROM productos ORDER BY id_producto')->fetchAll();   // READ

$base = '../'; $titulo = 'Productos';
include __DIR__ . '/../includes/header.php';
?>
<h2>Productos</h2>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>

<section class="card">
  <h3><?= $editar ? 'Editar producto #' . e($editar['id_producto']) : 'Nuevo producto' ?></h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">
    <?php if ($editar): ?><input type="hidden" name="id" value="<?= e($editar['id_producto']) ?>"><?php endif; ?>
    <div class="grid2">
      <div><label>Nombre</label><input type="text" name="nombre" required maxlength="150" value="<?= e($editar['nombre'] ?? '') ?>"></div>
      <div><label>Precio</label><input type="number" name="precio" step="0.01" min="0" required value="<?= e($editar['precio'] ?? '') ?>"></div>
      <div><label>Stock</label><input type="number" name="stock" min="0" required value="<?= e($editar['stock'] ?? 0) ?>"></div>
      <div><label>Imagen</label><input type="file" name="imagen" accept="image/*"></div>
    </div>
    <label>Descripción</label><textarea name="descripcion" rows="3"><?= e($editar['descripcion'] ?? '') ?></textarea>
    <?php if ($editar): ?><div class="opciones"><label><input type="checkbox" name="activo" <?= $editar['activo'] ? 'checked' : '' ?>> Activo</label></div><?php endif; ?>
    <p style="margin-top:1rem">
      <button class="btn" type="submit"><?= $editar ? 'Guardar cambios' : 'Crear producto' ?></button>
      <?php if ($editar): ?><a class="btn sec" href="productos.php">Cancelar</a><?php endif; ?>
    </p>
  </form>
</section>

<section class="card tabla-wrap">
  <table>
    <tr><th>ID</th><th>Img</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Activo</th><th>Acciones</th></tr>
    <?php foreach ($filas as $f): ?>
    <tr>
      <td><?= e($f['id_producto']) ?></td>
      <td><?php if ($f['imagen']): ?><img class="thumb" src="../imagenes_productos/<?= e($f['imagen']) ?>" alt=""><?php endif; ?></td>
      <td><?= e($f['nombre']) ?></td><td>$<?= number_format($f['precio'], 2) ?></td>
      <td><?= e($f['stock']) ?></td><td><?= $f['activo'] ? 'Sí' : 'No' ?></td>
      <td class="acciones">
        <a class="btn chico" href="?editar=<?= $f['id_producto'] ?>">Editar</a>
        <form method="post" onsubmit="return confirm('¿Eliminar este producto?')">
          <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $f['id_producto'] ?>">
          <button class="btn chico sec" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<p><a class="btn sec" href="index.php">← Volver al menú CRUD</a></p>
<?php include __DIR__ . '/../includes/footer.php'; ?>
