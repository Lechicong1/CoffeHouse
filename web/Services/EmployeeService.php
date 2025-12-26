<?php
/**
 * EmployeeService - Xử lý logic nghiệp vụ cho Employee
 */

use web\Entity\EmployeeEntity;

class EmployeeService extends Service {

    private $employeeRepo;

    public function __construct() {
        // Khởi tạo Repository thông qua Service base
        $this->employeeRepo = $this->repository('EmployeeRepository');
    }

    /**
     * Lấy tất cả nhân viên
     */
    public function getAllEmployees() {
        return $this->employeeRepo->findAll();
    }

    /**
     * Lấy nhân viên theo ID
     */
    public function getEmployeeById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID không hợp lệ");
        }

        return $this->employeeRepo->findById($id);
    }

    /**
     * Lấy nhân viên theo vai trò
     */
    public function getEmployeesByRole($roleId) {
        return $this->employeeRepo->findByRole($roleId);
    }

    /**
     * Tìm kiếm nhân viên
     */
    public function searchEmployees($keyword) {
        if (empty(trim($keyword))) {
            return $this->getAllEmployees();
        }

        return $this->employeeRepo->search($keyword);
    }

    /**
     * Tạo nhân viên mới
     */
    public function createEmployee($data) {
        // Validate dữ liệu (bao gồm cả check username và email trùng)
        $this->validateEmployeeData($data);

        // Tạo entity
        $employee = new EmployeeEntity();
        $employee->username = trim($data['username']);
        $employee->password = trim($data['password']);
        $employee->fullname = trim($data['fullname']);
        $employee->email = trim($data['email']);
        $employee->phonenumber = trim($data['phonenumber']);
        $employee->address = trim($data['address'] ?? '');
        $employee->roleId = $data['roleId'];
        $employee->luong = $data['luong'];

        // Lưu vào database
        $result = $this->employeeRepo->create($employee);
        
        if ($result) {
            return ['success' => true, 'message' => 'Tạo nhân viên thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi tạo nhân viên'];
        }
    }


    public function updateEmployee($id, $data) {
        // Kiểm tra nhân viên có tồn tại không
        $employee = $this->employeeRepo->findById($id);
        if (!$employee) {
            throw new Exception("Nhân viên không tồn tại");
        }

        // Validate dữ liệu (bao gồm cả check email trùng, loại trừ chính user này)
        $this->validateEmployeeData($data, true, $id);

        // Cập nhật thông tin
        $employee->fullname = trim($data['fullname']);
        $employee->email = trim($data['email']);
        $employee->phonenumber = trim($data['phonenumber']);
        $employee->address = trim($data['address'] ?? '');
        $employee->roleId = $data['roleId'];
        $employee->luong = $data['luong'];

        // Lưu vào database
        if ($this->employeeRepo->update($employee)) {
            return ['success' => true, 'message' => 'Cập nhật thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật'];
        }
    }

    /**
     * Đổi mật khẩu nhân viên
     */
    public function changePassword($id, $newPassword) {
        // Kiểm tra nhân viên có tồn tại không
        $employee = $this->employeeRepo->findById($id);
        if (!$employee) {
            throw new Exception("Nhân viên không tồn tại");
        }

        // Validate password
        if (strlen($newPassword) < 6) {
            throw new Exception("Mật khẩu phải có ít nhất 6 ký tự");
        }

        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Cập nhật
        if ($this->employeeRepo->updatePassword($id, $hashedPassword)) {
            return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi đổi mật khẩu'];
        }
    }

    /**
     * Xóa nhân viên
     */
    public function deleteEmployee($id) {
        // Kiểm tra nhân viên có tồn tại không
        $employee = $this->employeeRepo->findById($id);
        if (!$employee) {
            throw new Exception("Nhân viên không tồn tại");
        }

        // Xóa
        if ($this->employeeRepo->delete($id)) {
            return ['success' => true, 'message' => 'Xóa nhân viên thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi xóa nhân viên'];
        }
    }



    public function checkUsernameExists($username) {
        $employee = $this->employeeRepo->findByUsername($username);
        return $employee !== null;
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     * @param string $email Email cần kiểm tra
     * @param int|null $excludeId ID nhân viên cần loại trừ (khi update)
     * @return bool
     */
    public function checkEmailExists($email, $excludeId = null) {
        if (empty($email)) {
            return false; // Email rỗng thì không cần kiểm tra (vì có thể NULL)
        }
        $employee = $this->employeeRepo->findByEmail($email, $excludeId);
        return $employee !== null;
    }

    /**
     * Validate dữ liệu nhân viên
     */
    private function validateEmployeeData($data, $isUpdate = false, $excludeId = null) {
        // Validate username (chỉ khi tạo mới)
        if (!$isUpdate) {
            if (empty($data['username']) || strlen(trim($data['username'])) < 3) {
                throw new Exception("Username phải có ít nhất 3 ký tự");
            }

            // Validate password (chỉ khi tạo mới)
            if (empty($data['password']) || strlen($data['password']) < 6) {
                throw new Exception("Mật khẩu phải có ít nhất 6 ký tự");
            }
            
            // Kiểm tra username đã tồn tại chưa
            $existingEmployee = $this->employeeRepo->findByUsername($data['username']);
            if ($existingEmployee) {
                throw new Exception("Username đã tồn tại");
            }
        }

        // Validate fullname
        if (empty($data['fullname']) || strlen(trim($data['fullname'])) < 3) {
            throw new Exception("Họ tên phải có ít nhất 3 ký tự");
        }

        // Validate email
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ");
        }
        
        // Kiểm tra email đã tồn tại chưa
        if (!empty($data['email']) && $this->checkEmailExists($data['email'], $excludeId)) {
            throw new Exception("Email đã tồn tại");
        }

        // Validate phone
        if (empty($data['phonenumber']) || !preg_match('/^[0-9]{10}$/', $data['phonenumber'])) {
            throw new Exception("Số điện thoại phải có 10 chữ số");
        }

        // Validate roleId
        if (!isset($data['roleId']) || !in_array($data['roleId'], [1, 2, 3, 4, 5])) {
            throw new Exception("Vai trò không hợp lệ");
        }

        // Validate salary
        if (!isset($data['luong']) || $data['luong'] < 0) {
            throw new Exception("Lương không hợp lệ");
        }
    }
}
?>
