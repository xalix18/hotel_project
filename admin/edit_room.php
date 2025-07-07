<?php
// admin/edit_room.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$room = ['id' => null, 'name' => '', 'description' => '', 'price_per_night' => '', 'capacity' => '', 'main_image_url' => ''];
$page_title = 'افزودن اتاق جدید';
$is_editing = false;
$upload_dir = '../uploads/';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_editing = true;
    $room_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :id");
    $stmt->execute(['id' => $room_id]);
    $room = $stmt->fetch();
    if (!$room) {
        header('Location: manage_rooms');
        exit;
    }
    $page_title = 'ویرایش اتاق';
}

// --- Handle Form Submission for both Add and Edit ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_room'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price_per_night']);
    $capacity = trim($_POST['capacity']);
    $main_image_url = $room['main_image_url']; // Keep old image by default

    // --- Handle Main Image Upload ---
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['main_image'];
        $filename = 'room_main_' . ($is_editing ? $room_id : 'new') . '_' . uniqid() . '.' . pathinfo($image_file['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $filename;

        if (move_uploaded_file($image_file['tmp_name'], $destination)) {
            // Delete old main image if it exists
            if ($is_editing && !empty($room['main_image_url']) && file_exists('../' . $room['main_image_url'])) {
                unlink('../' . $room['main_image_url']);
            }
            $main_image_url = 'uploads/' . $filename; // Set new image path
        }
    }

    if (empty($name) || empty($description) || empty($price) || empty($capacity)) {
        $feedback = ['message' => 'لطفاً تمام فیلدهای ستاره‌دار را پر کنید.', 'type' => 'error'];
    } else {
        try {
            if ($is_editing) {
                $sql = "UPDATE rooms SET name = :name, description = :description, price_per_night = :price, capacity = :capacity, main_image_url = :image_url WHERE id = :id";
                $params = ['name' => $name, 'description' => $description, 'price' => $price, 'capacity' => $capacity, 'image_url' => $main_image_url, 'id' => $room_id];
            } else {
                $sql = "INSERT INTO rooms (name, description, price_per_night, capacity, main_image_url) VALUES (:name, :description, :price, :capacity, :image_url)";
                $params = ['name' => $name, 'description' => $description, 'price' => $price, 'capacity' => $capacity, 'image_url' => $main_image_url];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['feedback'] = ['message' => 'اتاق با موفقیت ذخیره شد.', 'type' => 'success'];
            header('Location: manage_rooms');
            exit;
        } catch (PDOException $e) {
            $feedback = ['message' => 'خطا در ذخیره اطلاعات اتاق.', 'type' => 'error'];
        }
    }
}

// Fetch gallery images only when editing
$gallery_images = [];
if ($is_editing) {
    $gallery_stmt = $pdo->prepare("SELECT * FROM room_images WHERE room_id = :room_id");
    $gallery_stmt->execute([':room_id' => $room_id]);
    $gallery_images = $gallery_stmt->fetchAll();
}

include 'includes/header.php';
?>

<div class="main-content">
    <h1><?php echo $page_title; ?></h1>

    <?php if (isset($feedback)): ?>
        <div class="feedback-banner <?php echo $feedback['type']; ?>">
            <?php echo $feedback['message']; ?>
        </div>
    <?php endif; ?>

    <!-- Room Details Form -->
    <div class="form-container">
        <form action="edit_room<?php echo $is_editing ? '?id=' . $room_id : ''; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="input-group"><label for="name">نام اتاق *</label><input type="text" id="name" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required></div>
                <div class="input-group"><label for="price_per_night">قیمت (تومان) *</label><input type="number" id="price_per_night" name="price_per_night" value="<?php echo htmlspecialchars($room['price_per_night']); ?>" required></div>
                <div class="input-group"><label for="capacity">ظرفیت (نفر) *</label><input type="number" id="capacity" name="capacity" value="<?php echo htmlspecialchars($room['capacity']); ?>" required></div>
            </div>
            <div class="input-group full-width"><label for="description">توضیحات *</label><textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($room['description']); ?></textarea></div>
            
            <?php if ($is_editing && !empty($room['main_image_url'])): ?>
            <div class="input-group full-width">
                <label>تصویر اصلی فعلی (Thumbnail)</label>
                <img src="../<?php echo htmlspecialchars($room['main_image_url']); ?>" alt="Current main image" class="form-current-image">
            </div>
            <?php endif; ?>

            <div class="input-group full-width">
                <label for="main_image">آپلود تصویر اصلی</label>
                <p class="input-hint">این تصویر به عنوان تصویر اصلی اتاق در لیست‌ها نمایش داده می‌شود.</p>
                <input type="file" id="main_image" name="main_image" <?php echo !$is_editing ? 'required' : ''; ?>>
            </div>

            <button type="submit" name="save_room" class="btn-submit">ذخیره</button>
            <a href="manage_rooms" class="btn-cancel">انصراف</a>
        </form>
    </div>

    <?php if ($is_editing): ?>
    <!-- Image Gallery Management -->
    <div class="form-container">
        <h2>مدیریت تصاویر گالری اتاق</h2>
        <!-- Gallery management code from previous steps goes here -->
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
