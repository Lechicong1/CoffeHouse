<?php

namespace web\Entity;
class CustomerEntity {
    public $id;
    public $username;
    public $password;
    public $full_name;
    public $phone;
    public $email;
    public $address;
    public $account_type;
    public $points;
    public $status;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->username = $data['username'] ?? null;
            $this->password = $data['password'] ?? null;
            $this->full_name = $data['full_name'] ?? null;
            $this->phone = $data['phone'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->account_type = $data['account_type'] ?? ($data['accountType'] ?? 'GUEST_POS');
            $this->points = isset($data['points']) ? (int)$data['points'] : 0;
            $this->status = isset($data['status']) ? (int)$data['status'] : 1;
        }
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'account_type' => $this->account_type,
            'points' => $this->points,
            'status' => $this->status
        ];
    }
}
?>
