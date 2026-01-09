<?php
/**
 * InventoryCheckEntity - Entity cho bảng inventory_checks
 */
class InventoryCheckEntity {
    private $id;
    private $ingredient;
    private $theoryQuantity;
    private $actualQuantity;
    private $difference;
    private $note;
    private $checked_at;

    /**
     * Constructor
     * @param array|null $data Dữ liệu từ database
     */
    public function __construct($data = null) {
        if ($data) {
            $this->id = $data['id'] ?? null;
            $this->ingredient = $data['ingredient'] ?? null;
            $this->theoryQuantity = $data['theoryQuantity'] ?? 0.00;
            $this->actualQuantity = $data['actualQuantity'] ?? 0.00;
            $this->difference = $data['difference'] ?? 0.00;
            $this->note = $data['note'] ?? null;
            $this->checked_at = $data['checked_at'] ?? null;
        }
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getIngredient() {
        return $this->ingredient;
    }

    public function getTheoryQuantity() {
        return $this->theoryQuantity;
    }

    public function getActualQuantity() {
        return $this->actualQuantity;
    }

    public function getDifference() {
        return $this->difference;
    }

    public function getNote() {
        return $this->note;
    }

    public function getCheckedAt() {
        return $this->checked_at;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setIngredient($ingredient) {
        $this->ingredient = $ingredient;
    }

    public function setTheoryQuantity($theoryQuantity) {
        $this->theoryQuantity = $theoryQuantity;
    }

    public function setActualQuantity($actualQuantity) {
        $this->actualQuantity = $actualQuantity;
    }

    public function setDifference($difference) {
        $this->difference = $difference;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function setCheckedAt($checked_at) {
        $this->checked_at = $checked_at;
    }

    /**
     * Chuyển entity thành array
     * @return array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'ingredient' => $this->ingredient,
            'theoryQuantity' => $this->theoryQuantity,
            'actualQuantity' => $this->actualQuantity,
            'difference' => $this->difference,
            'note' => $this->note,
            'checked_at' => $this->checked_at
        ];
    }
}
