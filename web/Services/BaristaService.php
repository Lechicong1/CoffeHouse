<?php
include_once './web/Repositories/BaristaRepository.php';
include_once './web/Repositories/OrderItemRepository.php';
include_once './web/Repositories/RecipeRepository.php';
include_once './web/Repositories/IngredientRepository.php';
include_once './web/Repositories/ProductSizeRepository.php';
include_once './Enums/status.enum.php';
include_once './Enums/size.enum.php';

class BaristaService {
    private $baristaRepo;
    private $orderItemRepo;
    private $recipeRepo;
    private $ingredientRepo;
    private $productSizeRepo;

    public function __construct() {
        $this->baristaRepo = new BaristaRepository();
        $this->orderItemRepo = new OrderItemRepository();
        $this->recipeRepo = new RecipeRepository();
        $this->ingredientRepo = new IngredientRepository();
        $this->productSizeRepo = new ProductSizeRepository();
    }

    /**
     * Lấy danh sách đơn hàng cho Barista
     */
    public function getBaristaOrders() {
        $orders = $this->baristaRepo->findBaristaOrders();
        foreach ($orders as $order) {
            $order->items = $this->orderItemRepo->findByOrderId($order->id);
        }
        return $orders;
    }

    /**
     * Barista nhận đơn -> Chuyển sang PREPARING
     */
    public function acceptOrder($orderId) {
        return $this->baristaRepo->updateStatus($orderId, OrderStatus::PREPARING);
    }

    /**
     * Barista hoàn thành đơn -> Chuyển sang READY
     */
    public function completeOrder($orderId) {
        // 1. Cập nhật kho nguyên liệu trước
        $orderItems = $this->orderItemRepo->findByOrderId($orderId);
        
        foreach ($orderItems as $item) {
            // Fix: item là mảng associative do Repository trả về, không phải Object
            $productSizeId = is_array($item) ? $item['product_size_id'] : $item->product_size_id;
            $quantity = is_array($item) ? $item['quantity'] : $item->quantity;

            // Lấy product_id từ product_size_id
            $productSize = $this->productSizeRepo->findById($productSizeId);
            
            if ($productSize) {
                // Lấy size multiplier (S=0.8, M=1.0, L=1.2)
                $multiplier = SizeEnum::getMultiplier($productSize->size_name);

                // Lấy công thức của sản phẩm
                $recipes = $this->recipeRepo->getByProductId($productSize->product_id);
                
                foreach ($recipes as $recipe) {
                    $ingredient = $this->ingredientRepo->findById($recipe->ingredient_id);
                    if ($ingredient) {
                        // Tính toán và trừ nguyên liệu: baseAmount * sizeMultiplier * quantity
                        $quantityUsed = $recipe->base_amount * $multiplier * $quantity;
                        
                        $ingredient->stock_quantity -= $quantityUsed;
                        $this->ingredientRepo->update($ingredient);
                    }
                }
            }
        }

        // 2. Sau đó update trạng thái
        return $this->baristaRepo->updateStatus($orderId, OrderStatus::READY);
    }
}
?>
