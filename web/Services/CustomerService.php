<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class CustomerService extends Service {

    /**
     * Lấy tất cả khách hàng
     * @return array
     */
    public function getAllCustomers() {
        $repository = $this->repository('CustomerRepository');
        return $repository->findAll();
    }

    /**
     * Lấy khách hàng theo ID
     * @param int $id
     * @return CustomerEntity|null
     */
    public function getCustomerById($id) {
        $repository = $this->repository('CustomerRepository');
        return $repository->findById($id);
    }

    /**
     * Lấy địa chỉ của khách hàng theo ID
     * @param int $id
     * @return string|null
     */
    public function getCustomerAddress($id) {
        $repository = $this->repository('CustomerRepository');
        return $repository->getAddressById($id);
    }

    /**
     * Tìm kiếm khách hàng
     * @param string $keyword
     * @return array
     */
    public function searchCustomers($keyword) {
        $repository = $this->repository('CustomerRepository');
        return $repository->search($keyword);
    }

    /**
     * Tạo khách hàng mới
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function createCustomer($data) {
        // Validate dữ liệu
        $validation = $this->validateCustomerData($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $repository = $this->repository('CustomerRepository');

        // Kiểm tra email đã tồn tại
        if (!empty($data['email'])) {
            $existingCustomer = $repository->findByEmail($data['email']);
            if ($existingCustomer) {
                return [
                    'success' => false,
                    'message' => 'Email đã được sử dụng bởi khách hàng khác!'
                ];
            }
        }

        // Kiểm tra số điện thoại đã tồn tại
        $existingPhone = $repository->findByPhone($data['phone']);
        if ($existingPhone) {
            return [
                'success' => false,
                'message' => 'Số điện thoại đã được sử dụng bởi khách hàng khác!'
            ];
        }

        // Tạo entity
        $customer = new CustomerEntity([
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'points' => isset($data['points']) ? (int)$data['points'] : 0,
            'status' => isset($data['status']) ? (int)$data['status'] : 1
        ]);

        // Lưu vào database
        $result = $repository->create($customer);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Thêm khách hàng thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm khách hàng!'
            ];
        }
    }

    /**
     * Cập nhật khách hàng
     * @param int $id
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateCustomer($id, $data) {
        $repository = $this->repository('CustomerRepository');

        // Kiểm tra khách hàng tồn tại
        $customer = $repository->findById($id);
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy khách hàng!'
            ];
        }

        // Validate dữ liệu
        $validation = $this->validateCustomerData($data, $id);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        // Kiểm tra email đã tồn tại (trừ khách hàng hiện tại)
        if (!empty($data['email'])) {
            $existingCustomer = $repository->findByEmail($data['email'], $id);
            if ($existingCustomer) {
                return [
                    'success' => false,
                    'message' => 'Email đã được sử dụng bởi khách hàng khác!'
                ];
            }
        }

        // Kiểm tra số điện thoại đã tồn tại (trừ khách hàng hiện tại)
        $existingPhone = $repository->findByPhone($data['phone'], $id);
        if ($existingPhone) {
            return [
                'success' => false,
                'message' => 'Số điện thoại đã được sử dụng bởi khách hàng khác!'
            ];
        }

        // Cập nhật thông tin
        $customer->full_name = $data['full_name'];
        $customer->phone = $data['phone'];
        $customer->email = $data['email'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->points = isset($data['points']) ? (int)$data['points'] : 0;
        $customer->status = isset($data['status']) ? (int)$data['status'] : 1;

        // Lưu vào database
        $result = $repository->update($customer);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật khách hàng thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật khách hàng!'
            ];
        }
    }

    /**
     * Xóa khách hàng
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteCustomer($id) {
        $repository = $this->repository('CustomerRepository');

        // Kiểm tra khách hàng tồn tại
        $customer = $repository->findById($id);
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy khách hàng!'
            ];
        }

        // Xóa khách hàng
        $result = $repository->delete($id);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Xóa khách hàng thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa khách hàng!'
            ];
        }
    }

    /**
     * Validate dữ liệu khách hàng
     * @param array $data
     * @param int|null $excludeId
     * @return array ['valid' => bool, 'message' => string]
     */
    private function validateCustomerData($data, $excludeId = null) {
        // Kiểm tra họ tên
        if (empty($data['full_name']) || strlen(trim($data['full_name'])) < 2) {
            return [
                'valid' => false,
                'message' => 'Họ tên phải có ít nhất 2 ký tự!'
            ];
        }

        // Kiểm tra số điện thoại
        if (empty($data['phone'])) {
            return [
                'valid' => false,
                'message' => 'Số điện thoại không được để trống!'
            ];
        }

        // Validate format số điện thoại (10-11 số)
        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            return [
                'valid' => false,
                'message' => 'Số điện thoại không hợp lệ! (10-11 số)'
            ];
        }

        // Kiểm tra email nếu có
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Email không hợp lệ!'
            ];
        }

        // Kiểm tra điểm tích lũy
        if (isset($data['points']) && (int)$data['points'] < 0) {
            return [
                'valid' => false,
                'message' => 'Điểm tích lũy không thể âm!'
            ];
        }

        return [
            'valid' => true,
            'message' => ''
        ];
    }

    /**
     * Kiểm tra email đã tồn tại
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function checkEmailExists($email, $excludeId = null) {
        $repository = $this->repository('CustomerRepository');
        $customer = $repository->findByEmail($email, $excludeId);
        return $customer !== null;
    }

    /**
     * Cập nhật điểm tích lũy
     * @param int $id
     * @param int $points
     * @return array ['success' => bool, 'message' => string]
     */
    public function updateCustomerPoints($id, $points) {
        $repository = $this->repository('CustomerRepository');

        // Kiểm tra khách hàng tồn tại
        $customer = $repository->findById($id);
        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy khách hàng!'
            ];
        }

        // Validate điểm
        if ($points < 0) {
            return [
                'success' => false,
                'message' => 'Điểm tích lũy không thể âm!'
            ];
        }

        // Cập nhật điểm
        $result = $repository->updatePoints($id, $points);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật điểm tích lũy thành công!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật điểm!'
            ];
        }
    }

    /**
     * Đếm tổng số khách hàng
     * @return int
     */
    public function countCustomers() {
        $repository = $this->repository('CustomerRepository');
        return $repository->count();
    }

    /**
     * Đếm khách hàng theo trạng thái
     * @param int $status
     * @return int
     */
    public function countCustomersByStatus($status) {
        $repository = $this->repository('CustomerRepository');
        return $repository->countByStatus($status);
    }
}
