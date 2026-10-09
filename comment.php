<?php $title='Edit comment'; require 'includes/header.php'; require_login(); $rm=new RecipeManager;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $rid=(int)$_POST['recipe_id']; $a=$_POST['action']??'';
  if($a==='add') $rm->addComment($rid,uid(),$_POST['body']??'');
  elseif($a==='edit') $rm->updateComment((int)$_POST['id'],uid(),$_POST['body']??'');
  elseif($a==='delete') $rm->deleteComment((int)$_POST['id'],uid());
  header("Location: recipe.php?id=$rid"); exit;
}
$c=$rm->findComment((int)($_GET['edit']??0));
if(!$c||$c['user_id']!=uid()){ echo '<p>Not allowed.</p>'; require 'includes/footer.php'; exit; } ?>
<section class="card narrow"><h1>Edit comment</h1><form method="post">
<input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $c['id'] ?>"><input type="hidden" name="recipe_id" value="<?= $c['recipe_id'] ?>">
<textarea name="body" required><?= e($c['body']) ?></textarea><button class="btn">Save</button></form></section>
<?php require 'includes/footer.php'; ?>
