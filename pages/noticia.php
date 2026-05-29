<?php
require_once __DIR__ . '/../includes/db.php';

$db = Database::getInstance();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$noticia = $db->fetch("SELECT * FROM noticias WHERE id = ?", [$id]);

if (!$noticia) {
    include __DIR__ . '/../partials/header.php';
    echo '<section class="banner-page"><div class="container"><h1>Noticia no encontrada</h1></div></section>';
    echo '<section class="section"><div class="container" style="text-align:center;"><p>La noticia que buscas no existe o ha sido eliminada.</p><br><a href="noticias.php" class="btn primary">← Volver a Noticias</a></div></section>';
    include __DIR__ . '/../partials/footer.php';
    exit;
}

// Obtener galería de imágenes
$galeria = $db->fetchAll("SELECT * FROM noticias_galeria WHERE noticia_id = ? ORDER BY orden ASC", [$id]);

$pageTitle = htmlspecialchars($noticia['titulo']) . ' - Fundación COPADES';
include __DIR__ . '/../partials/header.php';
?>

<article class="news-article">
    <div class="container">
        <div class="news-article-header">
            <div class="news-article-meta">
                <span class="news-article-date">
                    <i class="far fa-calendar-alt"></i>
                    <?php
                    $fechaTimestamp = strtotime($noticia['fecha']);
                    $dias = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
                    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                    echo $dias[date('w', $fechaTimestamp)] . ', ' . date('d', $fechaTimestamp) . ' de ' . $meses[date('n', $fechaTimestamp)-1] . ' de ' . date('Y', $fechaTimestamp);
                    ?>
                </span>
            </div>
            <h1 class="news-article-title"><?= htmlspecialchars($noticia['titulo']) ?></h1>
        </div>

        <?php if ($noticia['imagen']): ?>
        <div class="news-article-image">
            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
        </div>
        <?php endif; ?>

        <div class="news-article-content">
            <?= $noticia['contenido'] ?>
        </div>

        <?php if (count($galeria) > 0): ?>
        <div class="news-article-gallery">
            <h2 class="news-gallery-title"><i class="fas fa-images"></i> Galería de imágenes</h2>
            <div class="gallery-mosaic">
                <?php foreach ($galeria as $img): ?>
                <a href="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($img['imagen']) ?>" class="gallery-mosaic-item" target="_blank" rel="noopener">
                    <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($img['imagen']) ?>" alt="Galería - <?= htmlspecialchars($noticia['titulo']) ?>">
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="news-article-footer">
            <div class="news-article-share">
                <span>Compartir:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . 'pages/noticia.php?id=' . $noticia['id']) ?>" target="_blank" rel="noopener" class="share-icon" aria-label="Compartir en Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($noticia['titulo']) ?>&url=<?= urlencode(BASE_URL . 'pages/noticia.php?id=' . $noticia['id']) ?>" target="_blank" rel="noopener" class="share-icon" aria-label="Compartir en Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://wa.me/?text=<?= urlencode($noticia['titulo'] . ' - ' . BASE_URL . 'pages/noticia.php?id=' . $noticia['id']) ?>" target="_blank" rel="noopener" class="share-icon" aria-label="Compartir en WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
            <a href="noticias.php" class="btn-back"><i class="fas fa-arrow-left"></i> Volver a Noticias</a>
        </div>
    </div>
</article>

<?php include __DIR__ . '/../partials/footer.php'; ?>
