<?php
namespace web\Repositories;

use Config\Database;
use PDO;
use web\Models\UserEntity;

class UserRepository {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    /**
     * Lấy toàn bộ user
     */
    public function findAll() {
        try {
            $sql = "SELECT * FROM users ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            $list = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $list[] = new UserEntity($row);
            }
            return $list;
        } catch (\PDOException $e) {
            throw new \Exception("Database error in findAll: " . $e->getMessage());
        }
    }

    /**
     * Tìm user theo ID
     */
    public function findById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new UserEntity($data) : null;
        } catch (\PDOException $e) {
            throw new \Exception("Database error in findById: " . $e->getMessage());
        }
    }

    /**
     * Tìm user theo username
     */
    public function findByUsername($username) {
        try {
            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$username]);

            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            return $data ? new UserEntity($data) : null;
        } catch (\PDOException $e) {
            throw new \Exception("Database error in findByUsername: " . $e->getMessage());
        }
    }

    /**
     * Tạo user mới
     */
    public function create(UserEntity $user) {
        try {
            $sql = "INSERT INTO users (username, password_hash, full_name, email, phone_number, address, role, avatar_url)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($sql);

            return $stmt->execute([
                $user->username,
                $user->password_hash,
                $user->full_name,
                $user->email,
                $user->phone_number,
                $user->address,
                $user->role,
                $user->avatar_url
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Database error in create: " . $e->getMessage());
        }
    }

    /**
     * Cập nhật user
     */
    public function update(UserEntity $user) {
        try {
            $sql = "UPDATE users SET username=?, full_name=?, email=?, phone_number=?, address=?, role=?, avatar_url=? 
                    WHERE id=?";

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                $user->username,
                $user->full_name,
                $user->email,
                $user->phone_number,
                $user->address,
                $user->role,
                $user->avatar_url,
                $user->id,
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Database error in update: " . $e->getMessage());
        }
    }

    /**
     * Xóa user
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM users WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            throw new \Exception("Database error in delete: " . $e->getMessage());
        }
    }
}
?>
