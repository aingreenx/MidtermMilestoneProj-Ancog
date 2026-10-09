<?php
declare(strict_types=1);
require_once 'includes/init.php';

header('Content-Type: application/json; charset=utf-8');

if (!uid()) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

require_valid_csrf();

$recipeId = post_int('id');

if ($recipeId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_recipe']);
    exit;
}

$favorited = (new RecipeManager(Database::get()))->toggleFavorite(uid(), $recipeId);

echo json_encode(['favorited' => $favorited], JSON_THROW_ON_ERROR);