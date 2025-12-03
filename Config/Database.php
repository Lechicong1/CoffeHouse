<?php
/**
 * FILE: Database.php
 * DESCRIPTION: Kết nối cơ sở dữ liệu MySQL
 * AUTHOR: Coffee House System
 */

class Database {
    private $host = "localhost";        // Host database
    private $db_name = "coffee_php"; // Tên database
    private $username = "root";          // Username MySQL
    private $password = "742005";              // Password MySQL
    private $conn;

    /**
     * Kết nối database
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>