<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();

$title = 'Edit comment';
$rm = new RecipeManager(Database::get());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_valid_csrf();
    $action = post_string('action');
    $recipeId = post_int('recipe_id');
    
    if ($recipeId < 1) { 
        http_response_code(400); 
        exit('Invalid recipe.'); 
    }
    
    if ($action === 'add') {
        $rm->addComment($recipeId, uid(), post_string('body'));
    } elseif ($action === 'edit' || $action === 'delete') {
        $commentId = post_int('id');
        $comment = $rm->findComment($commentId);
        
        if (!$comment || (int)$comment['user_id'] !== uid() || (int)$comment['recipe_id'] !== $recipeId) {
            http_response_code(403);
            exit('Not allowed.');
        }
        
        if ($action === 'edit') {
            $rm->updateComment($commentId, uid(), post_string('body'));
        } else {
            $rm->deleteComment($commentId, uid());
        }
    } else {
        http_response_code(400);
        exit('Invalid action.');
    }
    
    header("Location: recipe.php?id={$recipeId}");
    exit;
}

$commentId = query_int('edit');
$c = $rm->findComment($commentId);

if (!$c || (int)$c['user_id'] !== uid()) {
    http_response_code(403);
    $title = 'Not allowed';
    require 'includes/header.php';
    echo '<p>Not allowed.</p>';
    require 'includes/footer.php';
    exit;
}

require 'includes/header.php';
?>

<section class="card narrow">
    <h1>Edit comment</h1>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?= e($c['id']) ?>">
        <input type="hidden" name="recipe_id" value="<?= e($c['recipe_id']) ?>">
        
        <textarea name="body" required><?= e($c['body']) ?></textarea>
        <button class="btn">Save</button>
    </form>
</section>

<?php require 'includes/footer.php'; ?>