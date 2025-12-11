# 🚀 QUICK REFERENCE - Developer Cheat Sheet

## 📦 TẠO CONTROLLER MỚI

```php
<?php
namespace web\Controllers;

use Config\Controller;
use web\Services\YourService;

class YourController extends Controller {

    private $yourService;

    public function __construct() {
        $this->yourService = new YourService();
    }

    public function index() {
        // Return JSON
        $this->json(['message' => 'Hello']);

        // Or return view
        $this->view('folder/file', ['data' => 'value']);
    }
}
```

## 🔌 ĐĂNG KÝ ROUTES

Trong `public/index.php`:

```php
// GET request
$router->get("/path", "ControllerName@methodName");

// POST request
$router->post("/api/resource", "ControllerName@create");

// Dynamic parameter
$router->get("/users/{id}", "UserController@show");
```

## 💾 TẠO REPOSITORY

```php
<?php
namespace web\Repositories;

use web\Models\Entity\YourEntity;
use Config\Database;
use PDO;

class YourRepository {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function findAll() {
        $sql = "SELECT * FROM your_table";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = new YourEntity($row);
        }
        return $list;
    }

    public function findById($id) {
        $sql = "SELECT * FROM your_table WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new YourEntity($data) : null;
    }
}
```

## 🎯 TẠO SERVICE

```php
<?php
namespace web\Services;

use web\Repositories\YourRepository;

class YourService {

    private $repo;

    public function __construct() {
        $this->repo = new YourRepository();
    }

    public function getAllItems() {
        try {
            return $this->repo->findAll();
        } catch (\Exception $e) {
            // Log error
            error_log($e->getMessage());
            return [];
        }
    }
}
```

## 📊 TẠO ENTITY

```php
<?php
namespace web\Models\Entity;

class YourEntity {

    public $id;
    public $name;
    public $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }
}
```

## 🔐 KIỂM TRA AUTHENTICATION

### Trong Controller:

```php
public function protectedAction() {
    // Kiểm tra đã login chưa
    if (!$this->authService->isLoggedIn()) {
        $this->json(['error' => 'Unauthorized'], 401);
        return;
    }

    // Kiểm tra role
    if (!$this->authService->hasRole('manager')) {
        $this->json(['error' => 'Forbidden'], 403);
        return;
    }

    // Lấy user hiện tại
    $user = $this->authService->getCurrentUser();

    // Your logic here...
}
```

## 🌐 CALL API TỪ FRONTEND

### JavaScript (Vanilla):

```javascript
// POST request
fetch("/COFFEE_PHP/public/index.php?url=/api/resource", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    field1: "value1",
    field2: "value2",
  }),
})
  .then((response) => response.json())
  .then((data) => {
    console.log(data);
  })
  .catch((error) => {
    console.error("Error:", error);
  });

// GET request
fetch("/COFFEE_PHP/public/index.php?url=/api/resource")
  .then((response) => response.json())
  .then((data) => console.log(data));
```

## 🗄️ DATABASE QUERIES

### Select with parameters:

```php
$sql = "SELECT * FROM users WHERE role = ? AND status = ?";
$stmt = $this->conn->prepare($sql);
$stmt->execute(['manager', 'active']);
```

### Insert:

```php
$sql = "INSERT INTO users (username, email) VALUES (?, ?)";
$stmt = $this->conn->prepare($sql);
$stmt->execute([$username, $email]);
$lastId = $this->conn->lastInsertId();
```

### Update:

```php
$sql = "UPDATE users SET email = ? WHERE id = ?";
$stmt = $this->conn->prepare($sql);
$stmt->execute([$email, $id]);
```

### Delete:

```php
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $this->conn->prepare($sql);
$stmt->execute([$id]);
```

## 🔒 PASSWORD OPERATIONS

### Hash password:

```php
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
```

### Verify password:

```php
if (password_verify($plainPassword, $hashedPassword)) {
    // Password correct
}
```

## 📝 SESSION OPERATIONS

### Set session:

```php
$_SESSION['user_id'] = $userId;
$_SESSION['username'] = $username;
```

### Get session:

```php
$userId = $_SESSION['user_id'] ?? null;
```

### Destroy session:

```php
session_destroy();
$_SESSION = [];
```

## 🐛 DEBUG TIPS

### Xem SQL query:

```php
error_log("SQL: $sql");
error_log("Params: " . print_r($params, true));
```

### Log variables:

```php
error_log(print_r($data, true));
```

### Check trong Apache log:

```bash
tail -f D:\xampp\apache\logs\error.log
```

## 🎨 RESPONSE FORMATS

### JSON Success:

```php
$this->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $result
]);
```

### JSON Error:

```php
$this->json([
    'success' => false,
    'message' => 'Error message'
], 400);
```

### Redirect:

```php
header("Location: /path/to/page");
exit;
```

## ⚡ PERFORMANCE TIPS

1. **Dùng indexes trong database**

   ```sql
   CREATE INDEX idx_username ON users(username);
   ```

2. **Tránh N+1 queries** - Dùng JOIN thay vì loop queries

3. **Cache results** nếu data ít thay đổi

4. **Limit results** khi lấy danh sách:
   ```sql
   SELECT * FROM users LIMIT 100
   ```

## 🔥 COMMON MISTAKES

❌ **Không dùng prepared statements**

```php
// SAI - SQL Injection risk!
$sql = "SELECT * FROM users WHERE id = $id";
```

✅ **Dùng prepared statements**

```php
// ĐÚNG
$sql = "SELECT * FROM users WHERE id = ?";
$stmt->execute([$id]);
```

---

❌ **Hardcode credentials**

```php
// SAI
$password = "123456";
```

✅ **Dùng environment variables**

```php
// ĐÚNG
$password = $_ENV['DB_PASS'];
```

---

❌ **Tiết lộ lỗi ra client**

```php
// SAI
echo "Database error: " . $e->getMessage();
```

✅ **Log lỗi, trả generic message**

```php
// ĐÚNG
error_log($e->getMessage());
echo json_encode(['error' => 'Internal server error']);
```

## 📞 CONTACTS

- Issues: GitHub Issues
- Docs: README.md
- API Docs: OPTIMIZATION_REPORT.md
