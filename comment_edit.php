<?php
require_once 'db.php';
require_once 'auth.php';
require_login();

$commentId = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM comments WHERE id = :id');
$stmt->execute(['id' => $commentId]);
$com = $stmt->fetch();

if (!$com || $com['user_id'] != $_SESSION['user_id']) {
    die('Unauthorized access.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
    if (!empty($content)) {
        $up = $pdo->prepare('UPDATE comments SET content = :c, is_edited = 1 WHERE id = :id AND user_id = :u');
        $up->execute(['c' => $content, 'id' => $commentId, 'u' => $_SESSION['user_id']]);

        header("Location: recipe_detail.php?id=" . $com['recipe_id']);
        exit();
    }
}

require_once 'header.php';
?>
<h2>Edit Comment</h2>
<form method="POST" action="comment_edit.php?id=<?= $com['id'] ?>">
    <textarea name="content" rows="3" required><?= htmlspecialchars($com['content']) ?></textarea>
    <button type="submit" style="margin-top: 8px;">Update Comment</button>
</form>
<?php require_once 'footer.php'; ?>