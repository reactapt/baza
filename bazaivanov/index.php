<?php
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Буквоежка - Обмен книгами</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <section class="hero">
            <h1>Добро пожаловать на Буквоежку!</h1>
            <p>Обменивайтесь книгами с другими читателями совершенно бесплатно</p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="auth-buttons">
                    <a href="login.php" class="btn btn-primary">Войти</a>
                    <a href="register.php" class="btn btn-secondary">Зарегистрироваться</a>
                </div>
            <?php else: ?>
                <div class="user-actions">
                    <a href="cards.php" class="btn btn-primary">Мои карточки</a>
                    <a href="create-card.php" class="btn btn-secondary">Добавить книгу</a>
                </div>
            <?php endif; ?>
        </section>
        
        <section class="features">
            <h2>Почему стоит использовать Буквоежку?</h2>
            <div class="feature-grid">
                <div class="feature">
                    <h3>Бесплатный обмен</h3>
                    <p>Обменивайтесь книгами без денежных затрат</p>
                </div>
                <div class="feature">
                    <h3>Большой выбор</h3>
                    <p>Найдите книги, которые давно хотели прочитать</p>
                </div>
                <div class="feature">
                    <h3>Простота использования</h3>
                    <p>Удобный интерфейс для быстрого поиска</p>
                </div>
            </div>
        </section>
        
        <?php
        // Показываем несколько случайных одобренных книг
        try {
            $stmt = $pdo->query("
                SELECT bc.title, bc.author, u.full_name 
                FROM book_cards bc
                JOIN users u ON bc.user_id = u.user_id
                WHERE bc.status_id = 2  -- approved
                ORDER BY RAND()
                LIMIT 3
            ");
            $randomBooks = $stmt->fetchAll();
            
            if ($randomBooks): ?>
                <section class="random-books">
                    <h2>Сейчас доступны:</h2>
                    <div class="book-list">
                        <?php foreach ($randomBooks as $book): ?>
                            <div class="book-card">
                                <h3><?= htmlspecialchars($book['title']) ?></h3>
                                <p>Автор: <?= htmlspecialchars($book['author']) ?></p>
                                <p>От пользователя: <?= htmlspecialchars($book['full_name']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif;
        } catch (PDOException $e) {
            // Ошибка не критичная для главной страницы, просто не показываем блок
        }
        ?>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>