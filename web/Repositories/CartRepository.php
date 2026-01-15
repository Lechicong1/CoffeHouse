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


    public function findCartByCustomerId($customerId) {
        $sql = "SELECT ci.*, p.id as product_id, p.name as product_name, p.image_url, ps.size_name, ps.price
                FROM cart_items ci
                JOIN product_sizes ps ON ci.product_size_id = ps.id
                JOIN products p ON ps.product_id = p.id
                WHERE ci.customer_id = ?";
        
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);  // kq tu cau lenh execute
        $cartItems = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $item = new CartItemEntity($row);
            $cartItems[] = $item;
        }
        
        return $cartItems;
    }

    public function findExisting($customerId, $productSizeId) {
        $sql = "SELECT * FROM cart_items WHERE customer_id = ? AND product_size_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $customerId, $productSizeId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        return $data ? new CartItemEntity($data) : null;
    }

    public function findById($id) {
        $sql = "SELECT * FROM cart_items WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return $data ? new CartItemEntity($data) : null;
    }

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


    public function updateQuantity($id, $quantity) {
        $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $quantity, $id);
        
        return mysqli_stmt_execute($stmt);
    }


    public function delete($id) {
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        return mysqli_stmt_execute($stmt);
    }


    public function clearCart($customerId) {
        $sql = "DELETE FROM cart_items WHERE customer_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        
        return mysqli_stmt_execute($stmt);
    }

    public function countItems($customerId) {
        $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE customer_id = ?";
        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        
        return $data['total'] ?? 0;
    }

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

