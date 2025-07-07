<?php
// room_details.php - Public page for a single room's details
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$room_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$room_id) {
    header('Location: rooms');
    exit;
}

// Fetch room details
try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
    $stmt->execute([':id' => $room_id]);
    $room = $stmt->fetch();

    if (!$room) {
        header('Location: rooms');
        exit;
    }

    // Fetch gallery images for this room
    $gallery_stmt = $pdo->prepare("SELECT image_url FROM room_images WHERE room_id = :room_id");
    $gallery_stmt->execute([':room_id' => $room_id]);
    $gallery_images = $gallery_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // If no specific gallery images, use the main thumbnail as a fallback
    if (empty($gallery_images) && !empty($room['main_image_url'])) {
        $gallery_images[] = $room['main_image_url'];
    }

} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات اتاق: " . $e->getMessage());
}

$page_title = htmlspecialchars($room['name']) . ' - هتل آسمان آبی';
$page_slug = 'rooms'; // Keep the 'rooms' nav item active
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($room['name']); ?></h1>
        <p>جزئیات کامل و گالری تصاویر اتاق</p>
    </div>
</div>

<div class="container page-content">
    <div class="room-details-layout">
        <!-- Gallery Section -->
        <div class="room-details-gallery">
            <div class="main-image-container">
                <img id="main-gallery-image" src="<?php echo htmlspecialchars($gallery_images[0] ?? 'https://placehold.co/800x600/87CEEB/FFFFFF?text=No+Image'); ?>" alt="Main view of <?php echo htmlspecialchars($room['name']); ?>">
            </div>
            <?php if (count($gallery_images) > 1): ?>
            <div class="thumbnails-container">
                <?php foreach ($gallery_images as $image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" class="thumbnail" alt="Thumbnail view of <?php echo htmlspecialchars($room['name']); ?>">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Details Section -->
        <div class="room-details-info">
            <h2>توضیحات و امکانات</h2>
            <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
            <div class="room-meta">
                <div class="meta-item">
                    <strong>ظرفیت:</strong> <?php echo htmlspecialchars($room['capacity']); ?> نفر
                </div>
                <div class="meta-item">
                    <strong>قیمت هر شب:</strong> <?php echo number_format($room['price_per_night']); ?> تومان
                </div>
            </div>
            <a href="reservation?room_id=<?php echo $room['id']; ?>" class="btn btn-primary btn-full">همین حالا رزرو کنید</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail');

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            mainImage.src = this.src;
            // Optional: add an 'active' class to the selected thumbnail
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
    // Set the first thumbnail as active initially
    if(thumbnails.length > 0) {
        thumbnails[0].classList.add('active');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
