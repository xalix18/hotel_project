<?php
// contact.php - Public Contact Us page
$feedback_message = '';
$feedback_type = '';
$show_form = true;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $feedback_message = 'لطفاً تمام فیلدهای فرم را تکمیل کنید.';
        $feedback_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = 'آدرس ایمیل وارد شده معتبر نیست.';
        $feedback_type = 'error';
    } else {
        // In a real application, you would send an email here.
        // For this example, we just show a success message.
        $feedback_message = 'پیام شما با موفقیت ارسال شد. از تماس شما سپاسگزاریم.';
        $feedback_type = 'success';
        $show_form = false;
    }
}

$page_title = 'تماس با ما - هتل آسمان آبی';
$page_slug = 'contact';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>تماس با ما</h1>
        <p>ما همیشه آماده پاسخگویی به شما هستیم</p>
    </div>
</div>

<div class="container page-content">
    <div class="contact-page-grid">
        <div class="contact-form-wrapper">
            <h2>ارسال پیام</h2>
            <?php if (!empty($feedback_message)): ?>
                <div class="feedback-banner <?php echo $feedback_type; ?>">
                    <?php echo htmlspecialchars($feedback_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($show_form): ?>
            <form action="contact" method="POST">
                <div class="input-group">
                    <label for="name">نام شما *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="input-group">
                    <label for="email">ایمیل شما *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="subject">موضوع *</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="input-group">
                    <label for="message">پیام شما *</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-full">ارسال پیام</button>
            </form>
            <?php endif; ?>
        </div>
        <div class="contact-details-wrapper">
            <h2>اطلاعات تماس</h2>
            <div class="contact-info-item">
                <h4>آدرس</h4>
                <p>تهران، خیابان فرشته، کوچه شبنم، پلاک ۱۰، هتل آسمان آبی</p>
            </div>
            <div class="contact-info-item">
                <h4>تلفن رزرو</h4>
                <p><a href="tel:+982112345678">۰۲۱-۱۲۳۴۵۶۷۸</a></p>
            </div>
            <div class="contact-info-item">
                <h4>ایمیل</h4>
                <p><a href="mailto:info@blueskyhotel.ir">info@blueskyhotel.ir</a></p>
            </div>
            <div class="contact-info-item">
                <h4>ساعات کاری</h4>
                <p>پذیرش به صورت ۲۴ ساعته در ۷ روز هفته آماده خدمت‌رسانی است.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
