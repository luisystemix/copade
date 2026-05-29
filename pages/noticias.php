<?php
require_once __DIR__ . '/../includes/db.php';

$db = Database::getInstance();
$noticias = $db->fetchAll("SELECT * FROM noticias ORDER BY fecha DESC, created_at DESC");

$pageTitle = 'Noticias - Fundación COPADES';
include __DIR__ . '/../partials/header.php';
?>

<section class="banner-page">
    <div class="container">
        <h1>Noticias</h1>
        <p style="font-size:18px;opacity:.9;margin-top:8px;">Mantente informado con las últimas novedades de la Fundación COPADES</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <?php if (count($noticias) > 0): ?>
        <div class="news-grid">
            <?php foreach ($noticias as $noticia): ?>
            <article class="news-card">
                <a href="noticia.php?id=<?= $noticia['id'] ?>" class="news-card-link">
                    <div class="news-card-img">
                        <?php if ($noticia['imagen']): ?>
                            <img src="<?= BASE_URL ?>assets/uploads/<?= htmlspecialchars($noticia['imagen']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                        <?php else: ?>
                            <div class="news-card-placeholder">
                                <i class="fas fa-newspaper"></i>
                            </div>
                        <?php endif; ?>
                        <div class="news-card-date">
                            <i class="far fa-calendar-alt"></i>
                            <?= date('d', strtotime($noticia['fecha'])) ?>
                            <span><?= date('M', strtotime($noticia['fecha'])) ?></span>
                        </div>
                    </div>
                    <div class="news-card-body">
                        <h3 class="news-card-title"><?= htmlspecialchars($noticia['titulo']) ?></h3>
                        <span class="news-card-more">Leer más <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="news-empty">
            <i class="fas fa-newspaper"></i>
            <h3>No hay noticias disponibles</h3>
            <p>Próximamente estaremos publicando nuestras novedades. ¡Vuelve pronto!</p>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
