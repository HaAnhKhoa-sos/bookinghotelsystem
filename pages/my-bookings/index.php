<?php
$pageTitle = 'Đặt phòng của tôi | HotelBooking';
$statusLabels = array('pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'cancelled' => 'Đã hủy');
?>
<main class="container page-content">
  <header class="account-heading"><div><p class="eyebrow">Tài khoản của bạn</p><h1>Đặt phòng của tôi</h1></div><a class="btn btn-primary" href="<?= e(url('rooms')) ?>">Tìm phòng</a></header>
  <?php if ($message = take_flash('success')): ?><p class="form-success" role="status"><?= e($message) ?></p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'registered'): ?><p class="form-success" role="status">Tạo tài khoản thành công. Bạn có thể bắt đầu đặt phòng.</p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'cancelled'): ?><p class="form-success" role="status">Đã hủy yêu cầu đặt phòng.</p><?php endif; ?>
  <?php if ($bookingActionError !== ''): ?><p class="form-error" role="alert"><?= e($bookingActionError) ?></p><?php endif; ?>
  <div class="table-scroll account-table-wrap">
    <table class="account-table">
      <thead><tr><th>Mã đặt phòng</th><th>Phòng</th><th>Thời gian</th><th>Số khách</th><th>Tổng tiền</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
      <tbody>
        <?php foreach ($customerBookings as $booking): ?>
          <tr>
            <td><?= e($booking['booking_code']) ?></td>
            <td><?= e($booking['room_name']) ?><br /><small><?= e($booking['room_type_name']) ?> · <?= e($booking['room_number']) ?></small></td>
            <td><?= e(date('d/m/Y', strtotime($booking['check_in']))) ?> – <?= e(date('d/m/Y', strtotime($booking['check_out']))) ?></td>
            <td><?= e($booking['guest_count']) ?></td>
            <td><?= e(money($booking['total_amount'])) ?></td>
            <td><?= e($statusLabels[$booking['status']] ?? $booking['status']) ?></td>
            <td>
              <?php if ($booking['status'] === 'pending'): ?>
                <form action="<?= e(url('my-bookings', array('action' => 'cancel', 'id' => $booking['id']))) ?>" method="post" onsubmit="return confirm('Bạn có chắc muốn hủy yêu cầu đặt phòng này?')">
                  <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
                  <button class="btn-sm btn-delete" type="submit">Hủy đơn</button>
                </form>
              <?php else: ?>—<?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$customerBookings): ?><tr><td colspan="7">Bạn chưa có đặt phòng nào.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
