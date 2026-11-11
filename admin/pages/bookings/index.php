<?php
$pageTitle = 'Quản lý đặt phòng | HotelBooking Admin';
$filters = array(
    'q' => isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '',
    'status' => isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : '',
);
$statusLabels = array('pending' => 'Chờ xác nhận', 'confirmed' => 'Đã xác nhận', 'completed' => 'Đã hoàn tất', 'cancelled' => 'Đã hủy');
if (!isset($statusLabels[$filters['status']])) {
    $filters['status'] = '';
}
$bookings = getAdminBookings($filters);
$statusClasses = array('pending' => 'status-pending', 'confirmed' => 'status-confirmed', 'completed' => 'status-booked', 'cancelled' => 'status-cancelled');
?>
<main class="admin-main">
  <header class="admin-page-heading"><div><p class="eyebrow">Theo dõi lưu trú</p><h1>Quản lý đặt phòng</h1></div></header>
  <?php if (!empty($adminActionError)): ?><p class="admin-error" role="alert"><?= e($adminActionError) ?></p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'updated'): ?><p class="admin-success" role="status">Đã cập nhật trạng thái đặt phòng.</p><?php endif; ?>
  <section class="admin-card">
    <form class="admin-toolbar admin-filter" action="<?= e(base_url('admin/index.php')) ?>" method="get">
      <input type="hidden" name="page" value="bookings" />
      <label class="search-label">Tìm đặt phòng<input class="search-box" type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Mã đặt phòng hoặc tên khách" /></label>
      <label>Trạng thái<select name="status"><option value="">Tất cả trạng thái</option>
        <?php foreach ($statusLabels as $status => $label): ?><option value="<?= e($status) ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
      </select></label>
      <button class="btn btn-primary" type="submit">Lọc</button>
    </form>
    <div class="table-scroll"><table class="admin-table">
      <thead><tr><th>Mã đặt phòng</th><th>Khách hàng</th><th>Phòng</th><th>Thời gian lưu trú</th><th>Tổng tiền</th><th>Trạng thái</th><th>Cập nhật</th></tr></thead>
      <tbody>
        <?php foreach ($bookings as $booking): ?>
          <tr>
            <td><?= e($booking['booking_code']) ?></td>
            <td><?= e($booking['full_name']) ?><br /><small><?= e($booking['email']) ?></small></td>
            <td><?= e($booking['room_type_name']) ?> <?= e($booking['room_number']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($booking['check_in']))) ?> – <?= e(date('d/m/Y', strtotime($booking['check_out']))) ?></td>
            <td><?= e(money($booking['total_amount'])) ?></td>
            <td><span class="admin-status <?= e($statusClasses[$booking['status']]) ?>"><?= e($statusLabels[$booking['status']]) ?></span></td>
            <td><form class="booking-status-form" action="<?= e(admin_url('bookings', array('action' => 'update_status', 'id' => $booking['id']))) ?>" method="post">
              <input type="hidden" name="_csrf_token" value="<?= e(admin_csrf_token()) ?>" />
              <label class="sr-only" for="booking-status-<?= e($booking['id']) ?>">Trạng thái đơn <?= e($booking['booking_code']) ?></label>
              <select id="booking-status-<?= e($booking['id']) ?>" name="status">
                <?php foreach ($statusLabels as $status => $label): ?><option value="<?= e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
              </select>
              <button class="btn-sm btn-view" type="submit">Lưu</button>
            </form></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?><tr><td colspan="7">Không tìm thấy đặt phòng phù hợp.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </section>
</main>
