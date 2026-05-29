<?php include __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle : 'Fundación COPADES'; ?></title>

<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='45' fill='%23009879'/><text x='50' y='62' font-family='Arial' font-size='40' font-weight='bold' fill='white' text-anchor='middle'>C</text></svg>">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
<div class="container nav-container">

<a href="<?= BASE_URL ?>" class="logo">
<img src="<?= BASE_URL ?>assets/images/logo-horizontal2.png" alt="Logo COPADES">
</a>

<nav>
<ul class="menu" id="menu">
<li><a href="<?= BASE_URL ?>">Inicio</a></li>
<li class="has-submenu">
    <a href="<?= BASE_URL ?>pages/quienessomos.php">¿Quienes Somos?</a>
    <ul class="submenu">
        <li><a href="<?= BASE_URL ?>pages/mision.php">Misión</a></li>
        <li><a href="<?= BASE_URL ?>pages/vision.php">Visión</a></li>
        <li><a href="<?= BASE_URL ?>pages/valores.php">Valores</a></li>
    </ul>
</li>
<!-- <li><a href="<?= BASE_URL ?>pages/nosotros.php">¿Quienes Somos?</a></li> -->
<li class="has-submenu">
    <a href="<?= BASE_URL ?>pages/ejesestrategicos.php">Ejes Estratégicos</a>
    <ul class="submenu">
        <li><a href="<?= BASE_URL ?>pages/gobernanza.php">Gobernanzas y Cohesión</a></li>
        <li><a href="<?= BASE_URL ?>pages/participacion.php">Participación y Liderazgo</a></li>
        <li><a href="<?= BASE_URL ?>pages/desarrollo.php">Desarrollo Sostenible</a></li>
        <li><a href="<?= BASE_URL ?>pages/educacion.php">Educación y Capacitación</a></li>
        <li><a href="<?= BASE_URL ?>pages/infraestructura.php">Infraestructura y Hábitat</a></li>
    </ul>
</li>

<li class="has-submenu">
    <a href="#">Proyectos</a>
    <ul class="submenu">
        <li><a href="<?= BASE_URL ?>pages/enejecucion.php">En Ejecución</a></li>
        <li><a href="#">Ejecutados</a></li>        
    </ul>
</li>
<li><a href="<?= BASE_URL ?>pages/noticias.php">Noticias</a></li> 
<li><a href="<?= BASE_URL ?>pages/contacto.php">Contacto</a></li>
</ul>
</nav>

<a href="#" class="btn-top">¡Súmate!</a>

<div class="menu-toggle" id="menu-toggle">
<span></span>
<span></span>
<span></span>
</div>

</div>
</header>
