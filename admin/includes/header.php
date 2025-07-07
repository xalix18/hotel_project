<?php // admin/includes/header.php ?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'پنل مدیریت'); ?> - هتل آسمان آبی</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>هتل آسمان آبی</h2>
            <p>پنل مدیریت</p>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="dashboard" class="<?php echo ($page_title === 'داشبورد') ? 'active' : ''; ?>">داشبورد</a></li>
                <li><a href="manage_reservations" class="<?php echo ($page_title === 'مدیریت رزروها') ? 'active' : ''; ?>">مدیریت رزروها</a></li>
                <li><a href="manage_rooms" class="<?php echo ($page_title === 'مدیریت اتاق‌ها' || $page_title === 'ویرایش اتاق') ? 'active' : ''; ?>">مدیریت اتاق‌ها</a></li>
                <li><a href="manage_announcements" class="<?php echo (strpos($page_title, 'اطلاعیه') !== false) ? 'active' : ''; ?>">مدیریت اطلاعیه‌ها</a></li>
                <li><a href="manage_gallery" class="<?php echo ($page_title === 'مدیریت گالری') ? 'active' : ''; ?>">مدیریت گالری</a></li>
                <li><a href="settings" class="<?php echo ($page_title === 'تنظیمات وب‌سایت') ? 'active' : ''; ?>">تنظیمات سایت</a></li>
                <li><a href="change_password" class="<?php echo ($page_title === 'تغییر رمز عبور') ? 'active' : ''; ?>">تغییر رمز عبور</a></li>
                <li><a href="logout" class="logout">خروج</a></li>
            </ul>
        </nav>
    </aside>
