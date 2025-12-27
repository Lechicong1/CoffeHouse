<?php
/**
 * FILE: CartRepository.php
 * DESCRIPTION: Repository cho Cart - CRUD giỏ hàng
 * TABLE: cart_items
 * AUTHOR: Coffee House System
 */

include_once './web/Entity/CartItemEntity.php';
use web\Entity\CartItemEntity;

class CartRepository extends ConnectDatabase {

    /**
     * Lấy tất cả items trong giỏ hàng của customer
     * @param int $customerId
     * @return array
     */
    public function findCartByCustomerId($customerId) {
        $sql = "SELECT ci.*, p.name as product_name, p.image_url, ps.size_name, ps.price
                FROM cart_items ci
                JOIN product_sizes ps ON ci.product_size_id = ps.id
                JOIN products p ON ps.product_id = p.id
                WHERE ci.customer_id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $cartItems = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $item = new CartItemEntity($row);
            $item->product_name = $row['product_name'];
            $item->image_url = $row['image_url'];
            $item->size_name = $row['size_name'];
            $item->price = $row['price'];
            $cartItems[] = $item;
        }
        
        return $cartItems;
    }

    /**
     * Kiểm tra xem sản phẩm (với biến thể size) đã có trong giỏ hàng chưa
     * @param int $customerId
     * @param int $productSizeId
     * @return CartItemEntity|null
     */
    public function findExisting($customerId, $productSizeId) {
        $sql = "SELECT * FROM cart_items WHERE customer_id = ? AND product_size_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $customerId, $productSizeId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        return $data ? new CartItemEntity($data) : null;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * @param CartItemEntity $cartItem
     * @return int ID của item vừa thêm
     */
    public function create($cartItem) {
        $sql = "INSERT INTO cart_items (customer_id, product_size_id, quantity) 
                VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "iii",
            $cartItem->customer_id,
            $cartItem->product_size_id,
            $cartItem->quantity
        );
        mysqli_stmt_execute($stmt);
        
        return mysqli_insert_id($this->con);
    }

    /**
     * Cập nhật số lượng của item trong giỏ hàng
     * @param int $id
     * @param int $quantity
     * @return bool
     */
    public function updateQuantity($id, $quantity) {
        $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa item khỏi giỏ hàng
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Xóa toàn bộ giỏ hàng của customer
     * @param int $customerId
     * @return bool
     */
    public function clearCart($customerId) {
        $sql = "DELETE FROM cart_items WHERE customer_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Đếm số lượng items trong giỏ hàng
     * @param int $customerId
     * @return int
     */
    public function countItems($customerId) {
        $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE customer_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        return $data['total'] ?? 0;
    }

    /**
     * Tính tổng giá trị giỏ hàng
     * @param int $customerId
     * @return float
     */
    public function calculateTotal($customerId) {
        $sql = "SELECT SUM(ci.quantity * ps.price) as total
                FROM cart_items ci
                JOIN product_sizes ps ON ci.product_size_id = ps.id
                WHERE ci.customer_id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        return $data['total'] ?? 0;
    }
}
?>
