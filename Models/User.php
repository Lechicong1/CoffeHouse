<?php

class User {
    public $id;
    public $username;
    public $password_hash;
    public $email;
    public $phone_number;
    public $full_name;
    public $role; // admin, staff, customer, shipper
    public $avatar_url;
    public $created_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->username = $data['username'] ?? null;
            $this->password_hash = $data['password_hash'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->phone_number = $data['phone_number'] ?? null;
            $this->full_name = $data['full_name'] ?? null;
            $this->role = $data['role'] ?? 'customer';
            $this->avatar_url = $data['avatar_url'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }
}
?>
