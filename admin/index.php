<?php

require_once __DIR__ . '/../config/init.php';

$routes = array(
    'dashboard' => 'dashboard/index.php',
    'rooms' => 'rooms/index.php',
    'bookings' => 'bookings/index.php',
);
$page = $_GET['page'] ?? 'dashboard';
$notFound = !is_string($page) || !isset($routes[$page]);
if ($notFound) {
    http_response_code(404);
    $pageFile = __DIR__ . '/pages/404/index.php';
} else {
    $pageFile = __DIR__ . '/pages/' . $routes[$page];
    $actionFile = dirname($pageFile) . '/action.php';
    if (is_file($actionFile)) {
        require $actionFile;
    }
}

ob_start();
require $pageFile;
$pageContent = ob_get_clean();

require __DIR__ . '/layouts/header.php';
echo $pageContent;
require __DIR__ . '/layouts/footer.php';
