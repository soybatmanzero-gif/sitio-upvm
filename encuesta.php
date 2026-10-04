<?php
require __DIR__ . '/config.php';
$titulo = 'Encuesta UPVM';
include __DIR__ . '/includes/header.php';
$error = $_SESSION['error_encuesta'] ?? ''; unset($_SESSION['error_encuesta']);
?>
<section class="card">
  <h2>Encuesta sobre la UPVM</h2>
  <p style="color:var(--suave)">Son solo 5 preguntas, ¡gracias por participar!</p>
  <?php if ($error): ?><div class="msg err"><?= e($error) ?></div><?php endif; ?>

  <form action="guardar_encuesta.php" method="post">
    <label for="carrera">1. ¿Qué carrera estudias?</label>
    <select name="carrera" id="carrera" required>
      <option value="">-- Selecciona --</option>
      <option>Ingeniería en Software</option>
      <option>Ingeniería en Biotecnología</option>
      <option>Ingeniería en Mecatrónica</option>
      <option>Ingeniería en Sistemas Automotrices</option>
      <option>Licenciatura en Administración y Gestión Empresarial</option>
      <option>Otra</option>
    </select>

    <label for="cuatrimestre">2. ¿En qué cuatrimestre vas?</label>
    <select name="cuatrimestre" id="cuatrimestre" required>
      <option value="">-- Selecciona --</option>
      <?php for ($i = 1; $i <= 11; $i++): ?><option value="<?= $i ?>"><?= $i ?>°</option><?php endfor; ?>
    </select>

    <label for="profesores">3. ¿Cómo calificas a tus profesores? (1 = muy mal, 5 = excelente)</label>
    <select name="profesores" id="profesores" required>
      <option value="">-- Selecciona --</option>
      <option value="5">5 - Excelente</option>
      <option value="4">4 - Bueno</option>
      <option value="3">3 - Regular</option>
      <option value="2">2 - Malo</option>
      <option value="1">1 - Muy malo</option>
    </select>

    <label>4. ¿Cómo consideras el estado de las instalaciones (aulas, laboratorios)?</label>
    <div class="opciones">
      <label><input type="radio" name="instalaciones" value="Excelente" required> Excelente</label>
      <label><input type="radio" name="instalaciones" value="Bueno"> Bueno</label>
      <label><input type="radio" name="instalaciones" value="Regular"> Regular</label>
      <label><input type="radio" name="instalaciones" value="Malo"> Malo</label>
    </div>

    <label for="mejorar">5. ¿Qué área mejorarías primero?</label>
    <select name="mejorar" id="mejorar" required>
      <option value="">-- Selecciona --</option>
      <option>Laboratorios y equipo</option>
      <option>Biblioteca</option>
      <option>Cafetería</option>
      <option>Internet / Wi-Fi</option>
      <option>Transporte y acceso</option>
      <option>Áreas deportivas</option>
    </select>

    <p style="margin-top:1.4rem"><button class="btn" type="submit">Enviar encuesta</button></p>
  </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
