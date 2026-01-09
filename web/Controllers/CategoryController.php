<?php
/**
 * Category Controller - Quản lý danh mục
 * Theo mô hình MVC chuẩn
 */
require_once __DIR__ . '/../../Config/ExcelHelper.php';

class CategoryController extends Controller {
    private $categoryService;
    
    function __construct() {
        // Khởi tạo Service thông qua Controller base
        $this->categoryService = $this->service('CategoryService');
    }
    
    /**
     * Hiển thị danh sách danh mục (Method mặc định)
     */
    function GetData() {
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Categories_v',
            'section' => 'categories',
            'categories' => $this->categoryService->getAllCategories(),
            'stats' => $this->categoryService->getStats(),
            'keyword' => ''
        ]);
    }

    /**
     * Tìm kiếm danh mục
     */
    function timkiem() {
        if (isset($_POST['btnTimkiem'])) {
            $keyword = $_POST['txtSearch'] ?? '';
            $categories = $this->categoryService->searchCategories($keyword);
            
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Categories_v',
                'section' => 'categories',
                'categories' => $categories,
                'stats' => $this->categoryService->getStats(),
                'keyword' => $keyword
            ]);
        }
    }

    /**
     * Thêm danh mục mới (POST)
     */
    function ins() {
        if (isset($_POST['btnThem'])) {
            try {
                $data = [
                    'name' => $_POST['txtName'],
                    'description' => $_POST['txtDescription'] ?? ''
                ];
                
                $result = $this->categoryService->createCategory($data);
                
                // Redirect để tránh resubmit
                header('Location: ?url=Category&success=' . urlencode($result['message']));
                exit;
                
            } catch (Exception $e) {
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Categories_v',
                    'section' => 'categories',
                    'categories' => $this->categoryService->getAllCategories(),
                    'stats' => $this->categoryService->getStats(),
                    'errorMessage' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Cập nhật danh mục (POST)
     */
    function upd() {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $id = (int)$_POST['txtId'];
                
                $data = [
                    'name' => $_POST['txtName'],
                    'description' => $_POST['txtDescription'] ?? ''
                ];
                
                $result = $this->categoryService->updateCategory($id, $data);
                
                // Redirect để tránh resubmit
                header('Location: ?url=Category&success=' . urlencode($result['message']));
                exit;
                
            } catch (Exception $e) {
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Categories_v',
                    'section' => 'categories',
                    'categories' => $this->categoryService->getAllCategories(),
                    'stats' => $this->categoryService->getStats(),
                    'errorMessage' => $e->getMessage()
                ]);
            }
        }
    }
    
    /**
     * Xóa danh mục (POST)
     */
    function del() {
        if (isset($_POST['btnXoa'])) {
            try {
                $id = (int)$_POST['txtId'];
                $result = $this->categoryService->deleteCategory($id);
                
                // Redirect để tránh resubmit
                header('Location: ?url=Category&success=' . urlencode($result['message']));
                exit;
                
            } catch (Exception $e) {
                $this->view('AdminDashBoard/MasterLayout', [
                    'page' => 'Categories_v',
                    'section' => 'categories',
                    'categories' => $this->categoryService->getAllCategories(),
                    'stats' => $this->categoryService->getStats(),
                    'errorMessage' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Xuất Excel danh sách danh mục
     */
    function xuatexcel() {
        if(isset($_POST['btnXuatexcel'])){
            // Lấy từ khóa tìm kiếm nếu có
            $keyword = isset($_POST['txtSearch']) ? $_POST['txtSearch'] : '';

            // Lấy dữ liệu danh mục
            if (!empty($keyword)) {
                $categories = $this->categoryService->searchCategories($keyword);
            } else {
                $categories = $this->categoryService->getAllCategories();
            }

            // Chuyển đổi object sang array để xuất Excel
            $data = array_map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description ?? '-',
                    'product_count' => $category->product_count ?? 0
                ];
            }, $categories);

            // Định nghĩa cấu trúc cột cho Excel
            $headers = [
                'id' => 'ID',
                'name' => 'Tên Danh Mục',
                'description' => 'Mô Tả',
                'product_count' => 'Số Sản Phẩm'
            ];

            // Gọi hàm xuất Excel từ Helper
            ExcelHelper::exportToExcel($data, $headers, 'DanhSachDanhMuc');
        }
    }
}
?>
