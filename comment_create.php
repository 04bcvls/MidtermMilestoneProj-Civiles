<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipeId = (int) ($_POST['recipe_id'] ?? 0);
    $content  = trim($_POST['content'] ?? '');

    if ($recipeId > 0 && !empty($content)) {
        $stmt = $pdo->prepare('INSERT INTO comments (recipe_id, user_id, content) VALUES (:r, :u, :c)');
        $stmt->execute(['r' => $recipeId, 'u' => $_SESSION['user_id'], 'c' => $content]);
    }
}

header("Location: recipe_detail.php?id=$recipeId");
exit();