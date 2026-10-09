<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$recipeId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('DELETE FROM recipes WHERE id = :id AND user_id = :u');
$stmt->execute(['id' => $recipeId, 'u' => $_SESSION['user_id']]);

header('Location: index.php');
exit();