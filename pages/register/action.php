<?php
$pageTitle = 'Đăng ký | HotelBooking';
$authErrors = array();
$registerValues = array('full_name' => '', 'email' => '', 'phone' => '');

if (current_customer_id()) {
    redirect(url('my-bookings'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $authErrors[] = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
    } else {
        foreach ($registerValues as $field => $default) {
            $registerValues[$field] = isset($_POST[$field]) && is_string($_POST[$field])
                ? trim($_POST[$field])
                : '';
        }
        $password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirmation = isset($_POST['password_confirmation']) && is_string($_POST['password_confirmation'])
            ? $_POST['password_confirmation']
            : '';

        if ($registerValues['full_name'] === '' || strlen($registerValues['full_name']) > 150) {
            $authErrors[] = 'Họ tên là bắt buộc và không vượt quá 150 ký tự.';
        }
        if (
            !filter_var($registerValues['email'], FILTER_VALIDATE_EMAIL)
            || strlen($registerValues['email']) > 190
        ) {
            $authErrors[] = 'Vui lòng nhập email hợp lệ (tối đa 190 ký tự).';
        } elseif (getCustomerByEmail($registerValues['email'])) {
            $authErrors[] = 'Email này đã được sử dụng. Nếu đây là hồ sơ cũ, hãy dùng email khác hoặc liên hệ quản trị viên.';
        }
        if ($registerValues['phone'] === '' || strlen($registerValues['phone']) > 30) {
            $authErrors[] = 'Số điện thoại là bắt buộc và không vượt quá 30 ký tự.';
        }
        if (strlen($password) < 8 || strlen($password) > 72) {
            $authErrors[] = 'Mật khẩu phải có từ 8 đến 72 ký tự.';
        } elseif ($password !== $passwordConfirmation) {
            $authErrors[] = 'Mật khẩu xác nhận không khớp.';
        }

        if (!$authErrors) {
            $customerId = createRegisteredCustomer($registerValues, password_hash($password, PASSWORD_DEFAULT));
            session_regenerate_id(true);
            $_SESSION['customer_id'] = $customerId;
            if (isset($_SESSION['after_login']) && $_SESSION['after_login'] === 'booking') {
                $destination = url('booking', array('room_id' => (int) $_SESSION['after_login_room_id']));
                unset($_SESSION['after_login'], $_SESSION['after_login_room_id']);
                redirect($destination);
            }
            redirect(url('my-bookings', array('success' => 'registered')));
        }
    }
}
