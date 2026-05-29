<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance();
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Error de seguridad. Por favor, intente nuevamente.';
    }

    $currentPassword     = $_POST['current_password'] ?? '';
    $newPassword         = $_POST['new_password'] ?? '';
    $confirmPassword     = $_POST['confirm_password'] ?? '';

    if (!$error) {
        // Validar que la contraseña actual sea correcta
        $user = $db->fetch("SELECT * FROM usuarios WHERE id = ?", [$_SESSION['admin_id']]);
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $error = 'La contraseña actual no es correcta.';
        } elseif (strlen($newPassword) < 6) {
            $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Las contraseñas nuevas no coinciden.';
        } else {
            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $db->execute("UPDATE usuarios SET password = ? WHERE id = ?", [$hash, $_SESSION['admin_id']]);
            $_SESSION['success'] = 'Contraseña actualizada correctamente.';
            header('Location: cambiar_password.php');
            exit;
        }
    }
}

// Mostrar mensaje de éxito desde sesión
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cambiar Contraseña - Admin COPADES</title>
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
    max-width:600px;
}
.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:32px;
}
.top-bar h1{font-size:28px;color:#222;}
.password-card{
    background:white;
    border-radius:16px;
    padding:40px;
    box-shadow:0 2px 8px rgba(0,0,0,.06);
}
.password-card .form-group{
    margin-bottom:24px;
}
.password-card label{
    display:block;
    font-size:14px;
    font-weight:600;
    color:#333;
    margin-bottom:8px;
}
.password-card .input-wrapper{
    position:relative;
}
.password-card .input-wrapper i{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#aaa;
    font-size:16px;
}
.password-card input[type="password"]{
    width:100%;
    padding:14px 16px 14px 46px;
    border:2px solid #e0e0e0;
    border-radius:12px;
    font-size:15px;
    font-family:'Poppins',sans-serif;
    transition:.3s;
    outline:none;
    background:#fafafa;
}
.password-card input[type="password"]:focus{
    border-color:#009879;
    background:white;
    box-shadow:0 0 0 4px rgba(0,152,121,.1);
}
.password-card .btn-save{
    width:100%;
    padding:16px;
    background:#009879;
    color:white;
    border:none;
    border-radius:12px;
    font-size:16px;
    font-weight:600;
    font-family:'Poppins',sans-serif;
    cursor:pointer;
    transition:.3s;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}
.password-card .btn-save:hover{
    background:#007a62;
    transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(0,152,121,.3);
}
.alert{
    padding:14px 20px;
    border-radius:12px;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
}
.alert-success{
    background:rgba(0,152,121,.1);
    border:1px solid rgba(0,152,121,.2);
    color:#009879;
}
.alert-error{
    background:#fff0f0;
    border:1px solid #ffc0c0;
    color:#c00;
}
.password-hint{
    font-size:12px;
    color:#888;
    margin-top:6px;
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
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="noticias.php"><i class="fas fa-newspaper"></i> Noticias</a>
        <a href="cambiar_password.php" class="active"><i class="fas fa-key"></i> Contraseña</a>
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
        <h1><i class="fas fa-key" style="color:#009879;margin-right:12px;"></i>Cambiar Contraseña</h1>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="password-card">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="current_password">Contraseña Actual</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="current_password" name="current_password" placeholder="Ingrese su contraseña actual" required autocomplete="current-password">
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-key"></i>
                    <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres" required minlength="6" autocomplete="new-password">
                </div>
                <p class="password-hint">Debe tener al menos 6 caracteres.</p>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Nueva Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-check-double"></i>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repita la nueva contraseña" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-save"><i class="fas fa-save"></i> Actualizar Contraseña</button>
        </form>
    </div>
</div>
</body>
</html>
