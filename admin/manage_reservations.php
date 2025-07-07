<?php
// admin/manage_reservations.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';

// --- Handle Status Update Request (GET) ---
if (isset($_GET['update_id']) && isset($_GET['status'])) {
    $update_id = $_GET['update_id'];
    $new_status = $_GET['status'];

    // Whitelist of allowed statuses to prevent security issues
    $allowed_statuses = ['تایید شده', 'لغو شده'];

    if (in_array($new_status, $allowed_statuses)) {
        try {
            $sql = "UPDATE reservations SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':status' => $new_status, ':id' => $update_id]);
            $feedback_message = 'وضعیت رزرو با موفقیت به‌روزرسانی شد.';
            $feedback_type = 'success';
        } catch (PDOException $e) {
            $feedback_message = 'خطا در به‌روزرسانی وضعیت رزرو: ' . $e->getMessage();
            $feedback_type = 'error';
        }
    } else {
        $feedback_message = 'وضعیت نامعتبر است.';
        $feedback_type = 'error';
    }
}

// --- Fetch all reservations with room names ---
try {
    $sql = "SELECT reservations.*, rooms.name AS room_name 
            FROM reservations 
            JOIN rooms ON reservations.room_id = rooms.id 
            ORDER BY reservations.created_at DESC";
    $reservations_stmt = $pdo->query($sql);
    $reservations = $reservations_stmt->fetchAll();
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات رزروها: " . $e->getMessage());
}

$page_title = 'مدیریت رزروها';
include 'includes/header.php';
?>

<div class="main-content">
    <h1>مدیریت رزروها</h1>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback-banner <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <!-- List of Existing Reservations -->
    <div class="table-container">
        <h2>لیست تمام رزروها</h2>
        <table class="content-table">
            <thead>
                <tr>
                    <th>نام مهمان</th>
                    <th>اتاق</th>
                    <th>تاریخ ورود</th>
                    <th>تاریخ خروج</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="6">هیچ رزروی یافت نشد.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['check_in_date']); ?></td>
                        <td><?php echo htmlspecialchars($res['check_out_date']); ?></td>
                        <td>
                            <?php
                                $status_class = '';
                                if ($res['status'] === 'تایید شده') $status_class = 'status-confirmed';
                                elseif ($res['status'] === 'لغو شده') $status_class = 'status-cancelled';
                                else $status_class = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $status_class; ?>">
                                <?php echo htmlspecialchars($res['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($res['status'] === 'در انتظار'): ?>
                                <a href="manage_reservations?update_id=<?php echo $res['id']; ?>&status=تایید شده" class="btn-action btn-approve">تایید</a>
                                <a href="manage_reservations?update_id=<?php echo $res['id']; ?>&status=لغو شده" class="btn-action btn-delete" onclick="return confirm('آیا از لغو این رزرو اطمینان دارید؟');">لغو</a>
                            <?php elseif ($res['status'] === 'تایید شده'): ?>
                                 <a href="manage_reservations?update_id=<?php echo $res['id']; ?>&status=لغو شده" class="btn-action btn-delete" onclick="return confirm('آیا از لغو این رزرو اطمینان دارید؟');">لغو کردن</a>
                            <?php else: ?>
                                --
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
