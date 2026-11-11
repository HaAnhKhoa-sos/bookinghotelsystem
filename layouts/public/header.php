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
          <a href="<?= e(url('rooms')) ?>">Đặt phòng</a>
        </nav>
      </div>
    </header>
