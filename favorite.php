<?php require 'includes/init.php'; header('Content-Type: application/json');
if(!uid()||$_SERVER['REQUEST_METHOD']!=='POST'){ http_response_code(403); echo '{"error":"forbidden"}'; exit; }
echo json_encode(['favorited'=>(new RecipeManager)->toggleFavorite(uid(),(int)($_POST['id']??0))]);
