<?php
// Проверка авторизации пользователя
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Проверка прав администратора
function checkAdmin() {
    if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
        header("Location: ../login.php");
        exit();
    }
}

// Редирект авторизованных пользователей
function redirectIfAuth() {
    if (isset($_SESSION['user_id'])) {
        if ($_SESSION['is_admin']) {
            header("Location: admin/index.php");
        } else {
            header("Location: cards.php");
        }
        exit();
    }
}
?>