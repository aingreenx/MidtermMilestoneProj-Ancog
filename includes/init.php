<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/AuthManager.php';
require_once __DIR__ . '/../classes/RecipeManager.php';
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function require_login(): void { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function uid(): int { return (int)($_SESSION['user_id'] ?? 0); }
function edited_tag($row): string { return !empty($row['updated_at']) ? ' <span class="edited">(Edited)</span>' : ''; }
