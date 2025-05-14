<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

// Получение карточек для модерации
try {
    $status_filter = isset($_GET['status']) ? intval($_GET['status']) : 1; // По умолчанию pending
    
    $stmt = $pdo->prepare("
        SELECT bc.*, u.login, u.full_name, ct.name as type_name, cs.name as status_name 
        FROM book_cards bc
        JOIN users u ON bc.user_id = u.user_id
        JOIN card_types ct ON bc.type_id = ct.type_id
        JOIN card_statuses cs ON bc.status_id = cs.status_id
        WHERE bc.status_id = ?
        ORDER BY bc.created_at DESC
    ");
    $stmt->execute([$status_filter]);
    $cards = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении карточек: " . $e->getMessage());
}

// Обработка действий администратора
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_id = intval($_POST['card_id']);
    $action = $_POST['action'];
    
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE book_cards SET status_id = 2 WHERE card_id = ?");
            $stmt->execute([$card_id]);
            $_SESSION['success'] = "Карточка одобрена";
        } elseif ($action === 'reject') {
            $rejection_reason = sanitizeInput($_POST['rejection_reason']);
            $stmt = $pdo->prepare("UPDATE book_cards SET status_id = 3, rejection_reason = ? WHERE card_id = ?");
            $stmt->execute([$rejection_reason, $card_id]);
            $_SESSION['success'] = "Карточка отклонена";
        }
        
        header("Location: index.php?status=" . $status_filter);
        exit();
    } catch (PDOException $e) {
        die("Ошибка при обработке карточки: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора | Буквоежка</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <main class="container">
        <h1>Панель администратора</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="admin-nav">
            <a href="index.php?status=1" class="btn <?= (!isset($_GET['status']) || $_GET['status'] == 1) ? 'btn-active' : '' ?>">
                На модерации
            </a>
            <a href="index.php?status=2" class="btn <?= (isset($_GET['status']) && $_GET['status'] == 2) ? 'btn-active' : '' ?>">
                Одобренные
            </a>
            <a href="index.php?status=3" class="btn <?= (isset($_GET['status']) && $_GET['status'] == 3) ? 'btn-active' : '' ?>">
                Отклоненные
            </a>
        </div>
        
        <div class="cards-container">
            <?php if (empty($cards)): ?>
                <p>Нет карточек с выбранным статусом</p>
            <?php else: ?>
                <?php foreach ($cards as $card): ?>
                    <div class="card admin-card">
                        <h3><?= htmlspecialchars($card['title']) ?></h3>
                        <p><strong>Автор:</strong> <?= htmlspecialchars($card['author']) ?></p>
                        <p><strong>Тип:</strong> <?= $card['type_name'] === 'share' ? 'Готов поделиться' : 'Хочу получить' ?></p>
                        <p><strong>Пользователь:</strong> <?= htmlspecialchars($card['full_name']) ?> (<?= htmlspecialchars($card['login']) ?>)</p>
                        <p><strong>Дата создания:</strong> <?= date('d.m.Y H:i', strtotime($card['created_at'])) ?></p>
                        
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
                        
                        <?php if ($card['status_name'] === 'rejected' && !empty($card['rejection_reason'])): ?>
                            <p><strong>Причина отклонения:</strong> <?= htmlspecialchars($card['rejection_reason']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($card['status_id'] == 1): // Только для карточек на модерации ?>
                            <form method="POST" class="moderation-form">
                                <input type="hidden" name="card_id" value="<?= $card['card_id'] ?>">
                                
                                <div class="form-group">
                                    <label for="rejection_reason_<?= $card['card_id'] ?>">Причина отклонения (если отклоняете):</label>
                                    <textarea id="rejection_reason_<?= $card['card_id'] ?>" name="rejection_reason" rows="2"></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Одобрить</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">Отклонить</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>