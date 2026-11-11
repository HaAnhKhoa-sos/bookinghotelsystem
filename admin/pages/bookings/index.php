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
      <thead><tr><th>Mã đặt phòng</th><th>Khách hàng</th><th>Phòng</th><th>Thời gian lưu trú</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
      <tbody>
        <?php foreach ($bookings as $booking): ?>
          <tr>
            <td><?= e($booking['booking_code']) ?></td>
            <td><?= e($booking['full_name']) ?><br /><small><?= e($booking['email']) ?></small></td>
            <td><?= e($booking['room_type_name']) ?> <?= e($booking['room_number']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($booking['check_in']))) ?> – <?= e(date('d/m/Y', strtotime($booking['check_out']))) ?></td>
            <td><?= e(money($booking['total_amount'])) ?></td>
            <td><span class="admin-status <?= e($statusClasses[$booking['status']]) ?>"><?= e($statusLabels[$booking['status']]) ?></span></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$bookings): ?><tr><td colspan="6">Không tìm thấy đặt phòng phù hợp.</td></tr><?php endif; ?>
      </tbody>
    </table></div>
  </section>
</main>
