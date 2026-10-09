<?php $title='Recipes'; require 'includes/header.php'; require_login();
$rm=new RecipeManager; $q=trim($_GET['q']??''); $cat=(int)($_GET['cat']??0); $fav=isset($_GET['fav']);
$recipes=$rm->search($q,$cat,$fav?uid():0); $userFavorites=$rm->favoriteIds(uid()); ?>
<h1><?= $fav?'My Favorites':'Latest Recipes' ?></h1>
<form class="filters search-bar" method="get">
<?php if($fav): ?><input type="hidden" name="fav" value="1"><?php endif; ?>
<input name="q" placeholder="Search recipes..." value="<?= e($q) ?>">
<select name="cat" onchange="this.form.submit()"><option value="0">All categories</option>
<?php foreach($rm->categories() as $c): ?><option value="<?= e($c['id']) ?>" <?= $cat==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
<button class="btn">Search</button></form>
<div class="grid">
<?php foreach($recipes as $r): ?>
<?php $isRecipeFavorite=in_array((int)$r['id'],$userFavorites,true); ?>
<article class="card"><span class="tag"><?= e($r['category']) ?></span>
<h2><a href="recipe.php?id=<?= e($r['id']) ?>"><?= e($r['title']) ?></a> <span class="favorite-star" aria-label="<?= $isRecipeFavorite?'Favorited':'Not favorited' ?>" title="<?= $isRecipeFavorite?'Favorited':'Not favorited' ?>"><?= $isRecipeFavorite?'★':'☆' ?></span></h2>
<p><?= e(mb_strimwidth($r['description'],0,110,'...')) ?></p>
<small>by <?= e($r['username']) ?> &middot; <?= e(date('M j, Y',strtotime($r['created_at']))) ?><?= edited_tag($r) ?></small></article>
<?php endforeach; if(!$recipes): ?><p>No recipes found.</p><?php endif; ?></div>
<?php require 'includes/footer.php'; ?>
