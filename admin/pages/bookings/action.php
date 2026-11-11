<?php
$adminActionError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!admin_csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $adminActionError = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang và thử lại.';
    } elseif (!isset($_GET['action']) || $_GET['action'] !== 'update_status') {
        http_response_code(400);
        $adminActionError = 'Thao tác quản lý đặt phòng không hợp lệ.';
    } else {
        $bookingId = isset($_GET['id']) && is_string($_GET['id'])
            ? filter_var($_GET['id'], FILTER_VALIDATE_INT)
            : false;
        $status = isset($_POST['status']) && is_string($_POST['status']) ? $_POST['status'] : '';
        $validStatuses = array('pending', 'confirmed', 'completed', 'cancelled');

        if (!$bookingId || !getAdminBookingById($bookingId)) {
            http_response_code(404);
            $adminActionError = 'Không tìm thấy đặt phòng cần cập nhật.';
        } elseif (!in_array($status, $validStatuses, true)) {
            http_response_code(400);
            $adminActionError = 'Trạng thái đặt phòng không hợp lệ.';
        } else {
            updateBookingStatus($bookingId, $status);
            redirect(admin_url('bookings', array('success' => 'updated')));
        }
    }
}
