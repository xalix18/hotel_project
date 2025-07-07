<?php
// admin/edit_announcement.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$announcement = ['id' => null, 'title' => '', 'content' => '', 'image_url' => ''];
$page_title = 'افزودن اطلاعیه جدید';
$is_editing = false;
$upload_dir = '../uploads/';

if (isset($_GET['id'])) {
    $is_editing = true;
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $announcement = $stmt->fetch();
    if (!$announcement) {
        header('Location: manage_announcements');
        exit;
    }
    $page_title = 'ویرایش اطلاعیه';
}

// --- Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_url = $_POST['existing_image_url']; // Keep old image by default

    // --- Handle New Image Upload ---
    if (isset($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
        $image_file = $_FILES['announcement_image'];
        $filename = 'announcement_' . uniqid() . '.' . pathinfo($image_file['name'], PATHINFO_EXTENSION);
        $destination = $upload_dir . $filename;

        if (move_uploaded_file($image_file['tmp_name'], $destination)) {
            // If editing and an old image exists, delete it
            if ($is_editing && !empty($announcement['image_url'])) {
                if (file_exists('../' . $announcement['image_url'])) {
                    unlink('../' . $announcement['image_url']);
                }
            }
            $image_url = 'uploads/' . $filename; // Set new image path
        } else {
            $feedback = ['message' => 'خطا در آپلود تصویر جدید.', 'type' => 'error'];
        }
    }

    if (empty($title) || empty($content)) {
        $feedback = ['message' => 'عنوان و محتوای اطلاعیه نمی‌توانند خالی باشند.', 'type' => 'error'];
    } elseif (!isset($feedback)) { // Proceed if no upload error
        try {
            if ($is_editing) {
                $sql = "UPDATE announcements SET title = :title, content = :content, image_url = :image_url WHERE id = :id";
                $params = ['title' => $title, 'content' => $content, 'image_url' => $image_url, 'id' => $id];
            } else {
                $sql = "INSERT INTO announcements (title, content, image_url) VALUES (:title, :content, :image_url)";
                $params = ['title' => $title, 'content' => $content, 'image_url' => $image_url];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            $_SESSION['feedback'] = ['message' => 'اطلاعیه با موفقیت ذخیره شد.', 'type' => 'success'];
            header('Location: manage_announcements');
            exit;
        } catch (PDOException $e) {
            $feedback = ['message' => 'خطا در ذخیره اطلاعیه.', 'type' => 'error'];
        }
    }
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

    <div class="form-container">
        <form action="edit_announcement<?php echo $is_editing ? '?id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
            <div class="input-group full-width">
                <label for="title">عنوان *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
            </div>
            <div class="input-group full-width">
                <label for="content">محتوا *</label>
                <textarea id="content" name="content" rows="8" required><?php echo htmlspecialchars($announcement['content']); ?></textarea>
            </div>
            
            <?php if ($is_editing && !empty($announcement['image_url'])): ?>
            <div class="input-group full-width">
                <label>تصویر فعلی</label>
                <img src="../<?php echo htmlspecialchars($announcement['image_url']); ?>" alt="Current announcement image" class="form-current-image">
            </div>
            <?php endif; ?>

            <div class="input-group full-width">
                <label for="announcement_image">آپلود تصویر (اختیاری)</label>
                 <p class="input-hint">با آپلود تصویر جدید، تصویر فعلی جایگزین خواهد شد.</p>
                <input type="file" id="announcement_image" name="announcement_image">
                <input type="hidden" name="existing_image_url" value="<?php echo htmlspecialchars($announcement['image_url']); ?>">
            </div>
            <button type="submit" class="btn-submit">ذخیره</button>
            <a href="manage_announcements" class="btn-cancel">انصراف</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
