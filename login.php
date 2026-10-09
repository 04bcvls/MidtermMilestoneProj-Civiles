<?php
require_once 'db.php';
require_once 'auth.php';
require_guest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE email = :e');
        $stmt->execute(['e' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Mitigates session hijacking (Topic 5)
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: index.php');
            exit();
        } else {
            $error = 'Invalid email or password credentials.';
        }
    }
}

require_once 'header.php';
?>
<h2>Member Login</h2>
<?php if (isset($_GET['registered'])): ?>
    <div style="background: #064e3b; color: #4ade80; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
        Registration successful! Log in below.
    </div>
<?php endif; ?>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="login.php">
    <div style="margin-bottom: 12px;">
        <label>Email Address<span class="req">*</span>:</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div style="margin-bottom: 12px;">
        <label>Password<span class="req">*</span>:</label>
        <input type="password" name="password" required>
    </div>
    <button type="submit">Log In</button>
</form>
<?php require_once 'footer.php'; ?>