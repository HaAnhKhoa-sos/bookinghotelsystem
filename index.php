<?php

require_once __DIR__ . '/config/init.php';

$routes = array(
    'home' => 'home/index.php',
    'rooms' => 'rooms/index.php',
    'room-detail' => 'room-detail/index.php',
);
$page = $_GET['page'] ?? 'home';
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

require __DIR__ . '/layouts/public/header.php';
echo $pageContent;
require __DIR__ . '/layouts/public/footer.php';
