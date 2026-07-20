<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Log In';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
    $user = mysqli_fetch_assoc($res);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];
        redirect($user['is_admin'] ? 'admin/index.php' : 'index.php');
    } else {
        $error = 'Incorrect email or password.';
    }
}

require 'includes/header.php';
?>
<section class="wrap">
    <div class="auth-box">
        <h2>Log in</h2>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="field"><label>Email</label><input type="email" name="email" required></div>
            <div class="field"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" class="btn btn-primary btn-block">Log in</button>
        </form>
        <p style="font-size:13px;color:#6b6156;margin-top:16px;">No account? <a href="register.php" style="color:var(--teal);">Create one</a></p>
        <p style="font-size:12px;color:#a89e8f;margin-top:20px;">Admin demo login: admin@kettlewood.test / admin123</p>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
