<?php
include '../partials/header.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre    = trim($_POST['nombre'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $asunto    = trim($_POST['asunto'] ?? '');
    $mensaje   = trim($_POST['mensaje'] ?? '');

    if (!$nombre || !$email || !$asunto || !$mensaje) {
        $error = 'Por favor, completa todos los campos obligatorios (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, ingresa un correo electrónico válido.';
    } else {
        $to      = 'info@fundacioncopade.com';
        $subject = "COPADES - $asunto";
        $body    = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\n\nMensaje:\n$mensaje";
        $headers = "From: $email\r\nReply-To: $email";

        if (mail($to, $subject, $body, $headers)) {
            $success = true;
        } else {
            $error = 'Hubo un problema al enviar el mensaje. Intenta nuevamente más tarde.';
        }
    }
}
?>

<section class="banner-page">
    <div class="container">
        <h1>Contacto</h1>
    </div>
</section>

<section class="section">
    <div class="container">

        <div class="contact-grid">

            <div class="contact-info">
                <h2>Ponte en contacto</h2>
                <p>Estamos aquí para escucharte. Si tienes preguntas, sugerencias o deseas sumarte a nuestros proyectos, no dudes en escribirnos.</p>

                <div class="contact-details">
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <div>
                            <strong>Dirección:</strong>
                            <p>Avenida 16 de julio esquina Colombia. Edificio Cámara Nacional de Comercio piso 8 oficina 811</p> <p>La Paz - Bolivia</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📧</span>
                        <div>
                            <strong>Email</strong>
                            <p>info@fundacioncopades.org</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <div>
                            <strong>Teléfono</strong>
                            <p>+591 72030460</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">🕐</span>
                        <div>
                            <strong>Horario</strong>
                            <p>Lun – Vie, 8:30 – 18:30</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-box">
                <?php if ($success): ?>
                    <div class="form-success">
                        <span class="success-icon">✅</span>
                        <h3>¡Mensaje enviado con éxito!</h3>
                        <p>Gracias por contactarnos. Te responderemos a la brevedad posible.</p>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="form-error"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nombre">Nombre *</label>
                                <input type="text" id="nombre" name="nombre" class="form-input"
                                       value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required
                                       placeholder="Tu nombre completo">
                            </div>
                            <div class="form-group">
                                <label for="email">Correo electrónico *</label>
                                <input type="email" id="email" name="email" class="form-input"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
                                       placeholder="tucorreo@ejemplo.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" class="form-input"
                                       value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                       placeholder="+591 70000000">
                            </div>
                            <div class="form-group">
                                <label for="asunto">Asunto *</label>
                                <input type="text" id="asunto" name="asunto" class="form-input"
                                       value="<?= htmlspecialchars($_POST['asunto'] ?? '') ?>" required
                                       placeholder="¿Sobre qué nos escribes?">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mensaje">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" class="form-textarea" rows="6" required
                                      placeholder="Escribe tu mensaje aquí..."><?= htmlspecialchars($_POST['mensaje'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn primary btn-submit">Enviar mensaje</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>

<?php include '../partials/footer.php'; ?>
