<?php
class RecipeManager {
    private PDO $db;
    public function __construct() { $this->db = Database::get(); }

    public function categories(): array { return $this->db->query('SELECT * FROM categories ORDER BY id')->fetchAll(); }
    public function categoryExists(int $id): bool {
        $s = $this->db->prepare('SELECT 1 FROM categories WHERE id=?'); $s->execute([$id]); return (bool)$s->fetch();
    }
    public function search(string $q, int $cat, int $favOf = 0): array {
        $sql = 'SELECT r.*, u.username, c.name AS category FROM recipes r JOIN users u ON u.id=r.user_id JOIN categories c ON c.id=r.category_id WHERE 1=1';
        $p = [];
        if ($q !== '') { $sql .= ' AND (r.title LIKE ? OR r.description LIKE ?)'; $p[] = "%$q%"; $p[] = "%$q%"; }
        if ($cat) { $sql .= ' AND r.category_id=?'; $p[] = $cat; }
        if ($favOf) { $sql .= ' AND r.id IN (SELECT recipe_id FROM favorites WHERE user_id=?)'; $p[] = $favOf; }
        $s = $this->db->prepare($sql . ' ORDER BY r.created_at DESC, r.id DESC'); $s->execute($p); return $s->fetchAll();
    }
    public function find(int $id) {
        $s = $this->db->prepare('SELECT r.*, u.username, c.name AS category FROM recipes r JOIN users u ON u.id=r.user_id JOIN categories c ON c.id=r.category_id WHERE r.id=?');
        $s->execute([$id]); return $s->fetch();
    }
    public function ingredients(int $rid): array {
        $s = $this->db->prepare('SELECT * FROM ingredients WHERE recipe_id=? ORDER BY id'); $s->execute([$rid]); return $s->fetchAll();
    }
    public function validate(array $d, array $ing): array {
        $e = [];
        if (trim($d['title']) === '') $e[] = 'Title is required.';
        if (mb_strlen(trim($d['description'])) < 20) $e[] = 'Description must be at least 20 characters.';
        if (!$this->categoryExists((int)$d['category_id'])) $e[] = 'Please choose a valid category.';
        if (trim($d['steps']) === '') $e[] = 'Cooking steps are required.';
        if (!$ing) $e[] = 'Add at least one ingredient.';
        return $e;
    }
    // Build clean ingredient rows from parallel POST arrays; drops blanks and negatives.
    public function cleanIngredients(array $names, array $amts, array $units): array {
        $out = [];
        foreach ($names as $i => $n) {
            $n = trim($n); $a = (float)($amts[$i] ?? 0);
            if ($n !== '' && $a >= 0) $out[] = [$n, $a, trim($units[$i] ?? '')];
        }
        return $out;
    }
    private function saveIngredients(int $rid, array $ing): void {
        $this->db->prepare('DELETE FROM ingredients WHERE recipe_id=?')->execute([$rid]);
        $s = $this->db->prepare('INSERT INTO ingredients(recipe_id,name,amount,unit) VALUES(?,?,?,?)');
        foreach ($ing as $r) $s->execute([$rid, $r[0], $r[1], $r[2]]);
    }
    public function create(int $uid, array $d, array $ing): int {
        $this->db->beginTransaction();
        $this->db->prepare('INSERT INTO recipes(user_id,category_id,title,description,steps) VALUES(?,?,?,?,?)')
            ->execute([$uid, $d['category_id'], trim($d['title']), trim($d['description']), trim($d['steps'])]);
        $id = (int)$this->db->lastInsertId();
        $this->saveIngredients($id, $ing);
        $this->db->commit(); return $id;
    }
    public function update(int $id, int $uid, array $d, array $ing): bool {
        $this->db->beginTransaction();
        $s = $this->db->prepare('UPDATE recipes SET category_id=?,title=?,description=?,steps=?,updated_at=NOW() WHERE id=? AND user_id=?');
        $s->execute([$d['category_id'], trim($d['title']), trim($d['description']), trim($d['steps']), $id, $uid]);
        $ok = $s->rowCount() > 0 || $this->owns($id, $uid);
        if ($ok) $this->saveIngredients($id, $ing);
        $this->db->commit(); return $ok;
    }
    private function owns(int $id, int $uid): bool {
        $s = $this->db->prepare('SELECT 1 FROM recipes WHERE id=? AND user_id=?'); $s->execute([$id, $uid]); return (bool)$s->fetch();
    }
    public function delete(int $id, int $uid): void {
        $this->db->prepare('DELETE FROM recipes WHERE id=? AND user_id=?')->execute([$id, $uid]);
    }
    public function comments(int $rid): array {
        $s = $this->db->prepare('SELECT c.*, u.username FROM comments c JOIN users u ON u.id=c.user_id WHERE c.recipe_id=? ORDER BY c.created_at DESC, c.id DESC');
        $s->execute([$rid]); return $s->fetchAll();
    }
    public function findComment(int $id) {
        $s = $this->db->prepare('SELECT * FROM comments WHERE id=?'); $s->execute([$id]); return $s->fetch();
    }
    public function addComment(int $rid, int $uid, string $body): void {
        if (trim($body) === '') return;
        $this->db->prepare('INSERT INTO comments(recipe_id,user_id,body) VALUES(?,?,?)')->execute([$rid, $uid, trim($body)]);
    }
    public function updateComment(int $id, int $uid, string $body): void {
        if (trim($body) === '') return;
        $this->db->prepare('UPDATE comments SET body=?, updated_at=NOW() WHERE id=? AND user_id=?')->execute([trim($body), $id, $uid]);
    }
    public function deleteComment(int $id, int $uid): void {
        $this->db->prepare('DELETE FROM comments WHERE id=? AND user_id=?')->execute([$id, $uid]);
    }
    public function isFavorite(int $uid, int $rid): bool {
        $s = $this->db->prepare('SELECT 1 FROM favorites WHERE user_id=? AND recipe_id=?'); $s->execute([$uid, $rid]); return (bool)$s->fetch();
    }
    public function favoriteIds(int $uid): array {
        $s = $this->db->prepare('SELECT recipe_id FROM favorites WHERE user_id=?');
        $s->execute([$uid]);
        return array_map('intval', $s->fetchAll(PDO::FETCH_COLUMN));
    }
    public function toggleFavorite(int $uid, int $rid): bool {
        if ($this->isFavorite($uid, $rid)) {
            $this->db->prepare('DELETE FROM favorites WHERE user_id=? AND recipe_id=?')->execute([$uid, $rid]); return false;
        }
        $this->db->prepare('INSERT INTO favorites(user_id,recipe_id) VALUES(?,?)')->execute([$uid, $rid]); return true;
    }
}
