
سیستم مدیریت هتل
یک وب‌سایت و سیستم مدیریت هتل کامل و پویا که با PHP، MySQL و فناوری‌های مدرن فرانت‌اند ساخته شده است. این پروژه یک رابط کاربری زیبا و کاربرپسند برای مهمانان و یک پنل مدیریت قدرتمند و امن برای کارکنان هتل جهت مدیریت تمام جنبه‌های کسب‌وکار فراهم می‌کند.

✨ امکانات
🏨 وب‌سایت عمومی (برای مهمانان)
صفحه اصلی مدرن: یک صفحه فرود واکنش‌گرا و جذاب با محتوای پویا.

لیست اتاق‌ها: صفحه‌ای اختصاصی برای مشاهده تمام اتاق‌های موجود همراه با سیستم جستجو و فیلتر.

جزئیات اتاق: نمای دقیق برای هر اتاق، کامل با گالری تصاویر و توضیحات.

رزرو آنلاین: فرمی با کاربری آسان برای مهمانان جهت رزرو اقامت خود.

پیگیری وضعیت رزرو: صفحه‌ای امن برای مهمانان جهت بررسی وضعیت رزرو خود با استفاده از شناسه رزرو و ایمیل.

گالری تصاویر: یک گالری با قابلیت لایت‌باکس برای نمایش تصاویر هتل.

محتوای پویا: نام هتل، اطلاعات تماس و متن صفحه اصلی همگی از پنل مدیریت قابل کنترل هستند.

⚙️ پنل مدیریت (برای کارکنان هتل)
ورود امن: سیستم ورود امن برای مدیران.

داشبورد پویا: نمای کلی از آمار کلیدی مانند رزروهای در انتظار و تعداد کل اتاق‌ها، به علاوه لیستی از آخرین رزروها.

مدیریت اتاق‌ها: قابلیت کامل CRUD (ایجاد، خواندن، به‌روزرسانی، حذف) برای اتاق‌های هتل.

مدیریت تصاویر: آپلود تصویر اصلی و گالری کامل تصاویر برای هر اتاق.

مدیریت رزروها: مشاهده تمام رزروها و قابلیت تایید یا لغو آن‌ها.

اطلاعیه‌ها: ایجاد و مدیریت اخبار یا پیشنهادات ویژه که در صفحه اصلی نمایش داده می‌شوند.

تنظیمات وب‌سایت: تغییر آسان نام هتل، اطلاعات تماس و تصویر اصلی صفحه اول.

تغییر رمز عبور امن: صفحه‌ای اختصاصی برای مدیر جهت به‌روزرسانی رمز عبور خود.

🛠️ پشته فناوری
بک‌اند: PHP

پایگاه داده: MySQL / MariaDB

فرانت‌اند: HTML5, CSS3 (با متغیرهای CSS), JavaScript

محیط سرور: XAMPP / WAMP / MAMP یا هر سرور دیگری با پشتیبانی از PHP و MySQL.

🚀 نصب و راه‌اندازی
برای اجرای پروژه روی سیستم محلی خود، این مراحل را دنبال کنید.

۱. پیش‌نیازها
اطمینان حاصل کنید که یک محیط سرور محلی مانند XAMPP یا WAMP را نصب کرده‌اید که Apache، PHP و MySQL را فراهم می‌کند.

۲. راه‌اندازی پایگاه داده
ابزار مدیریت پایگاه داده خود را باز کنید (مانند phpMyAdmin).

یک پایگاه داده جدید با نام hotel_db ایجاد کنید.

فایل .sql موجود در پوشه sql/ را در پایگاه داده hotel_db ایمپورت کنید. این کار تمام جداول لازم (admins, rooms, reservations و غیره) را ایجاد کرده و آن‌ها را با داده‌های نمونه پر می‌کند.

۳. پیکربندی اتصال به پایگاه داده
فایل includes/db_connect.php را باز کنید.

اگر اطلاعات پایگاه داده شما با مقادیر پیش‌فرض متفاوت است، خطوط زیر را به‌روزرسانی کنید:

define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // در XAMPP به طور پیش‌فرض خالی است

۴. ایجاد کاربر مدیر
در مرورگر وب خود، به فایل includes/create_admin.php بروید. برای مثال: http://localhost/your-project-folder/includes/create_admin.php

باید یک پیام موفقیت‌آمیز مبنی بر ایجاد کاربر مدیر مشاهده کنید.

مهم: به دلایل امنیتی، فایل create_admin.php را بلافاصله پس از مشاهده پیام موفقیت حذف کنید.

۵. دسترسی پوشه‌ها
اطمینان حاصل کنید که پوشه uploads/ در ریشه پروژه توسط وب‌سرور شما قابل نوشتن (writable) باشد. این کار برای عملکرد صحیح قابلیت آپلود تصاویر ضروری است.

۶. اجرای وب‌سایت
سایت عمومی: در مرورگر خود به پوشه اصلی پروژه بروید (مثلاً http://localhost/your-project-folder/).

پنل مدیریت: به پوشه ادمین بروید (مثلاً http://localhost/your-project-folder/admin/).

🔑 اطلاعات ورود پیش‌فرض مدیر
نام کاربری: admin

رمز عبور: 1384

اکیداً توصیه می‌شود که بلافاصله پس از اولین ورود، رمز عبور خود را از طریق بخش "تغییر رمز عبور" در پنل مدیریت تغییر دهید.

========================================================================

Hotel Management System
A complete and dynamic hotel website and management system built with PHP, MySQL, and modern frontend technologies. This project provides a beautiful, user-friendly interface for guests and a powerful, secure admin panel for hotel staff to manage all aspects of the business.

Features
🏨 Public-Facing Website (for Guests)
Modern Homepage: A responsive and attractive landing page with dynamic content.

Room Listings: A dedicated page to browse all available rooms with a search/filter system.

Room Details: A detailed view for each room, complete with a photo gallery and description.

Online Reservations: An easy-to-use form for guests to book their stay.

Reservation Status Tracking: A secure page for guests to check the status of their booking using their reservation ID and email.

Photo Gallery: A lightbox-enabled gallery to showcase the hotel.

Dynamic Content: Hotel name, contact info, and homepage text are all managed from the admin panel.

⚙️ Admin Panel (for Hotel Staff)
Secure Login: A secure login system for administrators.

Dynamic Dashboard: An at-a-glance view of key statistics like pending reservations and total rooms, plus a list of the most recent bookings.

Room Management: Full CRUD (Create, Read, Update, Delete) functionality for hotel rooms.

Image Management: Upload a main thumbnail and a full image gallery for each room.

Reservation Management: View all reservations, and approve or cancel them.

Announcements: Create and manage news or special offers that appear on the homepage.

Website Settings: Easily change the hotel's name, contact details, and homepage hero image.

Secure Password Change: A dedicated page for the admin to update their password.

🛠️ Technology Stack
Backend: PHP

Database: MySQL / MariaDB

Frontend: HTML5, CSS3 (with CSS Variables), JavaScript

Server Environment: XAMPP / WAMP / MAMP or any server with PHP and MySQL support.

🚀 Installation and Setup
Follow these steps to get the project running on your local machine.

1. Prerequisites
Make sure you have a local server environment like XAMPP or WAMP installed, which provides Apache, PHP, and MySQL.

2. Database Setup
Open your database management tool (e.g., phpMyAdmin).

Create a new database named hotel_db.

Import the .sql file from the sql/ directory into the hotel_db database. This will create all the necessary tables (admins, rooms, reservations, etc.) and populate them with some sample data.

3. Configure Database Connection
Open the file includes/db_connect.php.

Update the following lines with your database credentials if they are different from the defaults:

define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default is empty for XAMPP

4. Create Admin User
In your web browser, navigate to the file includes/create_admin.php. For example: http://localhost/your-project-folder/includes/create_admin.php

You should see a success message confirming the admin user was created.

IMPORTANT: For security reasons, delete the create_admin.php file immediately after you see the success message.

5. Folder Permissions
Ensure that the uploads/ directory in the root of the project is writable by your web server. This is necessary for the image upload features to work correctly.

6. Run the Website
Public Site: Navigate to your project's root folder in your browser (e.g., http://localhost/your-project-folder/).

Admin Panel: Navigate to the admin folder (e.g., http://localhost/your-project-folder/admin/).

🔑 Default Admin Credentials
Username: admin

Password: 1384

It is highly recommended to change the password immediately after your first login using the "Change Password" feature in the admin panel.


