<?php $activeAdminPage = $_GET['page'] ?? 'dashboard'; ?>
<aside class="admin-sidebar">
  <a class="admin-brand" href="<?= e(admin_url('dashboard')) ?>">HotelBooking <span>ADMIN · MÔ PHỎNG</span></a>
  <nav aria-label="Điều hướng quản trị">
    <a href="<?= e(admin_url('dashboard')) ?>" <?= $activeAdminPage === 'dashboard' ? 'aria-current="page"' : '' ?>>Tổng quan</a>
    <a href="<?= e(admin_url('rooms')) ?>" <?= $activeAdminPage === 'rooms' ? 'aria-current="page"' : '' ?>>Phòng</a>
    <a href="<?= e(admin_url('bookings')) ?>" <?= $activeAdminPage === 'bookings' ? 'aria-current="page"' : '' ?>>Đặt phòng</a>
    <a href="<?= e(admin_url('customers')) ?>" <?= $activeAdminPage === 'customers' ? 'aria-current="page"' : '' ?>>Khách hàng</a>
    <a href="<?= e(url('home')) ?>">Xem trang khách</a>
  </nav>
</aside>
