<?php
/**
 * FILE: RecipeEntity.php
 * DESCRIPTION: Entity Recipe - Chứa properties từ bảng recipes
 * TABLE: recipes
 * 
 * Bảng recipes lưu công thức sản phẩm (mapping product -> ingredient với định lượng)
 * Mỗi record là 1 dòng nguyên liệu trong công thức của sản phẩm
 * 
 * COLUMNS:
 * - id (INT, PK, auto increment)
 * - product_id (INT, FK -> products.id)
 * - ingredient_id (INT, FK -> ingredients.id)
 * - base_amount (DECIMAL) - Định lượng gốc cho Size M (1.0x)
 */

class RecipeEntity
{
    // Properties từ bảng recipes
    public ?int $id = null;
    public ?int $productId = null;
    public ?int $ingredientId = null;
    public ?float $baseAmount = null;

    // Extra properties for JOIN queries (không có trong DB)
    public ?string $ingredientName = null;
    public ?string $ingredientUnit = null;
    public ?string $productName = null;

    /**
     * Constructor - Khởi tạo entity rỗng hoặc từ array
     * @param array $data - Dữ liệu từ database (optional)
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = isset($data['id']) ? (int)$data['id'] : null;
            $this->productId = isset($data['product_id']) ? (int)$data['product_id'] : null;
            $this->ingredientId = isset($data['ingredient_id']) ? (int)$data['ingredient_id'] : null;
            $this->baseAmount = isset($data['base_amount']) ? (float)$data['base_amount'] : null;

            // Extra fields from JOIN
            $this->ingredientName = $data['ingredient_name'] ?? null;
            $this->ingredientUnit = $data['ingredient_unit'] ?? null;
            $this->productName = $data['product_name'] ?? null;
        }
    }

    /**
     * Chuyển entity thành array
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'ingredient_id' => $this->ingredientId,
            'base_amount' => $this->baseAmount,
            // Extra fields
            'ingredient_name' => $this->ingredientName,
            'ingredient_unit' => $this->ingredientUnit,
            'product_name' => $this->productName,
        ];
    }

    /**
     * Tính định lượng theo size
     * Size S = 0.8x, M = 1.0x (gốc), L = 1.2x
     * @param string $size - 'S', 'M', 'L'
     * @return float
     */
    public function getAmountBySize(string $size = 'M'): float
    {
        $multiplier = match (strtoupper($size)) {
            'S' => 0.8,
            'L' => 1.2,
            default => 1.0, // M
        };
        return round(($this->baseAmount ?? 0) * $multiplier, 2);
    }

    /**
     * Format định lượng với đơn vị
     * @param string $size
     * @return string
     */
    public function getFormattedAmount(string $size = 'M'): string
    {
        $amount = $this->getAmountBySize($size);
        return number_format($amount, 2, ',', '.') . ' ' . ($this->ingredientUnit ?? '');
    }
}
?>
