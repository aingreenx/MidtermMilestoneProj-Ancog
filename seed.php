<?php
/**
 * Test data seeder for Greek Recipe Hub.
 * Put in the project root, then run:  php seed.php   (or open /seed.php?confirm=1)
 * Re-runnable: it removes the seed users first (ON DELETE CASCADE wipes their
 * recipes, ingredients, comments and favorites), then inserts fresh data.
 * DELETE THIS FILE before submitting or deploying.
 */
require_once __DIR__ . '/classes/Database.php';

if (PHP_SAPI !== 'cli' && ($_GET['confirm'] ?? '') !== '1') {
    exit('Add ?confirm=1 to run the seeder.');
}
header('Content-Type: text/plain; charset=utf-8');

$db = Database::get();
$password = 'Test1234'; // letter + number, passes the site's regex
$hash = password_hash($password, PASSWORD_DEFAULT);

// Categories come from whatever is already in your DB (so your own names work)
$cats = $db->query('SELECT id FROM categories ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
if (!$cats) exit("No categories found. Import schema.sql first.\n");
$cat = fn(int $i) => $cats[$i % count($cats)];

$users = [
    ['maria_k',   'maria@example.com'],
    ['nikos_g',   'nikos@example.com'],
    ['eleni_p',   'eleni@example.com'],
    ['demo_user', 'demo@example.com'],
];

// [owner index, category index, title, description, steps, [[name, amount, unit], ...]]
$recipes = [
    [0, 0, 'Spanakopita Triangles',
     'Crispy phyllo triangles filled with spinach, feta and fresh dill, perfect for gatherings.',
     "Saute onion and spinach until wilted.\nMix with feta, egg and dill.\nFill buttered phyllo strips and fold into triangles.\nBake at 180C for 25 minutes until golden.",
     [['Spinach', 500, 'g'], ['Feta cheese', 250, 'g'], ['Phyllo sheets', 12, 'pcs'], ['Dill', 2, 'tbsp'], ['Olive oil', 3, 'tbsp']]],
    [1, 1, 'Classic Horiatiki Salad',
     'The village salad: ripe tomatoes, cucumber, olives and a thick slab of feta with oregano.',
     "Chop tomatoes, cucumber and onion into chunks.\nAdd olives and green pepper.\nTop with a slab of feta and dust with oregano.\nDrizzle generously with olive oil.",
     [['Tomatoes', 4, 'pcs'], ['Cucumber', 1, 'pcs'], ['Kalamata olives', 100, 'g'], ['Feta block', 200, 'g'], ['Dried oregano', 1, 'tsp']]],
    [2, 2, 'Grilled Octopus with Lemon',
     'Tender octopus simmered then charred on the grill, finished with lemon and olive oil.',
     "Simmer octopus gently for 45 minutes until tender.\nCool and cut into pieces.\nGrill over high heat until charred.\nDress with lemon juice, oil and oregano.",
     [['Octopus', 1.5, 'kg'], ['Lemon', 2, 'pcs'], ['Olive oil', 4, 'tbsp'], ['Oregano', 1, 'tsp']]],
    [3, 3, 'Honey Walnut Baklava',
     'Layers of buttery phyllo with spiced walnuts, soaked in fragrant honey syrup.',
     "Layer buttered phyllo in a pan.\nSpread chopped walnuts and cinnamon between layers.\nCut into diamonds and bake at 170C for 50 minutes.\nPour cooled honey syrup over the hot baklava.",
     [['Phyllo sheets', 20, 'pcs'], ['Walnuts', 400, 'g'], ['Butter', 250, 'g'], ['Honey', 300, 'g'], ['Cinnamon', 1.5, 'tsp']]],
    [0, 0, 'Tzatziki Dip',
     'Cool, garlicky yogurt and cucumber dip that goes with almost everything on the table.',
     "Grate cucumber and squeeze out the water.\nMix with strained yogurt, garlic and dill.\nSeason with salt, oil and a splash of vinegar.\nChill for an hour before serving.",
     [['Greek yogurt', 400, 'g'], ['Cucumber', 1, 'pcs'], ['Garlic cloves', 3, 'pcs'], ['Olive oil', 2, 'tbsp']]],
    [1, 2, 'Shrimp Saganaki',
     'Plump shrimp baked in a tomato and feta sauce with a hint of ouzo.',
     "Cook onion and garlic in olive oil.\nAdd tomatoes and simmer into a sauce.\nAdd shrimp and ouzo, cook 4 minutes.\nCrumble feta on top and bake until bubbling.",
     [['Shrimp', 500, 'g'], ['Crushed tomatoes', 400, 'g'], ['Feta cheese', 150, 'g'], ['Ouzo', 3, 'tbsp']]],
];

// [recipe index, commenter index, text, edited?]
$comments = [
    [0, 1, 'Made this for the barangay fiesta and it disappeared in minutes!', false],
    [0, 2, 'Added a little nutmeg to the filling. Highly recommend.', true],
    [1, 0, 'Simple and fresh. Use the ripest tomatoes you can find.', false],
    [2, 3, 'The lemon at the end makes it. Great recipe.', false],
    [3, 2, 'Too sweet for me at first, but better the next day.', true],
    [4, 3, 'Perfect with warm pita bread.', false],
];

// [user index, recipe index]
$favorites = [[3, 0], [3, 1], [3, 3], [0, 2], [1, 3], [2, 0]];

try {
    $db->beginTransaction();

    // Clean previous seed data
    $del = $db->prepare('DELETE FROM users WHERE username = ?');
    foreach ($users as $u) $del->execute([$u[0]]);

    // Users
    $insU = $db->prepare('INSERT INTO users(username,email,password_hash) VALUES(?,?,?)');
    $uid = [];
    foreach ($users as $i => $u) {
        $insU->execute([$u[0], $u[1], $hash]);
        $uid[$i] = (int)$db->lastInsertId();
    }

    // Recipes + ingredients (staggered dates so "latest first" is visible)
    $insR = $db->prepare('INSERT INTO recipes(user_id,category_id,title,description,steps,created_at,updated_at) VALUES(?,?,?,?,?,?,?)');
    $insI = $db->prepare('INSERT INTO ingredients(recipe_id,name,amount,unit) VALUES(?,?,?,?)');
    $rid = [];
    foreach ($recipes as $i => [$owner, $c, $title, $desc, $steps, $ings]) {
        $created = date('Y-m-d H:i:s', strtotime('-' . (count($recipes) - $i) . ' days'));
        $updated = ($i === 1) ? date('Y-m-d H:i:s') : null; // recipe #2 shows the (Edited) tag
        $insR->execute([$uid[$owner], $cat($c), $title, $desc, $steps, $created, $updated]);
        $rid[$i] = (int)$db->lastInsertId();
        foreach ($ings as $g) $insI->execute([$rid[$i], $g[0], $g[1], $g[2]]);
    }

    // Comments
    $insC = $db->prepare('INSERT INTO comments(recipe_id,user_id,body,created_at,updated_at) VALUES(?,?,?,?,?)');
    foreach ($comments as $n => [$r, $u, $text, $edited]) {
        $t = date('Y-m-d H:i:s', strtotime('-' . (count($comments) - $n) . ' hours'));
        $insC->execute([$rid[$r], $uid[$u], $text, $t, $edited ? date('Y-m-d H:i:s') : null]);
    }

    // Favorites
    $insF = $db->prepare('INSERT IGNORE INTO favorites(user_id,recipe_id) VALUES(?,?)');
    foreach ($favorites as [$u, $r]) $insF->execute([$uid[$u], $rid[$r]]);

    $db->commit();
} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    exit('Seeding failed: ' . $e->getMessage() . "\n");
}

echo "Seeded " . count($users) . " users, " . count($recipes) . " recipes, "
   . count($comments) . " comments, " . count($favorites) . " favorites.\n\n";
echo "Test logins (password for all: $password):\n";
foreach ($users as $u) echo "  - {$u[0]}\n";
echo "\nRemember to delete seed.php when you're done.\n";