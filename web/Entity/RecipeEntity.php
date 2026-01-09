<?php
class RecipeEntity
{
    // Properties từ bảng recipes
    public $id;
    public $product_id;
    public $ingredient_id;
    public $base_amount;

    // Extra properties for JOIN queries (không có trong DB)
    public $ingredient_name;
    public $ingredient_unit;
    public $product_name;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct($data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->product_id = $data['product_id'] ?? null;
            $this->ingredient_id = $data['ingredient_id'] ?? null;
            $this->base_amount = isset($data['base_amount']) ? (float)$data['base_amount'] : null;

            // Extra fields from JOIN
            $this->ingredient_name = $data['ingredient_name'] ?? null;
            $this->ingredient_unit = $data['ingredient_unit'] ?? null;
            $this->product_name = $data['product_name'] ?? null;
        }
    }

    /**
     * Chuyển entity thành array
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'ingredient_id' => $this->ingredient_id,
            'base_amount' => $this->base_amount,
            // Extra fields
            'ingredient_name' => $this->ingredient_name,
            'ingredient_unit' => $this->ingredient_unit,
            'product_name' => $this->product_name,
        ];
    }

    /**
     * Tính định lượng theo size
     * Size S = 0.8x, M = 1.0x (gốc), L = 1.2x
     * @param string $size - 'S', 'M', 'L'
     * @return float
     */
    public function getAmountBySize($size = 'M')
    {
        $multipliers = [
            'S' => 0.8,
            'M' => 1.0,
            'L' => 1.2
        ];
        $multiplier = $multipliers[strtoupper($size)] ?? 1.0;
        return round(($this->base_amount ?? 0) * $multiplier, 2);
    }

    /**
     * Format định lượng với đơn vị
     * @param string $size
     * @return string
     */
    public function getFormattedAmount($size = 'M')
    {
        $amount = $this->getAmountBySize($size);
        return number_format($amount, 2, ',', '.') . ' ' . ($this->ingredient_unit ?? '');
    }
}
?>
