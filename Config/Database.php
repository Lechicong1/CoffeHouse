<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static $conn = null;

    // Sử dụng getenv() để đọc từ environment variables (tốt hơn hardcode)
    private static $host;
    private static $db_name;
    private static $username;
    private static $password;
    
    private static function loadConfig() {
        // Đọc từ $_ENV hoặc dùng default values
        self::$host = $_ENV['DB_HOST'] ?? 'localhost';
        self::$db_name = $_ENV['DB_NAME'] ?? 'coffee_php';
        self::$username = $_ENV['DB_USER'] ?? 'root';
        self::$password = $_ENV['DB_PASS'] ?? '742005';
    }

    public static function connect() {
        if (self::$conn === null) {
            self::loadConfig();
            
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                self::$conn = new PDO(
                    $dsn,
                    self::$username,
                    self::$password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_PERSISTENT => false
                    ]
                );

            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
