<?php
/**
 * EmployeeEntity - Đại diện cho bảng employee
 * TABLE: employee
 */
namespace web\Entity;
class EmployeeEntity {

    // Properties tương ứng với các cột trong bảng employee
    public $id;
    public $username;
    public $password;
    public $fullname;
    public $email;
    public $phonenumber;
    public $address;
    public $roleName;
    public $luong;

    /**
     * Constructor - Khởi tạo entity từ array
     * @param array $data - Dữ liệu từ database
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->username = $data['username'] ?? null;
            $this->password = $data['password'] ?? null;
            $this->fullname = $data['fullname'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->phonenumber = $data['phonenumber'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->roleName = $data['roleName'] ?? null;
            $this->luong = $data['luong'] ?? null;
        }
    }

    /**
     * Lấy tên vai trò hiển thị đầy đủ
     */
    public function getRoleName() {
        return $this->roleName ?? 'Không xác định';
    }

    /**
     * Lấy tên vai trò để hiển thị (tên đầy đủ)
     */
    public function getRoleDisplayName() {
        $roleMap = [
            'ORDER' => 'Nhân viên Order',
            'BARTENDER' => 'Nhân viên Pha chế',
            'SHIPPER' => 'Nhân viên Giao hàng'
        ];
        return $roleMap[$this->roleName] ?? 'Không xác định';
    }

    /**
     * Format lương VNĐ
     */
    public function getFormattedSalary() {
        return number_format($this->luong, 0, ',', '.') . '₫';
    }

    /**
     * Chuyển entity thành array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phonenumber' => $this->phonenumber,
            'address' => $this->address,
            'roleName' => $this->roleName,
            'roleDisplayName' => $this->getRoleDisplayName(),
            'luong' => $this->luong,
            'formattedSalary' => $this->getFormattedSalary()
        ];
    }
}
?>

