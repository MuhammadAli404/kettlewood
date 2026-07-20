<?php
require_once '../config.php';
require_once '../includes/functions.php';
define('BASE_URL_ADMIN', '..');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND is_admin = 1 LIMIT 1");
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Incorrect admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><title>Admin Login — Kettlewood</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body style="background:var(--charcoal);">
<section class="wrap">
    <div class="auth-box" style="margin-top:100px;">
        <h2>Admin Login</h2>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="field"><label>Email</label><input type="email" name="email" required></div>
            <div class="field"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>
        <p style="font-size:12px;color:#a89e8f;margin-top:20px;">Demo: admin@kettlewood.test / admin123</p>
    </div>
</section>
</body>
</html>
