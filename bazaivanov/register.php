<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

redirectIfAuth(); // Редирект, если пользователь уже авторизован

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $login = sanitizeInput($_POST['login']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Валидация
    if (empty($full_name) || !preg_match('/^[а-яА-ЯёЁ\s]+$/u', $full_name)) {
        $errors[] = "ФИО должно содержать только кириллицу и пробелы";
    }
    
    if (empty($phone) || !preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors[] = "Телефон должен быть в формате +7(XXX)XXX-XX-XX";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email";
    }
    
    if (empty($login) || strlen($login) < 6 || !preg_match('/^[а-яА-ЯёЁa-zA-Z0-9]+$/u', $login)) {
        $errors[] = "Логин должен быть не менее 6 символов (кириллица, латиница, цифры)";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Пароль должен быть не менее 6 символов";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    // Проверка уникальности логина и email
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ? OR email = ?");
            $stmt->execute([$login, $email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Пользователь с таким логином или email уже существует";
            }
        } catch (PDOException $e) {
            $errors[] = "Ошибка при проверке данных: " . $e->getMessage();
        }
    }
    
    // Регистрация
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, email, login, password_hash) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$full_name, $phone, $email, $login, $password_hash]);
            
            $_SESSION['success'] = "Регистрация прошла успешно! Теперь вы можете войти.";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Ошибка при регистрации: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Буквоежка</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <h1>Регистрация</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="registration-form">
            <div class="form-group">
                <label for="full_name">ФИО:</label>
                <input type="text" id="full_name" name="full_name" required 
                       value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" required 
                       placeholder="+7(XXX)XXX-XX-XX"
                       pattern="\+7\(\d{3}\)\d{3}-\d{2}-\d{2}"
                       value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                <small class="form-text">Формат: +7(XXX)XXX-XX-XX</small>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="login">Логин (не менее 6 символов):</label>
                <input type="text" id="login" name="login" required minlength="6"
                       value="<?= isset($_POST['login']) ? htmlspecialchars($_POST['login']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Пароль (не менее 6 символов):</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Подтвердите пароль:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <button type="submit" class="btn">Зарегистрироваться</button>
            <p class="text-center">Уже зарегистрированы? <a href="login.php">Войдите</a></p>
        </form>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/script.js"></script>
</body>
</html>