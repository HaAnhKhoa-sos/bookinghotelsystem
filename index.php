<?php

require_once __DIR__ . '/config/init.php';

$routes = array(
    // Trang chủ: giao diện ở pages/home/index.php, dữ liệu/lọc ở pages/home/action.php.
    'home' => 'home/index.php',
    'rooms' => 'rooms/index.php',
    // Chi tiết phòng: giao diện ở pages/room-detail/index.php, lấy phòng theo ID ở action.php.
    'room-detail' => 'room-detail/index.php',
    'booking' => 'booking/index.php',
    'login' => 'login/index.php',
    'register' => 'register/index.php',
    'logout' => 'logout/index.php',
    'my-bookings' => 'my-bookings/index.php',
    'profile' => 'profile/index.php',
);
$page = $_GET['page'] ?? 'home';
$notFound = !is_string($page) || !isset($routes[$page]);
if ($notFound) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404/index.php';
} else {
    $pageFile = __DIR__ . '/pages/' . $routes[$page];
    // Tự nạp action.php cùng thư mục trước khi render trang (nếu có).
    $actionFile = dirname($pageFile) . '/action.php';
    if (is_file($actionFile)) {
        require $actionFile;
    }
}

ob_start();
require $pageFile;
$pageContent = ob_get_clean();

// Khung dùng chung cho home, room-detail và các trang public khác.
require __DIR__ . '/layouts/public/header.php';
echo $pageContent;
require __DIR__ . '/layouts/public/footer.php';
