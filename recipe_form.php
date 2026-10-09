<?php $title='Share a recipe'; require 'includes/header.php'; require_login(); $rm=new RecipeManager;
$id=(int)($_GET['id']??0); $d=['title'=>'','description'=>'','category_id'=>0,'steps'=>''];$ing=[['name'=>'','amount'=>'','unit'=>'']]; $errors=[];
$units=['Volume'=>['cups','tbsp','tsp','ml','L'],'Weight'=>['grams','kg'],'Count'=>['pieces','cloves','sprigs','pinches']];
if($id){ $r=$rm->find($id); if(!$r||$r['user_id']!=uid()){ echo '<p>Not allowed.</p>'; require 'includes/footer.php'; exit; }
  $d=$r; $ing=$rm->ingredients($id)?:$ing; }
if($_SERVER['REQUEST_METHOD']==='POST'){
  $d=['title'=>$_POST['title']??'','description'=>$_POST['description']??'','category_id'=>(int)($_POST['category_id']??0),'steps'=>$_POST['steps']??''];
  $clean=$rm->cleanIngredients($_POST['ing_name']??[],$_POST['ing_amount']??[],$_POST['ing_unit']??[]);
  $errors=$rm->validate($d,$clean);
  if(!$errors){ if($id){ $rm->update($id,uid(),$d,$clean); } else { $id=$rm->create(uid(),$d,$clean); }
    header("Location: recipe.php?id=$id"); exit; }
  $ing=array_map(fn($r)=>['name'=>$r[0],'amount'=>$r[1],'unit'=>$r[2]],$clean)?:$ing;
} ?>
<section class="card"><h1><?= $id?'Edit recipe':'Share a recipe' ?></h1>
<?php foreach($errors as $x): ?><p class="error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post">
<label>Title<input name="title" required maxlength="120" value="<?= e($d['title']) ?>"></label>
<label>Category<select name="category_id" required><option value="">Choose...</option>
<?php foreach($rm->categories() as $c): ?><option value="<?= e($c['id']) ?>" <?= $d['category_id']==$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>Short description (20+ characters)<textarea name="description" required minlength="20"><?= e($d['description']) ?></textarea></label>
<h3>Ingredients</h3><div id="ingredient-list">
<?php foreach($ing as $i): ?><div class="ingredient-row">
<input name="ing_name[]" placeholder="Ingredient" value="<?= e($i['name']) ?>">
<input name="ing_amount[]" type="number" min="0" step="any" placeholder="Amount" value="<?= e($i['amount']) ?>">
<select name="ing_unit[]"><option value="" <?= $i['unit']===''?'selected':'' ?>>(No unit)</option>
<?php foreach($units as $group=>$options): ?><optgroup label="<?= e($group) ?>"><?php foreach($options as $unit): ?><option value="<?= e($unit) ?>" <?= $i['unit']===$unit?'selected':'' ?>><?= e($unit) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select>
<button type="button" class="remove-btn" aria-label="Remove ingredient">Remove</button></div><?php endforeach; ?></div>
<template id="ingredient-template"><div class="ingredient-row">
<input name="ing_name[]" placeholder="Ingredient">
<input name="ing_amount[]" type="number" min="0" step="any" placeholder="Amount">
<select name="ing_unit[]"><option value="">(No unit)</option>
<?php foreach($units as $group=>$options): ?><optgroup label="<?= e($group) ?>"><?php foreach($options as $unit): ?><option value="<?= e($unit) ?>"><?= e($unit) ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select>
<button type="button" class="remove-btn" aria-label="Remove ingredient">Remove</button></div></template>
<button type="button" id="add-ingredient" class="btn alt">Add Ingredient</button>
<label>Cooking steps<textarea name="steps" required rows="6"><?= e($d['steps']) ?></textarea></label>
<button class="btn"><?= $id?'Save changes':'Post recipe' ?></button></form></section>
<?php require 'includes/footer.php'; ?>
