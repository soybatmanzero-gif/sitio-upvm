<?php
require __DIR__ . '/config.php';
if (empty($_SESSION['encuesta_ok'])) { header('Location: encuesta.php'); exit; }
unset($_SESSION['encuesta_ok']);
$titulo = '¡Tu regalo!';
include __DIR__ . '/includes/header.php';
?>
<section class="card regalo">
  <h2 style="border:0;padding:0">¡Gracias por contestar la encuesta!</h2>
  <p>Tienes un regalo esperándote. Haz clic en la caja 🎁</p>
  <div class="caja" id="caja"><div class="tapa"></div><div class="base"></div></div>
  <div id="sorpresa">
    <h3>🎉 ¡Felicidades! 🎉</h3>
    <img src="assets/img/regalo.png" alt="Regalo" style="width:100%;max-width:320px;border-radius:20px;margin:.8rem 0;box-shadow:0 0 40px rgba(255,45,45,.35)">
    <p><strong>"No te debo nada porque estoy jodida"</strong></p>
    <p style="margin-top:1rem"><a class="btn" href="index.php">Volver al inicio</a></p>
  </div>
</section>
<script>
document.getElementById('caja').addEventListener('click', function () {
  this.classList.add('abierta');
  document.getElementById('sorpresa').style.display = 'block';
  const colores = ['#b3263b', '#ffd166', '#f6e9e9', '#8b1a2b', '#ff7b8a'];
  for (let i = 0; i < 70; i++) {
    const c = document.createElement('div');
    c.className = 'confeti';
    c.style.left = Math.random() * 100 + 'vw';
    c.style.background = colores[Math.floor(Math.random() * colores.length)];
    c.style.animationDuration = (2 + Math.random() * 3) + 's';
    document.body.appendChild(c);
    setTimeout(() => c.remove(), 5500);
  }
});
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
