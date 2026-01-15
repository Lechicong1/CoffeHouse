<?php
include_once './web/Entity/RecipeEntity.php';

class RecipeService extends Service
{
    private RecipeRepository $recipeRepo;
    private IngredientRepository $ingredientRepo;

    public function __construct()
    {
        $this->recipeRepo = $this->repository('RecipeRepository');
        $this->ingredientRepo = $this->repository('IngredientRepository');
    }

    public function getRecipesByProductId(int $productId): array
    {
        return $this->recipeRepo->getByProductId($productId);
    }

    public function addRecipe(RecipeEntity $recipe): void
    {
        $this->validateRecipeData($recipe);

        if ($this->recipeRepo->exists($recipe->product_id, $recipe->ingredient_id, 0)) {
            throw new Exception("Công thức cho nguyên liệu này đã tồn tại!");
        }

        $result = $this->recipeRepo->create($recipe);
        if ($result <= 0) {
            throw new Exception("Thêm công thức thất bại!");
        }
    }

    public function updateRecipe(RecipeEntity $recipe): void
    {
        $this->validateRecipeData($recipe);

        if (!$recipe->id || $recipe->id <= 0) {
            throw new Exception("Dữ liệu công thức không hợp lệ!");
        }

        if ($this->recipeRepo->exists($recipe->product_id, $recipe->ingredient_id, $recipe->id)) {
            throw new Exception("Công thức bị trùng lặp với công thức khác!");
        }

        $result = $this->recipeRepo->update($recipe);
        if (!$result) {
            throw new Exception("Cập nhật công thức thất bại!");
        }
    }

    public function deleteRecipe(int $id): bool
    {
        return $this->recipeRepo->delete($id);
    }

    public function saveRecipesForProduct(int $productId, array $recipes): void
    {
        if ($productId <= 0) {
            throw new Exception("Dữ liệu công thức không hợp lệ!");
        }

        foreach ($recipes as $recipe) {
            if (!$recipe->ingredient_id || $recipe->ingredient_id <= 0) {
                throw new Exception("Dữ liệu công thức không hợp lệ!");
            }
            if (!$recipe->base_amount || $recipe->base_amount <= 0) {
                throw new Exception("Dữ liệu công thức không hợp lệ!");
            }
        }

        $result = $this->recipeRepo->saveRecipeForProduct($productId, $recipes);
        if (!$result) {
            throw new Exception("Lưu công thức thất bại!");
        }
    }

    public function getRecipeById(int $id): ?RecipeEntity
    {
        return $this->recipeRepo->findById($id);
    }

    private function validateRecipeData(RecipeEntity $recipe): void
    {
        if (!$recipe->product_id || $recipe->product_id <= 0) {
            throw new Exception("Dữ liệu công thức không hợp lệ!");
        }

        if (!$recipe->ingredient_id || $recipe->ingredient_id <= 0) {
            throw new Exception("Dữ liệu công thức không hợp lệ!");
        }

        if ($recipe->base_amount === null || $recipe->base_amount <= 0) {
            throw new Exception("Dữ liệu công thức không hợp lệ!");
        }

        // Check if ingredient exists
        $ingredient = $this->ingredientRepo->findById($recipe->ingredient_id);
        if (!$ingredient) {
            throw new Exception("Nguyên liệu không tồn tại!");
        }
    }
}
?>
