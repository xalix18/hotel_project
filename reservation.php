<?php
// reservation.php - Public reservation form with interactive preview
session_start();
require_once 'includes/db_connect.php';

$feedback_message = '';
$feedback_type = '';
$show_form = true;

// Fetch all room data including gallery images for the interactive preview
try {
    $sql = "SELECT 
                r.id, r.name, r.description, r.price_per_night, r.capacity, r.main_image_url,
                GROUP_CONCAT(ri.image_url SEPARATOR '|||') as gallery_images
            FROM rooms r
            LEFT JOIN room_images ri ON r.id = ri.room_id
            GROUP BY r.id
            ORDER BY r.price_per_night ASC";
            
    $stmt = $pdo->query($sql);
    $all_rooms_data = $stmt->fetchAll();

    // Process gallery images string into an array
    foreach ($all_rooms_data as $key => $room) {
        if ($room['gallery_images']) {
            $all_rooms_data[$key]['gallery_images'] = explode('|||', $room['gallery_images']);
        } else {
            // If no gallery images, use the main image as a fallback in an array
            $all_rooms_data[$key]['gallery_images'] = $room['main_image_url'] ? [$room['main_image_url']] : [];
        }
    }

} catch (PDOException $e) {
    die("خطا: امکان دریافت لیست اتاق‌ها وجود ندارد: " . $e->getMessage());
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = trim($_POST['guest_name']);
    $guest_email = trim($_POST['guest_email']);
    $guest_phone = trim($_POST['guest_phone']);
    $room_id = trim($_POST['room_id'] ?? '');
    $check_in = trim($_POST['check_in_date']);
    $check_out = trim($_POST['check_out_date']);

    if (empty($guest_name) || empty($guest_email) || empty($guest_phone) || empty($room_id) || empty($check_in) || empty($check_out)) {
        $feedback_message = 'لطفاً یک اتاق انتخاب کرده و تمام فیلدهای فرم را پر کنید.';
        $feedback_type = 'error';
    } elseif (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {
        $feedback_message = 'ایمیل وارد شده معتبر نیست.';
        $feedback_type = 'error';
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $feedback_message = 'تاریخ خروج باید بعد از تاریخ ورود باشد.';
        $feedback_type = 'error';
    } else {
        try {
            $sql = "INSERT INTO reservations (room_id, guest_name, guest_email, guest_phone, check_in_date, check_out_date, status) VALUES (:room_id, :guest_name, :guest_email, :guest_phone, :check_in, :check_out, 'در انتظار')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':room_id' => $room_id,
                ':guest_name' => $guest_name,
                ':guest_email' => $guest_email,
                ':guest_phone' => $guest_phone,
                ':check_in' => $check_in,
                ':check_out' => $check_out
            ]);
            $last_id = $pdo->lastInsertId();
            $feedback_message = "درخواست رزرو شما با موفقیت ثبت شد. <br><strong>شناسه رزرو شما: $last_id</strong><br> لطفاً این شناسه را برای پیگیری وضعیت رزرو خود از طریق صفحه 'پیگیری رزرو' نزد خود نگه دارید.";
            $feedback_type = 'success';
            $show_form = false;
        } catch (PDOException $e) {
            $feedback_message = 'خطا در ثبت رزرو. لطفاً دوباره تلاش کنید.';
            $feedback_type = 'error';
        }
    }
}

$page_title = 'رزرو آنلاین - هتل آسمان آبی';
$page_slug = 'reservation';
include 'includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>رزرو آنلاین</h1>
        <p>اتاق مورد نظر خود را انتخاب کرده و فرم زیر را تکمیل کنید</p>
    </div>
</div>

<div class="container page-content">
    <div class="reservation-form-container">
        <?php if (!empty($feedback_message)): ?>
            <div class="feedback-banner <?php echo $feedback_type; ?>">
                <?php echo $feedback_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_form): ?>
        <form action="reservation" method="POST" id="reservationForm">
            
            <!-- Room Preview Div (initially hidden) -->
            <div id="selected-room-preview" class="selected-room-preview-container" style="display: none;">
                <h3 id="preview-title"></h3>
                <div class="preview-content">
                    <div class="preview-gallery">
                        <div class="gallery-main-image">
                            <img id="main-preview-image" src="" alt="Selected room image">
                        </div>
                        <div id="gallery-thumbnails" class="gallery-thumbnails">
                            <!-- Thumbnails will be injected by JS -->
                        </div>
                    </div>
                    <div class="preview-details">
                        <p id="preview-description"></p>
                        <p><strong>ظرفیت:</strong> <span id="preview-capacity"></span> نفر</p>
                        <p><strong>قیمت هر شب:</strong> <span id="preview-price"></span> تومان</p>
                    </div>
                </div>
            </div>

            <!-- Form Fields -->
            <div class="form-grid-public">
                <div class="input-group full-width">
                    <label for="room_id">۱. ابتدا اتاق مورد نظر خود را انتخاب کنید *</label>
                    <select id="room_id" name="room_id" required>
                        <option value="">یک اتاق را انتخاب کنید...</option>
                        <?php foreach($all_rooms_data as $room): ?>
                            <option value="<?php echo $room['id']; ?>" <?php echo (isset($_GET['room_id']) && $_GET['room_id'] == $room['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($room['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label for="guest_name">نام و نام خانوادگی *</label>
                    <input type="text" id="guest_name" name="guest_name" required>
                </div>
                <div class="input-group">
                    <label for="guest_email">ایمیل *</label>
                    <input type="email" id="guest_email" name="guest_email" required>
                </div>
                 <div class="input-group">
                    <label for="guest_phone">شماره تماس *</label>
                    <input type="tel" id="guest_phone" name="guest_phone" required>
                </div>
                 <div class="input-group">
                    <label for="check_in_date">تاریخ ورود *</label>
                    <input type="date" id="check_in_date" name="check_in_date" required>
                </div>
                 <div class="input-group">
                    <label for="check_out_date">تاریخ خروج *</label>
                    <input type="date" id="check_out_date" name="check_out_date" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full">ثبت نهایی درخواست</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<!-- Pass PHP data to JavaScript -->
<script type="application/json" id="rooms-data">
    <?php echo json_encode($all_rooms_data, JSON_UNESCAPED_UNICODE); ?>
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_id');
    const roomsData = JSON.parse(document.getElementById('rooms-data').textContent);
    
    const previewContainer = document.getElementById('selected-room-preview');
    const previewTitle = document.getElementById('preview-title');
    const mainPreviewImage = document.getElementById('main-preview-image');
    const galleryThumbnails = document.getElementById('gallery-thumbnails');
    const previewDescription = document.getElementById('preview-description');
    const previewCapacity = document.getElementById('preview-capacity');
    const previewPrice = document.getElementById('preview-price');

    function updatePreview(roomId) {
        if (!roomId) {
            previewContainer.style.display = 'none';
            return;
        }

        const room = roomsData.find(r => r.id == roomId);
        if (!room) {
            previewContainer.style.display = 'none';
            return;
        }

        // Populate the preview
        previewTitle.textContent = `پیش‌نمایش: ${room.name}`;
        previewDescription.textContent = room.description;
        previewCapacity.textContent = room.capacity;
        previewPrice.textContent = new Intl.NumberFormat('fa-IR').format(room.price_per_night);
        
        // Set main image
        const mainImage = room.gallery_images && room.gallery_images.length > 0 ? room.gallery_images[0] : 'https://placehold.co/800x600/87CEEB/FFFFFF?text=No+Image';
        mainPreviewImage.src = mainImage;
        mainPreviewImage.alt = `Main image of ${room.name}`;

        // Populate thumbnails
        galleryThumbnails.innerHTML = '';
        if (room.gallery_images && room.gallery_images.length > 1) {
            room.gallery_images.forEach((imgUrl, index) => {
                const thumb = document.createElement('img');
                thumb.src = imgUrl;
                thumb.alt = `Thumbnail ${index + 1} for ${room.name}`;
                thumb.classList.add('thumbnail-item');
                if (index === 0) {
                    thumb.classList.add('active');
                }
                thumb.addEventListener('click', () => {
                    mainPreviewImage.src = imgUrl;
                    // Update active thumbnail
                    galleryThumbnails.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                    thumb.classList.add('active');
                });
                galleryThumbnails.appendChild(thumb);
            });
        }

        previewContainer.style.display = 'block';
    }

    // Event listener for the select dropdown
    roomSelect.addEventListener('change', (event) => {
        updatePreview(event.target.value);
    });

    // Initial call to show preview if a room is pre-selected
    if (roomSelect.value) {
        updatePreview(roomSelect.value);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
