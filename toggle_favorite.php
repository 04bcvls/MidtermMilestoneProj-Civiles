<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

header('Content-Type: application/json');

$recipeId = (int) ($_POST['recipe_id'] ?? 0);
$userId   = $_SESSION['user_id'];

if ($recipeId <= 0) {
    echo json_encode(['status' => 'error']);
    exit();
}

$checkStmt = $pdo->prepare('SELECT 1 FROM favorites WHERE user_id = :u AND recipe_id = :r');
$checkStmt->execute(['u' => $userId, 'r' => $recipeId]);

if ($checkStmt->fetch()) {
    // Remove from favorites
    $del = $pdo->prepare('DELETE FROM favorites WHERE user_id = :u AND recipe_id = :r');
    $del->execute(['u' => $userId, 'r' => $recipeId]);
    echo json_encode(['status' => 'removed']);
} else {
    // Add to favorites
    $add = $pdo->prepare('INSERT INTO favorites (user_id, recipe_id) VALUES (:u, :r)');
    $add->execute(['u' => $userId, 'r' => $recipeId]);
    echo json_encode(['status' => 'added']);
}