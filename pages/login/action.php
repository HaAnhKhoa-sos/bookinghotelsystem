<?php
$pageTitle = 'Đăng nhập | HotelBooking';
$authErrors = array();
$loginEmail = '';

if (current_customer_id()) {
    redirect(url('my-bookings'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $authErrors[] = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
    } else {
        $loginEmail = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
        $customer = filter_var($loginEmail, FILTER_VALIDATE_EMAIL) ? getCustomerByEmail($loginEmail) : null;

        if (!$customer || empty($customer['password_hash']) || !password_verify($password, $customer['password_hash'])) {
            $authErrors[] = 'Email hoặc mật khẩu không chính xác. Khách hàng tạo trước khi có tài khoản cần đăng ký bằng email khác.';
        } else {
            session_regenerate_id(true);
            $_SESSION['customer_id'] = (int) $customer['id'];
            $destination = isset($_SESSION['after_login']) && $_SESSION['after_login'] === 'booking'
                ? url('booking', array('room_id' => (int) $_SESSION['after_login_room_id']))
                : url('my-bookings');
            unset($_SESSION['after_login'], $_SESSION['after_login_room_id']);
            redirect($destination);
        }
    }
}
