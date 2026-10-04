# Sitio UPVM – CRUD, encuesta, registro y mapas de Google

Sitio en PHP + MySQL de Yuleang Ixchel Arteaga Ley (UPVM).

- `index.php` portada con mapa iframe y mapa interactivo (Google Maps JavaScript API)
- `login.php` registro / inicio de sesión de visitantes
- `encuesta.php` encuesta de 5 preguntas sobre la UPVM → `regalo.php`
- `crud/` CRUD de usuarios, productos y ventas
- `sql/` scripts de base de datos (`nube_todo.sql` crea todas las tablas)

## Configuración (sin contraseñas en el código)
Los datos se leen de variables de entorno: `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_SSL`, `MAPS_API_KEY`.
En local se pueden poner en `config.local.php` (ver `config.local.example.php`), que no se sube al repositorio.

## Despliegue
`Dockerfile` listo para Render (PHP 8.3 + Apache).
