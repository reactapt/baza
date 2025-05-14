<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $author = sanitizeInput($_POST['author']);
    $title = sanitizeInput($_POST['title']);
    $type_id = intval($_POST['type_id']);
    $publisher = isset($_POST['publisher']) ? sanitizeInput($_POST['publisher']) : null;
    $publication_year = isset($_POST['publication_year']) ? intval($_POST['publication_year']) : null;
    $binding_type = isset($_POST['binding_type']) ? sanitizeInput($_POST['binding_type']) : null;
    $condition_description = isset($_POST['condition_description']) ? sanitizeInput($_POST['condition_description']) : null;
    
    // Валидация обязательных полей
    if (empty($author)) {
        $errors[] = "Укажите автора книги";
    }
    
    if (empty($title)) {
        $errors[] = "Укажите название книги";
    }
    
    if (!in_array($type_id, [1, 2])) {
        $errors[] = "Выберите тип карточки";
    }
    
    // Если нет ошибок, сохраняем карточку
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO book_cards 
                (user_id, author, title, type_id, publisher, publication_year, binding_type, condition_description) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $author,
                $title,
                $type_id,
                $publisher,
                $publication_year,
                $binding_type,
                $condition_description
            ]);
            
            $_SESSION['success'] = "Карточка успешно создана и отправлена на модерацию";
            header("Location: cards.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при создании карточки: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать карточку | Буквоежка</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <h1>Создать новую карточку</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="card-form">
            <div class="form-group">
                <label for="author">Автор книги*:</label>
                <input type="text" id="author" name="author" required>
            </div>
            
            <div class="form-group">
                <label for="title">Название книги*:</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label>Тип карточки*:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="type_id" value="1" required> Готов поделиться
                    </label>
                    <label>
                        <input type="radio" name="type_id" value="2" required> Хочу получить
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="publisher">Издательство:</label>
                <input type="text" id="publisher" name="publisher">
            </div>
            
            <div class="form-group">
                <label for="publication_year">Год издания:</label>
                <input type="number" id="publication_year" name="publication_year" min="1800" max="<?= date('Y') ?>">
            </div>
            
            <div class="form-group">
                <label for="binding_type">Тип переплета:</label>
                <select id="binding_type" name="binding_type">
                    <option value="">Не указано</option>
                    <option value="Твердый">Твердый</option>
                    <option value="Мягкий">Мягкий</option>
                    <option value="Интегральный">Интегральный</option>
                    <option value="Кожаный">Кожаный</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="condition_description">Состояние книги:</label>
                <textarea id="condition_description" name="condition_description" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn">Отправить на модерацию</button>
            <a href="cards.php" class="btn btn-secondary">Отмена</a>
        </form>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>