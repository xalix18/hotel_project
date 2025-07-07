<?php
// admin/dashboard.php - Main Admin Dashboard
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

// --- Fetch Dashboard Stats ---
try {
    // Count pending reservations
    $stmt_pending = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'در انتظار'");
    $pending_reservations = $stmt_pending->fetchColumn();

    // Count total rooms
    $stmt_rooms = $pdo->query("SELECT COUNT(*) FROM rooms");
    $total_rooms = $stmt_rooms->fetchColumn();
    
    // Count total confirmed reservations
    $stmt_confirmed = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'تایید شده'");
    $total_confirmed = $stmt_confirmed->fetchColumn();

    // Fetch 5 most recent reservations
    $stmt_recent = $pdo->query("SELECT r.*, rm.name as room_name 
                                FROM reservations r
                                JOIN rooms rm ON r.room_id = rm.id
                                ORDER BY r.created_at DESC
                                LIMIT 5");
    $recent_reservations = $stmt_recent->fetchAll();

} catch (PDOException $e) {
    // Handle error gracefully
    $error = "خطا در دریافت آمار: " . $e->getMessage();
    $pending_reservations = 'N/A';
    $total_rooms = 'N/A';
    $total_confirmed = 'N/A';
    $recent_reservations = [];
}

$page_title = 'داشبورد';
include 'includes/header.php';
?>

<div class="main-content">
    <h1>داشبورد مدیریت</h1>
    <p>خوش آمدید، <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! آمار کلی و فعالیت‌های اخیر وب‌سایت در یک نگاه.</p>
    
    <?php if(isset($error)): ?>
        <div class="feedback-banner error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="dashboard-widgets">
        <div class="widget">
            <div class="icon reservations">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path></svg>
            </div>
            <div class="info">
                <h3>رزروهای در انتظار</h3>
                <p class="stat"><?php echo htmlspecialchars($pending_reservations); ?></p>
            </div>
        </div>
        <div class="widget">
            <div class="icon rooms">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            </div>
            <div class="info">
                <h3>تعداد کل اتاق‌ها</h3>
                <p class="stat"><?php echo htmlspecialchars($total_rooms); ?></p>
            </div>
        </div>
        <div class="widget">
            <div class="icon confirmed">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <div class="info">
                <h3>کل رزروهای تایید شده</h3>
                <p class="stat"><?php echo htmlspecialchars($total_confirmed); ?></p>
            </div>
        </div>
    </div>

    <!-- Recent Reservations Table -->
    <div class="table-container dashboard-recent-activity">
        <h2>۵ رزرو اخیر</h2>
        <table class="content-table">
            <thead>
                <tr>
                    <th>نام مهمان</th>
                    <th>اتاق</th>
                    <th>تاریخ ورود</th>
                    <th>وضعیت</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_reservations)): ?>
                    <tr>
                        <td colspan="4">هیچ رزروی یافت نشد.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_reservations as $res): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['check_in_date']); ?></td>
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
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
