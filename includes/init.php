<?php
declare(strict_types=1);

$isProduction = getenv('APP_ENV') === 'production';
session_set_cookie_params([
	'lifetime' => 0,
	'path' => '/',
	'secure' => $isProduction,
	'httponly' => true,
	'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/AuthManager.php';
require_once __DIR__ . '/../classes/RecipeManager.php';

function e($value): string {
	return htmlspecialchars(is_scalar($value) || $value === null ? (string)$value : '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function require_login(): void {
	if (empty($_SESSION['user_id'])) {
		header('Location: login.php');
		exit;
	}
}

function uid(): int {
	return (int)($_SESSION['user_id'] ?? 0);
}

function edited_tag(array $row): string {
	return !empty($row['updated_at']) ? ' <span class="edited">(Edited)</span>' : '';
}

function csrf_token(): string {
	if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

function require_valid_csrf(): void {
	$submitted = $_POST['csrf_token'] ?? null;
	$stored = $_SESSION['csrf_token'] ?? null;
	if (!is_string($submitted) || !is_string($stored) || !hash_equals($stored, $submitted)) {
		http_response_code(403);
		exit('Invalid request.');
	}
}

function post_string(string $key, string $default = ''): string {
	$value = $_POST[$key] ?? $default;
	if (!is_string($value)) {
		http_response_code(400);
		exit('Invalid request data.');
	}
	return $value;
}

function post_int(string $key, int $default = 0): int {
	$value = $_POST[$key] ?? $default;
	if ($value === '') {
		return $default;
	}
	if (is_int($value)) {
		return $value;
	}
	if (is_string($value) && filter_var($value, FILTER_VALIDATE_INT) !== false) {
		return (int)$value;
	}
	http_response_code(400);
	exit('Invalid request data.');
}

function post_string_array(string $key): array {
	$values = $_POST[$key] ?? [];
	if (!is_array($values)) {
		http_response_code(400);
		exit('Invalid request data.');
	}
	foreach ($values as $value) {
		if (!is_string($value)) {
			http_response_code(400);
			exit('Invalid request data.');
		}
	}
	return $values;
}

function query_string(string $key, string $default = ''): string {
	$value = $_GET[$key] ?? $default;
	if (!is_string($value)) {
		http_response_code(400);
		exit('Invalid request data.');
	}
	return $value;
}

function query_int(string $key, int $default = 0): int {
	$value = $_GET[$key] ?? $default;
	if (is_int($value)) {
		return $value;
	}
	if (is_string($value) && filter_var($value, FILTER_VALIDATE_INT) !== false) {
		return (int)$value;
	}
	http_response_code(400);
	exit('Invalid request data.');
}
