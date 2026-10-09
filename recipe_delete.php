<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Method not allowed.');
}
require_valid_csrf();
$recipeId = post_int('id');
if ($recipeId < 1) { http_response_code(400); exit('Invalid recipe.'); }
(new RecipeManager(Database::get()))->delete($recipeId, uid());
header('Location: index.php');
