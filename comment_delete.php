<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$commentId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT recipe_id FROM comments WHERE id = :id AND user_id = :u');
$stmt->execute(['id' => $commentId, 'u' => $_SESSION['user_id']]);
$com = $stmt->fetch();

if ($com) {
    $del = $pdo->prepare('DELETE FROM comments WHERE id = :id AND user_id = :u');
    $del->execute(['id' => $commentId, 'u' => $_SESSION['user_id']]);
    header("Location: recipe_detail.php?id=" . $com['recipe_id']);
    exit();
}

header('Location: index.php');
exit();