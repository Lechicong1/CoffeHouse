<?php
/**
 * EmployeeRepository - Xử lý truy vấn database cho bảng employee
 */
class EmployeeRepository {

    private $conn;

    public function __construct() {
        require_once __DIR__ . '/../../Config/Database.php';
        require_once __DIR__ . '/../Models/EmployeeEntity.php';

        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy tất cả nhân viên
     * @return array
     */
    public function findAll() {
        $sql = "SELECT * FROM employee ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $sql);

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = new EmployeeEntity($row);
        }

        return $employees;
    }

    /**
     * Lấy nhân viên theo ID
     * @param int $id
     * @return EmployeeEntity|null
     */
    public function findById($id) {
        $sql = "SELECT * FROM employee WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new EmployeeEntity($data) : null;
    }

    /**
     * Lấy nhân viên theo username
     * @param string $username
     * @return EmployeeEntity|null
     */
    public function findByUsername($username) {
        $sql = "SELECT * FROM employee WHERE username = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new EmployeeEntity($data) : null;
    }

    /**
     * Lấy nhân viên theo email
     * @param string $email
     * @param int|null $excludeId ID nhân viên cần loại trừ (khi update)
     * @return EmployeeEntity|null
     */
    public function findByEmail($email, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT * FROM employee WHERE email = ? AND id != ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $email, $excludeId);
        } else {
            $sql = "SELECT * FROM employee WHERE email = ?";
            $stmt = mysqli_prepare($this->conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new EmployeeEntity($data) : null;
    }

    /**
     * Lấy nhân viên theo vai trò
     * @param int $roleId
     * @return array
     */
    public function findByRole($roleId) {
        $sql = "SELECT * FROM employee WHERE roleId = ? ORDER BY fullname";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $roleId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = new EmployeeEntity($row);
        }

        return $employees;
    }

    /**
     * Tìm kiếm nhân viên
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $sql = "SELECT * FROM employee 
                WHERE fullname LIKE ? 
                OR username LIKE ? 
                OR email LIKE ? 
                OR phonenumber LIKE ?
                ORDER BY fullname";

        $stmt = mysqli_prepare($this->conn, $sql);
        $searchTerm = "%$keyword%";
        mysqli_stmt_bind_param($stmt, "ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = new EmployeeEntity($row);
        }

        return $employees;
    }

    /**
     * Tạo nhân viên mới
     * @param EmployeeEntity $employee
     * @return bool
     */
    public function create($employee) {
        $sql = "INSERT INTO employee (username, password, fullname, email, phonenumber, address, roleId, luong) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssii",
            $employee->username,
            $employee->password,
            $employee->fullname,
            $employee->email,
            $employee->phonenumber,
            $employee->address,
            $employee->roleId,
            $employee->luong
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật nhân viên
     * @param EmployeeEntity $employee
     * @return bool
     */
    public function update($employee) {
        $sql = "UPDATE employee 
                SET fullname = ?, email = ?, phonenumber = ?, address = ?, roleId = ?, luong = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssiis",
            $employee->fullname,
            $employee->email,
            $employee->phonenumber,
            $employee->address,
            $employee->roleId,
            $employee->luong,
            $employee->id
        );

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Cập nhật mật khẩu
     * @param int $id
     * @param string $newPassword (đã hash)
     * @return bool
     */
    public function updatePassword($id, $newPassword) {
        $sql = "UPDATE employee SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $newPassword, $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa nhân viên
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM employee WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số nhân viên
     * @return int
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM employee";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    /**
     * Đếm nhân viên theo vai trò
     * @param int $roleId
     * @return int
     */
    public function countByRole($roleId) {
        $sql = "SELECT COUNT(*) as total FROM employee WHERE roleId = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $roleId);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }
}
?>

