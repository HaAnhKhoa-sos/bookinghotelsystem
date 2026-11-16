<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
} elseif (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
    http_response_code(403);
    set_flash('notice', 'Phiên đăng xuất không hợp lệ. Vui lòng thử lại.');
} else {
    unset($_SESSION['customer_id'], $_SESSION['after_login'], $_SESSION['after_login_room_id']);
    session_regenerate_id(true);
    set_flash('success', 'Bạn đã đăng xuất.');
    redirect(url('login'));
}
