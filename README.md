# Hotel Booking System

Ứng dụng PHP/MySQL phục vụ bài tập BT03. Các trang chính dùng Router, layout dùng chung theo khu vực và dữ liệu được đọc từ MySQL.

## Yêu cầu

- PHP 8.1 trở lên, extension `mysqli` và MySQL 8.0 trở lên
- Web server PHP (Apache/XAMPP hoặc PHP built-in server)

## Cài đặt CSDL

1. Import [`database/hotel_booking_system.sql`](database/hotel_booking_system.sql) vào MySQL bằng phpMyAdmin hoặc dòng lệnh.
2. Sửa thông tin host, port, database, username và password trong [`config/database.php`](config/database.php) theo môi trường máy local.
3. Từ thư mục dự án khởi chạy `php -S 127.0.0.1:8000`.
4. Mở `http://127.0.0.1:8000/index.php` để xem khu Public, hoặc `http://127.0.0.1:8000/admin/index.php` để xem khu Admin mô phỏng.

## Router và chức năng

- Public: `index.php?page=home`, `index.php?page=rooms`, `index.php?page=room-detail&id=...`, `index.php?page=booking&room_id=...`, `index.php?page=register`, `index.php?page=login`, `index.php?page=my-bookings` và `index.php?page=profile`
- Admin mô phỏng: `admin/index.php?page=dashboard`, `admin/index.php?page=rooms`, `admin/index.php?page=room-types`, `admin/index.php?page=bookings` và `admin/index.php?page=customers`
- User có đăng ký/đăng nhập, cập nhật hồ sơ và mật khẩu, xem lịch sử, gửi yêu cầu đặt phòng theo ngày và hủy đơn đang chờ. Mật khẩu lưu bằng `password_hash`; đơn của khách hàng chỉ hiển thị trong tài khoản tương ứng.
- Admin có thống kê tổng quan; tìm kiếm, thêm, sửa và xóa phòng/loại phòng/khách hàng; cập nhật trạng thái đặt phòng. Không thể xóa phòng/loại phòng/khách hàng đang được đơn đặt phòng sử dụng. Biểu mẫu thay đổi dữ liệu dùng POST và CSRF token.
- Giá được quản lý riêng theo từng phòng; danh sách phòng có thể lọc theo loại, trạng thái và khoảng giá. Thông tin khách hàng có thể tìm theo họ tên, email hoặc số điện thoại.
- Trang chủ hiển thị tối đa 6 phòng, có bộ lọc theo loại phòng và khoảng giá, cùng khu vực phòng nổi bật; trang chi tiết nhận ID phòng.
- Có thể đánh dấu phòng nổi bật khi thêm phòng trong Admin. Tập lệnh SQL tự bổ sung cột `is_featured` cho CSDL đã tồn tại.
- Khi chạy lại trên CSDL cũ, tập lệnh SQL chuyển giá từ `room_types.base_price` sang `rooms.price` rồi loại bỏ cột giá ở loại phòng.
- Các URL `.html` cũ được giữ làm đường dẫn chuyển tiếp tới Router. CSDL cũ sẽ được bổ sung cột `customers.password_hash` nullable; hồ sơ khách tạo trước tính năng tài khoản chưa thể đăng nhập bằng mật khẩu cho đến khi đăng ký bằng một email chưa được sử dụng.

Admin chỉ phục vụ học tập trên localhost; theo cấu hình hiện tại chưa có đăng nhập hoặc phân quyền và không phải hệ thống an toàn để đưa lên Internet.
