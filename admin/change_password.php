<?php
// admin/change_password.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $admin_username = $_SESSION['admin_username'];

    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $feedback_message = 'تمام فیلدها الزامی هستند.';
        $feedback_type = 'error';
    } elseif ($new_password !== $confirm_new_password) {
        $feedback_message = 'رمز عبور جدید و تکرار آن مطابقت ندارند.';
        $feedback_type = 'error';
    } else {
        try {
            // Get current password hash from DB
            $stmt = $pdo->prepare("SELECT password_hash FROM admins WHERE username = :username");
            $stmt->execute([':username' => $admin_username]);
            $user = $stmt->fetch();

            // Verify current password
            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Hash the new password and update it
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE admins SET password_hash = :password_hash WHERE username = :username");
                $update_stmt->execute([':password_hash' => $new_password_hash, ':username' => $admin_username]);

                $feedback_message = 'رمز عبور با موفقیت تغییر کرد.';
                $feedback_type = 'success';
            } else {
                $feedback_message = 'رمز عبور فعلی اشتباه است.';
                $feedback_type = 'error';
            }
        } catch (PDOException $e) {
            $feedback_message = 'خطا در برقراری ارتباط با پایگاه داده.';
            $feedback_type = 'error';
        }
    }
}

$page_title = 'تغییر رمز عبور';
include 'includes/header.php';
?>

<div class="main-content">
    <h1>تغییر رمز عبور</h1>

    <?php if (!empty($feedback_message)): ?>
        <div class="feedback-banner <?php echo $feedback_type; ?>">
            <?php echo htmlspecialchars($feedback_message); ?>
        </div>
    <?php endif; ?>

    <div class="form-container" style="max-width: 600px;">
        <form action="change_password" method="POST">
            <div class="input-group full-width">
                <label for="current_password">رمز عبور فعلی *</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="input-group full-width">
                <label for="new_password">رمز عبور جدید *</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="input-group full-width">
                <label for="confirm_new_password">تکرار رمز عبور جدید *</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" required>
            </div>
            <button type="submit" name="change_password" class="btn-submit">تغییر رمز</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
