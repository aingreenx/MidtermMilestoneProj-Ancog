<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();
$title = 'Recipes';
$rm = new RecipeManager(Database::get());
$q = trim(query_string('q'));
$cat = query_int('cat');
$fav = query_string('fav') === '1';
$recipes = $rm->search($q, $cat, $fav ? uid() : 0);
$userFavorites = $rm->favoriteIds(uid());
$categoryImages = [
		'Meze (Appetizers)' => 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85',
		'Salads' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=900&q=85',
		'Seafood' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=900&q=85',
		'Sweets' => 'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=900&q=85',
];
$fallbackImage = 'https://images.unsplash.com/photo-1476224203421-9ac39bcb3327?auto=format&fit=crop&w=900&q=85';
require 'includes/header.php';
?>
<section class="hero-banner greek-border" aria-labelledby="home-title">
	<div class="hero-inner">
		<p class="hero-eyebrow">Greek food, made for gathering</p>
		<h1 id="home-title"><?= $fav ? 'A table of your favorites' : 'Bring the Greek table home' ?></h1>
		<p class="hero-copy">Recipes passed around the table, gathered from home cooks and kitchens across Greece.</p>
		<form class="search-bar" method="get" role="search">
			<?php if ($fav): ?><input type="hidden" name="fav" value="1"><?php endif; ?>
			<input type="search" name="q" placeholder="Search recipes or ingredients" aria-label="Search recipes or ingredients" value="<?= e($q) ?>">
			<select name="cat" aria-label="Filter by category" onchange="this.form.submit()">
				<option value="0">All categories</option>
				<?php foreach ($rm->categories() as $category): ?>
					<option value="<?= e($category['id']) ?>" <?= $cat === (int)$category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
				<?php endforeach; ?>
			</select>
			<button class="btn" type="submit">Search</button>
		</form>
		<nav class="category-pills" aria-label="Browse recipe categories">
			<?php $allHref = 'index.php' . ($fav ? '?fav=1' : ''); ?>
			<a href="<?= e($allHref) ?>" class="<?= $cat === 0 ? 'active' : '' ?>">All recipes</a>
			<?php foreach ($rm->categories() as $category): ?>
				<?php $categoryParams = ['cat' => (int)$category['id']]; if ($fav) $categoryParams['fav'] = '1'; ?>
				<a href="<?= e('index.php?' . http_build_query($categoryParams)) ?>" class="<?= $cat === (int)$category['id'] ? 'active' : '' ?>"><?= e($category['name']) ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<section class="recipe-section" aria-labelledby="recipes-title">
	<div class="section-heading">
		<div><p class="hero-eyebrow"><?= $fav ? 'Kept close' : 'Fresh from our kitchens' ?></p>
			<h2 id="recipes-title"><?= $fav ? 'Your saved recipes' : 'Newest from the table' ?></h2></div>
		<p><?= e(count($recipes)) ?> <?= count($recipes) === 1 ? 'recipe' : 'recipes' ?></p>
	</div>
	<div class="recipe-grid">
		<?php foreach ($recipes as $recipe): ?>
			<?php $isRecipeFavorite = in_array((int)$recipe['id'], $userFavorites, true); ?>
			<article class="recipe-card">
				<img class="recipe-card-image" src="<?= e($categoryImages[$recipe['category']] ?? $fallbackImage) ?>" alt="<?= e($recipe['category'] . ' recipe') ?>" loading="lazy" width="900" height="675">
				<div class="recipe-card-body">
					<span class="tag"><?= e($recipe['category']) ?></span>
					<h2><a href="recipe.php?id=<?= e($recipe['id']) ?>"><?= e($recipe['title']) ?></a>
						<span class="favorite-star" aria-label="<?= $isRecipeFavorite ? 'Favorited' : 'Not favorited' ?>" title="<?= $isRecipeFavorite ? 'Favorited' : 'Not favorited' ?>"><?= $isRecipeFavorite ? '★' : '☆' ?></span>
					</h2>
					<p><?= e(mb_strimwidth($recipe['description'], 0, 110, '...')) ?></p>
					<small>By <?= e($recipe['username']) ?> &middot; <?= e(date('M j, Y', strtotime($recipe['created_at']))) ?><?= edited_tag($recipe) ?></small>
				</div>
			</article>
		<?php endforeach; ?>
		<?php if (!$recipes): ?><p class="empty-state">No recipes found. Try another search or category.</p><?php endif; ?>
	</div>
</section>
<?php require 'includes/footer.php'; ?>
