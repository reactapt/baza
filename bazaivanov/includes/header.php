<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Буквоежка' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">Буквоежка</a>
            
            <nav>
                <ul>
                    <li><a href="index.php">Главная</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="cards.php">Мои карточки</a></li>
                        <li><a href="create-card.php">Добавить книгу</a></li>
                        <?php if ($_SESSION['is_admin']): ?>
                            <li><a href="admin/index.php">Панель администратора</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php">Выйти (<?= htmlspecialchars($_SESSION['login']) ?>)</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Вход</a></li>
                        <li><a href="register.php">Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container"></div>