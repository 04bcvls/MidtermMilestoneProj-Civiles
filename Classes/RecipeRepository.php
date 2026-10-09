<?php
class RecipeRepository {
    public function __construct(private PDO $pdo) {}

    public function getLatestRecipes(?string $search = null, ?int $categoryId = null): array {
        $sql = 'SELECT r.*, u.username, c.name AS category_name 
                FROM recipes r 
                JOIN users u ON r.user_id = u.id 
                JOIN categories c ON r.category_id = c.id 
                WHERE 1=1';
        $params = [];

        if (!empty($search)) {
            $sql .= ' AND (r.title LIKE :search OR r.description LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($categoryId)) {
            $sql .= ' AND r.category_id = :cat_id';
            $params['cat_id'] = $categoryId;
        }

        $sql .= ' ORDER BY r.created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createRecipe(
        int $userId, 
        int $categoryId, 
        string $title, 
        string $desc, 
        string $instructions, 
        array $ingredients, 
        int $prepTime, 
        int $servings, 
        float $estimatedCost, 
        ?string $image = null
    ): int {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                'INSERT INTO recipes (user_id, category_id, title, description, instructions, prep_time_mins, servings, estimated_cost, image, difficulty) 
                 VALUES (:user_id, :cat_id, :title, :desc, :inst, :prep, :servings, :cost, :img, :diff)'
            );
            $stmt->execute([
                'user_id'  => $userId,
                'cat_id'   => $categoryId,
                'title'    => $title,
                'desc'     => $desc,
                'inst'     => $instructions,
                'prep'     => $prepTime,
                'servings' => $servings,
                'cost'     => $estimatedCost,
                'img'      => $image,
                'diff'     => 'Easy'
            ]);

            $recipeId = (int) $this->pdo->lastInsertId();

            $ingStmt = $this->pdo->prepare('INSERT INTO ingredients (recipe_id, item_name) VALUES (:recipe_id, :item)');
            foreach ($ingredients as $item) {
                $itemClean = trim($item);
                if (!empty($itemClean)) {
                    $ingStmt->execute(['recipe_id' => $recipeId, 'item' => $itemClean]);
                }
            }

            $this->pdo->commit();
            return $recipeId;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}