<?php
$pageTitle = 'Hồ sơ cá nhân | HotelBooking';
if (!current_customer_id()) {
    set_flash('notice', 'Vui lòng đăng nhập để quản lý hồ sơ.');
    redirect(url('login'));
}

$profileErrors = array();
$customerProfile = getCustomerById(current_customer_id());
if (!$customerProfile) {
    unset($_SESSION['customer_id']);
    set_flash('notice', 'Phiên đăng nhập không còn hợp lệ. Vui lòng đăng nhập lại.');
    redirect(url('login'));
}
$profileValues = array(
    'full_name' => $customerProfile['full_name'],
    'email' => $customerProfile['email'],
    'phone' => $customerProfile['phone'],
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $profileErrors[] = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
    } else {
        $profileAction = isset($_POST['form_action']) && is_string($_POST['form_action']) ? $_POST['form_action'] : '';
        if ($profileAction === 'profile') {
            foreach ($profileValues as $field => $default) {
                $profileValues[$field] = isset($_POST[$field]) && is_string($_POST[$field])
                    ? trim($_POST[$field])
                    : '';
            }
            if ($profileValues['full_name'] === '' || strlen($profileValues['full_name']) > 150) {
                $profileErrors[] = 'Họ tên là bắt buộc và không vượt quá 150 ký tự.';
            }
            if (!filter_var($profileValues['email'], FILTER_VALIDATE_EMAIL) || strlen($profileValues['email']) > 190) {
                $profileErrors[] = 'Vui lòng nhập email hợp lệ (tối đa 190 ký tự).';
            } else {
                $emailOwner = getCustomerByEmail($profileValues['email']);
                if ($emailOwner && (int) $emailOwner['id'] !== current_customer_id()) {
                    $profileErrors[] = 'Email này đã được sử dụng bởi tài khoản khác.';
                }
            }
            if ($profileValues['phone'] === '' || strlen($profileValues['phone']) > 30) {
                $profileErrors[] = 'Số điện thoại là bắt buộc và không vượt quá 30 ký tự.';
            }
            if (!$profileErrors) {
                updateCustomerProfile(current_customer_id(), $profileValues);
                redirect(url('profile', array('success' => 'profile')));
            }
        } elseif ($profileAction === 'password') {
            $currentPassword = isset($_POST['current_password']) && is_string($_POST['current_password']) ? $_POST['current_password'] : '';
            $newPassword = isset($_POST['new_password']) && is_string($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirmation = isset($_POST['password_confirmation']) && is_string($_POST['password_confirmation']) ? $_POST['password_confirmation'] : '';
            $storedCustomer = getCustomerByEmail($customerProfile['email']);
            if (!$storedCustomer || empty($storedCustomer['password_hash']) || !password_verify($currentPassword, $storedCustomer['password_hash'])) {
                $profileErrors[] = 'Mật khẩu hiện tại không chính xác.';
            }
            if (strlen($newPassword) < 8 || strlen($newPassword) > 72) {
                $profileErrors[] = 'Mật khẩu mới phải có từ 8 đến 72 ký tự.';
            } elseif ($newPassword !== $confirmation) {
                $profileErrors[] = 'Mật khẩu xác nhận không khớp.';
            }
            if (!$profileErrors) {
                updateCustomerPassword(current_customer_id(), password_hash($newPassword, PASSWORD_DEFAULT));
                redirect(url('profile', array('success' => 'password')));
            }
        } else {
            http_response_code(400);
            $profileErrors[] = 'Thao tác cập nhật hồ sơ không hợp lệ.';
        }
    }
}
