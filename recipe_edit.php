<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$recipeId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM recipes WHERE id = :id');
$stmt->execute(['id' => $recipeId]);
$recipe = $stmt->fetch();

if (!$recipe || $recipe['user_id'] != $_SESSION['user_id']) {
    die('Unauthorized access.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = trim($_POST['title'] ?? '');
    $desc          = trim($_POST['description'] ?? '');
    $inst          = trim($_POST['instructions'] ?? '');
    $servings      = (int) ($_POST['servings'] ?? 4);
    $estimatedCost = (float) ($_POST['estimated_cost'] ?? 150.00);

    // Keep existing image as default
    $imageFilename = $recipe['image'];

    // Process new image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowedTypes)) {
            $newFilename = time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $newFilename)) {
                // Delete old image file from server if it exists
                if (!empty($recipe['image']) && file_exists('uploads/' . $recipe['image'])) {
                    unlink('uploads/' . $recipe['image']);
                }
                $imageFilename = $newFilename;
            }
        } else {
            $error = 'Invalid image format. Allowed formats: JPG, PNG, WEBP.';
        }
    }

    if (empty($error) && !empty($title) && !empty($desc) && !empty($inst)) {
        try {
            $update = $pdo->prepare('
                UPDATE recipes 
                SET title = :t, description = :d, instructions = :i, servings = :s, estimated_cost = :c, image = :img, is_edited = 1 
                WHERE id = :id AND user_id = :u
            ');
            $update->execute([
                't'   => $title,
                'd'   => $desc,
                'i'   => $inst,
                's'   => $servings,
                'c'   => $estimatedCost,
                'img' => $imageFilename,
                'id'  => $recipeId,
                'u'   => $_SESSION['user_id']
            ]);

            header("Location: recipe_detail.php?id=$recipeId");
            exit();
        } catch (Throwable $e) {
            $error = 'Failed to update recipe. Please try again.';
        }
    }
}

require_once 'header.php';
?>

<h2>Edit Recipe</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="recipe_edit.php?id=<?= $recipe['id'] ?>" enctype="multipart/form-data">
    <div style="margin-bottom: 16px;">
        <label>Title<span class="req">*</span>:</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($recipe['title']) ?>">
    </div>

    <!-- Attached Image Section (Optional) -->
    <div style="margin-bottom: 16px; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 14px; border-radius: 8px;">
        <label style="font-weight: bold; color: #15803d; display: block; margin-bottom: 6px;">Current Recipe Photo:</label>
        
        <?php if (!empty($recipe['image']) && file_exists('uploads/' . $recipe['image'])): ?>
            <div style="margin-bottom: 10px;">
                <img src="uploads/<?= htmlspecialchars($recipe['image']) ?>" 
                     alt="Current Recipe Photo" 
                     style="max-width: 100%; height: 160px; object-fit: cover; border-radius: 8px; border: 1px solid #cbd5e1; display: block;">
            </div>
        <?php else: ?>
            <p style="font-size: 0.9rem; color: #64748b; margin: 0 0 8px 0;">No photo attached to this recipe yet.</p>
        <?php endif; ?>

        <label for="image" style="font-size: 0.9rem; font-weight: bold;">Replace Photo (Optional):</label>
        <input type="file" id="image" name="image" accept="image/*">
    </div>

    <div style="margin-bottom: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 130px;">
            <label>Base Servings<span class="req">*</span>:</label>
            <input type="number" name="servings" value="<?= (int)$recipe['servings'] ?>" min="1" required>
        </div>
        <div style="flex: 1; min-width: 130px;">
            <label>Est. Cost (₱)<span class="req">*</span>:</label>
            <input type="number" step="0.50" name="estimated_cost" value="<?= (float)$recipe['estimated_cost'] ?>" min="10" required>
        </div>
    </div>

    <div style="margin-bottom: 16px;">
        <label>Description<span class="req">*</span>:</label>
        <textarea name="description" rows="2" required><?= htmlspecialchars($recipe['description']) ?></textarea>
    </div>

    <div style="margin-bottom: 16px;">
        <label>Instructions<span class="req">*</span>:</label>
        <textarea name="instructions" rows="5" required><?= htmlspecialchars($recipe['instructions']) ?></textarea>
    </div>

    <button type="submit">Save Changes</button>
    <a href="recipe_detail.php?id=<?= $recipe['id'] ?>" class="btn btn-secondary" style="margin-left: 8px;">Cancel</a>
</form>

<?php require_once 'footer.php'; ?>