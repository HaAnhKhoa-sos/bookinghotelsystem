<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($pageTitle ?? 'Quản lý | HotelBooking') ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>" />
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>" />
  </head>
  <body>
    <div class="admin-layout">
      <?php require __DIR__ . '/sidebar.php'; ?>
      <div class="admin-main">
        <p class="admin-notice">Khu vực mô phỏng học tập, chỉ chạy trên localhost. Không có đăng nhập, phân quyền hoặc CSRF; không triển khai lên Internet.</p>
