<?php
require_once 'db.php';
require_once 'auth.php';
require_guest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = 'Email is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (:u, :e, :p)');
                $stmt->execute(['u' => $username, 'e' => $email, 'p' => $hash]);

                header('Location: login.php?registered=1');
                exit();
            }
        } catch (Throwable $e) {
            $error = 'Registration failed. Try again.';
        }
    }
}

require_once 'header.php';
?>
<h2>Join the Barangay Recipe Exchange</h2>
<?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="POST" action="register.php">
    <div style="margin-bottom: 12px;">
        <label>Username<span class="req">*</span>:</label>
        <input type="text" name="username" required maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div style="margin-bottom: 12px;">
        <label>Email Address<span class="req">*</span>:</label>
        <input type="email" name="email" required maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div style="margin-bottom: 12px;">
        <label>Password<span class="req">*</span>:</label>
        <input type="password" name="password" required minlength="6">
    </div>
    <button type="submit">Create Account</button>
</form>
<?php require_once 'footer.php'; ?>