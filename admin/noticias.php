<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$noticias = $db->fetchAll("SELECT * FROM noticias ORDER BY fecha DESC, created_at DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Noticias - Admin COPADES</title>
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
.sidebar-logo img{height:48px;margin-bottom:8px;}
.sidebar-logo h2{font-size:16px;font-weight:600;}
.sidebar-logo p{font-size:12px;color:#888;}
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
.sidebar-nav a.active{background:rgba(0,152,121,.2);color:#009879;}
.sidebar-nav a i{width:20px;text-align:center;}
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
    width:40px;height:40px;border-radius:50%;
    background:#009879;
    display:flex;align-items:center;justify-content:center;
    font-size:16px;font-weight:600;
}
.sidebar-footer .admin-name{font-size:14px;font-weight:500;}
.sidebar-footer .admin-role{font-size:12px;color:#888;}
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
    flex-wrap:wrap;
    gap:16px;
}
.top-bar h1{font-size:28px;color:#222;}
.top-bar .btn-new{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 24px;
    background:#009879;
    color:white;
    text-decoration:none;
    border-radius:12px;
    font-weight:500;
    font-size:14px;
    transition:.3s;
}
.top-bar .btn-new:hover{
    background:#007a62;
    transform:translateY(-2px);
}
.noticias-table{
    background:white;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.06);
}
.noticias-table table{
    width:100%;
    border-collapse:collapse;
}
.noticias-table th{
    background:#f8f9fa;
    padding:16px 20px;
    text-align:left;
    font-size:13px;
    font-weight:600;
    color:#666;
    text-transform:uppercase;
    letter-spacing:.5px;
}
.noticias-table td{
    padding:16px 20px;
    border-top:1px solid #f0f0f0;
    font-size:14px;
}
.noticias-table .news-thumb{
    width:60px;
    height:45px;
    border-radius:8px;
    object-fit:cover;
    background:#f0f0f0;
}
.noticias-table .news-title{
    font-weight:600;
    color:#222;
}
.noticias-table .news-date{
    color:#888;
    font-size:13px;
    white-space:nowrap;
}
.noticias-table .news-resumen{
    color:#666;
    font-size:13px;
    max-width:300px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
}
.actions{
    display:flex;
    gap:8px;
}
.actions a{
    padding:8px 14px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
    font-weight:500;
    transition:.3s;
    display:inline-flex;
    align-items:center;
    gap:4px;
}
.actions .btn-edit{
    background:rgba(0,152,121,.1);
    color:#009879;
}
.actions .btn-edit:hover{background:rgba(0,152,121,.2);}
.actions .btn-delete{
    background:rgba(255,0,0,.08);
    color:#c00;
}
.actions .btn-delete:hover{background:rgba(255,0,0,.15);}
.empty-state{
    text-align:center;
    padding:60px 20px;
    color:#888;
}
.empty-state i{font-size:48px;margin-bottom:16px;color:#ddd;}
.empty-state p{font-size:16px;margin-bottom:20px;}
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
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="noticias.php" class="active"><i class="fas fa-newspaper"></i> Noticias</a>
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
        <h1><i class="fas fa-newspaper" style="color:#009879;margin-right:12px;"></i>Noticias</h1>
        <a href="noticias_crear.php" class="btn-new"><i class="fas fa-plus"></i> Nueva Noticia</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background:rgba(0,152,121,.1);border:1px solid rgba(0,152,121,.2);color:#009879;padding:14px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="noticias-table">
        <?php if (count($noticias) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th>Resumen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($noticias as $noticia): ?>
                <tr>
                    <td>
                        <?php if ($noticia['imagen']): ?>
                            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($noticia['imagen']) ?>" alt="" class="news-thumb">
                        <?php else: ?>
                            <div style="width:60px;height:45px;border-radius:8px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:20px;"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </td>
                    <td class="news-title"><?= htmlspecialchars($noticia['titulo']) ?></td>
                    <td class="news-date"><?= date('d/m/Y', strtotime($noticia['fecha'])) ?></td>
                    <td class="news-resumen"><?= htmlspecialchars($noticia['resumen']) ?></td>
                    <td>
                        <div class="actions">
                            <a href="noticias_editar.php?id=<?= $noticia['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Editar</a>
                            <a href="noticias_eliminar.php?id=<?= $noticia['id'] ?>" class="btn-delete" onclick="return confirm('¿Está seguro de eliminar esta noticia? Esta acción no se puede deshacer.')"><i class="fas fa-trash"></i> Eliminar</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-newspaper"></i>
            <p>No hay noticias registradas aún.</p>
            <a href="noticias_crear.php" style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:#009879;color:white;text-decoration:none;border-radius:12px;font-weight:500;">Crear primera noticia</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
