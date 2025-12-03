<?php
/**
 * FILE: Promotion.php
 * DESCRIPTION: Model quản lý khuyến mãi/mã giảm giá
 */

require_once __DIR__ . '/../Config/Database.php';

class Promotion {
    private $conn;
    private $table_name = "promotions";

    // Properties
    public $id;
    public $code;
    public $name;
    public $type; // ENUM: 'percentage', 'fixed_amount'
    public $discount_type;
    public $discount_value;
    public $min_order_value;
    public $max_discount_amount;
    public $usage_limit;
    public $used_count;
    public $user_limit;
    public $start_at;
    public $end_at;
    public $scope; // ENUM: 'all', 'category', 'product'
    public $is_active;
    public $created_at;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả promotion đang hoạt động
     */
    public function getActive() {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE is_active = 1 
                  AND (start_at IS NULL OR start_at <= NOW())
                  AND (end_at IS NULL OR end_at >= NOW())
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Lấy promotion theo code
     */
    public function getByCode($code) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->name = $row['name'];
            $this->type = $row['type'];
            $this->discount_type = $row['discount_type'];
            $this->discount_value = $row['discount_value'];
            $this->min_order_value = $row['min_order_value'];
            $this->max_discount_amount = $row['max_discount_amount'];
            $this->usage_limit = $row['usage_limit'];
            $this->used_count = $row['used_count'];
            $this->user_limit = $row['user_limit'];
            $this->start_at = $row['start_at'];
            $this->end_at = $row['end_at'];
            $this->is_active = $row['is_active'];
            return $row;
        }
        return false;
    }

    /**
     * Kiểm tra mã khuyến mãi có hợp lệ không
     */
    public function isValid($code, $orderAmount = 0) {
        $promo = $this->getByCode($code);
        
        if (!$promo) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi không tồn tại'];
        }

        // Kiểm tra active
        if (!$promo['is_active']) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết hiệu lực'];
        }

        // Kiểm tra thời gian
        $now = time();
        if ($promo['start_at'] && strtotime($promo['start_at']) > $now) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi chưa bắt đầu'];
        }
        if ($promo['end_at'] && strtotime($promo['end_at']) < $now) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết hạn'];
        }

        // Kiểm tra số lần sử dụng
        if ($promo['usage_limit'] && $promo['used_count'] >= $promo['usage_limit']) {
            return ['valid' => false, 'message' => 'Mã khuyến mãi đã hết lượt sử dụng'];
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($promo['min_order_value'] && $orderAmount < $promo['min_order_value']) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đủ giá trị tối thiểu'];
        }

        return ['valid' => true, 'promotion' => $promo];
    }

    /**
     * Tính toán số tiền giảm giá
     */
    public function calculateDiscount($code, $orderAmount) {
        $validation = $this->isValid($code, $orderAmount);
        
        if (!$validation['valid']) {
            return 0;
        }

        $promo = $validation['promotion'];
        $discount = 0;

        if ($promo['discount_type'] === 'percentage') {
            $discount = ($orderAmount * $promo['discount_value']) / 100;
            
            // Giới hạn giảm giá tối đa
            if ($promo['max_discount_amount'] && $discount > $promo['max_discount_amount']) {
                $discount = $promo['max_discount_amount'];
            }
        } else {
            $discount = $promo['discount_value'];
        }

        return $discount;
    }

    /**
     * Tạo promotion mới
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (code, name, type, discount_type, discount_value, min_order_value, 
                   max_discount_amount, usage_limit, user_limit, start_at, end_at, 
                   scope, is_active, created_at) 
                  VALUES (:code, :name, :type, :discount_type, :discount_value, :min_order_value,
                          :max_discount_amount, :usage_limit, :user_limit, :start_at, :end_at,
                          :scope, :is_active, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':code', $this->code);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':discount_type', $this->discount_type);
        $stmt->bindParam(':discount_value', $this->discount_value);
        $stmt->bindParam(':min_order_value', $this->min_order_value);
        $stmt->bindParam(':max_discount_amount', $this->max_discount_amount);
        $stmt->bindParam(':usage_limit', $this->usage_limit);
        $stmt->bindParam(':user_limit', $this->user_limit);
        $stmt->bindParam(':start_at', $this->start_at);
        $stmt->bindParam(':end_at', $this->end_at);
        $stmt->bindParam(':scope', $this->scope);
        $stmt->bindParam(':is_active', $this->is_active);
        
        return $stmt->execute();
    }

    /**
     * Tăng số lần sử dụng
     */
    public function incrementUsage($promoId) {
        $query = "UPDATE " . $this->table_name . " 
                  SET used_count = used_count + 1 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $promoId);
        
        return $stmt->execute();
    }

    /**
     * Cập nhật promotion
     */
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name,
                      discount_type = :discount_type,
                      discount_value = :discount_value,
                      min_order_value = :min_order_value,
                      max_discount_amount = :max_discount_amount,
                      usage_limit = :usage_limit,
                      user_limit = :user_limit,
                      start_at = :start_at,
                      end_at = :end_at,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':discount_type', $this->discount_type);
        $stmt->bindParam(':discount_value', $this->discount_value);
        $stmt->bindParam(':min_order_value', $this->min_order_value);
        $stmt->bindParam(':max_discount_amount', $this->max_discount_amount);
        $stmt->bindParam(':usage_limit', $this->usage_limit);
        $stmt->bindParam(':user_limit', $this->user_limit);
        $stmt->bindParam(':start_at', $this->start_at);
        $stmt->bindParam(':end_at', $this->end_at);
        $stmt->bindParam(':is_active', $this->is_active);
        
        return $stmt->execute();
    }

    /**
     * Xóa promotion
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}
?>
