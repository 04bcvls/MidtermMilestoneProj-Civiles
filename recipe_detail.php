<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$recipeId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('
    SELECT r.*, u.username, c.name AS category_name 
    FROM recipes r 
    JOIN users u ON r.user_id = u.id 
    JOIN categories c ON r.category_id = c.id 
    WHERE r.id = :id
');
$stmt->execute(['id' => $recipeId]);
$recipe = $stmt->fetch();

if (!$recipe) {
    die('Recipe not found.');
}

$ingStmt = $pdo->prepare('SELECT * FROM ingredients WHERE recipe_id = :id');
$ingStmt->execute(['id' => $recipeId]);
$ingredients = $ingStmt->fetchAll();

$comStmt = $pdo->prepare('
    SELECT c.*, u.username 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.recipe_id = :id 
    ORDER BY c.created_at ASC
');
$comStmt->execute(['id' => $recipeId]);
$comments = $comStmt->fetchAll();

$favStmt = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = :u AND recipe_id = :r');
$favStmt->execute(['u' => $_SESSION['user_id'], 'r' => $recipeId]);
$isFavorited = (bool) $favStmt->fetch();

require_once 'header.php';
?>

<!-- Hero Header Section -->
<div class="recipe-hero">
    <div class="hero-image-box">
        <?php if (!empty($recipe['image']) && file_exists('uploads/' . $recipe['image'])): ?>
            <img src="uploads/<?= htmlspecialchars($recipe['image']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>">
        <?php else: ?>
            <div class="hero-placeholder">🍲</div>
        <?php endif; ?>
    </div>

    <div class="hero-info-box">
        <div>
            <span class="badge"><?= htmlspecialchars($recipe['category_name']) ?></span>
            <h1><?= htmlspecialchars($recipe['title']) ?></h1>
            <p><?= htmlspecialchars($recipe['description']) ?></p>
        </div>

        <!-- Stat Grid -->
        <div class="stat-grid">
            <div class="stat-card">
                <span class="stat-icon">⏱️</span>
                <span class="stat-val"><?= (int)$recipe['prep_time_mins'] ?> Mins</span>
                <span class="stat-lbl">Cooking Time</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📊</span>
                <span class="stat-val"><?= htmlspecialchars($recipe['difficulty']) ?></span>
                <span class="stat-lbl">Difficulty</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">👥</span>
                <span class="stat-val">
                    <button type="button" id="btnMinus" class="btn btn-secondary btn-mini">-</button>
                    <span id="currentServings"><?= (int)$recipe['servings'] ?></span>
                    <button type="button" id="btnPlus" class="btn btn-secondary btn-mini">+</button>
                </span>
                <span class="stat-lbl">Serves</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">₱</span>
                <span id="costPerPlate" class="stat-val">₱<?= number_format($recipe['estimated_cost'] / max(1, $recipe['servings']), 2) ?></span>
                <span class="stat-lbl">Per Plate</span>
            </div>
        </div>

        <div>
            <div class="meta" style="margin-bottom: 14px; display: flex; align-items: center; flex-wrap: wrap; gap: 6px;">
                <span>Submitted by <strong><?= htmlspecialchars($recipe['username']) ?></strong> on <?= $recipe['created_at'] ?></span>
                <?php if ($recipe['is_edited']): ?>
                    <span class="edited-tag">(edited on <?= $recipe['updated_at'] ?>)</span>
                <?php endif; ?>

                <!-- Restyled Ownership Actions -->
                <?php if ($recipe['user_id'] == $_SESSION['user_id']): ?>
                    <span class="action-links">
                        <a href="recipe_edit.php?id=<?= $recipe['id'] ?>" class="btn-action btn-action-edit">✏️ Edit Recipe</a>
                        <a href="recipe_delete.php?id=<?= $recipe['id'] ?>" onclick="return confirm('Delete this recipe?')" class="btn-action btn-action-delete">🗑️ Delete</a>
                    </span>
                <?php endif; ?>
            </div>

            <button id="favBtn" class="btn <?= $isFavorited ? 'btn-danger' : '' ?>" style="width: 100%;">
                <?= $isFavorited ? '❤️ Saved in Favorites' : '🤍 Add to Favorites' ?>
            </button>
        </div>
    </div>
</div>

<!-- Two-Column Recipe Details -->
<div class="recipe-body-grid">
    <!-- Left Column: Ingredients -->
    <div class="content-card">
        <h3>Mga Sangkap (Ingredients)</h3>
        <ul class="ingredient-list">
            <?php foreach ($ingredients as $ing): ?>
                <li><span class="leaf-icon">🌿</span> <?= htmlspecialchars($ing['item_name']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Right Column: Cooking Instructions -->
    <div class="content-card">
        <h3>Paraan ng Pagluluto (Instructions)</h3>
        <div style="line-height: 1.6; font-size: 0.95rem; color: #334155;">
            <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
        </div>
    </div>
</div>

<!-- Comments Section -->
<div style="margin-top: 32px; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid var(--border);">
    <h3>Feedback & Comments</h3>

    <?php foreach ($comments as $com): ?>
        <div style="background: var(--card-subtle); padding: 14px; border-radius: 8px; margin-bottom: 12px; border: 1px solid #e0ebe3;">
            <p style="margin: 0 0 8px 0; font-size: 0.95rem;"><?= nl2br(htmlspecialchars($com['content'])) ?></p>
            <div class="meta" style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px;">
                <span>By <strong><?= htmlspecialchars($com['username']) ?></strong> on <?= $com['created_at'] ?></span>
                <?php if ($com['is_edited']): ?>
                    <span class="edited-tag">(edited)</span>
                <?php endif; ?>

                <!-- Restyled Comment Actions -->
                <?php if ($com['user_id'] == $_SESSION['user_id']): ?>
                    <span class="action-links">
                        <a href="comment_edit.php?id=<?= $com['id'] ?>" class="btn-action btn-action-edit">✏️ Edit</a>
                        <a href="comment_delete.php?id=<?= $com['id'] ?>" onclick="return confirm('Delete comment?')" class="btn-action btn-action-delete">🗑️ Delete</a>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <form method="POST" action="comment_create.php" style="margin-top: 16px;">
        <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
        <textarea name="content" rows="3" placeholder="Leave feedback or cooking tips..." required></textarea>
        <button type="submit" style="margin-top: 8px;">Post Comment</button>
    </form>
</div>

<script>
    // AJAX Favorite Toggle
    document.getElementById('favBtn').addEventListener('click', function() {
        const btn = this;
        fetch('toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'recipe_id=<?= $recipe['id'] ?>'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'added') {
                    btn.textContent = '❤️ Saved in Favorites';
                    btn.classList.add('btn-danger');
                } else if (data.status === 'removed') {
                    btn.textContent = '🤍 Add to Favorites';
                    btn.classList.remove('btn-danger');
                }
            });
    });

    // Dynamic Servings & Cost Scaler
    const baseServings = <?= (int)$recipe['servings'] ?>;
    const baseCost = <?= (float)$recipe['estimated_cost'] ?>;
    let currentServings = baseServings;

    const servingsSpan = document.getElementById('currentServings');
    const costSpan = document.getElementById('costPerPlate');

    function updateCalculator() {
        servingsSpan.textContent = currentServings;
        const costPerServing = baseCost / currentServings;
        costSpan.textContent = '₱' + costPerServing.toFixed(2);
    }

    document.getElementById('btnPlus').addEventListener('click', function() {
        currentServings++;
        updateCalculator();
    });

    document.getElementById('btnMinus').addEventListener('click', function() {
        if (currentServings > 1) {
            currentServings--;
            updateCalculator();
        }
    });
</script>

<?php require_once 'footer.php'; ?>