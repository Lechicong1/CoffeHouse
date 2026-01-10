# 📚 TÀI LIỆU HƯỚNG DẪN: CHỨC NĂNG ĐĂNG KÝ TÀI KHOẢN

> **Dành cho:** Sinh viên, Fresher, Junior Developer  
> **Kiến trúc:** MVC (Model - View - Controller)  
> **Ngôn ngữ:** PHP  
> **Project:** Coffee House Management System

---

## 📋 MỤC LỤC

1. [Tổng quan chức năng](#1-tổng-quan-chức-năng)
2. [Luồng xử lý tổng thể](#2-luồng-xử-lý-tổng-thể)
3. [Thiết kế theo MVC](#3-thiết-kế-theo-mvc)
4. [Luồng chi tiết từng bước](#4-luồng-chi-tiết-từng-bước)
5. [Các rule nghiệp vụ quan trọng](#5-các-rule-nghiệp-vụ-quan-trọng)
6. [Các lỗi sinh viên/junior hay mắc](#6-các-lỗi-sinh-viênjunior-hay-mắc)
7. [Best Practice](#7-best-practice)
8. [Tóm tắt cuối tài liệu](#8-tóm-tắt-cuối-tài-liệu)

---

## 1. TỔNG QUAN CHỨC NĂNG

### 1.1. Đăng ký là gì?

**Đăng ký (Registration/Sign Up)** là quá trình tạo tài khoản mới trong hệ thống. Người dùng cung cấp thông tin cá nhân và thông tin đăng nhập để hệ thống lưu trữ, từ đó có thể sử dụng cho các lần đăng nhập sau.

### 1.2. Khi nào người dùng cần đăng ký?

- **Lần đầu sử dụng hệ thống** - Người dùng mới cần tạo tài khoản
- **Muốn lưu thông tin cá nhân** - Để không phải nhập lại mỗi lần
- **Muốn tích điểm thưởng** - Tài khoản WEB được tích điểm mua hàng
- **Theo dõi lịch sử đơn hàng** - Xem lại các đơn đã đặt

### 1.3. Đăng ký khác gì đăng nhập?

| Tiêu chí | Đăng ký (Register) | Đăng nhập (Login) |
|----------|-------------------|-------------------|
| **Mục đích** | Tạo tài khoản MỚI | Truy cập tài khoản ĐÃ CÓ |
| **Thông tin yêu cầu** | Nhiều (họ tên, SĐT, email, username, password) | Ít (username, password) |
| **Thao tác DB** | INSERT (thêm mới) | SELECT (tìm kiếm) |
| **Tần suất** | Một lần duy nhất | Nhiều lần |

---

## 2. LUỒNG XỬ LÝ TỔNG THỂ

### 2.1. Sơ đồ luồng xử lý

```
┌─────────────┐     ┌────────────┐     ┌─────────────┐     ┌────────────┐     ┌──────────┐
│   NGƯỜI    │     │    VIEW    │     │ CONTROLLER │     │  SERVICE   │     │REPOSITORY│
│   DÙNG     │ ──▶ │  (UI/Form) │ ──▶ │            │ ──▶ │            │ ──▶ │   (DAO)  │
└─────────────┘     └────────────┘     └────────────┘     └────────────┘     └──────────┘
      │                   │                  │                  │                  │
      │   Nhập form       │                  │                  │                  │
      │──────────────────▶│                  │                  │                  │
      │                   │  Validate UI     │                  │                  │
      │                   │◀────────────────▶│                  │                  │
      │                   │                  │                  │                  │
      │                   │  POST request    │                  │                  │
      │                   │─────────────────▶│                  │                  │
      │                   │                  │  Gọi Service     │                  │
      │                   │                  │─────────────────▶│                  │
      │                   │                  │                  │  Validate logic  │
      │                   │                  │                  │◀────────────────▶│
      │                   │                  │                  │                  │
      │                   │                  │                  │  Check trùng     │
      │                   │                  │                  │─────────────────▶│
      │                   │                  │                  │                  │
      │                   │                  │                  │  Lưu DB          │
      │                   │                  │                  │─────────────────▶│
      │                   │                  │                  │                  │
      │   Hiển thị kết quả                                                        │
      │◀──────────────────────────────────────────────────────────────────────────│
```

### 2.2. Giải thích từng bước

| Bước | Tầng | Công việc |
|------|------|-----------|
| 1 | **View** | Hiển thị form đăng ký, validate cơ bản ở client |
| 2 | **Controller** | Nhận request POST, trích xuất dữ liệu, gọi Service |
| 3 | **Service** | Validate nghiệp vụ, kiểm tra trùng lặp, mã hóa password |
| 4 | **Repository** | Thực hiện truy vấn database (INSERT, SELECT) |
| 5 | **Database** | Lưu trữ dữ liệu khách hàng |

### 2.3. Vì sao cần tách tầng như vậy?

- **Dễ bảo trì:** Sửa logic nghiệp vụ chỉ cần sửa ở Service
- **Dễ test:** Có thể test riêng từng tầng
- **Tái sử dụng:** Repository có thể dùng cho nhiều Service khác nhau
- **Phân quyền rõ ràng:** Mỗi tầng có trách nhiệm cụ thể
- **Dễ mở rộng:** Thêm tính năng không ảnh hưởng các tầng khác

---

## 3. THIẾT KẾ THEO MVC

### 3.1. VIEW (Giao diện người dùng)

> **File:** [signup_v.php](file:///d:/XAMPP/htdocs/COFFEE_PHP/web/Views/Auth/signup_v.php) + [signup.js](file:///d:/XAMPP/htdocs/COFFEE_PHP/Public/Js/signup.js)

#### A. Các trường trong form đăng ký

| Field | Tên tiếng Việt | Bắt buộc | Validate |
|-------|---------------|----------|----------|
| `fullname` | Họ và tên | ✅ Có | Ít nhất 2 ký tự |
| `phone` | Số điện thoại | ✅ Có | 10-11 chữ số |
| `address` | Địa chỉ | ❌ Không | Tối đa 255 ký tự |
| `email` | Email | ❌ Không | Định dạng email hợp lệ |
| `username` | Tên đăng nhập | ✅ Có | Ít nhất 3 ký tự |
| `password` | Mật khẩu | ✅ Có | Ít nhất 6 ký tự |
| `confirmPassword` | Xác nhận mật khẩu | ✅ Có | Phải trùng password |

#### B. Validate ở UI (Client-side)

```javascript
// Ví dụ validate ở signup.js
// Validate họ tên
if (fullname.length < 2) {
    e.preventDefault();
    alert("Họ và tên phải có ít nhất 2 ký tự!");
    return false;
}

// Validate password match
if (password !== confirmPassword) {
    e.preventDefault();
    alert("Mật khẩu xác nhận không khớp!");
    return false;
}
```

#### C. Những lỗi UI cần chặn TRƯỚC khi gửi server

- ❌ Bỏ trống trường bắt buộc
- ❌ Họ tên quá ngắn (< 2 ký tự)
- ❌ Username quá ngắn (< 3 ký tự)
- ❌ Password quá ngắn (< 6 ký tự)
- ❌ Confirm password không khớp
- ❌ Số điện thoại không đúng format (10-11 số)

> [!NOTE]
> Validate ở UI giúp trải nghiệm người dùng tốt hơn (response nhanh), nhưng **KHÔNG được bỏ qua validate ở Server** vì người dùng có thể bypass JavaScript.

---

### 3.2. CONTROLLER (Điều khiển)

> **File:** [AuthController.php](file:///d:/XAMPP/htdocs/COFFEE_PHP/web/Controllers/AuthController.php)

#### A. Controller nhận request gì?

```php
// Method register() nhận POST request từ form
public function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Trích xuất dữ liệu từ $_POST
        $data = [
            'fullname' => isset($_POST['fullname']) ? trim($_POST['fullname']) : '',
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
            'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
            'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
            'username' => isset($_POST['username']) ? trim($_POST['username']) : '',
            'password' => isset($_POST['password']) ? trim($_POST['password']) : '',
            'confirmPassword' => isset($_POST['confirmPassword']) ? trim($_POST['confirmPassword']) : ''
        ];
        // ...
    }
}
```

#### B. Controller ĐƯỢC làm gì?

- ✅ Nhận request từ client
- ✅ Trích xuất và trim dữ liệu đầu vào
- ✅ Gọi Service để xử lý nghiệp vụ
- ✅ Trả kết quả cho View (redirect, alert, JSON response)

#### C. Controller KHÔNG ĐƯỢC làm gì?

- ❌ Viết logic nghiệp vụ (validate phức tạp, check trùng)
- ❌ Truy vấn database trực tiếp
- ❌ Mã hóa password
- ❌ Xử lý business rule

#### D. Cách Controller gọi Service

```php
// Lấy instance của AuthService
$authService = $this->service('AuthService');

// Gọi method đăng ký
$result = $authService->registerCustomer($data);

// Xử lý kết quả trả về
if ($result['success']) {
    // Thành công -> redirect đến login
    echo "<script>
        alert('Đăng ký thành công! Vui lòng đăng nhập.');
        window.location.href = '/COFFEE_PHP/Auth/showLogin';
    </script>";
} else {
    // Thất bại -> hiển thị lỗi
    echo "<script>
        alert('{$result['message']}');
        window.history.back();
    </script>";
}
```

---

### 3.3. SERVICE (Xử lý nghiệp vụ)

> **File:** [AuthService.php](file:///d:/XAMPP/htdocs/COFFEE_PHP/web/Services/AuthService.php)

#### A. Service thực hiện những rule nghiệp vụ nào?

**1. Validate dữ liệu đầu vào:**

```php
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
        return ['valid' => false, 'message' => 'Họ và tên phải có ít nhất 2 ký tự!'];
    }

    // Validate username
    if (strlen($data['username']) < 3) {
        return ['valid' => false, 'message' => 'Tên đăng nhập phải có ít nhất 3 ký tự!'];
    }

    // Validate phone format
    if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
        return ['valid' => false, 'message' => 'Số điện thoại phải có 10-11 chữ số!'];
    }

    // Validate password length
    if (strlen($data['password']) < 6) {
        return ['valid' => false, 'message' => 'Mật khẩu phải có ít nhất 6 ký tự!'];
    }

    // Validate confirm password
    if ($data['password'] !== $data['confirmPassword']) {
        return ['valid' => false, 'message' => 'Mật khẩu xác nhận không khớp!'];
    }

    // Validate email format (nếu có)
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Email không hợp lệ!'];
    }

    return ['valid' => true, 'message' => ''];
}
```

**2. Kiểm tra trùng username/phone/email:**

```php
// Check username đã tồn tại
public function checkUsernameExists($username) {
    $custRepo = $this->repository('CustomerRepository');
    $empRepo = $this->repository('EmployeeRepository');

    if ($custRepo->findByUsername($username) || $empRepo->findByUsername($username)) {
        return ['exists' => true, 'message' => 'Tên đăng nhập đã tồn tại!'];
    }
    return ['exists' => false, 'message' => ''];
}

// Check phone đã tồn tại
public function checkPhoneExists($phone) {
    $custRepo = $this->repository('CustomerRepository');
    if ($custRepo->findByPhone($phone)) {
        return ['exists' => true, 'message' => 'Số điện thoại đã được sử dụng!'];
    }
    return ['exists' => false, 'message' => ''];
}
```

**3. Logic đăng ký chính:**

```php
public function registerCustomer($data) {
    // Bước 1: Validate dữ liệu
    $validation = $this->validateRegistration($data);
    if (!$validation['valid']) {
        return ['success' => false, 'message' => $validation['message']];
    }

    // Bước 2: Kiểm tra username trùng
    $usernameCheck = $this->checkUsernameExists($data['username']);
    if ($usernameCheck['exists']) {
        return ['success' => false, 'message' => $usernameCheck['message']];
    }

    // Bước 3: Kiểm tra phone trùng (có logic upgrade từ GUEST_POS)
    // ...

    // Bước 4: Kiểm tra email trùng
    // ...

    // Bước 5: Tạo Entity và lưu
    $customer = new CustomerEntity([
        'username' => $data['username'],
        'password' => $data['password'], // Nên mã hóa!
        'full_name' => $data['fullname'],
        'phone' => $data['phone'],
        'email' => $data['email'] ?? '',
        'address' => $data['address'] ?? '',
        'account_type' => 'WEB',
        'points' => 0,
        'status' => 1
    ]);

    $result = $custRepo->create($customer);
    return $result 
        ? ['success' => true, 'message' => 'Đăng ký thành công!']
        : ['success' => false, 'message' => 'Đăng ký thất bại!'];
}
```

#### B. Khi nào Service cho phép tạo tài khoản?

Tài khoản được tạo khi thỏa mãn TẤT CẢ điều kiện:

- ✅ Tất cả trường bắt buộc đã nhập
- ✅ Dữ liệu đúng format
- ✅ Username chưa tồn tại
- ✅ Phone chưa được dùng (hoặc là GUEST_POS có thể upgrade)
- ✅ Email chưa được dùng (nếu có)
- ✅ Confirm password khớp với password

---

### 3.4. REPOSITORY / DAO (Truy cập dữ liệu)

> **File:** [CustomerRepository.php](file:///d:/XAMPP/htdocs/COFFEE_PHP/web/Repositories/CustomerRepository.php)

#### A. Repository CHỈ làm gì?

- ✅ Thực hiện các truy vấn SQL (CRUD)
- ✅ Chuyển đổi dữ liệu DB ↔ Entity
- ✅ Sử dụng Prepared Statement để tránh SQL Injection

#### B. Repository KHÔNG được làm gì?

- ❌ Chứa logic nghiệp vụ
- ❌ Validate dữ liệu
- ❌ Mã hóa password
- ❌ Quyết định cho phép/từ chối đăng ký

#### C. Các method cần có cho chức năng đăng ký

**1. findByUsername($username):**

```php
public function findByUsername($username) {
    $sql = "SELECT * FROM customers WHERE username = ?";
    $stmt = mysqli_prepare($this->con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    return $data ? new CustomerEntity($data) : null;
}
```

**2. findByPhone($phone):**

```php
public function findByPhone($phone, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT * FROM customers WHERE phone = ? AND id != ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $phone, $excludeId);
    } else {
        $sql = "SELECT * FROM customers WHERE phone = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $phone);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    return $data ? new CustomerEntity($data) : null;
}
```

**3. findByEmail($email):**

```php
public function findByEmail($email, $excludeId = null) {
    // Tương tự findByPhone
}
```

**4. create($customer):**

```php
public function create($customer) {
    $sql = "INSERT INTO customers 
            (username, password, full_name, phone, email, address, account_type, points, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($this->con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssii",
        $customer->username,
        $customer->password,
        $customer->full_name,
        $customer->phone,
        $customer->email,
        $customer->address,
        $customer->account_type,
        $customer->points,
        $customer->status
    );

    return mysqli_stmt_execute($stmt);
}
```

---

### 3.5. ENTITY (Đối tượng dữ liệu)

> **File:** [CustomerEntity.php](file:///d:/XAMPP/htdocs/COFFEE_PHP/web/Entity/CustomerEntity.php)

Entity là **Data Transfer Object (DTO)** - đối tượng thuần túy chứa dữ liệu:

```php
class CustomerEntity {
    public $id;
    public $username;
    public $password;
    public $full_name;
    public $phone;
    public $email;
    public $address;
    public $account_type;  // 'WEB' hoặc 'GUEST_POS'
    public $points;
    public $status;

    public function __construct($data = []) {
        // Khởi tạo từ array
    }

    public function toArray() {
        // Chuyển thành array
    }
}
```

---

### 3.6. DATABASE (Cơ sở dữ liệu)

#### A. Bảng `customers`

| Cột | Kiểu dữ liệu | Mô tả | Ràng buộc |
|-----|-------------|-------|-----------|
| `id` | INT | Khóa chính | PRIMARY KEY, AUTO_INCREMENT |
| `username` | VARCHAR(50) | Tên đăng nhập | UNIQUE, NOT NULL |
| `password` | VARCHAR(255) | Mật khẩu (nên hash) | NOT NULL |
| `full_name` | VARCHAR(100) | Họ và tên | NOT NULL |
| `phone` | VARCHAR(15) | Số điện thoại | UNIQUE, NOT NULL |
| `email` | VARCHAR(100) | Email | UNIQUE (nullable) |
| `address` | VARCHAR(255) | Địa chỉ | Nullable |
| `account_type` | ENUM | Loại tài khoản | 'WEB', 'GUEST_POS' |
| `points` | INT | Điểm tích lũy | DEFAULT 0 |
| `status` | TINYINT | Trạng thái | 1=active, 0=inactive |

#### B. Các Index/Constraint quan trọng

```sql
-- Primary Key
PRIMARY KEY (id)

-- Unique constraints (ngăn trùng lặp)
UNIQUE KEY uk_username (username)
UNIQUE KEY uk_phone (phone)
UNIQUE KEY uk_email (email)

-- Index để tìm kiếm nhanh
INDEX idx_phone (phone)
INDEX idx_username (username)
```

---

## 4. LUỒNG CHI TIẾT TỪNG BƯỚC

### Bước 1: User nhập form

**Làm gì:**
- User truy cập URL `/COFFEE_PHP/Auth/showSignup`
- Controller gọi `include './web/Views/Auth/signup_v.php'`
- Browser render form HTML

**Ví dụ:**
```
User điền:
- Họ tên: Nguyễn Văn A
- SĐT: 0901234567
- Email: nguyenvana@gmail.com
- Username: nguyenvana
- Password: 123456
- Confirm: 123456
```

---

### Bước 2: Validate ở Client (JavaScript)

**Làm gì:**
- File `signup.js` chặn submit nếu dữ liệu không hợp lệ
- Hiển thị alert thông báo lỗi ngay lập tức

**Ví dụ:**
```javascript
// Nếu username < 3 ký tự
alert("Tên đăng nhập phải có ít nhất 3 ký tự!");
return false; // Không gửi form
```

---

### Bước 3: Controller nhận request

**Làm gì:**
- Nhận POST request tại `/COFFEE_PHP/Auth/register`
- Trích xuất dữ liệu từ `$_POST`
- Trim để loại bỏ khoảng trắng thừa

**Ví dụ:**
```php
$data = [
    'fullname' => trim($_POST['fullname']),  // "Nguyễn Văn A"
    'phone' => trim($_POST['phone']),        // "0901234567"
    // ...
];
```

---

### Bước 4: Service validate nghiệp vụ

**Làm gì:**
- Kiểm tra trường bắt buộc
- Validate format (regex phone, email)
- Kiểm tra password match

**Ví dụ:**
```php
// Kiểm tra phone format
if (!preg_match('/^[0-9]{10,11}$/', "0901234567")) {
    return ['valid' => false, 'message' => 'SĐT không hợp lệ'];
}
// ✅ "0901234567" -> PASS
```

---

### Bước 5: Service kiểm tra trùng lặp

**Làm gì:**
- Gọi Repository tìm username trong DB
- Gọi Repository tìm phone trong DB
- Gọi Repository tìm email trong DB

**Ví dụ:**
```php
$existing = $custRepo->findByUsername("nguyenvana");
// Nếu $existing != null -> Username đã tồn tại -> REJECT
// Nếu $existing == null -> Chưa có -> PASS
```

---

### Bước 6: Repository lưu vào Database

**Làm gì:**
- Tạo CustomerEntity từ dữ liệu
- Thực thi INSERT query với Prepared Statement
- Trả về true/false

**Ví dụ:**
```sql
INSERT INTO customers 
(username, password, full_name, phone, email, address, account_type, points, status) 
VALUES ('nguyenvana', '123456', 'Nguyễn Văn A', '0901234567', 'nguyenvana@gmail.com', '', 'WEB', 0, 1)
```

---

### Bước 7: Trả kết quả về UI

**Làm gì:**
- Service trả `['success' => true/false, 'message' => '...']`
- Controller hiển thị thông báo bằng JavaScript alert
- Redirect user về trang login hoặc quay lại form

**Ví dụ thành công:**
```javascript
alert('Đăng ký thành công! Vui lòng đăng nhập.');
window.location.href = '/COFFEE_PHP/Auth/showLogin';
```

**Ví dụ thất bại:**
```javascript
alert('Tên đăng nhập đã tồn tại!');
window.history.back();
```

---

## 5. CÁC RULE NGHIỆP VỤ QUAN TRỌNG

### 5.1. Không cho trùng username/email/phone

```php
// Service kiểm tra trước khi cho đăng ký
if ($custRepo->findByUsername($username)) {
    return ['success' => false, 'message' => 'Username đã tồn tại!'];
}
```

### 5.2. Password phải được mã hóa

> [!WARNING]
> **Trong project hiện tại, password đang lưu plain-text. Đây là lỗ hổng bảo mật nghiêm trọng!**

**Cách sửa đúng:**
```php
// Khi đăng ký - MÃ HÓA password
$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

// Khi đăng nhập - VERIFY password
if (password_verify($inputPassword, $storedHash)) {
    // Đăng nhập thành công
}
```

### 5.3. Không log password

```php
// ❌ SAI - TUYỆT ĐỐI KHÔNG LÀM
error_log("User đăng ký với password: " . $data['password']);

// ✅ ĐÚNG - Chỉ log thông tin an toàn
error_log("User đăng ký: " . $data['username']);
```

### 5.4. Không trả lỗi kỹ thuật ra UI

```php
// ❌ SAI - Lộ thông tin hệ thống
return ['success' => false, 'message' => 'MySQL Error: Duplicate entry for key username'];

// ✅ ĐÚNG - Thông báo thân thiện
return ['success' => false, 'message' => 'Tên đăng nhập đã tồn tại!'];
```

---

## 6. CÁC LỖI SINH VIÊN/JUNIOR HAY MẮC

### 6.1. ❌ Viết SQL trực tiếp trong Controller

**Sai:**
```php
// Controller.php
public function register() {
    $sql = "INSERT INTO customers VALUES (...)"; // ❌ KHÔNG ĐƯỢC!
    mysqli_query($conn, $sql);
}
```

**Đúng:**
```php
// Controller.php
public function register() {
    $authService = $this->service('AuthService');
    $result = $authService->registerCustomer($data); // ✅ Gọi Service
}
```

---

### 6.2. ❌ Chỉ validate ở UI, bỏ qua Server

**Sai:**
```javascript
// Chỉ validate ở JS là đủ? KHÔNG!
if (password.length < 6) {
    alert("Password quá ngắn");
    return false;
}
// User có thể tắt JavaScript hoặc gửi request trực tiếp!
```

**Đúng:**
- ✅ Validate ở JavaScript (trải nghiệm người dùng)
- ✅ Validate lại ở Service (bảo mật)

---

### 6.3. ❌ Không mã hóa mật khẩu

**Sai:**
```php
$customer->password = $data['password']; // Lưu plain-text ❌
```

**Đúng:**
```php
$customer->password = password_hash($data['password'], PASSWORD_DEFAULT); // ✅
```

---

### 6.4. ❌ Bắt Exception nhưng không xử lý

**Sai:**
```php
try {
    $custRepo->create($customer);
} catch (Exception $e) {
    // Để trống hoặc chỉ echo ra browser
    echo $e->getMessage(); // ❌ Lộ thông tin hệ thống
}
```

**Đúng:**
```php
try {
    $custRepo->create($customer);
} catch (Exception $e) {
    // Log lỗi để debug
    error_log("Register error: " . $e->getMessage());
    
    // Trả về thông báo thân thiện
    return ['success' => false, 'message' => 'Có lỗi xảy ra, vui lòng thử lại!'];
}
```

---

### 6.5. ❌ Để logic nghiệp vụ trong Repository

**Sai:**
```php
// CustomerRepository.php
public function create($customer) {
    // ❌ Logic nghiệp vụ trong Repository
    if ($this->findByUsername($customer->username)) {
        return ['error' => 'Username đã tồn tại'];
    }
    
    // Insert...
}
```

**Đúng:**
```php
// AuthService.php - Logic nằm ở Service
public function registerCustomer($data) {
    if ($this->checkUsernameExists($data['username'])['exists']) {
        return ['success' => false, 'message' => 'Username đã tồn tại'];
    }
    
    // Gọi Repository để insert
    $custRepo->create($customer);
}
```

---

### 6.6. ❌ Không dùng Prepared Statement

**Sai (SQL Injection vulnerability):**
```php
$sql = "SELECT * FROM customers WHERE username = '$username'";
// Nếu $username = "'; DROP TABLE customers; --" thì...
```

**Đúng:**
```php
$sql = "SELECT * FROM customers WHERE username = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
```

---

## 7. BEST PRACTICE

### 7.1. Cấu trúc thư mục chuẩn

```
web/
├── Controllers/
│   └── AuthController.php       # Điều khiển request
├── Services/
│   └── AuthService.php          # Logic nghiệp vụ
├── Repositories/
│   └── CustomerRepository.php   # Truy cập database
├── Entity/
│   └── CustomerEntity.php       # Data Transfer Object
└── Views/
    └── Auth/
        └── signup_v.php         # Giao diện form

Public/
├── Css/
│   └── signup.css               # Style cho form
└── Js/
    └── signup.js                # Validate client-side
```

### 7.2. Nguyên tắc "Single Responsibility"

Mỗi class/method chỉ làm MỘT việc:

| Class/Method | Trách nhiệm duy nhất |
|-------------|---------------------|
| `AuthController::register()` | Nhận request, gọi service, trả response |
| `AuthService::registerCustomer()` | Xử lý logic đăng ký |
| `AuthService::validateRegistration()` | Validate dữ liệu đầu vào |
| `AuthService::checkUsernameExists()` | Kiểm tra username trùng |
| `CustomerRepository::create()` | INSERT vào database |
| `CustomerRepository::findByUsername()` | SELECT theo username |

### 7.3. Return format nhất quán

```php
// Luôn trả về cùng format
return [
    'success' => true/false,
    'message' => 'Thông báo cho user'
];
```

### 7.4. Validate hai lớp

```
┌─────────────────┐      ┌─────────────────┐
│   CLIENT-SIDE   │      │   SERVER-SIDE   │
│   (JavaScript)  │ ───▶ │    (Service)    │
│                 │      │                 │
│ ✓ UX nhanh      │      │ ✓ Bảo mật       │
│ ✓ Giảm request  │      │ ✓ Không bypass  │
│ ✗ Có thể bypass │      │ ✓ Tin cậy       │
└─────────────────┘      └─────────────────┘
```

### 7.5. Xử lý lỗi gracefully

```php
// Luôn có try-catch và trả về thông báo thân thiện
try {
    // Thao tác có thể lỗi
} catch (Exception $e) {
    error_log($e->getMessage()); // Log để debug
    return ['success' => false, 'message' => 'Có lỗi xảy ra!']; // Thông báo user
}
```

---

## 8. TÓM TẮT CUỐI TÀI LIỆU

### 🎯 5 ĐIỀU QUAN TRỌNG NHẤT CẦN NHỚ

> [!IMPORTANT]
> **1. TÁCH BIỆT TRÁCH NHIỆM THEO TẦNG**
> - **Controller:** Chỉ nhận request và gọi Service
> - **Service:** Chứa TẤT CẢ logic nghiệp vụ
> - **Repository:** Chỉ thao tác database
> - **View:** Chỉ hiển thị giao diện

> [!IMPORTANT]
> **2. VALIDATE Ở CẢ HAI TẦNG**
> - Client-side (JavaScript): Cải thiện UX
> - Server-side (Service): Đảm bảo bảo mật

> [!IMPORTANT]
> **3. KIỂM TRA TRÙNG LẶP TRƯỚC KHI INSERT**
> - Username phải unique
> - Phone phải unique
> - Email phải unique (nếu có)

> [!IMPORTANT]
> **4. MÃ HÓA PASSWORD**
> - Sử dụng `password_hash()` khi lưu
> - Sử dụng `password_verify()` khi kiểm tra
> - KHÔNG BAO GIỜ lưu plain-text

> [!IMPORTANT]
> **5. SỬ DỤNG PREPARED STATEMENT**
> - Tránh SQL Injection
> - Luôn dùng `?` làm placeholder
> - Bind parameter đúng kiểu

---

### 📊 Bảng tóm tắt luồng xử lý

| # | Tầng | File | Method | Công việc |
|---|------|------|--------|-----------|
| 1 | View | `signup_v.php` | - | Hiển thị form |
| 2 | View | `signup.js` | `submit` | Validate client |
| 3 | Controller | `AuthController.php` | `register()` | Nhận POST |
| 4 | Service | `AuthService.php` | `registerCustomer()` | Logic đăng ký |
| 5 | Service | `AuthService.php` | `validateRegistration()` | Validate server |
| 6 | Service | `AuthService.php` | `checkUsernameExists()` | Check trùng |
| 7 | Repository | `CustomerRepository.php` | `findByUsername()` | Query SELECT |
| 8 | Repository | `CustomerRepository.php` | `create()` | Query INSERT |
| 9 | Controller | `AuthController.php` | `register()` | Trả kết quả |

---

> **Tác giả:** Coffee House Development Team  
> **Ngày tạo:** 2025-01-10  
> **Phiên bản:** 1.0
