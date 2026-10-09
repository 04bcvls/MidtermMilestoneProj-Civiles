<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'Classes/RecipeRepository.php';
require_login();

$error = '';
$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title'] ?? '');
    $desc          = trim($_POST['description'] ?? '');
    $inst          = trim($_POST['instructions'] ?? '');
    $catId         = (int) ($_POST['category_id'] ?? 0);
    $prepTime      = (int) ($_POST['prep_time'] ?? 30);
    $servings      = (int) ($_POST['servings'] ?? 4);
    $estimatedCost = (float) ($_POST['estimated_cost'] ?? 150.00);
    $ingredients   = $_POST['ingredients'] ?? [];

    // Image Upload Handling
    $imageFilename = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowedTypes)) {
            $imageFilename = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $imageFilename);
        } else {
            $error = 'Invalid image format. Allowed formats: JPG, PNG, WEBP.';
        }
    }

    if (empty($error)) {
        if (empty($title) || empty($desc) || empty($inst) || $catId <= 0 || empty(array_filter($ingredients))) {
            $error = 'Please fill in all required fields and provide at least one ingredient.';
        } else {
            try {
                $repo = new RecipeRepository($pdo);
                $recipeId = $repo->createRecipe(
                    $_SESSION['user_id'], $catId, $title, $desc, $inst, 
                    $ingredients, $prepTime, $servings, $estimatedCost, $imageFilename
                );

                header("Location: recipe_detail.php?id=$recipeId");
                exit();
            } catch (Throwable $e) {
                $error = 'Error saving recipe: ' . $e->getMessage();
            }
        }
    }
}

require_once 'header.php';
?>
<h2>Share a Nutri-Sarap Recipe</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="recipe_create.php" enctype="multipart/form-data">
    <div style="margin-bottom: 12px;">
        <label>Recipe Title<span class="req">*</span>:</label>
        <input type="text" name="title" required maxlength="150" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    </div>

    <div style="margin-bottom: 12px;">
        <label>Category<span class="req">*</span>:</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 12px;">
        <label>Recipe Photo (Optional):</label>
        <input type="file" name="image" accept="image/*">
    </div>

    <div style="margin-bottom: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 130px;">
            <label>Prep Time (Mins)<span class="req">*</span>:</label>
            <input type="number" name="prep_time" value="30" min="5" required>
        </div>
        <div style="flex: 1; min-width: 130px;">
            <label>Base Servings<span class="req">*</span>:</label>
            <input type="number" name="servings" value="4" min="1" required>
        </div>
        <div style="flex: 1; min-width: 130px;">
            <label>Est. Cost (₱)<span class="req">*</span>:</label>
            <input type="number" step="0.50" name="estimated_cost" value="150.00" min="10" required>
        </div>
    </div>

    <div style="margin-bottom: 12px;">
        <label>Short Description<span class="req">*</span>:</label>
        <textarea name="description" rows="2" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div style="margin-bottom: 12px;">
        <label>Ingredients<span class="req">*</span>:</label>
        <div id="ingredientsList">
            <input type="text" name="ingredients[]" placeholder="e.g., 1/2 kg Baboy (Liempo)" required style="margin-bottom: 8px;">
            <input type="text" name="ingredients[]" placeholder="e.g., 1 tasa Suka" style="margin-bottom: 8px;">
        </div>
        <button type="button" id="addIngBtn" class="btn btn-secondary" style="font-size: 0.8rem; padding: 6px 12px;">+ Add Ingredient</button>
    </div>

    <div style="margin-bottom: 12px;">
        <label>Cooking Steps / Instructions<span class="req">*</span>:</label>
        <textarea name="instructions" rows="5" required><?= htmlspecialchars($_POST['instructions'] ?? '') ?></textarea>
    </div>

    <button type="submit">Publish Recipe</button>
</form>

<script>
    document.getElementById('addIngBtn').addEventListener('click', function() {
        const container = document.getElementById('ingredientsList');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'ingredients[]';
        input.placeholder = 'e.g., Next ingredient item';
        input.style.marginBottom = '8px';
        container.appendChild(input);
    });
</script>

<?php require_once 'footer.php'; ?>