<?php
/**
 * Customer Controller - Quản lý khách hàng
 */

require_once __DIR__ . '/../../Config/Controller.php';
require_once __DIR__ . '/../../Config/Database.php';

class CustomerController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hiển thị danh sách khách hàng (GET)
     */
    public function Get_data()
    {
        $keyword = $_GET['search'] ?? '';

        try {
            // Lấy danh sách khách hàng
            if (!empty($keyword)) {
                $customers = $this->searchCustomers($keyword);
            } else {
                $customers = $this->getAllCustomers();
            }

            // Lấy thống kê
            $totalCustomers = count($customers);

        } catch (Exception $e) {
            $customers = [];
            $totalCustomers = 0;
            $errorMessage = $e->getMessage();
        }

        // Gọi MasterLayout với view con Customers_v
        $this->view('AdminDashBoard/MasterLayout', [
            'page' => 'Customers_v',
            'section' => 'customers',
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'keyword' => $keyword,
            'successMessage' => $_GET['msg'] ?? null,
            'errorMessage' => $errorMessage ?? null
        ]);
    }

    /**
     * Lấy tất cả khách hàng
     */
    private function getAllCustomers()
    {
        $query = "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Tìm kiếm khách hàng
     */
    private function searchCustomers($keyword)
    {
        $query = "SELECT * FROM users 
                  WHERE role = 'customer' 
                  AND (fullname LIKE :keyword OR email LIKE :keyword OR phonenumber LIKE :keyword) 
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Xóa khách hàng (POST)
     */
    public function del()
    {
        if (isset($_POST['btnXoa'])) {
            try {
                $query = "DELETE FROM users WHERE id = :id AND role = 'customer'";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $_POST['txtId']);
                $stmt->execute();

                header("Location: ?url=Customer/Get_data&msg=" . urlencode("✅ Xóa khách hàng thành công!"));
            } catch (Exception $e) {
                header("Location: ?url=Customer/Get_data&msg=" . urlencode("❌ Lỗi: " . $e->getMessage()));
            }
            exit;
        }
    }
}
