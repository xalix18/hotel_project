<?php
// rooms.php - Public page to display all rooms with filtering
require_once 'includes/db_connect.php';

// --- Filtering Logic ---
$where_clauses = [];
$params = [];

// Filter by capacity
if (!empty($_GET['capacity'])) {
    $capacity = filter_input(INPUT_GET, 'capacity', FILTER_VALIDATE_INT);
    if ($capacity > 0) {
        $where_clauses[] = "capacity >= :capacity";
        $params[':capacity'] = $capacity;
    }
}

// --- Build the SQL Query ---
$sql = "SELECT * FROM rooms";
if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " ORDER BY price_per_night ASC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات اتاق‌ها: " . $e->getMessage());
}

$page_title = 'اتاق‌ها و سوئیت‌ها - هتل آسمان آبی';
$page_slug = 'rooms';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>اتاق‌ها و سوئیت‌ها</h1>
        <p>اتاق مناسب خود را پیدا کنید</p>
    </div>
</div>

<div class="container page-content">

    <!-- Filter Bar -->
    <div class="room-filter-bar">
        <form action="rooms" method="GET" class="room-filter-form">
            <div class="filter-group">
                <label for="capacity">حداقل ظرفیت (تعداد نفرات)</label>
                <input type="number" id="capacity" name="capacity" min="1" placeholder="مثال: 2" value="<?php echo htmlspecialchars($_GET['capacity'] ?? ''); ?>">
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">جستجو</button>
            </div>
        </form>
    </div>

    <!-- Rooms Listing -->
    <div class="rooms-list-grid">
        <?php if (empty($rooms)): ?>
            <div class="feedback-banner info">
                هیچ اتاقی با معیارهای جستجوی شما یافت نشد. لطفاً فیلترها را تغییر دهید یا <a href="rooms">همه اتاق‌ها را مشاهده کنید</a>.
            </div>
        <?php else: ?>
            <?php foreach ($rooms as $room): ?>
            <div class="room-list-card">
                <div class="room-list-image">
                    <img src="<?php echo htmlspecialchars($room['main_image_url'] ?: 'https://placehold.co/800x600/87CEEB/FFFFFF?text=Hotel+Room'); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>">
                </div>
                <div class="room-list-content">
                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                    <p class="room-capacity">ظرفیت: <?php echo htmlspecialchars($room['capacity']); ?> نفر</p>
                    <p class="room-description"><?php echo mb_substr(htmlspecialchars($room['description']), 0, 150) . '...'; // Shorten description ?></p>
                    <div class="room-list-footer">
                        <span class="price">شروع از شبی <strong><?php echo number_format($room['price_per_night']); ?> تومان</strong></span>
                        <a href="room_details?id=<?php echo $room['id']; ?>" class="btn btn-secondary">مشاهده جزئیات</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
