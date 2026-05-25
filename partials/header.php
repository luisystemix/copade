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
<li><a href="<?= BASE_URL ?>pages/nosotros.php">Nosotros</a></li>
<li><a href="<?= BASE_URL ?>pages/mision-vision.php">Misión y Visión</a></li>
<li><a href="<?= BASE_URL ?>pages/ejes.php">Ejes Estratégicos</a></li>
<li><a href="<?= BASE_URL ?>pages/noticias.php">Noticias</a></li>
<li><a href="<?= BASE_URL ?>pages/contacto.php">Contacto</a></li>
</ul>
</nav>

<a href="<?= BASE_URL ?>pages/contacto.php" class="btn-top">¡Súmate!</a>

<div class="menu-toggle" id="menu-toggle">
<span></span>
<span></span>
<span></span>
</div>

</div>
</header>
