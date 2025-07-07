<?php
// admin/index.php - Admin Login Page
session_start();

// If the admin is already logged in, redirect to the dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard');
    exit;
}

require_once '../includes/db_connect.php';
$error_message = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = 'نام کاربری و رمز عبور نمی‌توانند خالی باشند.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch();

            // Verify user exists and password is correct
            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Password is correct, start the session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $admin['username'];
                
                header('Location: dashboard');
                exit;
            } else {
                $error_message = 'نام کاربری یا رمز عبور اشتباه است.';
            }
        } catch (PDOException $e) {
            $error_message = 'خطای سرور. لطفا بعدا تلاش کنید.';
            // In a real app, you would log this error: error_log($e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به پنل مدیریت هتل</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>ورود به پنل مدیریت</h1>
            <p>هتل آسمان آبی</p>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-banner">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form action="index" method="POST">
                <div class="input-group">
                    <label for="username">نام کاربری</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="input-group">
                    <label for="password">رمز عبور</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-login">ورود</button>
            </form>
        </div>
    </div>
</body>
</html>
