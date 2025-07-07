<?php // includes/footer.php - Public Site Footer ?>
    </main><!-- End of <main> from header.php -->

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4><?php echo htmlspecialchars($settings['hotel_name'] ?? 'هتل ما'); ?></h4>
                    <p>ما متعهد به ارائه بهترین تجربه اقامتی برای شما هستیم. آسایش شما، افتخار ماست.</p>
                </div>
                <div class="footer-col">
                    <h4>لینک‌های سریع</h4>
                    <ul>
                        <li><a href="reservation">رزرو اتاق</a></li>
                        <li><a href="#">قوانین و مقررات</a></li>
                        <li><a href="#">فرصت‌های شغلی</a></li>
                        <li><a href="contact">تماس با ما</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>تماس با ما</h4>
                    <p><strong>آدرس:</strong> <?php echo htmlspecialchars($settings['hotel_address'] ?? ''); ?></p>
                    <p><strong>تلفن:</strong> <a href="tel:<?php echo htmlspecialchars($settings['hotel_phone'] ?? ''); ?>"><?php echo htmlspecialchars($settings['hotel_phone'] ?? ''); ?></a></p>
                    <p><strong>ایمیل:</strong> <a href="mailto:<?php echo htmlspecialchars($settings['hotel_email'] ?? ''); ?>"><?php echo htmlspecialchars($settings['hotel_email'] ?? ''); ?></a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>تمامی حقوق برای <?php echo htmlspecialchars($settings['hotel_name'] ?? 'هتل ما'); ?> محفوظ است. © <?php echo date('Y'); // Dynamic year ?> </p>
            </div>
        </div>
    </footer>

</body>
</html>
