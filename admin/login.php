<?php
require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT * FROM usuarios WHERE username = ?", [$username]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_nombre'] = $user['nombre'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Por favor ingrese usuario y contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administrador - Fundación COPADES</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Poppins',sans-serif;
    background:linear-gradient(135deg,#009879,#007a62);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}
.login-container{
    width:100%;
    max-width:420px;
    padding:20px;
}
.login-box{
    background:white;
    padding:48px 40px;
    border-radius:24px;
    box-shadow:0 20px 60px rgba(0,0,0,.15);
}
.login-box h1{
    font-size:28px;
    color:#222;
    text-align:center;
    margin-bottom:8px;
}
.login-box p.subtitle{
    text-align:center;
    color:#888;
    font-size:14px;
    margin-bottom:32px;
}
.login-form{
    display:flex;
    flex-direction:column;
    gap:20px;
}
.form-group{
    display:flex;
    flex-direction:column;
    gap:6px;
}
.form-group label{
    font-size:14px;
    font-weight:600;
    color:#333;
}
.form-group .input-wrapper{
    position:relative;
}
.form-group .input-wrapper i{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#aaa;
    font-size:16px;
}
.form-group input{
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
.form-group input:focus{
    border-color:#009879;
    background:white;
    box-shadow:0 0 0 4px rgba(0,152,121,.1);
}
.btn-login{
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
}
.btn-login:hover{
    background:#007a62;
    transform:translateY(-2px);
    box-shadow:0 8px 24px rgba(0,152,121,.3);
}
.error-msg{
    background:#fff0f0;
    border:1px solid #ffc0c0;
    color:#c00;
    padding:12px 16px;
    border-radius:12px;
    font-size:14px;
    text-align:center;
}
.login-logo{
    text-align:center;
    margin-bottom:20px;
}
.login-logo img{
    height:60px;
}
.login-footer{
    text-align:center;
    margin-top:24px;
    font-size:13px;
    color:#aaa;
}
.login-footer a{
    color:#009879;
    text-decoration:none;
}
</style>
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <img src="<?= BASE_URL ?>assets/images/logo-horizontal2.png" alt="COPADES">
        </div>
        <h1>Iniciar Sesión</h1>
        <p class="subtitle">Panel de administración de noticias</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Usuario</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required autocomplete="username" autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Ingresar
            </button>
        </form>

        <div class="login-footer">
            <a href="<?= BASE_URL ?>">&larr; Volver al sitio</a>
        </div>
    </div>
</div>
</body>
</html>
