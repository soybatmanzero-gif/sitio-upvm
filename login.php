<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/auth.php';

if (usuario_actual()) { header('Location: index.php'); exit; }

$modo = (($_GET['modo'] ?? 'registro') === 'login') ? 'login' : 'registro';
$err = '';
$vals = ['nombre' => '', 'edad' => '', 'correo' => '', 'sexo' => ''];
$sexos = ['Femenino', 'Masculino', 'Otro', 'Prefiero no decirlo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = db(DB_ENCUESTA);
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'registro') {
        $modo = 'registro';
        $vals = ['nombre' => trim($_POST['nombre'] ?? ''), 'edad' => (int)($_POST['edad'] ?? 0),
                 'correo' => trim($_POST['correo'] ?? ''), 'sexo' => $_POST['sexo'] ?? ''];
        if (mb_strlen($vals['nombre']) < 2 || mb_strlen($vals['nombre']) > 100) {
            $err = 'Escribe tu nombre completo.';
        } elseif ($vals['edad'] < 10 || $vals['edad'] > 120) {
            $err = 'Escribe una edad válida.';
        } elseif (!filter_var($vals['correo'], FILTER_VALIDATE_EMAIL)) {
            $err = 'Escribe un correo válido.';
        } elseif (!in_array($vals['sexo'], $sexos, true)) {
            $err = 'Selecciona una opción de sexo.';
        } else {
            try {
                $pdo->prepare('INSERT INTO registros (nombre, edad, correo, sexo) VALUES (?,?,?,?)')
                    ->execute([$vals['nombre'], $vals['edad'], $vals['correo'], $vals['sexo']]);
                iniciar_sesion(['id_registro' => $pdo->lastInsertId(), 'nombre' => $vals['nombre']]);
                header('Location: index.php'); exit;
            } catch (PDOException $ex) {
                if ($ex->getCode() == 23000) { $err = 'Ese correo ya está registrado. Inicia sesión.'; $modo = 'login'; }
                else { $err = 'No se pudo registrar. Intenta de nuevo.'; }
            }
        }
    } elseif ($accion === 'login') {
        $modo = 'login';
        $correo = trim($_POST['correo'] ?? '');
        $st = $pdo->prepare('SELECT * FROM registros WHERE correo = ?');
        $st->execute([$correo]);
        if ($r = $st->fetch()) { iniciar_sesion($r); header('Location: index.php'); exit; }
        $err = 'No encontramos ese correo. Regístrate primero.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $modo === 'login' ? 'Iniciar sesión' : 'Registro' ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth">
<div class="auth-wrap">
  <section class="auth-izq">
    <a class="auth-brand" href="index.php">
      <img src="assets/img/foto.png" alt="Foto de Yuleang" class="foto">
      <span><strong>Yuleang Ixchel</strong><small>UPVM</small></span>
    </a>
    <div>
      <h1>Comencemos</h1>
      <p>Regístrate para formar parte del sitio, contestar la encuesta de la UPVM y recibir tu regalo sorpresa.</p>
    </div>
  </section>

  <section class="auth-der">
    <div class="tabs">
      <a href="login.php?modo=registro" class="<?= $modo === 'registro' ? 'act' : '' ?>">Registro</a>
      <a href="login.php?modo=login" class="<?= $modo === 'login' ? 'act' : '' ?>">Iniciar sesión</a>
    </div>
    <?php if ($err): ?><div class="msg err"><?= e($err) ?></div><?php endif; ?>

    <?php if ($modo === 'registro'): ?>
    <form method="post">
      <input type="hidden" name="accion" value="registro">
      <input type="text" name="nombre" placeholder="Tu nombre" required maxlength="100" value="<?= e($vals['nombre']) ?>">
      <input type="number" name="edad" placeholder="Tu edad" required min="10" max="120" value="<?= e($vals['edad'] ?: '') ?>">
      <input type="email" name="correo" placeholder="Tu correo" required maxlength="150" value="<?= e($vals['correo']) ?>">
      <select name="sexo" required>
        <option value="">Sexo</option>
        <?php foreach ($sexos as $s): ?><option <?= $vals['sexo'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
      </select>
      <button class="btn" type="submit">Registrarme</button>
      <p class="aviso">¿Ya tienes cuenta? <a href="login.php?modo=login">Inicia sesión aquí</a></p>
    </form>
    <?php else: ?>
    <form method="post">
      <input type="hidden" name="accion" value="login">
      <input type="email" name="correo" placeholder="Tu correo registrado" required>
      <button class="btn" type="submit">Entrar</button>
      <p class="aviso">¿Aún no te registras? <a href="login.php?modo=registro">Crea tu cuenta</a></p>
    </form>
    <?php endif; ?>
  </section>
</div>
</body>
</html>
