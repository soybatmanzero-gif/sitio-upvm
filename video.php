<?php
require __DIR__ . '/config.php';
$titulo = 'Mi video';
include __DIR__ . '/includes/header.php';
?>
<h1 class="titulo" style="font-size:2.2rem">Mi video</h1>
<section class="card" style="max-width:900px;margin:1rem auto;text-align:center">
  <video controls playsinline preload="metadata" style="width:100%;border-radius:14px;background:#000">
    <source src="assets/video/mi_video.mp4" type="video/mp4">
    Tu navegador no soporta video HTML5.
  </video>
  <p style="margin-top:1rem"><a class="btn gris" href="index.php">← Volver al inicio</a></p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
