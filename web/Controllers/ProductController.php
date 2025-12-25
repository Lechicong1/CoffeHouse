<?php
/**
 * Product Controller - Quản lý sản phẩm
 */

require_once __DIR__ . '/../../Config/Controller.php';
require_once __DIR__ . '/../../Config/Database.php';

class ProductController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hiển thị danh sách sản phẩm (GET)
     */
    public function Get_data()
    {
        $keyword = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? 'all';

        try {
            // Lấy danh sách sản phẩm
            if (!empty($keyword)) {
                $products = $this->searchProducts($keyword);
            } elseif ($category !== 'all') {
                $products = $this->getProductsByCategory($category);
            } else {
                $products = $this->getAllProducts();
            }

            // Lấy danh sách danh mục
            $categories = $this->getCategories();
            
        } catch (Exception $e) {
            $products = [];
            $categories = [];
            $errorMessage = $e->getMessage();
        }

        // Gọi MasterLayout với view con Products_v
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Products_v',
            'section' => 'products',
            'products' => $products,
            'categories' => $categories,
            'keyword' => $keyword,
            'category' => $category,
            'successMessage' => $_GET['msg'] ?? null,
            'errorMessage' => $errorMessage ?? null
        ]);
    }

    /**
     * Lấy tất cả sản phẩm
     */
    private function getAllProducts()
    {
        $query = "SELECT * FROM products ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Tìm kiếm sản phẩm
     */
    private function searchProducts($keyword)
    {
        $query = "SELECT * FROM products WHERE name LIKE :keyword OR description LIKE :keyword ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy sản phẩm theo danh mục
     */
    private function getProductsByCategory($categoryId)
    {
        $query = "SELECT * FROM products WHERE category_id = :category ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':category', $categoryId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy danh sách danh mục
     */
    private function getCategories()
    {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Thêm sản phẩm mới (POST)
     */
    public function ins()
    {
        if (isset($_POST['btnThem'])) {
            try {
                $query = "INSERT INTO products (name, description, price, category_id, image) 
                          VALUES (:name, :description, :price, :category_id, :image)";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $_POST['txtName']);
                $stmt->bindParam(':description', $_POST['txtDescription']);
                $stmt->bindParam(':price', $_POST['txtPrice']);
                $stmt->bindParam(':category_id', $_POST['ddlCategoryId']);
                $stmt->bindParam(':image', $_POST['txtImage']);
                
                $stmt->execute();
                
                header("Location: ?url=Product/Get_data&msg=" . urlencode("✅ Thêm sản phẩm thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Product/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }

    /**
     * Cập nhật sản phẩm (POST)
     */
    public function upd()
    {
        if (isset($_POST['btnCapnhat'])) {
            try {
                $query = "UPDATE products SET name = :name, description = :description, 
                          price = :price, category_id = :category_id, image = :image 
                          WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['txtId']);
                $stmt->bindParam(':name', $_POST['txtName']);
                $stmt->bindParam(':description', $_POST['txtDescription']);
                $stmt->bindParam(':price', $_POST['txtPrice']);
                $stmt->bindParam(':category_id', $_POST['ddlCategoryId']);
                $stmt->bindParam(':image', $_POST['txtImage']);
                
                $stmt->execute();
                
                header("Location: ?url=Product/Get_data&msg=" . urlencode("✅ Cập nhật thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Product/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }

    /**
     * Xóa sản phẩm (POST)
     */
    public function del()
    {
        if (isset($_POST['btnXoa'])) {
            try {
                $query = "DELETE FROM products WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['txtId']);
                $stmt->execute();
                
                header("Location: ?url=Product/Get_data&msg=" . urlencode("✅ Xóa sản phẩm thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Product/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
}

