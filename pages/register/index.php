<?php
$pageTitle = 'Đăng ký | HotelBooking';
?>
<main class="container page-content account-page">
  <section class="form-box account-card">
    <p class="eyebrow">Tạo tài khoản</p>
    <h1>Đăng ký thành viên</h1>
    <?php foreach ($authErrors as $error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endforeach; ?>
    <form action="<?= e(url('register')) ?>" method="post">
      <input type="hidden" name="_csrf_token" value="<?= e(csrf_token()) ?>" />
      <label for="register-name">Họ tên</label>
      <input id="register-name" type="text" name="full_name" maxlength="150" autocomplete="name" value="<?= e($registerValues['full_name']) ?>" required />
      <label for="register-email">Email</label>
      <input id="register-email" type="email" name="email" maxlength="190" autocomplete="email" value="<?= e($registerValues['email']) ?>" required />
      <label for="register-phone">Số điện thoại</label>
      <input id="register-phone" type="tel" name="phone" maxlength="30" autocomplete="tel" value="<?= e($registerValues['phone']) ?>" required />
      <label for="register-password">Mật khẩu (ít nhất 8 ký tự)</label>
      <input id="register-password" type="password" name="password" minlength="8" maxlength="72" autocomplete="new-password" required />
      <label for="register-confirm">Nhập lại mật khẩu</label>
      <input id="register-confirm" type="password" name="password_confirmation" minlength="8" maxlength="72" autocomplete="new-password" required />
      <button class="btn btn-primary" type="submit">Tạo tài khoản</button>
    </form>
    <p>Đã có tài khoản? <a href="<?= e(url('login')) ?>">Đăng nhập</a></p>
  </section>
</main>
