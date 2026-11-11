<?php
$pageTitle = 'Tổng quan | HotelBooking Admin';
$stats = getAdminDashboardStats();
?>
<main class="admin-main">
  <header class="admin-page-heading">
    <div><p class="eyebrow">Quản lý lưu trú</p><h1>Tổng quan</h1></div>
    <span class="admin-date"><?= e(date('d/m/Y')) ?></span>
  </header>
  <section class="stats-grid" aria-label="Thống kê khách sạn">
    <article class="stat-card"><span>Tổng số phòng</span><strong><?= e($stats['room_count']) ?></strong><small><?= e($stats['available_count']) ?> phòng đang trống</small></article>
    <article class="stat-card"><span>Khách hàng</span><strong><?= e($stats['customer_count']) ?></strong><small>Hồ sơ khách hàng</small></article>
    <article class="stat-card"><span>Đặt phòng</span><strong><?= e($stats['booking_count']) ?></strong><small><?= e($stats['pending_count']) ?> đơn chờ xác nhận</small></article>
    <article class="stat-card"><span>Doanh thu xác nhận/hoàn tất</span><strong><?= e(money($stats['revenue'])) ?></strong><small>Tổng giá trị đơn không bị hủy</small></article>
  </section>
  <section class="admin-card">
    <h2>Quản lý dữ liệu khách sạn</h2>
    <p>Quản lý phòng, thông tin khách hàng và trạng thái các đơn đặt phòng.</p>
    <p><a class="btn btn-primary" href="<?= e(admin_url('rooms')) ?>">Quản lý phòng</a>
       <a class="btn btn-secondary admin-dashboard-link" href="<?= e(admin_url('bookings')) ?>">Quản lý đặt phòng</a>
       <a class="btn btn-secondary admin-dashboard-link" href="<?= e(admin_url('customers')) ?>">Quản lý khách hàng</a></p>
  </section>
</main>
