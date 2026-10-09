<?php session_start(); require_once 'classes/Database.php'; require_once 'classes/AuthManager.php';
(new AuthManager)->logout(); header('Location: login.php');
