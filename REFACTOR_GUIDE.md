# 📚 HƯỚNG DẪN REFACTOR - MVC PATTERN (CẬP NHẬT)

## 🎯 Cấu trúc mới (Master Layout Pattern)

### **Luồng hoạt động:**
```
Mỗi Controller riêng -> MasterLayout (View Cha) -> View Con (Pages)
```

### **❌ CÁCH CŨ (ĐÃ XÓA):**
```
AdminController -> View Con (trực tiếp)
```

### **✅ CÁCH MỚI (HIỆN TẠI):**
```
DashboardController -> MasterLayout -> Dashboard_v.php
EmployeeController  -> MasterLayout -> Employees_v.php
ProductController   -> MasterLayout -> Products_v.php
OrderController     -> MasterLayout -> Orders_v.php
CustomerController  -> MasterLayout -> Customers_v.php
```

**💡 Ý TƯỞNG:** Mỗi trang có 1 Controller riêng, tất cả đều gọi chung MasterLayout (chứa sidebar, header, modal), sau đó MasterLayout include view con tương ứng.

---

## 📂 Cấu trúc thư mục

```
web/
├── Controllers/
│   ├── DashboardController.php      # ✅ Trang Tổng quan
│   ├── EmployeeController.php       # ✅ Quản lý Nhân viên
│   ├── ProductController.php        # ✅ Quản lý Sản phẩm
│   ├── OrderController.php          # ✅ Quản lý Đơn hàng
│   ├── CustomerController.php       # ✅ Quản lý Khách hàng
│   ├── RevenueController.php        # 🔲 TODO: Thống kê Doanh thu
│   └── SettingsController.php       # 🔲 TODO: Cài đặt
│
├── Views/
│   └── AdminDashBoard/
│       ├── MasterLayout.php         # VIEW CHA (chứa sidebar, header, modal)
│       │
│       ├── Pages/                   # VIEW CON (các trang cụ thể)
│       │   ├── Dashboard_v.php      # ✅ Trang tổng quan
│       │   ├── Employees_v.php      # ✅ Trang quản lý nhân viên
│       │   ├── Products_v.php       # 🔲 TODO
│       │   ├── Orders_v.php         # 🔲 TODO
│       │   └── Customers_v.php      # 🔲 TODO
│       │
│       └── partials/                # COMPONENTS tái sử dụng
│           ├── sidebar.php          # Menu bên trái (đã cập nhật URL)
│           ├── header.php           # Thanh header
│           └── modal.php            # Modal popup
│
└── Services/
    └── EmployeeService.php          # Business Logic
```

---

## 🔧 Cách hoạt động

### **1. Controller gọi MasterLayout**

**Ví dụ:** `EmployeeController.php`

```php
function Get_data() {
    // Lấy dữ liệu từ Service/Repository
    $employees = $this->employeeService->getAllEmployees();
    $stats = $this->employeeService->getStatistics();
    
    // Gọi MasterLayout (view cha) và truyền page (view con)
    $this->view('AdminDashBoard/MasterLayout', [
        'page' => 'Employees_v',      // Tên view con
        'section' => 'employees',      // Section cho sidebar active
        'employees' => $employees,     // Dữ liệu
        'stats' => $stats
    ]);
}
```

**Ví dụ:** `ProductController.php`

```php
function Get_data() {
    $products = $this->getAllProducts();
    $categories = $this->getCategories();
    
    // Gọi MasterLayout với view con Products_v
    $this->view('AdminDashBoard/MasterLayout', [
        'page' => 'Products_v',
        'section' => 'products',
        'products' => $products,
        'categories' => $categories
    ]);
}
```

### **2. MasterLayout nhận dữ liệu và include View Con**

**File:** `MasterLayout.php` (KHÔNG THAY ĐỔI)

```php
<div class="admin-container">
    <!-- SIDEBAR - Tái sử dụng -->
    <aside>
        <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
    </aside>

    <!-- MAIN CONTENT -->
    <main>
        <!-- HEADER - Tái sử dụng -->
        <?php include_once __DIR__ . '/partials/header.php'; ?>

        <!-- DYNAMIC CONTENT - View con được include vào đây -->
        <div class="content-wrapper">
            <?php 
                // Include view con dựa vào tham số 'page'
                if (isset($data['page'])) {
                    $pageFile = __DIR__ . '/Pages/' . $data['page'] . '.php';
                    include_once $pageFile;
                }
            ?>
        </div>
    </main>
</div>
```

### **3. Sidebar đã được cập nhật với URL mới**

```php
$menuItems = [
    ['icon' => '📊', 'text' => 'Tổng quan', 'url' => '?url=Dashboard/index'],
    ['icon' => '🛍️', 'text' => 'Đơn hàng', 'url' => '?url=Order/Get_data'],
    ['icon' => '☕', 'text' => 'Sản phẩm', 'url' => '?url=Product/Get_data'],
    ['icon' => '👥', 'text' => 'Khách hàng', 'url' => '?url=Customer/Get_data'],
    ['icon' => '👔', 'text' => 'Nhân viên', 'url' => '?url=Employee/Get_data'],
];
```

---

## 🚀 Cách sử dụng

### **Truy cập các trang:**

| Trang | URL | Controller | View Con |
|-------|-----|------------|----------|
| 📊 Dashboard | `?url=Dashboard/index` | DashboardController | Dashboard_v.php |
| 👔 Nhân viên | `?url=Employee/Get_data` | EmployeeController | Employees_v.php |
| ☕ Sản phẩm | `?url=Product/Get_data` | ProductController | Products_v.php |
| 🛍️ Đơn hàng | `?url=Order/Get_data` | OrderController | Orders_v.php |
| 👥 Khách hàng | `?url=Customer/Get_data` | CustomerController | Customers_v.php |

---

## 🎨 Ưu điểm của cấu trúc mới

✅ **Tách biệt rõ ràng:** Mỗi Controller chỉ quản lý 1 nghiệp vụ  
✅ **Không còn AdminController "GOD OBJECT":** Tránh 1 Controller quá lớn  
✅ **Dễ bảo trì:** Sửa logic Nhân viên chỉ cần mở EmployeeController  
✅ **Tái sử dụng MasterLayout:** Sidebar, Header, Modal dùng chung  
✅ **Dễ mở rộng:** Thêm trang mới = Tạo Controller mới + View con mới  
✅ **Theo chuẩn Single Responsibility Principle (SRP)**

---

## 📝 Ví dụ: Thêm trang Revenue (Doanh thu)

### **Bước 1: Tạo Controller**
File: `web/Controllers/RevenueController.php`

```php
<?php
require_once __DIR__ . '/../../Config/Controller.php';

class RevenueController extends Controller {
    
    public function index() {
        // Lấy dữ liệu thống kê
        $revenue = $this->getRevenueData();
        
        // Gọi MasterLayout
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Revenue_v',
            'section' => 'revenue',
            'revenue' => $revenue
        ]);
    }
    
    private function getRevenueData() {
        // Logic lấy dữ liệu doanh thu
        return [];
    }
}
```

### **Bước 2: Tạo View Con**
File: `web/Views/AdminDashBoard/Pages/Revenue_v.php`

```php
<?php
$revenue = $data['revenue'] ?? [];
?>

<section id="revenue">
    <h2>📈 Thống kê Doanh thu</h2>
    <!-- Nội dung trang doanh thu -->
</section>
```

### **Bước 3: Truy cập**
URL: `?url=Revenue/index`

---

## 🔄 CRUD Operations

### **Employee (Nhân viên)**
- **Xem:** `?url=Employee/Get_data` (GET)
- **Thêm:** `?url=Employee/ins` (POST - button name: `btnThem`)
- **Sửa:** `?url=Employee/upd` (POST - button name: `btnCapnhat`)
- **Xóa:** `?url=Employee/del` (POST - button name: `btnXoa`)

### **Product (Sản phẩm)**
- **Xem:** `?url=Product/Get_data` (GET)
- **Thêm:** `?url=Product/ins` (POST)
- **Sửa:** `?url=Product/upd` (POST)
- **Xóa:** `?url=Product/del` (POST)

### **Order (Đơn hàng)**
- **Xem:** `?url=Order/Get_data` (GET)
- **Cập nhật trạng thái:** `?url=Order/updateStatus` (POST)
- **Xóa:** `?url=Order/del` (POST)

### **Customer (Khách hàng)**
- **Xem:** `?url=Customer/Get_data` (GET)
- **Xóa:** `?url=Customer/del` (POST)

---

## 📋 Danh sách Controllers đã tạo

| Controller | File | Trạng thái | View Con |
|------------|------|-----------|----------|
| Dashboard | DashboardController.php | ✅ Hoàn thành | Dashboard_v.php |
| Employee | EmployeeController.php | ✅ Hoàn thành | Employees_v.php |
| Product | ProductController.php | ✅ Hoàn thành | 🔲 Cần tạo view |
| Order | OrderController.php | ✅ Hoàn thành | 🔲 Cần tạo view |
| Customer | CustomerController.php | ✅ Hoàn thành | 🔲 Cần tạo view |
| Revenue | - | 🔲 TODO | 🔲 TODO |
| Settings | - | 🔲 TODO | 🔲 TODO |

---

## 🛠️ TODO - Các view con cần tạo

- [ ] **Products_v.php** - View quản lý sản phẩm
- [ ] **Orders_v.php** - View quản lý đơn hàng
- [ ] **Customers_v.php** - View quản lý khách hàng
- [ ] **Revenue_v.php** - View thống kê doanh thu
- [ ] **Settings_v.php** - View cài đặt hệ thống

---

## 🎯 Tóm tắt thay đổi

### **ĐÃ XÓA:**
- ❌ `AdminController.php` (Controller "God Object" quá lớn)

### **ĐÃ TẠO:**
- ✅ `DashboardController.php` - Quản lý trang Tổng quan
- ✅ `ProductController.php` - Quản lý Sản phẩm (CRUD đầy đủ)
- ✅ `OrderController.php` - Quản lý Đơn hàng (CRUD đầy đủ)
- ✅ `CustomerController.php` - Quản lý Khách hàng (View + Delete)

### **ĐÃ CẬP NHẬT:**
- ✅ `sidebar.php` - Đổi URL từ `?section=xxx` sang `?url=Controller/method`
- ✅ `EmployeeController.php` - Gọi MasterLayout thay vì view trực tiếp

---

## 💡 Lưu ý quan trọng

1. **Mỗi Controller chỉ quản lý 1 nghiệp vụ** (Single Responsibility)
2. **Tất cả Controller đều gọi MasterLayout** (Tái sử dụng layout)
3. **View con chỉ hiển thị, không xử lý logic** (Separation of Concerns)
4. **URL theo format:** `?url=ControllerName/MethodName`
5. **POST actions redirect về GET page** (PRG Pattern)

---

## 📞 Hỗ trợ

Tham khảo code mẫu:
- **Controller:** `EmployeeController.php`, `ProductController.php`
- **View Con:** `Employees_v.php`, `Dashboard_v.php`
- **Master Layout:** `MasterLayout.php`
