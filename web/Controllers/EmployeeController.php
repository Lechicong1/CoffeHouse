<?php
/**
 * Employee Controller - Quản lý nhân viên
 * Theo mô hình MVC chuẩn
 */
require_once __DIR__ . '/../../Config/ExcelHelper.php';
class EmployeeController extends Controller {
    private $employeeService;
    
    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->employeeService = $this->service('EmployeeService');
    }
    
    /**
     * Lấy danh sách roles cho dropdown
     * @return array
     */
    private function getRoles() {
        // Ủy quyền cho Service lấy roles (giữ flow Controller -> Service -> Repository)
        return $this->employeeService->getRoles();
    }
    
    /**
     * Hiển thị danh sách nhân viên (Method mặc định)
     */
    function GetData() {
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Employees_v',
            'section' => 'employees',
            'employees' => $this->employeeService->getAllEmployees(),
            'keyword' => '',
            'roles' => $this->getRoles()
        ]);
    }

    /**
     * Tìm kiếm nhân viên
     */
    function timkiem() {
        if (isset($_POST['btnTimkiem'])) {
            $keyword = $_POST['txtSearch'] ?? '';
            $employees = $this->employeeService->searchEmployees($keyword);
            
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Employees_v',
                'section' => 'employees',
                'employees' => $employees,
                'keyword' => $keyword,
                'roles' => $this->getRoles()
            ]);
        }
    }
    function xuatexcel(){
        if(isset($_POST['btnXuatexcel'])){
            // Lấy từ khóa tìm kiếm nếu có
            $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';

            // Lấy dữ liệu nhân viên (có thể là kết quả tìm kiếm hoặc toàn bộ)
            if (!empty($keyword)) {
                $employees = $this->employeeService->searchEmployees($keyword);
            } else {
                $employees = $this->employeeService->getAllEmployees();
            }

            // Chuyển đổi object sang array để xuất Excel
            $data = array_map(function($employee) {
                return [
                    'id' => $employee->id,
                    'username' => $employee->username,
                    'fullname' => $employee->fullname,
                    'roleName' => $employee->getRoleDisplayName(),
                    'email' => $employee->email ?? '-',
                    'phonenumber' => $employee->phonenumber,
                    'luong' => $employee->luong,
                    'address' => $employee->address ?? '-'
                ];
            }, $employees);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'id' => 'ID',
                'username' => 'Username',
                'fullname' => 'Họ và Tên',
                'roleName' => 'Vai Trò',
                'email' => 'Email',
                'phonenumber' => 'Số Điện Thoại',
                'luong' => 'Lương (VNĐ)',
                'address' => 'Địa Chỉ'
            ];

            // Gọi hàm xuất Excel từ Helper
            ExcelHelper::exportToExcel($data, $headers, 'DanhSachNhanVien');
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
                    echo "<script>alert('Username đã tồn tại!')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                    return;
                }
                
                $data = [
                    'username' => $_POST['txtUsername'],
                    'password' => $_POST['txtPassword'],
                    'fullname' => $_POST['txtFullname'],
                    'email' => $_POST['txtEmail'] ?? null,
                    'phonenumber' => $_POST['txtPhonenumber'],
                    'address' => $_POST['txtAddress'] ?? null,
                    'roleName' => $_POST['ddlRoleName'],
                    'luong' => (float)$_POST['txtLuong'],
                    'create_at' => $_POST['txtCreateAt'] ?? date('Y-m-d')
                ];
                
                $result = $this->employeeService->createEmployee($data);
                
                if ($result['success']) {
                    echo "<script>alert('Thêm nhân viên thành công!')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                } else {
                    echo "<script>alert('Thêm thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees(),
                    'roles' => $this->getRoles()
                ]);
            }
        }
    }

    /**
     * Cập nhật nhân viên (POST)
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
                    'roleName' => $_POST['ddlRoleName'],
                    'luong' => (float)$_POST['txtLuong'],
                    'create_at' => $_POST['txtCreateAt'] ?? null
                ];
                
                // Nếu có password mới
                if (!empty($_POST['txtPassword'])) {
                    $data['password'] = $_POST['txtPassword'];
                }
                
                $result = $this->employeeService->updateEmployee($id, $data);
                
                if ($result['success']) {
                    echo "<script>alert('Cập nhật thành công!')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                } else {
                    echo "<script>alert('Cập nhật thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees(),
                    'roles' => $this->getRoles()
                ]);
            }
        }
    }
    
    /**
     * Xóa nhân viên (POST)
     */
    function del() {
        if (isset($_POST['btnXoa'])) {
            try {
                $id = (int)$_POST['txtId'];
                $result = $this->employeeService->deleteEmployee($id);
                
                if ($result['success']) {
                    echo "<script>alert('Xóa nhân viên thành công!')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                } else {
                    echo "<script>alert('Xóa thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees(),
                        'roles' => $this->getRoles()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees(),
                    'roles' => $this->getRoles()
                ]);
            }
        }
    }
}
?>
