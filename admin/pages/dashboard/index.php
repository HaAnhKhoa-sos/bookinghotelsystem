<?php $pageTitle = 'Tổng quan | HotelBooking Admin'; ?>
<main class="admin-main">
  <header class="admin-page-heading">
    <div><p class="eyebrow">Quản lý lưu trú</p><h1>Tổng quan</h1></div>
    <span class="admin-date"><?= e(date('d/m/Y')) ?></span>
  </header>
  <section class="admin-card">
    <h2>Quản lý dữ liệu khách sạn</h2>
    <p>Chọn một chức năng để xem dữ liệu hiện có trong cơ sở dữ liệu.</p>
    <p><a class="btn btn-primary" href="<?= e(admin_url('rooms')) ?>">Quản lý phòng</a>
       <a class="btn btn-secondary" href="<?= e(admin_url('bookings')) ?>">Quản lý đặt phòng</a></p>
  </section>
</main>
