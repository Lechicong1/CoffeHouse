<?php
/**
 * FILE: PaymentEntity.php
 * DESCRIPTION: Entity Payment - Chứa properties từ bảng payments
 * TABLE: payments
 * AUTHOR: Coffee House System
 */
namespace web\Models\Entity;
class PaymentEntity {
    // Properties từ bảng payments
    public $id;
    public $order_id;
    public $payment_method; // cash, banking
    public $amount;
    public $status; // unpaid, paid, refunded
    public $transaction_code;
    public $paid_at;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->order_id = $data['order_id'] ?? null;
            $this->payment_method = $data['payment_method'] ?? 'cash';
            $this->amount = $data['amount'] ?? null;
            $this->status = $data['status'] ?? 'unpaid';
            $this->transaction_code = $data['transaction_code'] ?? null;
            $this->paid_at = $data['paid_at'] ?? null;
        }
    }
}
?>
