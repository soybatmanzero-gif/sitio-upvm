<?php
require __DIR__ . '/config.php';
$titulo = 'Inicio';
include __DIR__ . '/includes/header.php';
$q = urlencode(MAPA_NOMBRE);
?>
<!-- ================= HERO ================= -->
<section class="hero-grid">
  <div class="hero-texto">
    <span class="chip">✦ Estudiante UPVM</span>
    <h1 class="titulo">Hola, soy<br>Yuleang <span class="grad">Ixchel</span></h1>
    <p class="sub">Estudio en la Universidad Politécnica del Valle de México. Aquí encontrarás mi CRUD, una encuesta sobre la UPVM y mi ubicación en el mapa.</p>
    <div class="botones">
      <a class="btn gris" href="crud/index.php">Ver CRUD</a>
      <a class="btn" href="encuesta.php">Contestar encuesta</a>
      <a class="btn sec" href="video.php">▶ Ver mi video</a>
    </div>
    <div class="stats">
      <div class="stat"><span>Encuesta</span><b>5</b><small>preguntas</small></div>
      <div class="stat"><span>CRUD</span><b>3</b><small>tablas</small></div>
      <div class="stat"><span>Mapas</span><b>2</b><small>de Google</small></div>
    </div>
  </div>
  <div class="hero-art">
    <img src="assets/img/hero.png" alt="Ilustración principal">
    <div class="mini">
      <img src="assets/img/foto.png" alt="Mi foto" class="foto">
      <div><strong>Yuleang Ixchel Arteaga Ley</strong><small>UPVM</small></div>
    </div>
  </div>
</section>

<!-- ================= ACERCA ================= -->
<section class="about">
  <span class="chip">• Acerca del sitio</span>
  <p>Soy estudiante de la <em>UPVM</em> y este sitio reúne un <em>CRUD de ventas</em>, una <em>encuesta</em> sobre la universidad y <em>mapas de Google</em> para llegar a ella.</p>
</section>

<!-- ================= SECCIONES ================= -->
<section class="servicios">
  <a class="srv" href="encuesta.php"><span class="num">(01)</span><h3>Encuesta UPVM</h3><p>Cuéntanos qué opinas de profesores e instalaciones.</p><div class="tags"><span>5 preguntas</span><span>Regalo final</span></div></a>
  <a class="srv" href="crud/index.php"><span class="num">(02)</span><h3>CRUD de ventas</h3><p>Crea, consulta, edita y elimina usuarios, productos y ventas.</p><div class="tags"><span>Usuarios</span><span>Productos</span><span>Ventas</span></div></a>
  <a class="srv" href="#ubicacion"><span class="num">(03)</span><h3>Ubicación</h3><p>Mapa incrustado y mapa interactivo con marcador.</p><div class="tags"><span>iframe</span><span>JavaScript</span></div></a>
</section>

<!-- ============ ETAPA 1: MAPA CON IFRAME ============ -->
<section class="card" id="ubicacion">
  <h2>Ubicación</h2>
  <p style="margin-bottom:1rem;color:var(--suave)">Así puedes llegar a la UPVM:</p>
  <!-- Sustituye el src por tu propio enlace: Google Maps > Compartir > Insertar un mapa > Copiar HTML -->
  <iframe class="mapa"
    src="https://www.google.com/maps?q=<?= $q ?>&output=embed"
    allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"
    title="Mapa de la ubicación"></iframe>
</section>

<!-- ============ ETAPA 3: MAPA INTERACTIVO CON JAVASCRIPT ============ -->
<section class="card" id="como-llegar">
  <h2>Cómo llegar (mapa interactivo)</h2>
  <div id="mapa-js" class="mapa"
       data-lat="<?= MAPA_LAT ?>" data-lng="<?= MAPA_LNG ?>" data-nombre="<?= e(MAPA_NOMBRE) ?>"></div>
</section>

<script src="assets/js/mapa.js"></script>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=<?= e(MAPS_API_KEY) ?>&callback=initMap"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
