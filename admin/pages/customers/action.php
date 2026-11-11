<?php
$requestedAction = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : 'create')
    : (isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '');
$customerId = isset($_GET['id']) && is_string($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false;
$editingCustomer = null;
$showCustomerForm = in_array($requestedAction, array('create', 'edit'), true);
$formTitle = $requestedAction === 'edit' ? 'Chỉnh sửa khách hàng' : 'Thêm khách hàng';
$errors = array();
$adminActionError = '';

if ($requestedAction === 'edit') {
    if (!$customerId) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy khách hàng cần chỉnh sửa.';
        $showCustomerForm = false;
    } else {
        $editingCustomer = getAdminCustomerById($customerId);
        if (!$editingCustomer) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy khách hàng cần chỉnh sửa.';
            $showCustomerForm = false;
        }
    }
}

$formValues = array(
    'full_name' => $editingCustomer ? $editingCustomer['full_name'] : '',
    'email' => $editingCustomer ? $editingCustomer['email'] : '',
    'phone' => $editingCustomer ? $editingCustomer['phone'] : '',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $csrfError = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang và thử lại.';
        if (in_array($requestedAction, array('create', 'edit'), true)) {
            $errors[] = $csrfError;
            $showCustomerForm = true;
        } else {
            $adminActionError = $csrfError;
        }
    } elseif ($requestedAction === 'delete') {
        if (!$customerId || !getAdminCustomerById($customerId)) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy khách hàng cần xóa.';
        } elseif (customerHasBookings($customerId)) {
            $adminActionError = 'Không thể xóa khách hàng đã có lịch sử đặt phòng.';
        } else {
            deleteCustomer($customerId);
            redirect(admin_url('customers', array('success' => 'deleted')));
        }
    } elseif ($requestedAction === 'edit' && (!$customerId || !getAdminCustomerById($customerId))) {
        http_response_code(404);
        $adminActionError = 'Không tìm thấy khách hàng cần chỉnh sửa.';
        $showCustomerForm = false;
    } elseif (!in_array($requestedAction, array('create', 'edit'), true)) {
        http_response_code(400);
        $adminActionError = 'Thao tác quản lý khách hàng không hợp lệ.';
    } else {
        $showCustomerForm = true;
        foreach ($formValues as $field => $default) {
            $formValues[$field] = '';
        }
        foreach ($formValues as $field => $default) {
            if (isset($_POST[$field]) && is_string($_POST[$field])) {
                $formValues[$field] = trim($_POST[$field]);
            } elseif (array_key_exists($field, $_POST)) {
                $errors[] = 'Dữ liệu gửi lên không hợp lệ.';
            }
        }

        if ($formValues['full_name'] === '' || strlen($formValues['full_name']) > 150) {
            $errors[] = 'Họ tên là bắt buộc và không vượt quá 150 ký tự.';
        }
        if (
            $formValues['email'] === ''
            || strlen($formValues['email']) > 190
            || !filter_var($formValues['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $errors[] = 'Vui lòng nhập địa chỉ email hợp lệ (tối đa 190 ký tự).';
        } elseif (getAdminCustomerEmailOwner($formValues['email'], $requestedAction === 'edit' ? $customerId : null)) {
            $errors[] = 'Email này đã được sử dụng bởi khách hàng khác.';
        }
        if ($formValues['phone'] === '' || strlen($formValues['phone']) > 30) {
            $errors[] = 'Số điện thoại là bắt buộc và không vượt quá 30 ký tự.';
        }

        if (!$errors) {
            if ($requestedAction === 'edit') {
                updateCustomer($customerId, $formValues);
                redirect(admin_url('customers', array('success' => 'updated')));
            }

            createCustomer($formValues);
            redirect(admin_url('customers', array('success' => 'created')));
        }
    }
}
