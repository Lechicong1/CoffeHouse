<?php
require_once __DIR__ . '/../Entity/EmployeeEntity.php';
use web\Entity\EmployeeEntity;


class EmployeeRepository extends ConnectDatabase {

    public function findAll() {
        $sql = "SELECT * FROM employee ORDER BY id DESC";
        $result = mysqli_query($this->con, $sql);

        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = new EmployeeEntity($row);
        }

        return $employees;
    }


    public function findById($id) {
        $sql = "SELECT * FROM employee WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
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
        $stmt = mysqli_prepare($this->con, $sql);
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
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "si", $email, $excludeId);
        } else {
            $sql = "SELECT * FROM employee WHERE email = ?";
            $stmt = mysqli_prepare($this->con, $sql);
            mysqli_stmt_bind_param($stmt, "s", $email);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new EmployeeEntity($data) : null;
    }

    /**
     * Lấy nhân viên theo vai trò
     * @param string $roleName
     * @return array
     */
    public function findByRole($roleName) {
        $sql = "SELECT * FROM employee WHERE roleName = ? ORDER BY fullname";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $roleName);
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

        $stmt = mysqli_prepare($this->con, $sql);
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
        $sql = "INSERT INTO employee (username, password, fullname, email, phonenumber, address, roleName, luong) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssi",
            $employee->username,
            $employee->password,
            $employee->fullname,
            $employee->email,
            $employee->phonenumber,
            $employee->address,
            $employee->roleName,
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
                SET fullname = ?, email = ?, phonenumber = ?, address = ?, roleName = ?, luong = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "sssssis",
            $employee->fullname,
            $employee->email,
            $employee->phonenumber,
            $employee->address,
            $employee->roleName,
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
        $stmt = mysqli_prepare($this->con, $sql);
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
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm tổng số nhân viên
     * @return int
     */
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM employee";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    /**
     * Đếm nhân viên theo vai trò
     * @param string $roleName
     * @return int
     */
    public function countByRole($roleName) {
        $sql = "SELECT COUNT(*) as total FROM employee WHERE roleName = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $roleName);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        return $row['total'];
    }
}
?>
