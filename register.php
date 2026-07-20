<?php
require_once 'config.php';
require_once 'includes/functions.php';
$page_title = 'Create Account';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'An account with this email already exists.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hash')");
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_name'] = $name;
        $_SESSION['is_admin'] = false;
        redirect('index.php');
    }
}

require 'includes/header.php';
?>
<section class="wrap">
    <div class="auth-box">
        <h2>Create account</h2>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="field"><label>Full name</label><input type="text" name="name" required></div>
            <div class="field"><label>Email</label><input type="email" name="email" required></div>
            <div class="field"><label>Password</label><input type="password" name="password" required minlength="6"></div>
            <button type="submit" class="btn btn-primary btn-block">Create account</button>
        </form>
        <p style="font-size:13px;color:#6b6156;margin-top:16px;">Already have an account? <a href="login.php" style="color:var(--teal);">Log in</a></p>
    </div>
</section>
<?php require 'includes/footer.php'; ?>
