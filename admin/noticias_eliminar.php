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
        // Eliminar imagen de portada
        if ($noticia['imagen']) {
            $imagePath = __DIR__ . '/../assets/uploads/' . $noticia['imagen'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Eliminar imágenes de galería
        $galeria = $db->fetchAll("SELECT * FROM noticias_galeria WHERE noticia_id = ?", [$id]);
        foreach ($galeria as $img) {
            $imgPath = __DIR__ . '/../assets/uploads/' . $img['imagen'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        // El ON DELETE CASCADE en la FK eliminará los registros de noticias_galeria automáticamente
        $db->execute("DELETE FROM noticias WHERE id = ?", [$id]);
        $_SESSION['success'] = 'Noticia y todas sus imágenes eliminadas exitosamente.';
    }
}

header('Location: noticias.php');
exit;
