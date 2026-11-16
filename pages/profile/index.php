<?php
$pageTitle = 'Hồ sơ cá nhân | HotelBooking';
?>
<main class="container page-content account-page">
  <header class="account-heading"><div><p class="eyebrow">Tài khoản của bạn</p><h1>Hồ sơ cá nhân</h1></div><a href="<?= e(url('my-bookings')) ?>">Xem đặt phòng</a></header>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'profile'): ?><p class="form-success" role="status">Đã cập nhật hồ sơ.</p><?php endif; ?>
  <?php if (!empty($_GET['success']) && $_GET['success'] === 'password'): ?><p class="form-success" role="status">Đã đổi mật khẩu.</p><?php endif; ?>
  <?php foreach ($profileErrors as $error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
  <section class="form-box account-card">
    <h2>Thông tin cá nhân</h2>
    <form action="<?= e(url('profile')) ?>" method="post">
      <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
      <input type="hidden" name="form_action" value="profile" />
      <label for="profile-name">Họ tên</label><input id="profile-name" type="text" name="full_name" maxlength="150" value="<?= e($profileValues['full_name']) ?>" required />
      <label for="profile-email">Email</label><input id="profile-email" type="email" name="email" maxlength="190" value="<?= e($profileValues['email']) ?>" required />
      <label for="profile-phone">Số điện thoại</label><input id="profile-phone" type="tel" name="phone" maxlength="30" value="<?= e($profileValues['phone']) ?>" required />
      <button class="btn btn-primary" type="submit">Lưu thông tin</button>
    </form>
  </section>
  <section class="form-box account-card">
    <h2>Đổi mật khẩu</h2>
    <form action="<?= e(url('profile')) ?>" method="post">
      <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
      <input type="hidden" name="form_action" value="password" />
      <label for="current-password">Mật khẩu hiện tại</label><input id="current-password" type="password" name="current_password" autocomplete="current-password" required />
      <label for="new-password">Mật khẩu mới</label><input id="new-password" type="password" name="new_password" minlength="8" maxlength="72" autocomplete="new-password" required />
      <label for="password-confirmation">Nhập lại mật khẩu mới</label><input id="password-confirmation" type="password" name="password_confirmation" minlength="8" maxlength="72" autocomplete="new-password" required />
      <button class="btn btn-primary" type="submit">Đổi mật khẩu</button>
    </form>
  </section>
</main>
