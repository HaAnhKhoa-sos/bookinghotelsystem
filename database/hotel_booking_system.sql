CREATE DATABASE IF NOT EXISTS hotel_booking_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE hotel_booking_system;

CREATE TABLE IF NOT EXISTS room_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  capacity TINYINT UNSIGNED NOT NULL,
  area_sqm DECIMAL(6,2) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_room_types_name (name),
  CHECK (capacity > 0),
  CHECK (area_sqm > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS customers (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_customers_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS rooms (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  room_number VARCHAR(20) NOT NULL,
  room_name VARCHAR(100) NOT NULL,
  room_type_id INT UNSIGNED NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  status ENUM('available', 'occupied', 'maintenance') NOT NULL DEFAULT 'available',
  bed_description VARCHAR(100) NOT NULL DEFAULT '',
  image_url VARCHAR(1000) NOT NULL DEFAULT '',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_rooms_room_number (room_number),
  KEY idx_rooms_room_type_status (room_type_id, status),
  CONSTRAINT fk_rooms_room_type FOREIGN KEY (room_type_id) REFERENCES room_types (id),
  CONSTRAINT chk_rooms_price CHECK (price >= 0)
) ENGINE=InnoDB;

SET @has_room_price_column = (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'rooms'
    AND column_name = 'price'
);
SET @add_room_price_column = IF(
  @has_room_price_column = 0,
  'ALTER TABLE rooms ADD COLUMN price DECIMAL(12,2) NOT NULL DEFAULT 0 AFTER room_type_id, ADD CONSTRAINT chk_rooms_price CHECK (price >= 0)',
  'SELECT 1'
);
PREPARE add_room_price_column FROM @add_room_price_column;
EXECUTE add_room_price_column;
DEALLOCATE PREPARE add_room_price_column;

SET @has_legacy_room_type_price = (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'room_types'
    AND column_name = 'base_price'
);
SET @copy_legacy_room_prices = IF(
  @has_room_price_column = 0 AND @has_legacy_room_type_price > 0,
  'UPDATE rooms r INNER JOIN room_types rt ON rt.id = r.room_type_id SET r.price = rt.base_price',
  'SELECT 1'
);
PREPARE copy_legacy_room_prices FROM @copy_legacy_room_prices;
EXECUTE copy_legacy_room_prices;
DEALLOCATE PREPARE copy_legacy_room_prices;

SET @legacy_price_check = (
  SELECT tc.constraint_name
  FROM information_schema.table_constraints tc
  INNER JOIN information_schema.check_constraints cc
    ON cc.constraint_schema = tc.constraint_schema
    AND cc.constraint_name = tc.constraint_name
  WHERE tc.constraint_schema = DATABASE()
    AND tc.table_name = 'room_types'
    AND tc.constraint_type = 'CHECK'
    AND cc.check_clause LIKE '%base_price%'
  LIMIT 1
);
SET @drop_legacy_price_check = IF(
  @legacy_price_check IS NOT NULL,
  CONCAT('ALTER TABLE room_types DROP CHECK `', REPLACE(@legacy_price_check, '`', '``'), '`'),
  'SELECT 1'
);
PREPARE drop_legacy_price_check FROM @drop_legacy_price_check;
EXECUTE drop_legacy_price_check;
DEALLOCATE PREPARE drop_legacy_price_check;

SET @drop_legacy_room_type_price = IF(
  @has_legacy_room_type_price > 0,
  'ALTER TABLE room_types DROP COLUMN base_price',
  'SELECT 1'
);
PREPARE drop_legacy_room_type_price FROM @drop_legacy_room_type_price;
EXECUTE drop_legacy_room_type_price;
DEALLOCATE PREPARE drop_legacy_room_type_price;

SET @has_room_featured_column = (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'rooms'
    AND column_name = 'is_featured'
);
SET @add_room_featured_column = IF(
  @has_room_featured_column = 0,
  'ALTER TABLE rooms ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER image_url',
  'SELECT 1'
);
PREPARE add_room_featured_column FROM @add_room_featured_column;
EXECUTE add_room_featured_column;
DEALLOCATE PREPARE add_room_featured_column;

CREATE TABLE IF NOT EXISTS bookings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_code VARCHAR(30) NOT NULL,
  customer_id INT UNSIGNED NOT NULL,
  room_id INT UNSIGNED NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guest_count TINYINT UNSIGNED NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending', 'confirmed', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_bookings_booking_code (booking_code),
  KEY idx_bookings_customer (customer_id),
  KEY idx_bookings_room_dates (room_id, check_in, check_out),
  KEY idx_bookings_status_created (status, created_at),
  CONSTRAINT fk_bookings_customer FOREIGN KEY (customer_id) REFERENCES customers (id),
  CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms (id),
  CHECK (check_out > check_in),
  CHECK (guest_count > 0),
  CHECK (total_amount >= 0)
) ENGINE=InnoDB;

INSERT INTO room_types (id, name, description, capacity, area_sqm) VALUES
  (1, 'Superior', 'Không gian ấm cúng với giường đôi, phù hợp cho kỳ nghỉ ngắn ngày.', 2, 28),
  (2, 'Deluxe', 'Phòng rộng rãi, cửa sổ lớn và khu vực nghỉ ngơi thoáng đãng.', 2, 35),
  (3, 'Suite', 'Suite rộng rãi với phòng khách riêng, lý tưởng cho gia đình.', 4, 52)
ON DUPLICATE KEY UPDATE
  description = VALUES(description),
  capacity = VALUES(capacity),
  area_sqm = VALUES(area_sqm);

INSERT INTO customers (id, full_name, email, phone) VALUES
  (1, 'Nguyễn Minh Anh', 'minhanh@example.com', '0900000001'),
  (2, 'Trần Gia Bảo', 'giabao@example.com', '0900000002'),
  (3, 'Lê Thu Hà', 'thuha@example.com', '0900000003')
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  phone = VALUES(phone);

INSERT INTO rooms (id, room_number, room_name, room_type_id, price, status, bed_description, image_url, is_featured) VALUES
  (1, '105', 'Superior Garden', 1, 1200000, 'available', '1 giường đôi', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=85', 1),
  (2, '302', 'Deluxe City View', 2, 1900000, 'occupied', '1 giường đôi', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 0),
  (3, '303', 'Deluxe Garden', 2, 1750000, 'available', '1 giường đôi', 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=900&q=85', 1),
  (4, '501', 'Family Suite', 3, 2600000, 'maintenance', '2 giường đôi', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=85', 0),
  (5, '106', 'Superior Balcony', 1, 1350000, 'available', '1 giường đôi', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=85', 0),
  (6, '502', 'Suite Pool View', 3, 2800000, 'available', '2 giường đôi', 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=85', 1)
ON DUPLICATE KEY UPDATE
  room_name = VALUES(room_name),
  room_type_id = VALUES(room_type_id),
  price = VALUES(price),
  status = VALUES(status),
  bed_description = VALUES(bed_description),
  image_url = VALUES(image_url),
  is_featured = VALUES(is_featured);

INSERT INTO bookings (id, booking_code, customer_id, room_id, check_in, check_out, guest_count, total_amount, status) VALUES
  (1, 'HB2026VN', 1, 2, '2026-10-15', '2026-10-18', 2, 5400000, 'confirmed'),
  (2, 'HB2027AB', 2, 1, '2026-10-18', '2026-10-20', 2, 2400000, 'pending'),
  (3, 'HB2028CD', 3, 4, '2026-10-20', '2026-10-23', 3, 7800000, 'cancelled')
ON DUPLICATE KEY UPDATE
  customer_id = VALUES(customer_id),
  room_id = VALUES(room_id),
  check_in = VALUES(check_in),
  check_out = VALUES(check_out),
  guest_count = VALUES(guest_count),
  total_amount = VALUES(total_amount),
  status = VALUES(status);
