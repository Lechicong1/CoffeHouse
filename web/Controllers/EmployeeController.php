<?php
/**
 * Employee Controller - Quản lý nhân viên
 * Theo mô hình MVC chuẩn
 */

class EmployeeController extends Controller {
    private $employeeService;
    
    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->employeeService = $this->service('EmployeeService');

    }
    
    /**
     * Hiển thị danh sách nhân viên (Method mặc định)
     */
    function GetData() {
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Employees_v',
            'section' => 'employees',
            'employees' => $this->employeeService->getAllEmployees(),
            'keyword' => ''
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
                'keyword' => $keyword
            ]);
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
                        'employees' => $this->employeeService->getAllEmployees()
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
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (float)$_POST['txtLuong']
                ];
                
                $result = $this->employeeService->createEmployee($data);
                
                if ($result['success']) {
                    echo "<script>alert('Thêm nhân viên thành công!')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                } else {
                    echo "<script>alert('Thêm thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees()
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
                    'roleId' => (int)$_POST['ddlRoleId'],
                    'luong' => (float)$_POST['txtLuong']
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
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                } else {
                    echo "<script>alert('Cập nhật thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees()
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
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                } else {
                    echo "<script>alert('Xóa thất bại: " . $result['message'] . "')</script>";
                    $this->view('AdminDashBoard/MasterLayout', [
                        'page' => 'Employees_v',
                        'section' => 'employees',
                        'employees' => $this->employeeService->getAllEmployees()
                    ]);
                }
            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . addslashes($e->getMessage()) . "')</script>";
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Employees_v',
                    'section' => 'employees',
                    'employees' => $this->employeeService->getAllEmployees()
                ]);
            }
        }
    }
}
?>
