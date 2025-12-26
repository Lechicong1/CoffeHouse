<?php
/**
 * Category Controller - Quản lý danh mục
 * Theo mô hình MVC chuẩn
 */

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
        try {
            // Xử lý tìm kiếm nếu có
            $keyword = $_GET['search'] ?? '';
            $categories = empty($keyword) 
                ? $this->categoryService->getAllCategories() 
                : $this->categoryService->searchCategories($keyword);

            $stats = $this->categoryService->getStats();

            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Categories_v',
                'section' => 'categories',
                'categories' => $categories,
                'stats' => $stats,
                'keyword' => $keyword
            ]);
        } catch (Exception $e) {
            $this->view('AdminDashBoard/MasterLayout', [
                'page' => 'Categories_v',
                'section' => 'categories',
                'categories' => [],
                'stats' => ['total' => 0],
                'errorMessage' => $e->getMessage()
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
}
?>
