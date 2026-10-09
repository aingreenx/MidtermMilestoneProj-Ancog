<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();
$title = 'Share a recipe';
$rm = new RecipeManager(Database::get());
$id = query_int('id');
$d = ['title'=>'','description'=>'','category_id'=>0,'steps'=>''];
$ing = [['name'=>'','amount'=>'','unit'=>'']];
$errors = [];
$units=['Volume'=>['cups','tbsp','tsp','ml','L'],'Weight'=>['grams','kg'],'Count'=>['pieces','cloves','sprigs','pinches']];
if ($id > 0) {
  $recipe = $rm->find($id);
  if (!$recipe || (int)$recipe['user_id'] !== uid()) {
    http_response_code(403);
    $title = 'Not allowed';
    require 'includes/header.php';
    echo '<p>Not allowed.</p>';
    require 'includes/footer.php';
    exit;
  } else {
    $d = $recipe;
    $ing = $rm->ingredients($id) ?: $ing;
  }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_valid_csrf();
  $d = [
    'title' => post_string('title'),
    'description' => post_string('description'),
    'category_id' => post_int('category_id'),
    'steps' => post_string('steps'),
  ];
  try {
    $clean = $rm->cleanIngredients(post_string_array('ing_name'), post_string_array('ing_amount'), post_string_array('ing_unit'));
  } catch (InvalidArgumentException $exception) {
    $clean = [];
    $errors[] = $exception->getMessage();
  }
  $errors = array_merge($errors, $rm->validate($d, $clean));
  if (!$errors) {
    if ($id > 0) {
      if (!$rm->update($id, uid(), $d, $clean)) {
        http_response_code(403);
        $errors[] = 'You cannot edit this recipe.';
      }
    } else {
      $id = $rm->create(uid(), $d, $clean);
    }
    if (!$errors) { header("Location: recipe.php?id={$id}"); exit; }
  }
  $ing = array_map(static fn(array $row): array => ['name'=>$row[0], 'amount'=>$row[1], 'unit'=>$row[2]], $clean) ?: $ing;
}
require 'includes/header.php';
?>
<section class="card"><h1><?= $id?'Edit recipe':'Share a recipe' ?></h1>
<?php foreach($errors as $x): ?><p class="error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
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
