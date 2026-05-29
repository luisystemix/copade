<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $db = Database::getInstance();
    $noticia = $db->fetch("SELECT * FROM noticias WHERE id = ?", [$id]);

    if ($noticia) {
        // Eliminar imagen
        if ($noticia['imagen']) {
            $imagePath = __DIR__ . '/../assets/uploads/' . $noticia['imagen'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Eliminar noticia
        $db->execute("DELETE FROM noticias WHERE id = ?", [$id]);
        $_SESSION['success'] = 'Noticia eliminada exitosamente.';
    }
}

header('Location: noticias.php');
exit;
