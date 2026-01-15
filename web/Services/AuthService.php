<?php
require_once __DIR__ . '/../Entity/CustomerEntity.php';
use web\Entity\CustomerEntity;

class AuthService extends Service {
    public function authenticateUser($username, $password, $userType = null) {
        if (empty($username) || empty($password)) {
            return [
                'success' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập!',
                'user' => null,
                'userType' => null
            ];
        }
        

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
            $user = $custRepo->findByUsername($username);
            $userType = 'customer';
            if (!$user) {
                $user = $empRepo->findByUsername($username);
                $userType = $user ? 'employee' : $userType;
            }
        }

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
            $role = strtoupper(trim((string)$roleName));
            // Map role => Staff pages. Nếu cần trang riêng cho BARTENDER/SHIPPER,
            // ta có thể tạo route tương ứng; hiện tại dùng pages có sẵn.
            switch ($role) {
                case 'ORDER':
                    return '/COFFEE_PHP/Staff/pos';
                case 'BARTENDER':
                    return '/COFFEE_PHP/BaristaController';
                case 'SHIPPER':
                    return '/COFFEE_PHP/ShipperController';
                case 'ADMIN':
                    return '/COFFEE_PHP/ProductController/GetData';
                default:
                    return '/COFFEE_PHP/EmployeeController/GetData';
            }
        }

        // Customer -> chuyển về trang User dashboard (index)
        return '/COFFEE_PHP/User/index';
    }

    //Đăng ký
    public function validateRegistration($data) {
        if (empty($data['fullname']) || empty($data['phone']) || 
            empty($data['username']) || empty($data['password'])) {
            return [
                'valid' => false,
                'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc!'
            ];
        }

        if (strlen($data['fullname']) < 2) {
            return [
                'valid' => false,
                'message' => 'Họ và tên phải có ít nhất 2 ký tự!'
            ];
        }

        if (strlen($data['username']) < 3) {
            return [
                'valid' => false,
                'message' => 'Tên đăng nhập phải có ít nhất 3 ký tự!'
            ];
        }

        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            return [
                'valid' => false,
                'message' => 'Số điện thoại phải có 10-11 chữ số!'
            ];
        }

        if (strlen($data['password']) < 6) {
            return [
                'valid' => false,
                'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'
            ];
        }

        if ($data['password'] !== $data['confirmPassword']) {
            return [
                'valid' => false,
                'message' => 'Mật khẩu xác nhận không khớp!'
            ];
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'message' => 'Email không hợp lệ!'
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

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

    public function registerCustomer($data) {
        $validation = $this->validateRegistration($data);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $usernameCheck = $this->checkUsernameExists($data['username']);
        if ($usernameCheck['exists']) {
            return [
                'success' => false,
                'message' => $usernameCheck['message']
            ];
        }

        $custRepo = $this->repository('CustomerRepository');
        $existingCustomer = $custRepo->findByPhone($data['phone']);
        
        if ($existingCustomer) {
            if ($existingCustomer->account_type === 'GUEST_POS') {
                if (!empty($data['email'])) {
                    $emailExists = $custRepo->findByEmail($data['email'], $existingCustomer->id);
                    if ($emailExists) {
                        return ['success' => false, 'message' => 'Email đã được sử dụng! Vui lòng sử dụng email khác.'];
                    }
                }
                
                $result = $custRepo->upgradeToWebAccount(
                    $existingCustomer->id,
                    $data['username'],
                    $data['password'],
                    $data['address'] ?? null,
                    $data['email'] ?? null,
                    $data['fullname'] ?? null
                );
                
                return $result 
                    ? ['success' => true, 'message' => 'Đăng ký thành công! Tài khoản của bạn đã được nâng cấp.']
                    : ['success' => false, 'message' => 'Có lỗi xảy ra khi nâng cấp tài khoản!'];
            }
            return ['success' => false, 'message' => 'Số điện thoại đã được sử dụng!'];
        }

        $emailCheck = $this->checkEmailExists($data['email'] ?? '');
        if ($emailCheck['exists']) {
            return ['success' => false, 'message' => $emailCheck['message']];
        }

        $customer = new CustomerEntity([
            'username' => $data['username'],
            'password' => $data['password'],
            'full_name' => $data['fullname'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? '',
            'address' => $data['address'] ?? '',
            'account_type' => 'WEB',
            'points' => 0,
            'status' => 1
        ]);

        $result = $custRepo->create($customer);

        if ($result) {
            return ['success' => true, 'message' => 'Đăng ký thành công!'];
        }
        
        $error = mysqli_error($custRepo->con);
        return ['success' => false, 'message' => 'Đăng ký thất bại! Lỗi: ' . $error];
    }
}
?>
