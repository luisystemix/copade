<?php
// BASE_URL: Cambiar según el entorno de despliegue
// - Local (Laragon con carpeta 'copades'): '/copades/'
// - Hosting (raíz del dominio, ej: https://fundacioncopades.org/): '/'
defined('BASE_URL') || define('BASE_URL', '/copade/');

// REDES SOCIALES - Modificar URLs aquí
defined('FACEBOOK_URL') || define('FACEBOOK_URL', 'https://www.facebook.com/profile.php?id=61590455641354');
defined('INSTAGRAM_URL') || define('INSTAGRAM_URL', '#');
defined('YOUTUBE_URL') || define('YOUTUBE_URL', '#');

// BASE DE DATOS
defined('DB_HOST') || define('DB_HOST', 'localhost');
defined('DB_PORT') || define('DB_PORT', '3307');
defined('DB_NAME') || define('DB_NAME', 'copade_db');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

// ZONA HORARIA
date_default_timezone_set('America/La_Paz');

// INICIAR SESIÓN SI NO ESTÁ INICIADA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>