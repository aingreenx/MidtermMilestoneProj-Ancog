<?php $title='Register'; require 'includes/header.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  try {
    $errors=(new AuthManager)->register(trim($_POST['username']??''),trim($_POST['email']??''),$_POST['password']??'',$_POST['confirm_password']??'');
  } catch (Exception $exception) {
    $errors[]=$exception->getMessage();
  }
  if(!$errors){ header('Location: login.php?registered=1'); exit; }
} ?>
<section class="card narrow"><h1>Join the Taverna</h1>
<?php foreach($errors as $x): ?><p class="error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post" onsubmit="return validatePasswordConfirmation(this)">
<label>Username<input name="username" required value="<?= e($_POST['username']??'') ?>"></label>
<label>Email<input type="email" name="email" required value="<?= e($_POST['email']??'') ?>"></label>
<label>Password<input type="password" name="password" required pattern="(?=.*[A-Za-z])(?=.*\d).{6,}" title="6+ characters with at least one letter and one number"></label>
<label>Confirm password<input type="password" name="confirm_password" required></label>
<button class="btn">Create account</button></form>
<p>Already a member? <a href="login.php">Log in</a></p></section>
<?php require 'includes/footer.php'; ?>
