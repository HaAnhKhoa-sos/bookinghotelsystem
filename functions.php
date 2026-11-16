<?php

// Lấy danh sách loại phòng cho bộ lọc trang chủ (pages/home/action.php).
function getPublicRoomTypeOptions()
{
    return db()->fetchAll(
        'SELECT id, name FROM room_types ORDER BY name ASC'
    );
}

// Lấy danh sách phòng theo loại, khoảng giá hoặc trạng thái nổi bật (home/action.php).
function getPublicRooms($limit = null, $roomTypeId = null, $featuredOnly = false, $minPrice = null, $maxPrice = null)
{
    $conditions = array();
    $params = array();

    if ($roomTypeId !== null) {
        $conditions[] = 'r.room_type_id = ?';
        $params[] = $roomTypeId;
    }
    if ($featuredOnly) {
        $conditions[] = 'r.is_featured = 1';
    }
    if ($minPrice !== null) {
        $conditions[] = 'r.price >= ?';
        $params[] = $minPrice;
    }
    if ($maxPrice !== null) {
        $conditions[] = 'r.price <= ?';
        $params[] = $maxPrice;
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $limitSql = $limit === null ? '' : ' LIMIT ' . max(1, min((int) $limit, 100));

    return db()->fetchAll(
        'SELECT r.id, r.room_number, r.room_name, r.room_type_id, r.status,
                r.bed_description, r.image_url, r.is_featured, r.created_at, r.price,
                rt.name AS room_type_name, rt.description, rt.capacity, rt.area_sqm
         FROM rooms r
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         ' . $where . '
         ORDER BY r.is_featured DESC, r.created_at DESC, r.id DESC' . $limitSql,
        $params
    );
}

// Lấy phòng nổi bật hiển thị trên trang chủ (pages/home/action.php).
function getFeaturedRooms($limit = 3)
{
    return getPublicRooms($limit, null, true);
}

// Lấy thông tin một phòng theo ID cho trang chi tiết (pages/room-detail/action.php).
function getPublicRoomById($id)
{
    return db()->fetchOne(
        'SELECT r.id, r.room_number, r.room_name, r.room_type_id, r.status,
                r.bed_description, r.image_url, r.is_featured, r.price,
                rt.name AS room_type_name, rt.description, rt.capacity, rt.area_sqm
         FROM rooms r
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         WHERE r.id = ?',
        array($id)
    );
}

// Kiểm tra loại phòng tồn tại, dùng khi lọc trang chủ (pages/home/action.php).
function getPublicRoomTypeById($id)
{
    return db()->fetchOne(
        'SELECT id, name FROM room_types WHERE id = ?',
        array($id)
    );
}

function getAdminRooms($filters = array())
{
    $conditions = array();
    $params = array();

    if (!empty($filters['q'])) {
        $conditions[] = '(r.room_number LIKE ? OR r.room_name LIKE ?)';
        $search = '%' . $filters['q'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    if (!empty($filters['type_id'])) {
        $conditions[] = 'r.room_type_id = ?';
        $params[] = $filters['type_id'];
    }
    if (!empty($filters['status'])) {
        $conditions[] = 'r.status = ?';
        $params[] = $filters['status'];
    }
    if (isset($filters['min_price']) && $filters['min_price'] !== '') {
        $conditions[] = 'r.price >= ?';
        $params[] = $filters['min_price'];
    }
    if (isset($filters['max_price']) && $filters['max_price'] !== '') {
        $conditions[] = 'r.price <= ?';
        $params[] = $filters['max_price'];
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    return db()->fetchAll(
        'SELECT r.id, r.room_number, r.room_name, r.status, r.bed_description, r.image_url,
                r.is_featured, r.room_type_id, r.price,
                rt.name AS room_type_name, rt.capacity
         FROM rooms r
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         ' . $where . '
         ORDER BY r.room_number ASC
         LIMIT 100',
        $params
    );
}

function getAdminRoomById($id)
{
    return db()->fetchOne(
        'SELECT id, room_number, room_name, room_type_id, price, status,
                bed_description, image_url, is_featured
         FROM rooms WHERE id = ?',
        array($id)
    );
}

function getAdminBookings($filters = array())
{
    $conditions = array();
    $params = array();

    if (!empty($filters['q'])) {
        $conditions[] = '(b.booking_code LIKE ? OR c.full_name LIKE ?)';
        $search = '%' . $filters['q'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    if (!empty($filters['status'])) {
        $conditions[] = 'b.status = ?';
        $params[] = $filters['status'];
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    return db()->fetchAll(
        'SELECT b.id, b.booking_code, c.full_name, c.email, r.room_number, rt.name AS room_type_name,
                b.check_in, b.check_out, b.total_amount, b.status
         FROM bookings b
         INNER JOIN customers c ON c.id = b.customer_id
         INNER JOIN rooms r ON r.id = b.room_id
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         ' . $where . '
         ORDER BY b.created_at DESC
         LIMIT 100',
        $params
    );
}

function getAdminBookingById($id)
{
    return db()->fetchOne(
        'SELECT id, status FROM bookings WHERE id = ?',
        array($id)
    );
}

function getAdminCustomers($filters = array())
{
    $conditions = array();
    $params = array();

    if (!empty($filters['q'])) {
        $conditions[] = '(c.full_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)';
        $search = '%' . $filters['q'] . '%';
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }

    $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
    return db()->fetchAll(
        'SELECT c.id, c.full_name, c.email, c.phone, c.created_at,
                COUNT(b.id) AS booking_count
         FROM customers c
         LEFT JOIN bookings b ON b.customer_id = c.id
         ' . $where . '
         GROUP BY c.id, c.full_name, c.email, c.phone, c.created_at
         ORDER BY c.created_at DESC, c.id DESC
         LIMIT 100',
        $params
    );
}

function getAdminCustomerById($id)
{
    return db()->fetchOne(
        'SELECT id, full_name, email, phone FROM customers WHERE id = ?',
        array($id)
    );
}

function getAdminDashboardStats()
{
    return db()->fetchOne(
        'SELECT
            (SELECT COUNT(*) FROM rooms) AS room_count,
            (SELECT COUNT(*) FROM rooms WHERE status = ?) AS available_count,
            (SELECT COUNT(*) FROM customers) AS customer_count,
            (SELECT COUNT(*) FROM bookings) AS booking_count,
            (SELECT COUNT(*) FROM bookings WHERE status = ?) AS pending_count,
            (SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE status IN (?, ?)) AS revenue',
        array('available', 'pending', 'confirmed', 'completed')
    );
}

function getRoomTypes()
{
    return db()->fetchAll(
        'SELECT id, name, capacity FROM room_types ORDER BY name ASC'
    );
}

function getAdminRoomTypes()
{
    return db()->fetchAll(
        'SELECT rt.id, rt.name, rt.description, rt.capacity, rt.area_sqm, COUNT(r.id) AS room_count
         FROM room_types rt
         LEFT JOIN rooms r ON r.room_type_id = rt.id
         GROUP BY rt.id, rt.name, rt.description, rt.capacity, rt.area_sqm
         ORDER BY rt.name ASC
         LIMIT 100'
    );
}

function getAdminRoomTypeById($id)
{
    return db()->fetchOne(
        'SELECT id, name, description, capacity, area_sqm FROM room_types WHERE id = ?',
        array($id)
    );
}

function roomTypeNameExists($name, $excludeId = null)
{
    $sql = 'SELECT id FROM room_types WHERE name = ?';
    $params = array($name);
    if ($excludeId !== null) {
        $sql .= ' AND id <> ?';
        $params[] = $excludeId;
    }

    return db()->fetchOne($sql, $params) !== null;
}

function roomTypeHasRooms($id)
{
    return db()->fetchOne(
        'SELECT id FROM rooms WHERE room_type_id = ? LIMIT 1',
        array($id)
    ) !== null;
}

function createRoomType($roomType)
{
    db()->execute(
        'INSERT INTO room_types (name, description, capacity, area_sqm) VALUES (?, ?, ?, ?)',
        array($roomType['name'], $roomType['description'], $roomType['capacity'], $roomType['area_sqm'])
    );

    return db()->insertId();
}

function updateRoomType($id, $roomType)
{
    return db()->execute(
        'UPDATE room_types SET name = ?, description = ?, capacity = ?, area_sqm = ? WHERE id = ?',
        array($roomType['name'], $roomType['description'], $roomType['capacity'], $roomType['area_sqm'], $id)
    );
}

function deleteRoomType($id)
{
    return db()->execute('DELETE FROM room_types WHERE id = ?', array($id));
}

function getCustomerByEmail($email)
{
    return db()->fetchOne(
        'SELECT id, full_name, email, phone, password_hash FROM customers WHERE email = ?',
        array($email)
    );
}

function getCustomerById($id)
{
    return db()->fetchOne(
        'SELECT id, full_name, email, phone FROM customers WHERE id = ?',
        array($id)
    );
}

function createRegisteredCustomer($customer, $passwordHash)
{
    db()->execute(
        'INSERT INTO customers (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)',
        array($customer['full_name'], $customer['email'], $customer['phone'], $passwordHash)
    );

    return db()->insertId();
}

function updateCustomerProfile($id, $customer)
{
    return db()->execute(
        'UPDATE customers SET full_name = ?, email = ?, phone = ? WHERE id = ?',
        array($customer['full_name'], $customer['email'], $customer['phone'], $id)
    );
}

function updateCustomerPassword($id, $passwordHash)
{
    return db()->execute(
        'UPDATE customers SET password_hash = ? WHERE id = ?',
        array($passwordHash, $id)
    );
}

function getCustomerBookings($customerId)
{
    return db()->fetchAll(
        'SELECT b.id, b.booking_code, b.check_in, b.check_out, b.guest_count, b.total_amount, b.status,
                r.room_number, r.room_name, rt.name AS room_type_name
         FROM bookings b
         INNER JOIN rooms r ON r.id = b.room_id
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         WHERE b.customer_id = ?
         ORDER BY b.created_at DESC, b.id DESC
         LIMIT 100',
        array($customerId)
    );
}

function getBookingRoom($roomId)
{
    return db()->fetchOne(
        'SELECT r.id, r.room_name, r.room_number, r.price, r.status, rt.capacity, rt.name AS room_type_name
         FROM rooms r
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         WHERE r.id = ?',
        array($roomId)
    );
}

function roomIsAvailableForDates($roomId, $checkIn, $checkOut)
{
    return db()->fetchOne(
        'SELECT id FROM bookings
         WHERE room_id = ? AND status IN (?, ?)
           AND check_in < ? AND check_out > ?
         LIMIT 1',
        array($roomId, 'pending', 'confirmed', $checkOut, $checkIn)
    ) === null;
}

function createCustomerBooking($customerId, $roomId, $checkIn, $checkOut, $guestCount)
{
    $database = db();
    $database->beginTransaction();
    try {
        $room = $database->fetchOne(
            'SELECT r.id, r.price, r.status, rt.capacity
             FROM rooms r INNER JOIN room_types rt ON rt.id = r.room_type_id
             WHERE r.id = ? FOR UPDATE',
            array($roomId)
        );
        if (!$room || $room['status'] !== 'available' || (int) $guestCount > (int) $room['capacity']) {
            $database->rollBack();
            return false;
        }

        if (!roomIsAvailableForDates($roomId, $checkIn, $checkOut)) {
            $database->rollBack();
            return false;
        }

        $nights = (int) ((strtotime($checkOut) - strtotime($checkIn)) / 86400);
        $bookingCode = 'HB' . date('ymd') . strtoupper(bin2hex(random_bytes(4)));
        $database->execute(
            'INSERT INTO bookings (booking_code, customer_id, room_id, check_in, check_out, guest_count, total_amount, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            array($bookingCode, $customerId, $roomId, $checkIn, $checkOut, $guestCount, $room['price'] * $nights, 'pending')
        );
        $bookingId = $database->insertId();
        $database->commit();
        return $bookingId;
    } catch (Throwable $error) {
        $database->rollBack();
        throw $error;
    }
}

function cancelCustomerBooking($bookingId, $customerId)
{
    return db()->execute(
        'UPDATE bookings SET status = ? WHERE id = ? AND customer_id = ? AND status = ?',
        array('cancelled', $bookingId, $customerId, 'pending')
    );
}

// Kiểm tra định dạng giá trước khi dùng bộ lọc phòng (home/action.php).
function validPriceFilter($value)
{
    if (!is_string($value) || !preg_match('/^\d{1,10}(?:\.\d{1,2})?$/D', $value)) {
        return null;
    }

    return $value;
}

function roomNumberExists($roomNumber, $excludeId = null)
{
    $sql = 'SELECT id FROM rooms WHERE room_number = ?';
    $params = array($roomNumber);
    if ($excludeId !== null) {
        $sql .= ' AND id <> ?';
        $params[] = $excludeId;
    }

    return db()->fetchOne($sql, $params) !== null;
}

function createRoom($room)
{
    db()->execute(
        'INSERT INTO rooms (room_number, room_name, room_type_id, price, status, bed_description, image_url, is_featured)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        array(
            $room['room_number'],
            $room['room_name'],
            $room['room_type_id'],
            $room['price'],
            $room['status'],
            $room['bed_description'],
            $room['image_url'],
            $room['is_featured'],
        )
    );

    return db()->insertId();
}

function updateRoom($id, $room)
{
    return db()->execute(
        'UPDATE rooms
         SET room_number = ?, room_name = ?, room_type_id = ?, price = ?, status = ?,
             bed_description = ?, image_url = ?, is_featured = ?
         WHERE id = ?',
        array(
            $room['room_number'],
            $room['room_name'],
            $room['room_type_id'],
            $room['price'],
            $room['status'],
            $room['bed_description'],
            $room['image_url'],
            $room['is_featured'],
            $id,
        )
    );
}

function roomHasBookings($id)
{
    return db()->fetchOne(
        'SELECT id FROM bookings WHERE room_id = ? LIMIT 1',
        array($id)
    ) !== null;
}

function deleteRoom($id)
{
    return db()->execute('DELETE FROM rooms WHERE id = ?', array($id));
}

function getAdminCustomerEmailOwner($email, $excludeId = null)
{
    $sql = 'SELECT id FROM customers WHERE email = ?';
    $params = array($email);
    if ($excludeId !== null) {
        $sql .= ' AND id <> ?';
        $params[] = $excludeId;
    }

    return db()->fetchOne($sql, $params);
}

function createCustomer($customer)
{
    db()->execute(
        'INSERT INTO customers (full_name, email, phone) VALUES (?, ?, ?)',
        array($customer['full_name'], $customer['email'], $customer['phone'])
    );

    return db()->insertId();
}

function updateCustomer($id, $customer)
{
    return db()->execute(
        'UPDATE customers SET full_name = ?, email = ?, phone = ? WHERE id = ?',
        array($customer['full_name'], $customer['email'], $customer['phone'], $id)
    );
}

function customerHasBookings($id)
{
    return db()->fetchOne(
        'SELECT id FROM bookings WHERE customer_id = ? LIMIT 1',
        array($id)
    ) !== null;
}

function deleteCustomer($id)
{
    return db()->execute('DELETE FROM customers WHERE id = ?', array($id));
}

function updateBookingStatus($id, $status)
{
    return db()->execute(
        'UPDATE bookings SET status = ? WHERE id = ?',
        array($status, $id)
    );
}
