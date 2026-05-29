<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$noticia = $db->fetch("SELECT * FROM noticias WHERE id = ?", [$id]);

if (!$noticia) {
    header('Location: noticias.php');
    exit;
}

$error = '';

// Procesar eliminación de imagen de galería vía GET
if (isset($_GET['delete_galeria']) && (int)$_GET['delete_galeria'] > 0) {
    $galeriaId = (int)$_GET['delete_galeria'];
    $img = $db->fetch("SELECT * FROM noticias_galeria WHERE id = ? AND noticia_id = ?", [$galeriaId, $id]);
    if ($img) {
        $imgPath = __DIR__ . '/../assets/uploads/' . $img['imagen'];
        if (file_exists($imgPath)) unlink($imgPath);
        $db->execute("DELETE FROM noticias_galeria WHERE id = ?", [$galeriaId]);
        $_SESSION['success'] = 'Imagen de galería eliminada.';
        header('Location: noticias_editar.php?id=' . $id);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    }

    $titulo    = trim($_POST['titulo'] ?? '');
    $fecha     = $_POST['fecha'] ?? date('Y-m-d');
    $contenido = $_POST['contenido'] ?? '';

    if (!$error && (!$titulo || !$contenido)) {
        $error = 'El título y el contenido son obligatorios.';
    }

    if (!$error) {
        $imagenNombre = $noticia['imagen'];

        // Procesar nueva imagen de portada
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $maxSize = 5 * 1024 * 1024;

            if (!in_array($_FILES['imagen']['type'], $allowedTypes)) {
                $error = 'La imagen de portada debe ser JPG, PNG, WebP o GIF.';
            } elseif ($_FILES['imagen']['size'] > $maxSize) {
                $error = 'La imagen de portada no debe superar los 5MB.';
            } else {
                // Eliminar imagen anterior
                if ($noticia['imagen']) {
                    $oldPath = __DIR__ . '/../assets/uploads/' . $noticia['imagen'];
                    if (file_exists($oldPath)) unlink($oldPath);
                }

                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imagenNombre = uniqid('noticia_') . '.' . $ext;
                $uploadPath = __DIR__ . '/../assets/uploads/' . $imagenNombre;

                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
                    $error = 'Error al subir la imagen de portada.';
                }
            }
        }

        // Eliminar imagen de portada si se solicitó
        $subioImagenNueva = isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK;
        if (isset($_POST['eliminar_imagen']) && $noticia['imagen'] && !$error && !$subioImagenNueva) {
            $oldPath = __DIR__ . '/../assets/uploads/' . $noticia['imagen'];
            if (file_exists($oldPath)) unlink($oldPath);
            $imagenNombre = null;
        }

        if (!$error) {
            $db->execute(
                "UPDATE noticias SET titulo = ?, fecha = ?, contenido = ?, imagen = ? WHERE id = ?",
                [$titulo, $fecha, $contenido, $imagenNombre, $id]
            );

            // Procesar nuevas imágenes de galería
            if (!empty($_FILES['galeria']['name'][0])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
                $maxSize = 5 * 1024 * 1024;

                // Obtener el orden máximo actual
                $maxOrden = $db->fetch("SELECT MAX(orden) as max_orden FROM noticias_galeria WHERE noticia_id = ?", [$id]);
                $orden = ($maxOrden['max_orden'] ?? -1) + 1;

                foreach ($_FILES['galeria']['name'] as $key => $name) {
                    if ($_FILES['galeria']['error'][$key] !== UPLOAD_ERR_OK) continue;
                    if (!in_array($_FILES['galeria']['type'][$key], $allowedTypes)) continue;
                    if ($_FILES['galeria']['size'][$key] > $maxSize) continue;

                    $ext = pathinfo($name, PATHINFO_EXTENSION);
                    $galeriaNombre = uniqid('galeria_') . '.' . $ext;
                    $uploadPath = __DIR__ . '/../assets/uploads/' . $galeriaNombre;

                    if (move_uploaded_file($_FILES['galeria']['tmp_name'][$key], $uploadPath)) {
                        $db->insert(
                            "INSERT INTO noticias_galeria (noticia_id, imagen, orden) VALUES (?, ?, ?)",
                            [$id, $galeriaNombre, $orden]
                        );
                        $orden++;
                    }
                }
            }

            $_SESSION['success'] = 'Noticia actualizada exitosamente.';
            header('Location: noticias.php');
            exit;
        }
    }
}

// Obtener imágenes de galería
$galeria = $db->fetchAll("SELECT * FROM noticias_galeria WHERE noticia_id = ? ORDER BY orden ASC", [$id]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Noticia - Admin COPADES</title>
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
    width:260px;background:#111;color:white;padding:24px;
    display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;
}
.sidebar-logo{text-align:center;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,.1);margin-bottom:24px;}
.sidebar-logo img{height:48px;margin-bottom:8px;}
.sidebar-logo h2{font-size:16px;font-weight:600;}
.sidebar-logo p{font-size:12px;color:#888;}
.sidebar-nav{display:flex;flex-direction:column;gap:4px;flex:1;}
.sidebar-nav a{
    display:flex;align-items:center;gap:12px;padding:12px 16px;
    border-radius:12px;color:#ccc;text-decoration:none;font-size:14px;font-weight:500;transition:.3s;
}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(0,152,121,.2);color:#009879;}
.sidebar-nav a i{width:20px;text-align:center;}
.sidebar-footer{border-top:1px solid rgba(255,255,255,.1);padding-top:16px;}
.sidebar-footer .admin-info{display:flex;align-items:center;gap:12px;margin-bottom:12px;}
.sidebar-footer .admin-avatar{width:40px;height:40px;border-radius:50%;background:#009879;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:600;}
.sidebar-footer .admin-name{font-size:14px;font-weight:500;}
.sidebar-footer .admin-role{font-size:12px;color:#888;}
.main-content{margin-left:260px;flex:1;padding:32px;}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;}
.top-bar h1{font-size:28px;color:#222;}
.form-card{
    background:white;border-radius:16px;padding:40px;
    box-shadow:0 2px 8px rgba(0,0,0,.06);max-width:900px;
}
.form-group{margin-bottom:24px;}
.form-group label{
    display:block;font-size:14px;font-weight:600;color:#333;margin-bottom:8px;
}
.form-group input[type="text"],
.form-group input[type="date"],
.form-group textarea{
    width:100%;padding:14px 18px;border:2px solid #e0e0e0;border-radius:12px;
    font-size:15px;font-family:'Poppins',sans-serif;transition:.3s;outline:none;background:#fafafa;
}
.form-group input:focus,
.form-group textarea:focus{
    border-color:#009879;background:white;box-shadow:0 0 0 4px rgba(0,152,121,.1);
}
.form-group textarea{resize:vertical;min-height:120px;}
.form-group .file-input-wrapper{
    position:relative;
}
.form-group .file-input-wrapper input[type="file"]{
    width:100%;padding:40px 20px;border:2px dashed #e0e0e0;border-radius:12px;
    background:#fafafa;cursor:pointer;text-align:center;font-size:14px;font-family:'Poppins',sans-serif;
    transition:.3s;
}
.form-group .file-input-wrapper input[type="file"]:hover{
    border-color:#009879;background:rgba(0,152,121,.03);
}
.form-group .file-hint{font-size:12px;color:#aaa;margin-top:6px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.form-actions{display:flex;gap:12px;margin-top:32px;padding-top:24px;border-top:1px solid #f0f0f0;}
.btn-save{
    padding:14px 36px;background:#009879;color:white;border:none;border-radius:12px;
    font-size:15px;font-weight:600;font-family:'Poppins',sans-serif;cursor:pointer;transition:.3s;
}
.btn-save:hover{background:#007a62;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,152,121,.3);}
.btn-cancel{
    padding:14px 36px;background:white;color:#666;border:2px solid #e0e0e0;border-radius:12px;
    font-size:15px;font-weight:500;font-family:'Poppins',sans-serif;cursor:pointer;text-decoration:none;transition:.3s;display:inline-flex;align-items:center;
}
.btn-cancel:hover{border-color:#ccc;color:#333;}
.error-msg{
    background:#fff0f0;border:1px solid #ffc0c0;color:#c00;
    padding:14px 20px;border-radius:12px;margin-bottom:24px;font-size:14px;display:flex;align-items:center;gap:10px;
}
.editor-hint{
    background:#f0f7ff;border:1px solid #cce5ff;color:#0066cc;
    padding:12px 16px;border-radius:12px;font-size:13px;margin-bottom:16px;
}
.editor-hint i{margin-right:6px;}
.current-image{
    margin-top:12px;padding:12px;background:#f9f9f9;border-radius:8px;display:flex;align-items:center;gap:12px;
}
.current-image img{width:80px;height:60px;border-radius:6px;object-fit:cover;}
.current-image .img-info{font-size:13px;color:#666;}
.current-image .img-info strong{display:block;color:#333;}
.delete-img-label{
    display:flex;align-items:center;gap:6px;font-size:13px;color:#c00;cursor:pointer;margin-left:auto;
}
/* Galería actual */
.current-gallery{
    display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px;margin-top:12px;
}
.gallery-item{
    position:relative;border-radius:8px;overflow:hidden;border:2px solid #e0e0e0;aspect-ratio:4/3;
}
.gallery-item img{width:100%;height:100%;object-fit:cover;}
.gallery-item .del-btn{
    position:absolute;top:4px;right:4px;width:28px;height:28px;border-radius:50%;
    background:rgba(255,0,0,.85);color:white;border:none;cursor:pointer;
    display:flex;align-items:center;justify-content:center;font-size:14px;
    transition:.3s;text-decoration:none;
}
.gallery-item .del-btn:hover{background:#c00;transform:scale(1.1);}
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
        <a href="cambiar_password.php"><i class="fas fa-key"></i> Contraseña</a>
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
        <a href="logout.php" style="display:flex;align-items:center;gap:8px;color:#888;text-decoration:none;font-size:13px;padding:8px 12px;border-radius:8px;transition:.3s;" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background='transparent'"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</div>
<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-edit" style="color:#009879;margin-right:12px;"></i>Editar Noticia</h1>
        <a href="noticias.php" style="color:#888;text-decoration:none;font-size:14px;"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="background:rgba(0,152,121,.1);border:1px solid rgba(0,152,121,.2);color:#009879;padding:14px 20px;border-radius:12px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form-card">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <div class="form-row">
            <div class="form-group" style="grid-column:1/-1;">
                <label for="titulo">Título de la noticia *</label>
                <input type="text" id="titulo" name="titulo" placeholder="Ingrese el título" required value="<?= htmlspecialchars($_POST['titulo'] ?? $noticia['titulo']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="fecha">Fecha *</label>
                <input type="date" id="fecha" name="fecha" required value="<?= $_POST['fecha'] ?? $noticia['fecha'] ?>">
            </div>
            <div class="form-group">
                <label for="imagen">Imagen de portada</label>
                <div class="file-input-wrapper">
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp,image/gif">
                </div>
                <div class="file-hint">Deje vacío para mantener la imagen actual. Formatos: JPG, PNG, WebP, GIF. Máximo 5MB.</div>

                <?php if ($noticia['imagen']): ?>
                <div class="current-image">
                    <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen actual">
                    <div class="img-info">
                        <strong>Imagen de portada actual</strong>
                        <span><?= htmlspecialchars($noticia['imagen']) ?></span>
                    </div>
                    <label class="delete-img-label">
                        <input type="checkbox" name="eliminar_imagen" value="1">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Galería de imágenes actual</label>
            <?php if (count($galeria) > 0): ?>
            <div class="current-gallery">
                <?php foreach ($galeria as $img): ?>
                <div class="gallery-item">
                    <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($img['imagen']) ?>" alt="Galería">
                    <a href="noticias_editar.php?id=<?= $id ?>&delete_galeria=<?= $img['id'] ?>" class="del-btn" onclick="return confirm('¿Eliminar esta imagen de la galería?')"><i class="fas fa-times"></i></a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p style="color:#aaa;font-size:14px;">No hay imágenes en la galería.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="galeria">Agregar más imágenes a la galería</label>
            <div class="file-input-wrapper">
                <input type="file" id="galeria" name="galeria[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            </div>
            <div class="file-hint">Puede seleccionar varias imágenes. Se agregarán al final de la galería actual. Formatos: JPG, PNG, WebP, GIF. Máximo 5MB cada una.</div>
        </div>

        <div class="form-group">
            <label for="contenido">Contenido completo *</label>
            <div class="editor-hint"><i class="fas fa-info-circle"></i> Puede usar etiquetas HTML básicas para dar formato (&lt;p&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;ul&gt;, &lt;li&gt;, etc.)</div>
            <textarea id="contenido" name="contenido" placeholder="Escriba el contenido completo de la noticia aquí..." rows="15" required><?= htmlspecialchars($_POST['contenido'] ?? $noticia['contenido']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Cambios</button>
            <a href="noticias.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</div>
</body>
</html>
