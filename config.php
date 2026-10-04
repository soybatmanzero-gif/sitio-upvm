<?php
// ============ CONFIGURACIÓN ============
// Este archivo NO contiene contraseñas. Los datos se leen de:
//   1) variables de entorno (Render), o
//   2) config.local.php (solo en tu computadora; está en .gitignore)
session_start();

$GLOBALS['__local'] = is_file(__DIR__ . '/config.local.php') ? (require __DIR__ . '/config.local.php') : [];

function cfg(string $clave, string $defecto = ''): string {
    $v = getenv($clave);
    if ($v !== false && $v !== '') return $v;
    return (string)($GLOBALS['__local'][$clave] ?? $defecto);
}

define('DB_HOST',     cfg('DB_HOST', 'localhost'));
define('DB_PORT',     cfg('DB_PORT', '3306'));
define('DB_USER',     cfg('DB_USER', 'root'));
define('DB_PASS',     cfg('DB_PASS', ''));
// Si solo hay una base (Railway), define DB_NAME y se usa para las dos
define('DB_SISTEMA',  cfg('DB_SISTEMA',  cfg('DB_NAME', 'sistema')));
define('DB_ENCUESTA', cfg('DB_ENCUESTA', cfg('DB_NAME', 'encuesta')));

define('MAPS_API_KEY', cfg('MAPS_API_KEY', 'PEGA_AQUI_TU_API_KEY'));
define('MAPA_LAT',    (float) cfg('MAPA_LAT', '19.6745'));
define('MAPA_LNG',    (float) cfg('MAPA_LNG', '-99.1640'));
define('MAPA_NOMBRE', cfg('MAPA_NOMBRE', 'Universidad Politécnica del Valle de México'));

function db(string $nombre): PDO {
    static $conexiones = [];
    if (!isset($conexiones[$nombre])) {
        $opciones = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        // Bases en la nube (Aiven, etc.) exigen conexión cifrada: DB_SSL=1
        if (cfg('DB_SSL') === '1') {
            $ca = cfg('DB_SSL_CA');   // ruta a ca.pem (solo necesaria en Windows)
            if ($ca === '' || !is_file($ca)) {
                foreach (['/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt'] as $p) {
                    if (is_file($p)) { $ca = $p; break; }
                }
            }
            if ($ca !== '' && is_file($ca)) $opciones[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            $opciones[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
        $conexiones[$nombre] = new PDO(
            'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . $nombre . ';charset=utf8mb4',
            DB_USER, DB_PASS, $opciones
        );
    }
    return $conexiones[$nombre];
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
