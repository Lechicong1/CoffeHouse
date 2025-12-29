<?php
/**
 * FILE: CartService.php
 * DESCRIPTION: Service xử lý business logic cho giỏ hàng
 * CHUẨN MVC: Service chứa TOÀN BỘ logic nghiệp vụ, validation
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

    /**
     * Thêm sản phẩm vào giỏ hàng
     * LOGIC: Validate -> Kiểm tra tồn tại -> Thêm mới hoặc cập nhật
     */
    public function addToCart($customerId, $productSizeId, $quantity) {
        try {
            // VALIDATION - Logic ở Service, không phải Controller
            if (!$productSizeId) {
                return [
                    'success' => false,
                    'message' => 'Vui lòng chọn size sản phẩm'
                ];
            }

            if ($quantity < 1) {
                return [
                    'success' => false,
                    'message' => 'Số lượng phải lớn hơn 0'
                ];
            }

            // BUSINESS LOGIC: Kiểm tra sản phẩm đã có trong giỏ chưa
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

    /**
     * Lấy giỏ hàng của customer
     */
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
     * LOGIC: Validate -> Kiểm tra quyền sở hữu -> Cập nhật hoặc xóa
     */
    public function updateQuantity($customerId, $cartItemId, $quantity) {
        try {
            // VALIDATION
            if (!$cartItemId) {
                return [
                    'success' => false,
                    'message' => 'Thiếu thông tin sản phẩm'
                ];
            }

            // BUSINESS LOGIC: Kiểm tra quyền sở hữu
            $cartItem = $this->cartRepository->findById($cartItemId);
            if (!$cartItem || $cartItem->customer_id != $customerId) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng của bạn'
                ];
            }

            // Nếu số lượng <= 0 thì xóa item
            if ($quantity <= 0) {
                $this->cartRepository->delete($cartItemId);
                return [
                    'success' => true,
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng'
                ];
            }

            // Cập nhật số lượng
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
     * LOGIC: Validate -> Kiểm tra quyền sở hữu -> Xóa
     */
    public function removeItem($customerId, $cartItemId) {
        try {
            // VALIDATION
            if (!$cartItemId) {
                return [
                    'success' => false,
                    'message' => 'Thiếu thông tin sản phẩm'
                ];
            }

            // BUSINESS LOGIC: Kiểm tra quyền sở hữu
            $cartItem = $this->cartRepository->findById($cartItemId);
            if (!$cartItem || $cartItem->customer_id != $customerId) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm trong giỏ hàng của bạn'
                ];
            }

            // Xóa sản phẩm
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
