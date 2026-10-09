<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();
$rm = new RecipeManager(Database::get());
$r = $rm->find(query_int('id'));
if (!$r) {
	http_response_code(404);
	$title = 'Recipe not found';
	require 'includes/header.php';
	echo '<p>Recipe not found.</p>';
	require 'includes/footer.php';
	exit;
}
$title = (string)$r['title'];
$mine = (int)$r['user_id'] === uid();
$isFav = $rm->isFavorite(uid(), (int)$r['id']);
require 'includes/header.php';
?>
<article class="card recipe-container">
<span class="tag"><?= e($r['category']) ?></span>
<h1><?= e($r['title']) ?></h1>
<small>by <?= e($r['username']) ?> &middot; <?= e(date('M j, Y',strtotime($r['created_at']))) ?><?= edited_tag($r) ?></small>
<p><?= nl2br(e($r['description'])) ?></p>
<div class="recipe-actions"><button class="btn fav-btn" data-id="<?= e($r['id']) ?>"><?= $isFav?'Remove from favorites':'Save to favorites' ?></button>
<?php if($mine): ?><a class="btn alt" href="recipe_form.php?id=<?= e($r['id']) ?>">Edit</a>
<form class="inline" method="post" action="recipe_delete.php" onsubmit="return confirm('Delete this recipe?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($r['id']) ?>"><button class="btn danger">Delete</button></form><?php endif; ?></div>
<button type="button" class="btn print-btn" onclick="window.print()">🖨️ Print Recipe</button>
<h2>Ingredients</h2>
<div class="yield">Yield: <button type="button" class="btn alt mult" data-m="1">1x</button><button type="button" class="btn alt mult" data-m="2">2x</button><button type="button" class="btn alt mult" data-m="3">3x</button></div>
<ul id="ingredients"><?php foreach($rm->ingredients($r['id']) as $i): ?>
<li><span class="amt" data-base="<?= e($i['amount']) ?>"><?= e(rtrim(rtrim($i['amount'],'0'),'.')) ?></span> <?= e($i['unit']) ?> <?= e($i['name']) ?></li><?php endforeach; ?></ul>
<h2>Steps</h2><p><?= nl2br(e($r['steps'])) ?></p></article>
<section class="card comments-section"><h2>Comments</h2>
<form method="post" action="comment.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="add"><input type="hidden" name="recipe_id" value="<?= e($r['id']) ?>">
<textarea name="body" required placeholder="Share your thoughts..."></textarea><button class="btn">Post comment</button></form>
<?php foreach($rm->comments($r['id']) as $c): ?>
<div class="comment"><strong><?= e($c['username']) ?></strong> <small><?= e(date('M j, Y g:ia',strtotime($c['created_at']))) ?><?= edited_tag($c) ?></small>
<p><?= nl2br(e($c['body'])) ?></p>
<?php if($c['user_id']==uid()): ?><a href="comment.php?edit=<?= e($c['id']) ?>">Edit</a>
<form class="inline" method="post" action="comment.php" onsubmit="return confirm('Delete comment?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($c['id']) ?>"><input type="hidden" name="recipe_id" value="<?= e($r['id']) ?>"><button class="link">Delete</button></form><?php endif; ?></div>
<?php endforeach; ?></section>
<?php require 'includes/footer.php'; ?>
