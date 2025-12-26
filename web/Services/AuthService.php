<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class AuthService extends Service {

    /**
     * Xác thực đăng nhập
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => object|null, 'userType' => string]
     */
    public function authenticateUser($username, $password, $userType = null) {
        // Validate đầu vào
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập!',
                'user' => null,
                'userType' => null
            ];
        }

        if (strlen($username) < 3) {
            return [
                'success' => false,
                'message' => 'Tên đăng nhập phải có ít nhất 3 ký tự!',
                'user' => null,
                'userType' => null
            ];
        }

        // Nếu userType được truyền vào thì chỉ query bảng tương ứng
        $custRepo = $this->repository('CustomerRepository');
        $empRepo = $this->repository('EmployeeRepository');

        $user = null;
        if ($userType === 'employee') {
            $user = $empRepo->findByUsername($username);
            $userType = 'employee';
        } elseif ($userType === 'customer') {
            $user = $custRepo->findByUsername($username);
            $userType = 'customer';
        } else {
            // Mặc định: tìm trong customers rồi employee
            $user = $custRepo->findByUsername($username);
            $userType = 'customer';
            if (!$user) {
                $user = $empRepo->findByUsername($username);
                $userType = $user ? 'employee' : $userType;
            }
        }

        // Kiểm tra tồn tại
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Tài khoản không tồn tại!',
                'user' => null,
                'userType' => null
            ];
        }

        // Kiểm tra mật khẩu
        if (!$this->verifyPassword($password, $user->password ?? '')) {
            return [
                'success' => false,
                'message' => 'Mật khẩu không chính xác!',
                'user' => null,
                'userType' => null
            ];
        }

        // Đăng nhập thành công
        return [
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'user' => $user,
            'userType' => $userType
        ];
    }

    /**
     * Verify mật khẩu (hỗ trợ hash và plain text)
     * @param string $password
     * @param string $storedHash
     * @return bool
     */
    private function verifyPassword($password, $storedHash) {
        if (empty($storedHash)) {
            return false;
        }

        // Thử verify bằng password_verify nếu có hash
        if (password_verify($password, $storedHash)) {
            return true;
        }

        // Fallback so sánh thẳng (plain text)
        if ($password === $storedHash) {
            return true;
        }

        return false;
    }

    /**
     * Xác định URL redirect theo roleName và userType
     * @param string $userType
     * @param string $roleName
     * @return string
     */
    public function getRedirectUrl($userType, $roleName) {
        if ($userType === 'employee') {
            // Employee: chuyển hướng dựa trên roleName
            // Tất cả nhân viên đều vào trang quản lý
            return '/COFFEE_PHP/EmployeeController/GetData';
        } else {
            // Customer
            return '/COFFEE_PHP/CustomerController/GetData';
        }
    }

    /**
     * Validate dữ liệu đăng ký
     * @param array $data
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validateRegistration($data) {
        // Kiểm tra các trường bắt buộc
        if (empty($data['fullname']) || empty($data['phone']) || 
            empty($data['username']) || empty($data['password'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc!'
            ];
        }

        // Validate họ tên
        if (strlen($data['fullname']) < 2) {
            return [
                'valid' => false,
                'message' => 'Họ và tên phải có ít nhất 2 ký tự!'
            ];
        }

        // Validate username
        if (strlen($data['username']) < 3) {
            return [
                'valid' => false,
                'message' => 'Tên đăng nhập phải có ít nhất 3 ký tự!'
            ];
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            return [
                'valid' => false,
                'message' => 'Số điện thoại phải có 10-11 chữ số!'
            ];
        }

        // Validate password
        if (strlen($data['password']) < 6) {
            return [
                'valid' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'
            ];
        }

        // Validate confirm password
        if ($data['password'] !== $data['confirmPassword']) {
            return [
                'valid' => false,
                'message' => 'Mật khẩu xác nhận không khớp!'
            ];
        }

        // Validate email format (nếu có)
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Email không hợp lệ!'
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Kiểm tra username đã tồn tại
     * @param string $username
     * @return array ['exists' => bool, 'message' => string]
     */
    public function checkUsernameExists($username) {
        $custRepo = $this->repository('CustomerRepository');
        $empRepo = $this->repository('EmployeeRepository');

        if ($custRepo->findByUsername($username) || $empRepo->findByUsername($username)) {
            return [
                'exists' => true,
                'message' => 'Tên đăng nhập đã tồn tại!'
            ];
        }

        return ['exists' => false, 'message' => ''];
    }

    /**
     * Kiểm tra phone đã tồn tại
     * @param string $phone
     * @return array ['exists' => bool, 'message' => string]
     */
    public function checkPhoneExists($phone) {
        $custRepo = $this->repository('CustomerRepository');

        if ($custRepo->findByPhone($phone)) {
            return [
                'exists' => true,
                'message' => 'Số điện thoại đã được sử dụng!'
            ];
        }

        return ['exists' => false, 'message' => ''];
    }

    /**
     * Kiểm tra email đã tồn tại
     * @param string $email
     * @return array ['exists' => bool, 'message' => string]
     */
    public function checkEmailExists($email) {
        if (empty($email)) {
            return ['exists' => false, 'message' => ''];
        }

        $custRepo = $this->repository('CustomerRepository');

        if ($custRepo->findByEmail($email)) {
            return [
                'exists' => true,
                'message' => 'Email đã được sử dụng!'
            ];
        }

        return ['exists' => false, 'message' => ''];
    }

    /**
     * Đăng ký customer mới
     * @param array $data
     * @return array ['success' => bool, 'message' => string]
     */
    public function registerCustomer($data) {
        // Validate
        $validation = $this->validateRegistration($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        // Kiểm tra username đã tồn tại
        $usernameCheck = $this->checkUsernameExists($data['username']);
        if ($usernameCheck['exists']) {
            return [
                'success' => false,
                'message' => $usernameCheck['message']
            ];
        }

        // Kiểm tra phone đã tồn tại
        $phoneCheck = $this->checkPhoneExists($data['phone']);
        if ($phoneCheck['exists']) {
            return [
                'success' => false,
                'message' => $phoneCheck['message']
            ];
        }

        // Kiểm tra email đã tồn tại (nếu có)
        $emailCheck = $this->checkEmailExists($data['email'] ?? '');
        if ($emailCheck['exists']) {
            return [
                'success' => false,
                'message' => $emailCheck['message']
            ];
        }

        // Lưu mật khẩu nguyên bản (plain-text) theo yêu cầu
        $plainPassword = $data['password'];

        // Tạo customer mới
        $custRepo = $this->repository('CustomerRepository');
        
        // Tạo entity
        $customer = new CustomerEntity([
            'username' => $data['username'],
            'password' => $plainPassword,
            'full_name' => $data['fullname'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? '',
            'points' => 0,
            'status' => 1,
            'roleId' => 5
        ]);

        // Lưu vào database
        $result = $custRepo->create($customer);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Đăng ký thành công!'
            ];
        } else {
            // Lấy lỗi MySQL để debug
            $error = mysqli_error($custRepo->con);
            return [
                'success' => false,
                'message' => 'Đăng ký thất bại! Lỗi: ' . $error
            ];
        }
    }
}
?>
