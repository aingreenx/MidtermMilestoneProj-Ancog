<?php require 'includes/init.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST') (new RecipeManager)->delete((int)$_POST['id'],uid());
header('Location: index.php');
