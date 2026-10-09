<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$stmt = $pdo->prepare('
    SELECT r.*, u.username, c.name AS category_name 
    FROM favorites f 
    JOIN recipes r ON f.recipe_id = r.id 
    JOIN users u ON r.user_id = u.id 
    JOIN categories c ON r.category_id = c.id 
    WHERE f.user_id = :u 
    ORDER BY f.created_at DESC
');
$stmt->execute(['u' => $_SESSION['user_id']]);
$favorites = $stmt->fetchAll();

require_once 'header.php';
?>
<h2>My Favorite Recipes</h2>

<div class="grid">
    <?php if (empty($favorites)): ?>
        <p>You haven't favorited any recipes yet.</p>
    <?php endif; ?>

    <?php foreach ($favorites as $r): ?>
        <div class="card">
            <?php if (!empty($r['image']) && file_exists('uploads/' . $r['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
            <?php else: ?>
                <div style="background: #e2e8f0; height: 180px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 12px;">🍲</div>
            <?php endif; ?>

            <h3><?= htmlspecialchars($r['title']) ?></h3>

            <div style="margin-bottom: 8px;">
                <span class="badge"><?= htmlspecialchars($r['category_name']) ?></span>
                <span class="badge badge-amber">⏱️ <?= (int)$r['prep_time_mins'] ?> mins</span>
                <span class="badge badge-blue">₱<?= number_format($r['estimated_cost'] / max(1, $r['servings']), 2) ?>/plate</span>
            </div>

            <p style="font-size: 0.9rem; color: #475569; margin: 8px 0;"><?= htmlspecialchars($r['description']) ?></p>

            <div class="meta">
                Posted by <strong><?= htmlspecialchars($r['username']) ?></strong>
            </div>

            <a href="recipe_detail.php?id=<?= $r['id'] ?>" class="btn" style="margin-top: 10px; width: 100%; text-align: center; box-sizing: border-box;">View Full Recipe</a>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>