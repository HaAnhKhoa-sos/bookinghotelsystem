<?php
$pageTitle = 'Đặt phòng của tôi | HotelBooking';
if (!current_customer_id()) {
    set_flash('notice', 'Vui lòng đăng nhập để xem lịch sử đặt phòng.');
    redirect(url('login'));
}

$bookingActionError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['_csrf_token'] ?? null)) {
        http_response_code(403);
        $bookingActionError = 'Phiên biểu mẫu không hợp lệ hoặc đã hết hạn. Vui lòng tải lại trang.';
    } elseif (!isset($_GET['action']) || $_GET['action'] !== 'cancel') {
        http_response_code(400);
        $bookingActionError = 'Thao tác đặt phòng không hợp lệ.';
    } else {
        $bookingId = isset($_GET['id']) && is_string($_GET['id'])
            ? filter_var($_GET['id'], FILTER_VALIDATE_INT)
            : false;
        if (!$bookingId) {
            http_response_code(404);
            $bookingActionError = 'Không tìm thấy đặt phòng cần hủy.';
        } elseif (cancelCustomerBooking($bookingId, current_customer_id())) {
            redirect(url('my-bookings', array('success' => 'cancelled')));
        } else {
            $bookingActionError = 'Chỉ có thể hủy đơn đang chờ xác nhận của tài khoản hiện tại.';
        }
    }
}

$customerBookings = getCustomerBookings(current_customer_id());
