<?php
// index.php - Main Homepage
include 'includes/header.php'; // This now includes db_connect, functions, and settings
$page_title = htmlspecialchars($settings['hotel_name'] ?? 'هتل') . ' - به وب‌سایت رسمی ما خوش آمدید';
$page_slug = 'home';

// Fetch 3 most recent rooms for the preview section
try {
    $rooms_stmt = $pdo->query("SELECT * FROM rooms ORDER BY id DESC LIMIT 3");
    $preview_rooms = $rooms_stmt->fetchAll();
} catch (PDOException $e) {
    $preview_rooms = [];
}
// Fetch the latest announcement
try {
    $announcement_stmt = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 1");
    $latest_announcement = $announcement_stmt->fetch();
} catch (PDOException $e) {
    $latest_announcement = null;
}

?>

<!-- Hero Section with Dynamic Background -->
<section class="hero-section" style="background-image: url('<?php echo htmlspecialchars($settings['hero_image_url'] ?? 'https://placehold.co/1920x1080/87CEEB/333?text=Hotel+View'); ?>');">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1><?php echo htmlspecialchars($settings['hotel_welcome_title'] ?? 'به هتل ما خوش آمدید'); ?></h1>
        <p><?php echo htmlspecialchars($settings['hotel_welcome_subtitle'] ?? 'بهترین خدمات و امکانات در انتظار شماست.'); ?></p>
        <a href="rooms" class="btn btn-secondary">مشاهده اتاق‌ها</a>
    </div>
</section>

<!-- Latest Announcement Section -->
<?php if ($latest_announcement): ?>
<section class="announcement-section">
    <div class="container">
        <div class="announcement-card">
            <div class="announcement-image">
                <img src="<?php echo htmlspecialchars($latest_announcement['image_url'] ?: 'https://placehold.co/600x400/3498db/FFFFFF?text=News'); ?>" alt="<?php echo htmlspecialchars($latest_announcement['title']); ?>">
            </div>
            <div class="announcement-content">
                <h2><?php echo htmlspecialchars($latest_announcement['title']); ?></h2>
                <p><?php echo htmlspecialchars($latest_announcement['content']); ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Other sections remain the same -->
<section class="about-section">
    <div class="container">
        <h2>به خانه خودتان خوش آمدید</h2>
        <p class="section-subtitle">کمی بیشتر درباره ما بدانید</p>
        <div class="about-content">
            <div class="about-text">
                <h3>تجربه مهمان‌نوازی اصیل ایرانی</h3>
                <p>هتل آسمان آبی با افتخار ترکیبی از معماری مدرن و مهمان‌نوازی سنتی ایرانی را به نمایش می‌گذارد. هدف ما فراهم آوردن فضایی آرام و دلنشین برای شماست تا از سفر خود نهایت لذت را ببرید. از لحظه ورود تا زمان خروج، تیم حرفه‌ای ما آماده خدمت‌رسانی به شما عزیزان است.</p>
            </div>
            <div class="about-image">
                <img src="https://placehold.co/500x350/87CEEB/FFFFFF?text=Lobby" alt="لابی هتل آسمان آبی">
            </div>
        </div>
    </div>
</section>

<section class="rooms-preview-section">
    <div class="container">
        <h2>اتاق‌ها و سوئیت‌های ما</h2>
        <p class="section-subtitle">فضایی برای هر سلیقه و نیازی</p>
        <div class="rooms-grid">
            <?php if (empty($preview_rooms)): ?>
                <p style="text-align: center; grid-column: 1 / -1;">در حال حاضر اتاقی برای نمایش وجود ندارد.</p>
            <?php else: ?>
                <?php foreach ($preview_rooms as $room): ?>
                <div class="room-card">
                    <img src="<?php echo htmlspecialchars($room['main_image_url'] ?: 'https://placehold.co/400x250/87CEEB/FFFFFF?text=Room'); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                    <div class="room-card-content">
                        <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                        <p><?php echo mb_substr(htmlspecialchars($room['description']), 0, 100) . '...'; ?></p>
                        <div class="room-card-footer">
                            <span class="price">از شبی <?php echo number_format($room['price_per_night']); ?> تومان</span>
                            <a href="room_details?id=<?php echo $room['id']; ?>" class="btn btn-outline">مشاهده جزئیات</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
?>
