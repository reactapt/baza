<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получение карточек пользователя
try {
    $stmt = $pdo->prepare("
        SELECT bc.*, ct.name as type_name, cs.name as status_name 
        FROM book_cards bc
        JOIN card_types ct ON bc.type_id = ct.type_id
        JOIN card_statuses cs ON bc.status_id = cs.status_id
        WHERE bc.user_id = ?
        ORDER BY bc.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $cards = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении карточек: " . $e->getMessage());
}

// Удаление карточки
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $card_id = $_GET['delete'];
    
    try {
        $stmt = $pdo->prepare("UPDATE book_cards SET status_id = 4 WHERE card_id = ? AND user_id = ?");
        $stmt->execute([$card_id, $_SESSION['user_id']]);
        
        $_SESSION['success'] = "Карточка перемещена в архив";
        header("Location: cards.php");
        exit();
    } catch (PDOException $e) {
        die("Ошибка при удалении карточки: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои карточки | Буквоежка</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <main class="container">
        <h1>Мои карточки</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <a href="create-card.php" class="btn">Создать новую карточку</a>
        
        <div class="cards-container">
            <?php if (empty($cards)): ?>
                <p>У вас пока нет карточек</p>
            <?php else: ?>
                <?php foreach ($cards as $card): ?>
                    <div class="card <?= $card['status_name'] ?>">
                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                        <p><strong>Автор:</strong> <?= htmlspecialchars($card['author']) ?></p>
                        <p><strong>Тип:</strong> 
                            <?= $card['type_name'] === 'share' ? 'Готов поделиться' : 'Хочу получить' ?>
                        </p>
                        <p><strong>Статус:</strong> 
                            <?= $card['status_name'] === 'pending' ? 'На модерации' : 
                               ($card['status_name'] === 'approved' ? 'Одобрена' : 
                               ($card['status_name'] === 'rejected' ? 'Отклонена' : 'В архиве')) ?>
                        </p>
                        
                        <?php if ($card['status_name'] === 'rejected' && !empty($card['rejection_reason'])): ?>
                            <p><strong>Причина отклонения:</strong> <?= htmlspecialchars($card['rejection_reason']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['publisher'])): ?>
                            <p><strong>Издательство:</strong> <?= htmlspecialchars($card['publisher']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['publication_year'])): ?>
                            <p><strong>Год издания:</strong> <?= $card['publication_year'] ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['binding_type'])): ?>
                            <p><strong>Переплет:</strong> <?= htmlspecialchars($card['binding_type']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($card['condition_description'])): ?>
                            <p><strong>Состояние:</strong> <?= htmlspecialchars($card['condition_description']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($card['status_name'] !== 'archived'): ?>
                            <a href="cards.php?delete=<?= $card['card_id'] ?>" class="btn btn-danger" 
                               onclick="return confirm('Вы уверены, что хотите переместить эту карточку в архив?')">
                                В архив
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>