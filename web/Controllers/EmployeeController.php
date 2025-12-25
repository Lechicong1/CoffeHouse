<?php
/**
 * Employee Controller - Quản lý nhân viên
 * Theo mô hình MVC chuẩn
 */

require_once __DIR__ . '/../Services/EmployeeService.php';

class EmployeeController extends Controller {
    private $employeeService;
    
    function __construct() {
        $this->employeeService = new EmployeeService();
    }
    
    /**
     * Hiển thị danh sách nhân viên (GET)
     */
    function Get_data() {
        $keyword = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? 'all';


        try {
            // Lấy danh sách nhân viên
            if (!empty($keyword)) {
                $employees = $this->employeeService->searchEmployees($keyword);
            } elseif ($roleFilter !== 'all') {
                $employees = $this->employeeService->getEmployeesByRole((int)$roleFilter);
            } else {
                $employees = $this->employeeService->getAllEmployees();
            }
            
            // Lấy thống kê
            $stats = $this->employeeService->getStatistics();
            
        } catch (Exception $e) {
            $employees = [];
            $stats = ['total' => 0, 'manager' => 0, 'barista' => 0, 'cashier' => 0, 'waiter' => 0, 'cleaner' => 0];
            $errorMessage = $e->getMessage();
        }
        
        // Render view
        $this->view('AdminDashBoard/sections/employees', [
            'employees' => $employees,
            'stats' => $stats,
            'keyword' => $keyword,
            'roleFilter' => $roleFilter,
            'successMessage' => $_GET['msg'] ?? null,
            'errorMessage' => $errorMessage ?? null
        ]);
    }
    
    /**
     * Tìm kiếm nhân viên
     */
    function timkiem() {
        if (isset($_POST['btnTimkiem'])) {
            $keyword = $_POST['txtSearch'];
            $roleFilter = $_POST['ddlRole'] ?? 'all';
            
            header("Location: ?url=Employee/Get_data&search=" . urlencode($keyword) . "&role=" . $roleFilter);
            exit;
        }
    }
    
    /**
     * Thêm nhân viên mới (POST)
     */
    function ins() {
        if (isset($_POST['btnThem'])) {
            try {
                // Kiểm tra username có trùng không
                if ($this->employeeService->checkUsernameExists($_POST['txtUsername'])) {
                    echo "<script>alert('❌ Username đã tồn tại!');</script>";
                    header("Location: ?section=employees&msg=" . urlencode("❌ Username đã tồn tại!"));
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
                    'luong' => (float)$_POST['txtLuong']
                ];
                
                $result = $this->employeeService->createEmployee($data);
                
                if ($result['success']) {
                    echo "<script>alert('✅ Thêm nhân viên thành công!');</script>";
                    header("Location: ?section=employees&msg=" . urlencode("✅ Thêm nhân viên thành công!"));
                } else {
                    echo "<script>alert('❌ " . $result['message'] . "');</script>";
                    header("Location: ?section=employees&msg=" . urlencode("❌ " . $result['message']));
                }
                
            } catch (Exception $e) {
                echo "<script>alert('⚠️ Lỗi: " . $e->getMessage() . "');</script>";
                header("Location: ?section=employees&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
    
    /**
     * Hiển thị form sửa nhân viên
     */
    function sua($id) {
        try {
            $employee = $this->employeeService->getEmployeeById($id);
            
            if (!$employee) {
                header("Location: ?section=employees&msg=" . urlencode("❌ Không tìm thấy nhân viên!"));
                exit;
            }
            
            // Trả về JSON cho modal (nếu cần)
            // Hoặc render view riêng
            
        } catch (Exception $e) {
            header("Location: ?section=employees&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            exit;
        }
    }
    
    /**
     * Cập nhật thông tin nhân viên (POST)
     */
    function upd() {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $id = (int)$_POST['txtId'];
                
                $data = [
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (float)$_POST['txtLuong']
                ];
                
                // Nếu có password mới thì cập nhật
                if (!empty($_POST['txtPassword'])) {
                    $data['password'] = $_POST['txtPassword'];
                }
                
                $result = $this->employeeService->updateEmployee($id, $data);
                
                if ($result['success']) {
                    echo "<script>alert('✅ Cập nhật thành công!');</script>";
                    header("Location: ?section=employees&msg=" . urlencode("✅ Cập nhật thành công!"));
                } else {
                    echo "<script>alert('❌ " . $result['message'] . "');</script>";
                    header("Location: ?section=employees&msg=" . urlencode("❌ " . $result['message']));
                }
                
            } catch (Exception $e) {
                echo "<script>alert('⚠️ Lỗi: " . $e->getMessage() . "');</script>";
                header("Location: ?section=employees&msg=" . urlencode("⚠️ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
    
    /**
     * Xóa nhân viên
     */
    function xoa($id) {
        try {
            if ($this->employeeService->deleteEmployee($id)) {
                $successMessage = "✅ Xóa nhân viên thành công!";
            } else {
                $errorMessage = "❌ Xóa nhân viên thất bại!";
            }
            
        } catch (Exception $e) {
            $errorMessage = "⚠️ Lỗi: " . $e->getMessage();
        }
        
        header("Location: ?url=Employee/Get_data&msg=" . urlencode($successMessage ?? $errorMessage));
        exit;
    }
    
    /**
     * Xuất Excel danh sách nhân viên
     */
    function xuatExcel() {
        if (isset($_POST['btnXuatexcel'])) {
            try {
                $keyword = $_POST['txtSearch'] ?? '';
                $roleFilter = $_POST['ddlRole'] ?? 'all';
                
                // Lấy dữ liệu
                if (!empty($keyword)) {
                    $employees = $this->employeeService->searchEmployees($keyword);
                } elseif ($roleFilter !== 'all') {
                    $employees = $this->employeeService->getEmployeesByRole((int)$roleFilter);
                } else {
                    $employees = $this->employeeService->getAllEmployees();
                }
                
                // Xóa output buffer
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                $filename = "DanhSachNhanVien_" . date('Y-m-d') . ".xls";
                
                // Set headers
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="' . $filename . '"');
                header('Cache-Control: max-age=0');
                
                // Tạo HTML table
                echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
                echo '<head>';
                echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
                echo '</head>';
                echo '<body>';
                echo '<table border="1">';
                echo '<tr style="background-color: #667eea; font-weight: bold; color: white;">';
                echo '<th>ID</th>';
                echo '<th>Username</th>';
                echo '<th>Họ tên</th>';
                echo '<th>Vai trò</th>';
                echo '<th>Email</th>';
                echo '<th>Số điện thoại</th>';
                echo '<th>Địa chỉ</th>';
                echo '<th>Lương</th>';
                echo '<th>Ngày tạo</th>';
                echo '</tr>';
                
                foreach ($employees as $emp) {
                    echo '<tr>';
                    echo '<td>' . $emp->id . '</td>';
                    echo '<td>' . $emp->username . '</td>';
                    echo '<td>' . $emp->fullname . '</td>';
                    echo '<td>' . $emp->getRoleName() . '</td>';
                    echo '<td>' . ($emp->email ?? '-') . '</td>';
                    echo '<td>' . $emp->phonenumber . '</td>';
                    echo '<td>' . ($emp->address ?? '-') . '</td>';
                    echo '<td>' . number_format($emp->luong, 0, ',', '.') . '</td>';
                    echo '<td>' . date('d/m/Y H:i', strtotime($emp->created_at)) . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
                echo '</body>';
                echo '</html>';
                exit;
                
            } catch (Exception $e) {
                header("Location: ?url=Employee/Get_data&msg=" . urlencode("⚠️ Lỗi xuất Excel: " . $e->getMessage()));
                exit;
            }
        }
    }
}
?>
