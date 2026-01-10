<?php
namespace web\Entity;
class VoucherEntity{
    public $id;
    public $name;
    public $point_cost;
    public $discount_type;          
    public $discount_value;
    public $max_discount_value;
    public $min_bill_total;
    public $start_date;
    public $end_date;
    public $quantity;
    public $used_count;
    public $is_active;
    public $created_at;

    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? null;
            $this->point_cost = $data['point_cost'] ?? 0;
            $this->discount_type = $data['discount_type'] ?? 'FIXED';
            $this->discount_value = $data['discount_value'] ?? 0;
            $this->max_discount_value = $data['max_discount_value'] ?? null;
            $this->min_bill_total = $data['min_bill_total'] ?? 0;
            $this->start_date = $data['start_date'] ?? null;
            $this->end_date = $data['end_date'] ?? null;
            $this->quantity = $data['quantity'] ?? null;
            $this->used_count = $data['used_count'] ?? 0;
            $this->is_active = $data['is_active'] ?? true;
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'point_cost' => $this->point_cost,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'max_discount_value' => $this->max_discount_value,
            'min_bill_total' => $this->min_bill_total,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'quantity' => $this->quantity,
            'used_count' => $this->used_count,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at
        ];
    }
}
?>
