<?php
class RecipeController extends Controller
{
    public function GetData(): void
    {
        $recipeService = $this->service('RecipeService');
        $ingredientService = $this->service('IngredientService');
        $productService = $this->service('ProductService');

        $selectedProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : null;

        $products = $productService->getAllProducts();
        // Lấy danh sách ingredients (chỉ lấy active)
        $ingredients = $ingredientService->getActiveIngredients();

        $currentRecipe = [];
        $selectedProduct = null;
        
        if ($selectedProductId) {
            $recipes = $recipeService->getRecipesByProductId($selectedProductId);
            $currentRecipe = $this->mapRecipesToViewFormat($recipes);
            
            foreach ($products as $product) {
                if ($product->id == $selectedProductId) {
                    $selectedProduct = $product;
                    break;
                }
            }
        }

        $this->view('AdminDashBoard/MasterLayout', [
            'section' => 'recipe',
            'page' => 'Recipe_v',
            'products' => $products,
            'ingredients' => $ingredients,
            'currentRecipe' => $currentRecipe,
            'selectedProductId' => $selectedProductId,
            'selectedProduct' => $selectedProduct
        ]);
    }

    public function Save(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/RecipeController/GetData');
            return;
        }

        $recipeService = $this->service('RecipeService');
        $ingredientService = $this->service('IngredientService');

        $productId = isset($_POST['txtProductId']) ? (int)$_POST['txtProductId'] : 0;
        $selectedIngredients = isset($_POST['chkIngredient']) ? $_POST['chkIngredient'] : [];
        $quantities = isset($_POST['txtQuantity']) ? $_POST['txtQuantity'] : [];

        if ($productId <= 0) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn sản phẩm!', null);
            return;
        }

        if (empty($selectedIngredients)) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn ít nhất một nguyên liệu!', $productId);
            return;
        }

        $successCount = 0;
        $errorCount = 0;
        $errorMessages = [];

        foreach ($selectedIngredients as $ingredientId) {
            $ingredientId = (int)$ingredientId;
            $quantity = isset($quantities[$ingredientId]) ? (float)$quantities[$ingredientId] : 0;

            if ($quantity <= 0) {
                $ingredient = $ingredientService->getIngredientById($ingredientId);
                $ingredientName = $ingredient ? $ingredient->name : "ID $ingredientId";
                $errorMessages[] = "[$ingredientName] Định lượng phải lớn hơn 0!";
                $errorCount++;
                continue;
            }

            $recipe = new RecipeEntity();
            $recipe->product_id = $productId;
            $recipe->ingredient_id = $ingredientId;
            $recipe->base_amount = $quantity;

            try {
                $recipeService->addRecipe($recipe);
                $successCount++;
            } catch (Exception $e) {
                $ingredient = $ingredientService->getIngredientById($ingredientId);
                $ingredientName = $ingredient ? $ingredient->name : "ID $ingredientId";
                $errorMessages[] = "[$ingredientName] " . $e->getMessage();
                $errorCount++;
            }
        }

        $message = $this->buildResultMessage($successCount, $errorCount, $errorMessages, 'Thêm');
        $this->showAlertAndRedirect($message, $productId);
    }

    public function Update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/RecipeController/GetData');
            return;
        }

        $recipeService = $this->service('RecipeService');
        $ingredientService = $this->service('IngredientService');

        $productId = isset($_POST['txtProductId']) ? (int)$_POST['txtProductId'] : 0;
        $selectedIngredients = isset($_POST['chkIngredient']) ? $_POST['chkIngredient'] : [];
        $quantities = isset($_POST['txtQuantity']) ? $_POST['txtQuantity'] : [];

        if ($productId <= 0) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn sản phẩm!', null);
            return;
        }

        if (empty($selectedIngredients)) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn ít nhất một nguyên liệu!', $productId);
            return;
        }

        $recipes = [];
        $hasError = false;
        
        foreach ($selectedIngredients as $ingredientId) {
            $ingredientId = (int)$ingredientId;
            $quantity = isset($quantities[$ingredientId]) ? (float)$quantities[$ingredientId] : 0;

            if ($quantity <= 0) {
                $hasError = true;
                break;
            }

            $recipe = new RecipeEntity();
            $recipe->product_id = $productId;
            $recipe->ingredient_id = $ingredientId;
            $recipe->base_amount = $quantity;
            $recipes[] = $recipe;
        }

        if ($hasError) {
            $this->showAlertAndRedirect('⚠️ Tất cả nguyên liệu phải có định lượng lớn hơn 0!', $productId);
            return;
        }

        try {
            $recipeService->saveRecipesForProduct($productId, $recipes);
            $this->showAlertAndRedirect('✅ Cập nhật công thức thành công! (' . count($recipes) . ' nguyên liệu)', $productId);
        } catch (Exception $e) {
            $this->showAlertAndRedirect('❌ Lỗi: ' . $e->getMessage(), $productId);
        }
    }

    public function UpdateQuantity(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/RecipeController/GetData');
            return;
        }

        $recipeService = $this->service('RecipeService');

        $productId = isset($_POST['txtProductId']) ? (int)$_POST['txtProductId'] : 0;
        $quantities = isset($_POST['txtUpdateQty']) ? $_POST['txtUpdateQty'] : [];

        if ($productId <= 0) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn sản phẩm!', null);
            return;
        }

        if (empty($quantities)) {
            $this->showAlertAndRedirect('⚠️ Không có định lượng để cập nhật!', $productId);
            return;
        }

        $recipes = $recipeService->getRecipesByProductId($productId);
        
        $successCount = 0;
        $errorCount = 0;

        foreach ($quantities as $ingredientId => $quantity) {
            $ingredientId = (int)$ingredientId;
            $quantity = (float)$quantity;

            if ($quantity <= 0) {
                $errorCount++;
                continue;
            }

            foreach ($recipes as $recipe) {
                if ($recipe->ingredient_id == $ingredientId) {
                    try {
                        $recipe->base_amount = $quantity;
                        $recipeService->updateRecipe($recipe);
                        $successCount++;
                    } catch (Exception $e) {
                        error_log("UpdateQuantity error: " . $e->getMessage());
                        $errorCount++;
                    }
                    break;
                }
            }
        }

        if ($errorCount === 0) {
            $this->showAlertAndRedirect("✅ Cập nhật thành công $successCount định lượng!", $productId);
        } else {
            $this->showAlertAndRedirect("⚠️ Thành công: $successCount, Thất bại: $errorCount", $productId);
        }
    }

    public function Delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /COFFEE_PHP/RecipeController/GetData');
            return;
        }

        $recipeService = $this->service('RecipeService');

        $productId = isset($_POST['txtProductId']) ? (int)$_POST['txtProductId'] : 0;
        $deleteIngredients = isset($_POST['chkDelete']) ? $_POST['chkDelete'] : [];

        if ($productId <= 0) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn sản phẩm!', null);
            return;
        }

        if (empty($deleteIngredients)) {
            $this->showAlertAndRedirect('⚠️ Vui lòng chọn ít nhất một nguyên liệu để xóa!', $productId);
            return;
        }

        $recipes = $recipeService->getRecipesByProductId($productId);
        
        $successCount = 0;
        $errorCount = 0;

        foreach ($deleteIngredients as $ingredientId) {
            $ingredientId = (int)$ingredientId;
            
            foreach ($recipes as $recipe) {
                if ($recipe->ingredient_id == $ingredientId) {
                    $deleted = $recipeService->deleteRecipe($recipe->id);
                    if ($deleted) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                    break;
                }
            }
        }

        if ($errorCount === 0) {
            $this->showAlertAndRedirect("✅ Đã xóa $successCount nguyên liệu khỏi công thức!", $productId);
        } else {
            $this->showAlertAndRedirect("⚠️ Xóa thành công: $successCount, Thất bại: $errorCount", $productId);
        }
    }

    private function mapRecipesToViewFormat(array $recipes): array
    {
        $ingredientService = $this->service('IngredientService');
        $result = [];

        foreach ($recipes as $recipe) {
            $ingredient = $ingredientService->getIngredientById($recipe->ingredient_id);
            
            $result[] = [
                'ingredient_id' => $recipe->ingredient_id,
                'ingredient_name' => $ingredient ? $ingredient->name : 'Không xác định',
                'quantity' => $recipe->base_amount,
                'unit' => $ingredient ? $ingredient->unit : ''
            ];
        }

        return $result;
    }

    private function buildResultMessage(int $successCount, int $errorCount, array $errorMessages, string $action): string
    {
        if ($errorCount === 0) {
            return "✅ $action thành công $successCount công thức!";
        }
        
        $message = "⚠️ $action công thức: Thành công: $successCount, Thất bại: $errorCount";
        
        if (!empty($errorMessages)) {
            $message .= "\\n" . implode("\\n", array_slice($errorMessages, 0, 5));
            if (count($errorMessages) > 5) {
                $message .= "\\n... và " . (count($errorMessages) - 5) . " lỗi khác";
            }
        }
        
        return $message;
    }

    private function showAlertAndRedirect(string $message, ?int $productId = null): void
    {
        $redirectUrl = '/COFFEE_PHP/RecipeController/GetData';
        if ($productId) {
            $redirectUrl .= '?product_id=' . $productId;
        }

        echo "<script>
            alert('$message');
            window.location.href = '$redirectUrl';
        </script>";
    }
}
?>
