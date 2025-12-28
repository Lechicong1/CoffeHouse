<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class CustomerRepository extends ConnectDatabase {

    /**
     * Lấy tất cả khách hàng
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM customers ORDER BY `id` DESC";
        $result = mysqli_query($this->con, $sql);

        $customers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = new CustomerEntity($row);
        }

        return $customers;
    }

    /**
     * Lấy khách hàng theo ID
     * @param int $id
     * @return CustomerEntity|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

    /**
     * Lấy địa chỉ của khách hàng theo ID
     * @param int $id
     * @return string|null
     */
    public function getAddressById($id) {
        // Thử lấy toàn bộ thông tin customer trước
        $customer = $this->findById($id);

        if ($customer) {
            // Trả về address nếu có, nếu không có thì trả về null
            return $customer->address ?? null;
        }

        return null;
    }

    /**
     * Lấy khách hàng theo email
     * @param string $email
     * @param int|null $excludeId ID khách hàng cần loại trừ (khi update)
     * @return CustomerEntity|null
     */
    public function findByEmail($email, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM customers WHERE email = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $email, $excludeId);
        } else {
            $sql = "SELECT * FROM customers WHERE email = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

    /**
     * Lấy khách hàng theo số điện thoại
     * @param string $phone
     * @param int|null $excludeId ID khách hàng cần loại trừ (khi update)
     * @return CustomerEntity|null
     */
    public function findByPhone($phone, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM customers WHERE phone = ? AND id != ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $phone, $excludeId);
        } else {
            $sql = "SELECT * FROM customers WHERE phone = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $phone);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

    /**
     * Lấy khách hàng theo username
     * @param string $username
     * @return CustomerEntity|null
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM customers WHERE username = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

    /**
     * Tìm kiếm khách hàng
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM customers 
                WHERE full_name LIKE ? 
                OR phone LIKE ? 
                OR email LIKE ?
                ORDER BY full_name";

        $stmt = mysqli_prepare($this->con, $sql);
        $searchTerm = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "sss", $searchTerm, $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $customers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = new CustomerEntity($row);
        }

        return $customers;
    }

    /**
     * Tạo khách hàng mới
     * @param CustomerEntity $customer
     * @return bool
     */
    public function create($customer) {
        // customers table schema: username, password, full_name, phone, email, address, account_type, points, status
        $sql = "INSERT INTO customers (username, password, full_name, phone, email, address, account_type, points, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        // types: username(s), password(s), full_name(s), phone(s), email(s), address(s), account_type(s), points(i), status(i)
        mysqli_stmt_bind_param($stmt, "sssssssii",
            $customer->username,
            $customer->password,
            $customer->full_name,
            $customer->phone,
            $customer->email,
            $customer->address,
            $customer->account_type,
            $customer->points,
            $customer->status
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật khách hàng
     * @param CustomerEntity $customer
     * @return bool
     */
    public function update($customer) {
        $sql = "UPDATE customers 
            SET full_name = ?, phone = ?, email = ?, address = ?, account_type = ?, points = ?, status = ?
            WHERE id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssiii",
        $customer->full_name,
        $customer->phone,
        $customer->email,
        $customer->address,
        $customer->account_type,
        $customer->points,
        $customer->status,
        $customer->id
        );


        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật username/password/address/email/full_name và chuyển thành account_type = 'WEB'
     * @param int $id
     * @param string $username
     * @param string $password
     * @param string|null $address
     * @param string|null $email
     * @param string|null $full_name
     * @return bool
     */

    // Chuyển khách hàng GUEST_POS thành tài khoản WEB
    public function upgradeToWebAccount($id,$username,$password,$address = null,$email = null,$full_name = null) {
        $sql = "UPDATE customers 
            SET username = ?, password = ?, address = ?, email = ?, full_name = ?, account_type = 'WEB' 
            WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $password, $address, $email, $full_name, $id);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa khách hàng
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM customers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số khách hàng
     * @return int
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM customers";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    /**
     * Đếm khách hàng theo trạng thái
     * @param int $status
     * @return int
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM customers WHERE status = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $status);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    /**
     * Cập nhật điểm tích lũy
     * @param int $id
     * @param int $points
     * @return bool
     */
    public function updatePoints($id, $points) {
        $sql = "UPDATE customers SET points = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $points, $id);

        return mysqli_stmt_execute($stmt);
    }
}
