<?php // includes/header.php - Public Site Header
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Load all settings for use in the template
$settings = load_settings($pdo);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? $settings['hotel_name'] ?? 'هتل'); ?></title>
    <meta name="description" content="هتل مجلل <?php echo htmlspecialchars($settings['hotel_name'] ?? ''); ?>، اقامتی به یاد ماندنی در قلب شهر.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;700&display=swap" rel="stylesheet">
    
    <!-- Main CSS File -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <nav class="main-nav">
                <a href="index" class="logo"><?php echo htmlspecialchars($settings['hotel_name'] ?? 'هتل ما'); ?></a>
                <ul>
                    <li><a href="index" class="<?php echo ($page_slug === 'home') ? 'active' : ''; ?>">صفحه اصلی</a></li>
                    <li><a href="rooms" class="<?php echo ($page_slug === 'rooms') ? 'active' : ''; ?>">اتاق‌ها</a></li>
                    <li><a href="gallery" class="<?php echo ($page_slug === 'gallery') ? 'active' : ''; ?>">گالری</a></li>
                    <li><a href="about" class="<?php echo ($page_slug === 'about') ? 'active' : ''; ?>">درباره ما</a></li>
                    <li><a href="contact" class="<?php echo ($page_slug === 'contact') ? 'active' : ''; ?>">تماس با ما</a></li>
                    <li><a href="check_status" class="<?php echo ($page_slug === 'check_status') ? 'active' : ''; ?>">پیگیری رزرو</a></li>
                </ul>
                <a href="reservation" class="btn btn-primary">رزرو آنلاین</a>
            </nav>
        </div>
    </header>

    <main>
