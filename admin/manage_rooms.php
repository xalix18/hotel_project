<?php
// admin/manage_rooms.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index');
    exit;
}

require_once '../includes/db_connect.php';

// --- Search & Pagination Setup ---
$limit = 5; // Number of rooms per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// --- Handle Delete Room Request (GET) ---
if (isset($_GET['delete_id'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id', FILTER_VALIDATE_INT);
    
    $pdo->beginTransaction();
    try {
        // 1. Get all image paths associated with the room before deleting
        $image_paths_to_delete = [];
        
        // Get main image path
        $stmt_main = $pdo->prepare("SELECT main_image_url FROM rooms WHERE id = :id");
        $stmt_main->execute([':id' => $delete_id]);
        $main_image = $stmt_main->fetchColumn();
        if ($main_image && strpos($main_image, 'uploads/') === 0) { // Ensure it's an uploaded file
            $image_paths_to_delete[] = $main_image;
        }
        
        // Get all gallery image paths
        $stmt_gallery = $pdo->prepare("SELECT image_url FROM room_images WHERE room_id = :id");
        $stmt_gallery->execute([':room_id' => $delete_id]);
        $gallery_images = $stmt_gallery->fetchAll(PDO::FETCH_COLUMN);
        $image_paths_to_delete = array_merge($image_paths_to_delete, $gallery_images);

        // 2. Delete the room from the database. Due to `ON DELETE CASCADE`,
        // records in `room_images` will be deleted automatically.
        $sql = "DELETE FROM rooms WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $delete_id]);
        
        $pdo->commit();

        // 3. If DB deletion was successful, delete the actual files from the server
        foreach ($image_paths_to_delete as $path) {
            $full_path = '../' . $path;
            if (file_exists($full_path) && is_file($full_path)) {
                unlink($full_path);
            }
        }

        $_SESSION['feedback'] = ['message' => 'اتاق و تمام تصاویر مرتبط با موفقیت حذف شدند.', 'type' => 'success'];

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['feedback'] = ['message' => 'خطا در حذف اتاق.', 'type' => 'error'];
    }
    header('Location: manage_rooms?page=' . $page . '&search=' . urlencode($search_query)); // Redirect back to the current page
    exit;
}

// --- Build SQL query based on search ---
$where_clause = '';
$params = [];
if (!empty($search_query)) {
    $where_clause = " WHERE name LIKE :search_query";
    $params[':search_query'] = '%' . $search_query . '%';
}

// --- Fetch total number of rooms for pagination ---
try {
    $total_rooms_sql = "SELECT COUNT(*) FROM rooms" . $where_clause;
    $total_rooms_stmt = $pdo->prepare($total_rooms_sql);
    $total_rooms_stmt->execute($params);
    $total_rooms = $total_rooms_stmt->fetchColumn();
    $total_pages = ceil($total_rooms / $limit);
} catch (PDOException $e) {
    die("خطا در شمارش اتاق‌ها: " . $e->getMessage());
}

// --- Fetch rooms for the current page ---
try {
    $rooms_sql = "SELECT * FROM rooms" . $where_clause . " ORDER BY id DESC LIMIT :limit OFFSET :offset";
    $rooms_stmt = $pdo->prepare($rooms_sql);
    
    // Bind search param if it exists
    if (!empty($search_query)) {
        $rooms_stmt->bindParam(':search_query', $params[':search_query'], PDO::PARAM_STR);
    }
    
    $rooms_stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $rooms_stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $rooms_stmt->execute();
    $rooms = $rooms_stmt->fetchAll();
} catch (PDOException $e) {
    die("خطا در دریافت اطلاعات اتاق‌ها: " . $e->getMessage());
}

$page_title = 'مدیریت اتاق‌ها';
include 'includes/header.php';
?>

<div class="main-content">
    <div class="page-title-bar">
        <h1>مدیریت اتاق‌ها</h1>
        <a href="edit_room" class="btn-primary-action">افزودن اتاق جدید</a>
    </div>

    <?php
    if (isset($_SESSION['feedback'])) {
        echo '<div class="feedback-banner ' . $_SESSION['feedback']['type'] . '">' . $_SESSION['feedback']['message'] . '</div>';
        unset($_SESSION['feedback']);
    }
    ?>

    <!-- Search Form -->
    <div class="search-container">
        <form action="manage_rooms" method="GET">
            <input type="text" name="search" placeholder="جستجوی نام اتاق..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">جستجو</button>
        </form>
    </div>

    <!-- List of Existing Rooms -->
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>تصویر</th>
                    <th>نام اتاق</th>
                    <th>ظرفیت</th>
                    <th>قیمت هر شب</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rooms)): ?>
                    <tr>
                        <td colspan="5">هیچ اتاقی یافت نشد. <?php if(!empty($search_query)) echo '<a href="manage_rooms">نمایش همه</a>'; ?></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td>
                            <img src="../<?php echo htmlspecialchars($room['main_image_url'] ?: 'https://placehold.co/100x75/eee/ccc?text=No+Image'); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="table-thumbnail">
                        </td>
                        <td><?php echo htmlspecialchars($room['name']); ?></td>
                        <td><?php echo htmlspecialchars($room['capacity']); ?> نفر</td>
                        <td><?php echo number_format($room['price_per_night']); ?> تومان</td>
                        <td>
                            <a href="edit_room?id=<?php echo $room['id']; ?>" class="btn-action btn-edit">ویرایش</a>
                            <a href="manage_rooms?delete_id=<?php echo $room['id']; ?>&page=<?php echo $page; ?>&search=<?php echo urlencode($search_query); ?>" class="btn-action btn-delete" onclick="return confirm('آیا از حذف این اتاق و تمام تصاویر گالری آن اطمینان دارید؟');">حذف</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($total_pages > 1): ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
