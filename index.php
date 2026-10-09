<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'Classes/RecipeRepository.php';
require_login();

$repo = new RecipeRepository($pdo);
// Fetch all recipes initially so client-side JavaScript can dynamically search/filter them
$recipes = $repo->getLatestRecipes();
$categories = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();

require_once 'header.php';
?>
<h2>Barangay Recipes Feed</h2>

<!-- Dynamic Client-Side Search & Filter Bar -->
<div style="margin-bottom: 24px; display: flex; gap: 10px; flex-wrap: wrap;">
    <input type="text" 
           id="searchInput" 
           placeholder="Search recipe title or keyword..." 
           style="flex: 2; min-width: 200px;">

    <select id="categorySelect" style="flex: 1; min-width: 150px;">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="button" id="resetBtn" class="btn btn-secondary">Reset Filters</button>
</div>

<!-- Message shown when dynamic search yields no matches -->
<div id="noResultsMsg" style="display: none; text-align: center; padding: 20px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #991b1b; margin-bottom: 20px;">
    No recipes found matching your search criteria.
</div>

<div class="grid" id="recipesGrid">
    <?php if (empty($recipes)): ?>
        <p>No recipes found. Be the first to share one!</p>
    <?php endif; ?>

    <?php foreach ($recipes as $r): ?>
        <!-- Data attributes attached to feed cards for DOM searching -->
        <div class="card recipe-card" 
             data-title="<?= htmlspecialchars(strtolower($r['title'])) ?>" 
             data-desc="<?= htmlspecialchars(strtolower($r['description'])) ?>" 
             data-category="<?= $r['category_id'] ?>">
            
            <?php if (!empty($r['image']) && file_exists('uploads/' . $r['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['title']) ?>">
            <?php else: ?>
                <div style="background: #e2e8f0; height: 160px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 12px;">🍲</div>
            <?php endif; ?>

            <h3><?= htmlspecialchars($r['title']) ?></h3>
            <span class="badge"><?= htmlspecialchars($r['category_name']) ?></span>
            <span class="badge badge-amber">⏱️ <?= (int)$r['prep_time_mins'] ?> mins</span>
            <span class="badge badge-blue">₱<?= number_format($r['estimated_cost'] / max(1, $r['servings']), 2) ?>/plate</span>
            
            <p><?= htmlspecialchars($r['description']) ?></p>
            
            <div class="meta">
                Posted by <strong><?= htmlspecialchars($r['username']) ?></strong> on <?= $r['created_at'] ?>
                <?php if ($r['is_edited']): ?><span class="edited-tag">(edited)</span><?php endif; ?>
            </div>

            <a href="recipe_detail.php?id=<?= $r['id'] ?>" class="btn" style="margin-top: 10px; width: 100%; text-align: center; box-sizing: border-box;">View Full Recipe</a>
        </div>
    <?php endforeach; ?>
</div>

<!-- Dynamic Search & Filter Script using .includes() -->
<script>
    const searchInput    = document.getElementById('searchInput');
    const categorySelect = document.getElementById('categorySelect');
    const resetBtn       = document.getElementById('resetBtn');
    const recipeCards    = document.querySelectorAll('.recipe-card');
    const noResultsMsg   = document.getElementById('noResultsMsg');

    function filterRecipes() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCategory = categorySelect.value;
        let visibleCount = 0;

        recipeCards.forEach(card => {
            const title    = card.getAttribute('data-title') || '';
            const desc     = card.getAttribute('data-desc') || '';
            const category = card.getAttribute('data-category') || '';

            // Using JavaScript .includes() to check substring matches in title or description
            const matchesSearch   = title.includes(query) || desc.includes(query);
            const matchesCategory = selectedCategory === '' || category === selectedCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Toggle no-results feedback message
        if (noResultsMsg) {
            noResultsMsg.style.display = (visibleCount === 0 && recipeCards.length > 0) ? 'block' : 'none';
        }
    }

    // Attach event listeners for real-time dynamic filtering
    searchInput.addEventListener('input', filterRecipes);
    categorySelect.addEventListener('change', filterRecipes);

    // Reset button handler
    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        categorySelect.value = '';
        filterRecipes();
    });
</script>

<?php require_once 'footer.php'; ?>