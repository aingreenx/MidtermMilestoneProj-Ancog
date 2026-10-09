<?php $title='Login'; require 'includes/header.php';
if (uid()) { header('Location: index.php'); exit; }
$err='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if ((new AuthManager)->login(trim($_POST['username']??''),$_POST['password']??'')) { header('Location: index.php'); exit; }
  $err='Wrong username or password.';
} ?>
<section class="card narrow"><h1>Welcome back</h1>
<?php if(isset($_GET['registered'])): ?><p class="ok">Account created. Please log in.</p><?php endif; ?>
<?php if($err): ?><p class="error"><?= e($err) ?></p><?php endif; ?>
<form method="post"><label>Username<input name="username" required></label>
<label>Password<input type="password" name="password" required></label>
<button class="btn">Log in</button></form>
<p>New here? <a href="register.php">Create an account</a></p></section>
<?php require 'includes/footer.php'; ?>
