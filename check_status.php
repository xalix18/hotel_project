<?php
// check_status.php - Public page for guests to check their reservation status
session_start();
require_once 'includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';
$reservation_details = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = trim($_POST['reservation_id']);
    $guest_email = trim($_POST['guest_email']);

    if (empty($reservation_id) || empty($guest_email)) {
        $feedback_message = 'شناسه رزرو و ایمیل هر دو الزامی هستند.';
        $feedback_type = 'error';
    } elseif (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = 'ایمیل وارد شده معتبر نیست.';
        $feedback_type = 'error';
    } else {
        try {
            $sql = "SELECT r.*, rm.name as room_name 
                    FROM reservations r 
                    JOIN rooms rm ON r.room_id = rm.id 
                    WHERE r.id = :id AND r.guest_email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $reservation_id, ':email' => $guest_email]);
            $reservation_details = $stmt->fetch();

            if (!$reservation_details) {
                $feedback_message = 'رزروی با این مشخصات یافت نشد. لطفاً اطلاعات وارد شده را بررسی کنید.';
                $feedback_type = 'error';
            }
        } catch (PDOException $e) {
            $feedback_message = 'خطای سرور. لطفاً بعدا تلاش کنید.';
            $feedback_type = 'error';
        }
    }
}

$page_title = 'پیگیری رزرو - هتل آسمان آبی';
$page_slug = 'check_status';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>پیگیری وضعیت رزرو</h1>
        <p>برای مشاهده وضعیت رزرو خود، شناسه رزرو و ایمیلتان را وارد کنید</p>
    </div>
</div>

<div class="container page-content">
    <div class="form-container-public">
        <?php if (!empty($feedback_message) && !$reservation_details): ?>
            <div class="feedback-banner <?php echo $feedback_type; ?>">
                <?php echo htmlspecialchars($feedback_message); ?>
            </div>
        <?php endif; ?>

        <form action="check_status" method="POST">
            <div class="form-grid-public">
                <div class="input-group">
                    <label for="reservation_id">شناسه رزرو *</label>
                    <input type="text" id="reservation_id" name="reservation_id" value="<?php echo htmlspecialchars($_POST['reservation_id'] ?? ''); ?>" required>
                </div>
                <div class="input-group">
                    <label for="guest_email">ایمیل *</label>
                    <input type="email" id="guest_email" name="guest_email" value="<?php echo htmlspecialchars($_POST['guest_email'] ?? ''); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full">پیگیری</button>
        </form>

        <?php if ($reservation_details): ?>
            <div class="status-display-box">
                <h3>جزئیات رزرو شما (شناسه: <?php echo htmlspecialchars($reservation_details['id']); ?>)</h3>
                <p><strong>نام مهمان:</strong> <?php echo htmlspecialchars($reservation_details['guest_name']); ?></p>
                <p><strong>اتاق:</strong> <?php echo htmlspecialchars($reservation_details['room_name']); ?></p>
                <p><strong>تاریخ ورود:</strong> <?php echo htmlspecialchars($reservation_details['check_in_date']); ?></p>
                <p><strong>تاریخ خروج:</strong> <?php echo htmlspecialchars($reservation_details['check_out_date']); ?></p>
                <p class="status-text">
                    <strong>وضعیت:</strong> 
                    <?php
                        $status_class = '';
                        if ($reservation_details['status'] === 'تایید شده') $status_class = 'status-confirmed';
                        elseif ($reservation_details['status'] === 'لغو شده') $status_class = 'status-cancelled';
                        else $status_class = 'status-pending';
                    ?>
                    <span class="status-badge <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($reservation_details['status']); ?>
                    </span>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
