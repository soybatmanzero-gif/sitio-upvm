<?php
// Funciones de sesión para el registro / login de visitantes
function usuario_actual(): ?array { return $_SESSION['visitante'] ?? null; }

function iniciar_sesion(array $r): void {
    session_regenerate_id(true);
    $_SESSION['visitante'] = ['id' => $r['id_registro'], 'nombre' => $r['nombre']];
}

// Úsala al inicio de cualquier página que quieras proteger:  requerir_login($base);
function requerir_login(string $base = ''): void {
    if (!usuario_actual()) { header('Location: ' . $base . 'login.php?modo=login'); exit; }
}
