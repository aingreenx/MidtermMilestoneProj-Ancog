<?php
declare(strict_types=1);
require_once 'includes/init.php';
$title = 'Register';
$errors = [];
$username = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $username = trim(post_string('username'));
    $email = trim(post_string('email'));
    try {
        $errors = (new AuthManager(Database::get()))->register($username, $email, post_string('password'), post_string('confirm_password'));
    } catch (InvalidArgumentException $exception) {
        $errors[] = $exception->getMessage();
    }
    if (!$errors) { header('Location: login.php?registered=1'); exit; }
}
require 'includes/header.php';
?>
<section class="card narrow"><h1>Join the Taverna</h1>
<?php foreach($errors as $x): ?><p class="error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post" onsubmit="return validatePasswordConfirmation(this)"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>Username<input name="username" required value="<?= e($username) ?>"></label>
<label>Email<input type="email" name="email" required value="<?= e($email) ?>"></label>
<label>Password<input type="password" name="password" required pattern="(?=.*[A-Za-z])(?=.*\d).{6,}" title="6+ characters with at least one letter and one number"></label>
<label>Confirm password<input type="password" name="confirm_password" required></label>
<button class="btn">Create account</button></form>
<p>Already a member? <a href="login.php">Log in</a></p></section>
<?php require 'includes/footer.php'; ?>
