<?php
/**
 * FILE: CartService.php
 * DESCRIPTION: Service xử lý business logic cho giỏ hàng
 * AUTHOR: Coffee House System
 */

require_once './web/Repositories/CartRepository.php';
require_once './web/Entity/CartItemEntity.php';

use web\Entity\CartItemEntity;

class CartService extends Service {
    private $cartRepository;

    public function __construct() {
        $this->cartRepository = new CartRepository();
    }


    public function addToCart($customerId, $productSizeId, $quantity) {
        try {
            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $existing = $this->cartRepository->findExisting($customerId, $productSizeId);

            if ($existing) {
                // Nếu đã có -> cập nhật số lượng
                $newQuantity = $existing->quantity + $quantity;
                $this->cartRepository->updateQuantity($existing->id, $newQuantity);

                return [
                    'success' => true,
                    'message' => 'Đã cập nhật số lượng trong giỏ hàng',
                    'action' => 'updated'
                ];
            } else {
                // Nếu chưa có -> thêm mới
                $cartItem = new CartItemEntity([
                    'customer_id' => $customerId,
                    'product_size_id' => $productSizeId,
                    'quantity' => $quantity
                ]);

                $this->cartRepository->create($cartItem);

                return [
                    'success' => true,
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                    'action' => 'added'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }


    public function getCart($customerId) {
        try {
            $items = $this->cartRepository->findCartByCustomerId($customerId);
            $total = $this->cartRepository->calculateTotal($customerId);

            return [
                'success' => true,
                'items' => $items,
                'total' => $total,
                'count' => count($items)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cập nhật số lượng sản phẩm
     * @param int $cartItemId
     * @param int $quantity
     * @return array
     */
    public function updateQuantity($cartItemId, $quantity) {
        try {
            if ($quantity <= 0) {
                // Nếu số lượng <= 0 thì xóa item
                $this->cartRepository->delete($cartItemId);
                return [
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
                ];
            }

            $this->cartRepository->updateQuantity($cartItemId, $quantity);

            return [
                'success' => true,
                'message' => 'Đã cập nhật số lượng'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     * @param int $cartItemId
     * @return array
     */
    public function removeItem($cartItemId) {
        try {
            $this->cartRepository->delete($cartItemId);

            return [
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Xóa toàn bộ giỏ hàng
     * @param int $customerId
     * @return array
     */
    public function clearCart($customerId) {
        try {
            $this->cartRepository->clearCart($customerId);

            return [
                'success' => true,
                'message' => 'Đã xóa toàn bộ giỏ hàng'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lấy số lượng items trong giỏ hàng
     * @param int $customerId
     * @return array
     */
    public function getCartCount($customerId) {
        try {
            $count = $this->cartRepository->countItems($customerId);

            return [
                'success' => true,
                'count' => (int)$count
            ];
        } catch (Exception $e) {
            return [
                'success' => true,
                'count' => 0
            ];
        }
    }
}
?>
