<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class CustomerRepository extends ConnectDatabase {

    public function findAll() {
        $sql = "SELECT * FROM customers ORDER BY `id` DESC";
        $result = mysqli_query($this->con, $sql);

        $customers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = new CustomerEntity($row);
        }

        return $customers;
    }

    public function findById($id) {
        $sql = "SELECT * FROM customers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

    public function getAddressById($id) {
        $customer = $this->findById($id);

        if ($customer) {
            return $customer->address ?? null;
        }

        return null;
    }

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

    public function findByUsername($username) {
        $sql = "SELECT * FROM customers WHERE username = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CustomerEntity($data) : null;
    }

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

    public function create($customer) {
        $sql = "INSERT INTO customers (username, password, full_name, phone, email, address, account_type, points, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
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

    public function upgradeToWebAccount($id,$username,$password,$address = null,$email = null,$full_name = null) {
        $sql = "UPDATE customers 
            SET username = ?, password = ?, address = ?, email = ?, full_name = ?, account_type = 'WEB' 
            WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $username, $password, $address, $email, $full_name, $id);
        return mysqli_stmt_execute($stmt);
    }

    public function delete($id) {
        $sql = "DELETE FROM customers WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    public function count() {
        $sql = "SELECT COUNT(*) as total FROM customers";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM customers WHERE status = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $status);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    public function updatePoints($id, $points) {
        $sql = "UPDATE customers SET points = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $points, $id);

        return mysqli_stmt_execute($stmt);
    }
}
