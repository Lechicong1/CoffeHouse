<?php
/**
 * filepath: /home/cong/Documents/COFFEE_PHP/web/Views/AdminDashBoard/admin.php
 * Admin Dashboard - Layout chính với khả năng load dynamic sections
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập admin (uncomment khi đã có hệ thống auth)
// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role'] !== 'admin') {
//     header('Location: /web/Views/Auth/Login/login.html');
//     exit;
// }

// Lấy section hiện tại từ URL (mặc định là dashboard)
$currentSection = $_GET['section'] ?? 'dashboard';

// Danh sách các section được phép truy cập
$allowedSections = [
    'dashboard',
    'orders',
    'products',
    'customers',
    'employees',
    'revenue',
    'settings'
];

// Validate section
if (!in_array($currentSection, $allowedSections)) {
    $currentSection = 'dashboard';
}

// ============================================
// XỬ LÝ POST/DELETE CHO EMPLOYEES (TRƯỚC KHI OUTPUT HTML)
// ============================================
if ($currentSection === 'employees') {
    require_once __DIR__ . '/../../../Config/Database.php';
    require_once __DIR__ . '/../../Services/EmployeeService.php';
    
    // XỬ LÝ POST (THÊM/SỬA)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $employeeService = new EmployeeService();
        
        try {
            // Thêm nhân viên mới
            if (isset($_POST['btnThem'])) {
                error_log("=== POST THEM NHAN VIEN in admin.php ===");
                error_log("POST data: " . print_r($_POST, true));
                
                // Kiểm tra username tồn tại
                if ($employeeService->checkUsernameExists($_POST['txtUsername'])) {
                    $errorMessage = "❌ Username đã tồn tại!";
                    header("Location: ?section=employees&msg=" . urlencode($errorMessage));
                    exit;
                }
                
                $data = [
                    'username' => $_POST['txtUsername'],
                    'password' => $_POST['txtPassword'],
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (int)$_POST['txtLuong']
                ];
                
                $result = $employeeService->createEmployee($data);
                if ($result['success']) {
                    $successMessage = "✅ Thêm nhân viên thành công!";
                } else {
                    $errorMessage = "❌ " . $result['message'];
                }
                
                header("Location: ?section=employees&msg=" . urlencode($successMessage ?? $errorMessage));
                exit;
            }
            // Cập nhật nhân viên
            elseif (isset($_POST['btnCapnhat'])) {
                $id = (int)$_POST['txtId'];
                
                $data = [
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (int)$_POST['txtLuong']
                ];
                
                // Nếu có password mới
                if (!empty($_POST['txtPassword'])) {
                    $data['password'] = $_POST['txtPassword'];
                }
                
                $result = $employeeService->updateEmployee($id, $data);
                if ($result['success']) {
                    $successMessage = "✅ Cập nhật thành công!";
                } else {
                    $errorMessage = "❌ " . $result['message'];
                }
                
                header("Location: ?section=employees&msg=" . urlencode($successMessage ?? $errorMessage));
                exit;
            }
            
        } catch (Exception $e) {
            $errorMessage = "⚠️ Lỗi: " . $e->getMessage();
            error_log("=== EXCEPTION in admin.php employees POST ===");
            error_log("Error: " . $e->getMessage());
            header("Location: ?section=employees&msg=" . urlencode($errorMessage));
            exit;
        }
    }
    
    // XỬ LÝ DELETE
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        error_log("=== DELETE EMPLOYEE REQUEST ===");
        error_log("ID to delete: " . $_GET['id']);
        
        $employeeService = new EmployeeService();
        
        try {
            $result = $employeeService->deleteEmployee((int)$_GET['id']);
            error_log("Delete result: " . print_r($result, true));
            
            if ($result['success']) {
                $successMessage = "✅ Xóa nhân viên thành công!";
            } else {
                $errorMessage = "❌ " . $result['message'];
            }
        } catch (Exception $e) {
            $errorMessage = "⚠️ Lỗi: " . $e->getMessage();
            error_log("Delete exception: " . $e->getMessage());
            error_log("Trace: " . $e->getTraceAsString());
        }
        
        header("Location: ?section=employees&msg=" . urlencode($successMessage ?? $errorMessage ?? ''));
        exit;
    }
}

// Lấy thông tin admin từ session
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee House Admin - <?= ucfirst($currentSection) ?></title>
    <link rel="stylesheet" href="admin-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar Component (Tái sử dụng cho tất cả các trang) -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Header Component (Tái sử dụng cho tất cả các trang) -->
            <?php include __DIR__ . '/partials/header.php'; ?>

            <!-- Dynamic Content Wrapper -->
            <div class="content-wrapper">
                <?php 
                // QUAN TRỌNG: Với section employees, phải lấy dữ liệu trước
                if ($currentSection === 'employees') {
                    // Load dependencies
                    require_once __DIR__ . '/../../Services/EmployeeService.php';
                    
                    // LẤY DỮ LIỆU ĐỂ HIỂN THỊ (POST/DELETE đã xử lý ở trên rồi)
                    $employeeService = new EmployeeService();
                    $keyword = $_GET['search'] ?? '';
                    $roleFilter = $_GET['role'] ?? 'all';
                    
                    try {
                        // Lấy danh sách nhân viên
                        if (!empty($keyword)) {
                            $employees = $employeeService->searchEmployees($keyword);
                        } elseif ($roleFilter !== 'all') {
                            $employees = $employeeService->getEmployeesByRole((int)$roleFilter);
                        } else {
                            $employees = $employeeService->getAllEmployees();
                        }
                        
                        $stats = $employeeService->getStatistics();
                        
                    } catch (Exception $e) {
                        $employees = [];
                        $stats = ['total' => 0, 'manager' => 0, 'barista' => 0, 'cashier' => 0, 'waiter' => 0, 'cleaner' => 0];
                        $errorMessage = $e->getMessage();
                    }
                    
                    // Truyền dữ liệu qua global để view sử dụng
                    $GLOBALS['employees'] = $employees;
                    $GLOBALS['stats'] = $stats;
                    $GLOBALS['keyword'] = $keyword;
                    $GLOBALS['roleFilter'] = $roleFilter;
                    $GLOBALS['successMessage'] = $_GET['msg'] ?? null;
                    $GLOBALS['errorMessage'] = $errorMessage ?? null;
                    
                    // Include view
                    include __DIR__ . "/sections/employees.php";
                } else {
                    // Các section khác load bình thường
                    $sectionFile = __DIR__ . "/sections/{$currentSection}.php";
                    
                    if (file_exists($sectionFile)) {
                        include $sectionFile;
                    } else {
                        // Fallback nếu không tìm thấy section
                        echo '<div class="error-message">';
                        echo '<h2>⚠️ Không tìm thấy trang</h2>';
                        echo '<p>Section "' . htmlspecialchars($currentSection) . '" không tồn tại.</p>';
                        echo '<a href="?section=dashboard" class="btn-primary">← Quay về Dashboard</a>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <!-- Modal Component (Tái sử dụng cho tất cả các trang) -->
            <?php include __DIR__ . '/partials/modal.php'; ?>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="admin-script.js"></script>
    <script>
        // Truyền section hiện tại vào JavaScript
        window.CURRENT_SECTION = '<?= $currentSection ?>';
    </script>
</body>
</html>
