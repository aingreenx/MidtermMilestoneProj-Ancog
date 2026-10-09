<?php
declare(strict_types=1);
require_once 'includes/init.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Method not allowed.');
}
require_valid_csrf();
AuthManager::logout();
header('Location: login.php');
