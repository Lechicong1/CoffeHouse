<?php

class Payment {
    // Properties từ ERD - Bảng payments
    public $id;
    public $order_id;
    public $payment_method; // ENUM: 'cash', 'card', 'momo', 'bank_transfer'
    public $amount;
    public $status; // ENUM: 'pending', 'completed', 'failed', 'refunded'
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
            $this->amount = $data['amount'] ?? 0;
            $this->status = $data['status'] ?? 'pending';
            $this->transaction_code = $data['transaction_code'] ?? null;
            $this->paid_at = $data['paid_at'] ?? null;
        }
    }
}
?>
