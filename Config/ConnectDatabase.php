<?php
/**
 * Class ConnectDatabase - Kết nối cơ sở dữ liệu
 * Sử dụng MySQLi theo mô hình MVC truyền thống
 */
class ConnectDatabase {
    private $host = '127.0.0.1';
    private $username = 'root';
<<<<<<< HEAD
    private $password = '742005';
    private $database = 'CoffeePHP';
=======
    private $password = '123456';
    private $database = 'coffee_php';
>>>>>>> 07ab933d2ec1b15f21786d43f3c59cc83525cae9
    public $con;

    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct() {
        $this->con = mysqli_connect($this->host, $this->username, $this->password, $this->database);

        // Kiểm tra kết nối
        if (!$this->con) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }

        // Set charset UTF-8
        mysqli_query($this->con, "SET NAMES 'utf8'");
    }

    /**
     * Lấy connection object
     * @return mysqli
     */
    public function getConnection() {
        return $this->con;
    }

    /**
     * Đóng kết nối database
     */
    public function close() {
        if ($this->con) {
            mysqli_close($this->con);
        }
    }
}
