<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class CustomerService extends Service {

    public function getAllCustomers() {
        return $this->repository('CustomerRepository')->findAll();
    }

    public function getCustomerById($id) {
        return $this->repository('CustomerRepository')->findById($id);
    }

    public function getCustomerByPhone($phone) {
        return $this->repository('CustomerRepository')->findByPhone($phone);
    }

    public function getCustomerAddress($id) {
        return $this->repository('CustomerRepository')->getAddressById($id);
    }

    public function searchCustomers($keyword) {
        return $this->repository('CustomerRepository')->search($keyword);
    }

    public function createCustomer($data) {
        $validation = $this->validateCustomerData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        $repository = $this->repository('CustomerRepository');

        if (!empty($data['email']) && $repository->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email đã được sử dụng bởi khách hàng khác!'];
        }

        if ($repository->findByPhone($data['phone'])) {
            return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng bởi khách hàng khác!'];
        }

        $customer = new CustomerEntity([
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'account_type' => $data['account_type'] ?? 'WEB',
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'points' => isset($data['points']) ? (int)$data['points'] : 0,
            'status' => isset($data['status']) ? (int)$data['status'] : 1
        ]);

        $result = $repository->create($customer);
        return $result 
            ? ['success' => true, 'message' => 'Thêm khách hàng thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khách hàng!'];
    }

    public function updateCustomer($id, $data) {
        $repository = $this->repository('CustomerRepository');
        $customer = $repository->findById($id);
        
        if (!$customer) {
            return ['success' => false, 'message' => 'Không tìm thấy khách hàng!'];
        }

        $validation = $this->validateCustomerData($data, $id);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        if (!empty($data['email']) && $repository->findByEmail($data['email'], $id)) {
            return ['success' => false, 'message' => 'Email đã được sử dụng bởi khách hàng khác!'];
        }

        if ($repository->findByPhone($data['phone'], $id)) {
            return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng bởi khách hàng khác!'];
        }

        $customer->full_name = $data['full_name'];
        $customer->phone = $data['phone'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->points = isset($data['points']) ? (int)$data['points'] : $customer->points;
        $customer->status = isset($data['status']) ? (int)$data['status'] : $customer->status;

        $result = $repository->update($customer);
        return $result 
            ? ['success' => true, 'message' => 'Cập nhật khách hàng thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật khách hàng!'];
    }

    public function posUpsertCustomer($data) {
        $phone = $data['phone'] ?? null;
        if (!$phone) {
            return ['success' => false, 'message' => 'Phone is required'];
        }

        $repository = $this->repository('CustomerRepository');
        $cust = $repository->findByPhone($phone);
        $pointsToAdd = (int)($data['pointsToAdd'] ?? 0);

        if ($cust) {
            if ($pointsToAdd > 0) {
                $cust->points += $pointsToAdd;
                $repository->updatePoints($cust->id, $cust->points);
            }
            return ['success' => true, 'customer' => $cust, 'created' => false];
        }

        $customer = new CustomerEntity([
            'username' => 'pos_' . preg_replace('/[^0-9]/', '', $phone),
            'password' => '',
            'full_name' => $data['fullname'] ?? 'Khách lẻ',
            'phone' => $phone,
            'email' => $data['email'] ?? '',
            'address' => $data['address'] ?? '',
            'account_type' => 'GUEST_POS',
            'points' => $pointsToAdd,
            'status' => 1
        ]);

        if ($repository->create($customer)) {
            return ['success' => true, 'customer' => $repository->findByPhone($phone), 'created' => true];
        }
        return ['success' => false, 'message' => 'Create failed'];
    }

    public function upgradeToWebAccountByPhone($phone, $username, $password, $address = null) {
        $repository = $this->repository('CustomerRepository');
        $cust = $repository->findByPhone($phone);
        return $cust ? $repository->upgradeToWebAccount($cust->id, $username, $password, $address) : false;
    }

    public function deleteCustomer($id) {
        $repository = $this->repository('CustomerRepository');
        $customer = $repository->findById($id);
        
        if (!$customer) {
            return ['success' => false, 'message' => 'Không tìm thấy khách hàng!'];
        }

        $result = $repository->delete($id);
        return $result 
            ? ['success' => true, 'message' => 'Xóa khách hàng thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khách hàng!'];
    }

    private function validateCustomerData($data, $excludeId = null) {
        if (empty($data['full_name']) || strlen(trim($data['full_name'])) < 2) {
            return ['valid' => false, 'message' => 'Họ tên phải có ít nhất 2 ký tự!'];
        }
        if (empty($data['phone'])) {
            return ['valid' => false, 'message' => 'Số điện thoại không được để trống!'];
        }
        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            return ['valid' => false, 'message' => 'Số điện thoại không hợp lệ! (10-11 số)'];
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'Email không hợp lệ!'];
        }
        if (isset($data['points']) && (int)$data['points'] < 0) {
            return ['valid' => false, 'message' => 'Điểm tích lũy không thể âm!'];
        }
        return ['valid' => true, 'message' => ''];
    }

    public function checkEmailExists($email, $excludeId = null) {
        return $this->repository('CustomerRepository')->findByEmail($email, $excludeId) !== null;
    }

    public function updateCustomerPoints($id, $points) {
        $repository = $this->repository('CustomerRepository');
        $customer = $repository->findById($id);
        
        if (!$customer) {
            return ['success' => false, 'message' => 'Không tìm thấy khách hàng!'];
        }
        if ($points < 0) {
            return ['success' => false, 'message' => 'Điểm tích lũy không thể âm!'];
        }

        $result = $repository->updatePoints($id, $points);
        return $result 
            ? ['success' => true, 'message' => 'Cập nhật điểm tích lũy thành công!']
            : ['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật điểm!'];
    }

    public function countCustomers() {
        return $this->repository('CustomerRepository')->count();
    }

    public function countCustomersByStatus($status) {
        return $this->repository('CustomerRepository')->countByStatus($status);
    }
}
