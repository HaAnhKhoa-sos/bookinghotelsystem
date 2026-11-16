<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($pageTitle ?? 'HotelBooking') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>" />
    <link rel="stylesheet" href="<?= e(asset('css/public.css')) ?>" />
  </head>
  <body>
    <header class="site-header">
      <div class="container nav">
        <a class="logo" href="<?= e(url('home')) ?>">HotelBooking</a>
        <nav aria-label="Điều hướng chính">
          <a href="<?= e(url('home')) ?>">Trang chủ</a>
          <a href="<?= e(url('rooms')) ?>">Phòng nghỉ</a>
          <?php if (current_customer_id()): ?>
            <a href="<?= e(url('my-bookings')) ?>">Đặt phòng của tôi</a>
            <a href="<?= e(url('profile')) ?>">Tài khoản</a>
            <form class="nav-logout" action="<?= e(url('logout')) ?>" method="post">
              <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
              <button type="submit">Đăng xuất</button>
            </form>
          <?php else: ?>
            <a href="<?= e(url('login')) ?>">Đăng nhập</a>
            <a href="<?= e(url('register')) ?>">Đăng ký</a>
          <?php endif; ?>
        </nav>
      </div>
    </header>
    <?php if ($notice = take_flash('notice')): ?>
      <div class="container"><p class="form-success" role="status"><?= e($notice) ?></p></div>
    <?php endif; ?>
