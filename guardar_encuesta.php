<?php
require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: encuesta.php'); exit; }

$carrera       = trim($_POST['carrera'] ?? '');
$cuatrimestre  = (int)($_POST['cuatrimestre'] ?? 0);
$profesores    = (int)($_POST['profesores'] ?? 0);
$instalaciones = trim($_POST['instalaciones'] ?? '');
$mejorar       = trim($_POST['mejorar'] ?? '');

$validas = ['Excelente', 'Bueno', 'Regular', 'Malo'];
if ($carrera === '' || $mejorar === '' || $cuatrimestre < 1 || $cuatrimestre > 11
    || $profesores < 1 || $profesores > 5 || !in_array($instalaciones, $validas, true)) {
    $_SESSION['error_encuesta'] = 'Por favor contesta todas las preguntas.';
    header('Location: encuesta.php'); exit;
}

try {
    $st = db(DB_ENCUESTA)->prepare(
        'INSERT INTO respuestas (carrera, cuatrimestre, profesores, instalaciones, mejorar) VALUES (?,?,?,?,?)');
    $st->execute([$carrera, $cuatrimestre, $profesores, $instalaciones, $mejorar]);
    $_SESSION['encuesta_ok'] = true;
    header('Location: regalo.php'); exit;
} catch (PDOException $ex) {
    $_SESSION['error_encuesta'] = 'No se pudo guardar tu respuesta. Intenta de nuevo.';
    header('Location: encuesta.php'); exit;
}
