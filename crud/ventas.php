<?php
require __DIR__ . '/../config.php';
$pdo = db(DB_SISTEMA); $msg = ''; $err = '';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $a = $_POST['accion'] ?? '';
        $pdo->beginTransaction();

        if ($a === 'crear') {                                   // CREATE
            $idp = (int)$_POST['id_producto']; $cant = (int)$_POST['cantidad'];
            $st = $pdo->prepare('SELECT precio FROM productos WHERE id_producto=?'); $st->execute([$idp]);
            $precio = $st->fetchColumn();
            if ($precio === false) throw new RuntimeException('Producto no encontrado.');
            // Descuenta stock (el CHECK de la tabla evita que quede negativo)
            $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto=?')->execute([$cant, $idp]);
            // Los totales los calcula el TRIGGER
            $pdo->prepare('INSERT INTO ventas (id_usuario,id_producto,cantidad,precio_unit,tasa_impuesto) VALUES (?,?,?,?,?)')
                ->execute([(int)$_POST['id_usuario'], $idp, $cant, $precio, $_POST['tasa_impuesto']]);
            $msg = 'Venta registrada.';

        } elseif ($a === 'actualizar') {                        // UPDATE (cantidad e impuesto)
            $id = (int)$_POST['id']; $nueva = (int)$_POST['cantidad'];
            $st = $pdo->prepare('SELECT id_producto, cantidad FROM ventas WHERE id_venta=?'); $st->execute([$id]);
            $v = $st->fetch();
            if (!$v) throw new RuntimeException('Venta no encontrada.');
            $dif = $nueva - (int)$v['cantidad'];
            $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto=?')->execute([$dif, $v['id_producto']]);
            $pdo->prepare('UPDATE ventas SET id_usuario=?, cantidad=?, tasa_impuesto=? WHERE id_venta=?')
                ->execute([(int)$_POST['id_usuario'], $nueva, $_POST['tasa_impuesto'], $id]);
            $msg = 'Venta actualizada.';

        } elseif ($a === 'eliminar') {                          // DELETE (regresa el stock)
            $id = (int)$_POST['id'];
            $st = $pdo->prepare('SELECT id_producto, cantidad FROM ventas WHERE id_venta=?'); $st->execute([$id]);
            if ($v = $st->fetch()) {
                $pdo->prepare('UPDATE productos SET stock = stock + ? WHERE id_producto=?')->execute([$v['cantidad'], $v['id_producto']]);
                $pdo->prepare('DELETE FROM ventas WHERE id_venta=?')->execute([$id]);
            }
            $msg = 'Venta eliminada.';
        }
        $pdo->commit();
    }
} catch (RuntimeException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $err = $ex->getMessage();
} catch (PDOException $ex) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $err = 'No se pudo completar (¿stock insuficiente?).';
}

$editar = null;
if (isset($_GET['editar'])) {
    $st = $pdo->prepare('SELECT * FROM ventas WHERE id_venta=?'); $st->execute([(int)$_GET['editar']]);
    $editar = $st->fetch() ?: null;
}
$usuarios  = $pdo->query('SELECT id_usuario, nombre FROM usuarios WHERE activo=1 ORDER BY nombre')->fetchAll();
$productos = $pdo->query('SELECT id_producto, nombre, precio, stock FROM productos WHERE activo=1 ORDER BY nombre')->fetchAll();
$filas = $pdo->query(                                                                   // READ
    'SELECT v.*, u.nombre AS usuario, p.nombre AS producto
       FROM ventas v JOIN usuarios u ON u.id_usuario=v.id_usuario JOIN productos p ON p.id_producto=v.id_producto
      ORDER BY v.id_venta')->fetchAll();

$base = '../'; $titulo = 'Ventas';
include __DIR__ . '/../includes/header.php';
?>
<h2>Ventas</h2>
<?php if ($msg): ?><div class="msg ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>

<section class="card">
  <h3><?= $editar ? 'Editar venta #' . e($editar['id_venta']) : 'Nueva venta' ?></h3>
  <form method="post">
    <input type="hidden" name="accion" value="<?= $editar ? 'actualizar' : 'crear' ?>">
    <?php if ($editar): ?><input type="hidden" name="id" value="<?= e($editar['id_venta']) ?>"><?php endif; ?>
    <div class="grid2">
      <div>
        <label>Usuario</label>
        <select name="id_usuario" required>
          <?php foreach ($usuarios as $u): ?>
          <option value="<?= $u['id_usuario'] ?>" <?= ($editar['id_usuario'] ?? 0) == $u['id_usuario'] ? 'selected' : '' ?>><?= e($u['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label>Producto</label>
        <select name="id_producto" required <?= $editar ? 'disabled' : '' ?>>
          <?php foreach ($productos as $p): ?>
          <option value="<?= $p['id_producto'] ?>" <?= ($editar['id_producto'] ?? 0) == $p['id_producto'] ? 'selected' : '' ?>>
            <?= e($p['nombre']) ?> ($<?= number_format($p['precio'], 2) ?> · stock <?= $p['stock'] ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <div><label>Cantidad</label><input type="number" name="cantidad" min="1" required value="<?= e($editar['cantidad'] ?? 1) ?>"></div>
      <div><label>Tasa de impuesto (%)</label><input type="number" name="tasa_impuesto" step="0.01" min="0" required value="<?= e($editar['tasa_impuesto'] ?? '16.00') ?>"></div>
    </div>
    <p style="margin-top:1rem">
      <button class="btn" type="submit"><?= $editar ? 'Guardar cambios' : 'Registrar venta' ?></button>
      <?php if ($editar): ?><a class="btn sec" href="ventas.php">Cancelar</a><?php endif; ?>
    </p>
  </form>
</section>

<section class="card tabla-wrap">
  <table>
    <tr><th>ID</th><th>Usuario</th><th>Producto</th><th>Cant.</th><th>Precio</th><th>Bruto</th><th>IVA</th><th>Total</th><th>Fecha</th><th>Acciones</th></tr>
    <?php foreach ($filas as $f): ?>
    <tr>
      <td><?= $f['id_venta'] ?></td><td><?= e($f['usuario']) ?></td><td><?= e($f['producto']) ?></td><td><?= $f['cantidad'] ?></td>
      <td>$<?= number_format($f['precio_unit'], 2) ?></td><td>$<?= number_format($f['total_bruto'], 2) ?></td>
      <td>$<?= number_format($f['total_impuesto'], 2) ?></td><td><strong>$<?= number_format($f['total_final'], 2) ?></strong></td>
      <td><?= e($f['created_at']) ?></td>
      <td class="acciones">
        <a class="btn chico" href="?editar=<?= $f['id_venta'] ?>">Editar</a>
        <form method="post" onsubmit="return confirm('¿Eliminar esta venta?')">
          <input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $f['id_venta'] ?>">
          <button class="btn chico sec" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</section>
<p><a class="btn sec" href="index.php">← Volver al menú CRUD</a></p>
<?php include __DIR__ . '/../includes/footer.php'; ?>
