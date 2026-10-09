<?php
declare(strict_types=1);
require_once __DIR__ . '/init.php';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Greek Recipe Hub') ?> | Greek Recipe Hub</title>
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<link rel="stylesheet" href="assets/style.css"></head><body>
<nav class="topnav"><a class="brand" href="index.php">Greek Recipe Hub</a>
<?php if (uid()): ?><div class="links"><a href="index.php">Recipes</a><a href="index.php?fav=1">Favorites</a><a href="recipe_form.php">Share</a>
<form method="post" action="logout.php" class="inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="link">Logout (<?= e($_SESSION['username']) ?>)</button></form></div><?php endif; ?></nav>
<main class="wrap">
