<?php

function getPublicRoomTypeOptions()
{
    return db()->fetchAll(
        'SELECT id, name FROM room_types ORDER BY name ASC'
    );
}

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

function getFeaturedRooms($limit = 3)
{
    return getPublicRooms($limit, null, true);
}

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
