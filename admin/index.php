<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$totalNoticias = $db->fetch("SELECT COUNT(*) as total FROM noticias")['total'];
$ultimaNoticia = $db->fetch("SELECT titulo, fecha FROM noticias ORDER BY fecha DESC LIMIT 1");
$totalUsuarios = $db->fetch("SELECT COUNT(*) as total FROM usuarios")['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Admin COPADES</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    background:#f0f2f5;
    display:flex;
    min-height:100vh;
}
.sidebar{
    width:260px;
    background:#111;
    color:white;
    padding:24px;
    display:flex;
    flex-direction:column;
    position:fixed;
    top:0;
    left:0;
    height:100vh;
}
.sidebar-logo{
    text-align:center;
    padding-bottom:24px;
    border-bottom:1px solid rgba(255,255,255,.1);
    margin-bottom:24px;
}
.sidebar-logo img{
    height:48px;
    margin-bottom:8px;
}
.sidebar-logo h2{
    font-size:16px;
    font-weight:600;
}
.sidebar-logo p{
    font-size:12px;
    color:#888;
}
.sidebar-nav{
    display:flex;
    flex-direction:column;
    gap:4px;
    flex:1;
}
.sidebar-nav a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 16px;
    border-radius:12px;
    color:#ccc;
    text-decoration:none;
    font-size:14px;
    font-weight:500;
    transition:.3s;
}
.sidebar-nav a:hover,
.sidebar-nav a.active{
    background:rgba(0,152,121,.2);
    color:#009879;
}
.sidebar-nav a i{
    width:20px;
    text-align:center;
}
.sidebar-footer{
    border-top:1px solid rgba(255,255,255,.1);
    padding-top:16px;
}
.sidebar-footer .admin-info{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:12px;
}
.sidebar-footer .admin-avatar{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#009879;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
    font-weight:600;
}
.sidebar-footer .admin-name{
    font-size:14px;
    font-weight:500;
}
.sidebar-footer .admin-role{
    font-size:12px;
    color:#888;
}
.main-content{
    margin-left:260px;
    flex:1;
    padding:32px;
}
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:32px;
}
.top-bar h1{
    font-size:28px;
    color:#222;
}
.top-bar .btn-logout{
    padding:10px 20px;
    background:#f0f0f0;
    border-radius:10px;
    text-decoration:none;
    color:#666;
    font-size:14px;
    font-weight:500;
    transition:.3s;
}
.top-bar .btn-logout:hover{
    background:#ffebee;
    color:#c00;
}
.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:32px;
}
.stat-card{
    background:white;
    padding:24px;
    border-radius:16px;
    box-shadow:0 2px 8px rgba(0,0,0,.06);
}
.stat-card .stat-icon{
    width:48px;
    height:48px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    margin-bottom:16px;
}
.stat-card .stat-icon.green{background:rgba(0,152,121,.1);color:#009879;}
.stat-card .stat-icon.orange{background:rgba(245,130,32,.1);color:#f58220;}
.stat-card .stat-icon.blue{background:rgba(33,150,243,.1);color:#2196f3;}
.stat-card .stat-number{
    font-size:32px;
    font-weight:700;
    color:#222;
}
.stat-card .stat-label{
    font-size:14px;
    color:#888;
    margin-top:4px;
}
.quick-actions{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
}
.quick-actions a{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:14px 24px;
    background:#009879;
    color:white;
    text-decoration:none;
    border-radius:12px;
    font-weight:500;
    font-size:14px;
    transition:.3s;
}
.quick-actions a:hover{
    background:#007a62;
    transform:translateY(-2px);
}
.quick-actions a.secondary{
    background:white;
    color:#009879;
    border:2px solid #009879;
}
.quick-actions a.secondary:hover{
    background:rgba(0,152,121,.05);
}
</style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        <img src="<?= BASE_URL ?>assets/images/logo-horizontal2.png" alt="COPADES">
        <h2>Fundación COPADES</h2>
        <p>Panel Administrativo</p>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="noticias.php"><i class="fas fa-newspaper"></i> Noticias</a>
        <a href="<?= BASE_URL ?>"><i class="fas fa-globe"></i> Ver Sitio</a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_nombre'], 0, 1)) ?></div>
            <div>
                <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_nombre']) ?></div>
                <div class="admin-role">Administrador</div>
            </div>
        </div>
        <a href="logout.php" style="display:flex;align-items:center;gap:8px;color:#888;text-decoration:none;font-size:13px;padding:8px 12px;border-radius:8px;transition:.3s;" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background='transparent'">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
</div>
<div class="main-content">
    <div class="top-bar">
        <h1>Dashboard</h1>
        <a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Salir</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-newspaper"></i></div>
            <div class="stat-number"><?= $totalNoticias ?></div>
            <div class="stat-label">Noticias Publicadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-number"><?= $ultimaNoticia ? date('d/m/Y', strtotime($ultimaNoticia['fecha'])) : '—' ?></div>
            <div class="stat-label">Última Noticia</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="stat-number"><?= $totalUsuarios ?></div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>

    <div class="quick-actions">
        <a href="noticias_crear.php"><i class="fas fa-plus"></i> Nueva Noticia</a>
        <a href="noticias.php" class="secondary"><i class="fas fa-list"></i> Gestionar Noticias</a>
    </div>
</div>
</body>
</html>
