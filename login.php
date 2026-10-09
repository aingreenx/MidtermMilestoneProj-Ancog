<?php
declare(strict_types=1);
require_once 'includes/init.php';
$title = 'Login';
if (uid()) { header('Location: index.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    if ((new AuthManager(Database::get()))->login(trim(post_string('username')), post_string('password'))) {
        header('Location: index.php');
        exit;
    }
    $err = 'Wrong username or password.';
}
$registered = query_string('registered') === '1';
require 'includes/header.php';
?>
<section class="card narrow"><h1>Welcome back</h1>
<?php if($registered): ?><p class="ok">Account created. Please log in.</p><?php endif; ?>
<?php if($err): ?><p class="error"><?= e($err) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>Username<input name="username" required></label>
<label>Password<input type="password" name="password" required></label>
<button class="btn">Log in</button></form>
<p>New here? <a href="register.php">Create an account</a></p></section>
<?php require 'includes/footer.php'; ?>
