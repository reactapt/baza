<?php
require_once 'includes/config.php';

// Уничтожаем сессию
$_SESSION = array();
session_destroy();

// Перенаправляем на главную страницу
header("Location: index.php");
exit();
?>