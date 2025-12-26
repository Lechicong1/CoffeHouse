<?php
/**
 * FILE: CustomerEntity.php
 * DESCRIPTION: Entity Customer - Chứa properties từ bảng customers
 * TABLE: customers
 * AUTHOR: Coffee House System
 */
namespace web\Entity;
class CustomerEntity {
    // Properties từ bảng customers
    public $id;
    public $full_name;
    public $phone;
    public $email;
    public $points;
    public $status;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->full_name = $data['full_name'] ?? null;
            $this->phone = $data['phone'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->points = isset($data['points']) ? (int)$data['points'] : 0;
            $this->status = isset($data['status']) ? (bool)$data['status'] : true;
        }
    }

    /**
     * Chuyển entity thành mảng (tùy chọn cho insert/update)
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'points' => $this->points,
            'status' => $this->status
        ];
    }
}
?>
