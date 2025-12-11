<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    private static $conn = null;

    private static $host = "localhost";
    private static $db_name = "coffee_php";
    private static $username = "root";
    private static $password = "742005";

    public static function connect() {
        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );

                self::$conn->exec("SET NAMES utf8");
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}
