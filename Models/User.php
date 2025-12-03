<?php
/**
 * FILE: User.php
 * DESCRIPTION: Model quản lý người dùng (User) - Hỗ trợ đăng nhập/đăng ký
 * AUTHOR: Coffee House System
 */

require_once __DIR__ . '/../Config/Database.php';

class User {
    private $conn;
    private $table = "users";

    // Properties từ ERD
    public $user_id;
    public $username;
    public $password;
    public $email;
    public $phone;
    public $full_name;
    public $role; // admin, staff, customer, shipper
    public $created_at;
    public $updated_at;
    public $is_active;

    /**
     * Constructor
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * ĐĂNG NHẬP - Xác thực username/password
     * @param string $username
     * @param string $password (plain text)
     * @return array|false - Trả về user data hoặc false
     */
    public function login($username, $password) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE username = :username AND is_active = 1 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Kiểm tra password (giả sử đã hash với password_hash)
            if (password_verify($password, $row['password'])) {
                // Không trả về password
                unset($row['password']);
                return $row;
            }
        }

        return false;
    }

    /**
     * ĐĂNG KÝ - Tạo user mới
     * @return bool
     */
    public function register() {
        $query = "INSERT INTO " . $this->table . " 
                  (username, password, email, phone, full_name, role, is_active) 
                  VALUES 
                  (:username, :password, :email, :phone, :full_name, :role, 1)";

        $stmt = $this->conn->prepare($query);

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":role", $this->role);

        if ($stmt->execute()) {
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * LẤY THÔNG TIN USER THEO ID
     * @param int $id
     * @return array|null
     */
    public function getUserById($id) {
        $query = "SELECT user_id, username, email, phone, full_name, role, created_at, is_active 
                  FROM " . $this->table . " 
                  WHERE user_id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    }

    /**
     * KIỂM TRA USERNAME ĐÃ TỒN TẠI
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        $query = "SELECT user_id FROM " . $this->table . " 
                  WHERE username = :username 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * KIỂM TRA EMAIL ĐÃ TỒN TẠI
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        $query = "SELECT user_id FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * CẬP NHẬT THÔNG TIN USER
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET email = :email, 
                      phone = :phone, 
                      full_name = :full_name,
                      updated_at = NOW()
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }

    /**
     * ĐỔI MẬT KHẨU
     * @param string $old_password
     * @param string $new_password
     * @return bool
     */
    public function changePassword($old_password, $new_password) {
        // Lấy password hiện tại
        $query = "SELECT password FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Xác thực password cũ
            if (password_verify($old_password, $row['password'])) {
                // Cập nhật password mới
                $update_query = "UPDATE " . $this->table . " 
                                 SET password = :password, 
                                     updated_at = NOW() 
                                 WHERE user_id = :user_id";

                $update_stmt = $this->conn->prepare($update_query);
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update_stmt->bindParam(":password", $hashed_password);
                $update_stmt->bindParam(":user_id", $this->user_id);

                return $update_stmt->execute();
            }
        }

        return false;
    }

    /**
     * KÍCH HOẠT/VÔ HIỆU HÓA USER
     * @param int $user_id
     * @param bool $status
     * @return bool
     */
    public function setActiveStatus($user_id, $status) {
        $query = "UPDATE " . $this->table . " 
                  SET is_active = :status, 
                      updated_at = NOW() 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $active = $status ? 1 : 0;

        $stmt->bindParam(":status", $active);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    /**
     * LẤY TẤT CẢ USER THEO ROLE
     * @param string $role
     * @return array
     */
    public function getUsersByRole($role) {
        $query = "SELECT user_id, username, email, phone, full_name, role, created_at, is_active 
                  FROM " . $this->table . " 
                  WHERE role = :role 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":role", $role);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * LẤY DANH SÁCH TẤT CẢ USER (Dành cho admin)
     * @return array
     */
    public function getAllUsers() {
        $query = "SELECT user_id, username, email, phone, full_name, role, created_at, is_active 
                  FROM " . $this->table . " 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
