<?php
// admin/manage_announcements.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

// --- Handle Delete Request ---
if (isset($_GET['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = :id");
        $stmt->execute([':id' => $_GET['delete_id']]);
        $_SESSION['feedback'] = ['message' => 'اطلاعیه با موفقیت حذف شد.', 'type' => 'success'];
    } catch (PDOException $e) {
        $_SESSION['feedback'] = ['message' => 'خطا در حذف اطلاعیه.', 'type' => 'error'];
    }
    header('Location: manage_announcements');
    exit;
}

// --- Fetch all announcements ---
$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll();

$page_title = 'مدیریت اطلاعیه‌ها';
include 'includes/header.php';
?>

<div class="main-content">
    <div class="page-title-bar">
        <h1>مدیریت اطلاعیه‌ها</h1>
        <a href="edit_announcement" class="btn-primary-action">افزودن اطلاعیه جدید</a>
    </div>

    <?php
    if (isset($_SESSION['feedback'])) {
        echo '<div class="feedback-banner ' . $_SESSION['feedback']['type'] . '">' . $_SESSION['feedback']['message'] . '</div>';
        unset($_SESSION['feedback']);
    }
    ?>

    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>عنوان</th>
                    <th>تاریخ ایجاد</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($announcements)): ?>
                    <tr>
                        <td colspan="3">هیچ اطلاعیه‌ای یافت نشد.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($announcements as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($item['created_at'])); ?></td>
                        <td>
                            <a href="edit_announcement?id=<?php echo $item['id']; ?>" class="btn-action btn-edit">ویرایش</a>
                            <a href="manage_announcements?delete_id=<?php echo $item['id']; ?>" class="btn-action btn-delete" onclick="return confirm('آیا از حذف این اطلاعیه اطمینان دارید؟');">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
