<?php
// gallery.php - Public gallery page
require_once 'includes/db_connect.php';

$upload_dir = 'uploads/';
$gallery_images = $pdo->query("SELECT * FROM gallery ORDER BY uploaded_at DESC")->fetchAll();

$page_title = 'گالری تصاویر - هتل آسمان آبی';
$page_slug = 'gallery';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>گالری تصاویر</h1>
        <p>لحظات و فضاهای خاطره‌انگیز هتل آسمان آبی</p>
    </div>
</div>

<div class="container page-content">
    <div class="photo-gallery-grid">
        <?php if (empty($gallery_images)): ?>
            <p style="text-align: center;">در حال حاضر تصویری برای نمایش در گالری وجود ندارد.</p>
        <?php else: ?>
            <?php foreach ($gallery_images as $image): ?>
                <a href="<?php echo $upload_dir . htmlspecialchars($image['image_filename']); ?>" class="gallery-item">
                    <img src="<?php echo $upload_dir . htmlspecialchars($image['image_filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>" loading="lazy">
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox container - initially hidden -->
<div id="lightbox" class="lightbox-container">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <div class="lightbox-caption" id="lightbox-caption"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const closeBtn = document.querySelector('.lightbox-close');

    galleryItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            lightbox.style.display = 'flex';
            lightboxImg.src = this.href;
            lightboxCaption.textContent = this.querySelector('img').alt;
        });
    });

    function closeLightbox() {
        lightbox.style.display = 'none';
    }

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
