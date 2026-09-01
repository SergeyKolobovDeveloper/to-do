<?php
session_start();

require_once '../config/db.php';
require_once '../includes/security.php';

if(!isset($_SESSION['user'])){
    header('Location:' . BASE_URL . '/auth/login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $errorBag = [
        'title' => [],
        'due_date' => []
    ];

    $title = trim($_POST['title'] ?? '');
    $userId = $_SESSION['user']['id'];
    $dueDate = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $listId = !empty($_POST['list_id']) ? (int)$_POST['list_id'] : null;

    $remindAt = null;
    if (!empty($_POST['remind_at'])) {
        $dt = new DateTime($_POST['remind_at'], new DateTimeZone('Europe/Kyiv'));

        $dt->setTimezone(new DateTimeZone('UTC'));

        $remindAt = $dt->format('Y-m-d H:i:s');
    }
    
    $notifyVia = $_POST['notify_via'] ?? null;
    

    if(empty($title)){
        $errorBag['title'][] = 'Поле не може бути пустим!';
    } elseif(mb_strlen($title) < 3){
        $errorBag['title'][] = 'Назва задачі має містити щонайменше 3 символи!';
    } elseif(mb_strlen($title) > 500){
        $errorBag['title'][] = 'Назва задачі надто довга (максимум 500 символів)!';
    }

    if(!empty($dueDate) && $dueDate < date('Y-m-d')){
        $errorBag['due_date'][] = 'Вказана дата не може бути у минулому';
    }

    // Якщо є помилки — повертаємо назад
    if(!empty($errorBag['title']) || !empty($errorBag['due_date'])){
        $_SESSION['errors'] = $errorBag;
        $_SESSION['old'] = $_POST;
        header('Location: ../pages/create.php' . ($listId ? '?list_id=' . $listId : ''));
        exit;
    }

    $encryptedTitle = encryptText($title);

    $sql = 'INSERT INTO `tasks` (title, user_id, list_id, due_date, remind_at, notify_via) 
            VALUES (:title, :user_id, :list_id, :due_date, :remind_at, :notify_via)';

    $result = $pdo->prepare($sql);
    $result->execute([
        'title' => $encryptedTitle,
        'user_id' => $userId,
        'list_id' => $listId,
        'due_date' => $dueDate,
        'remind_at' => $remindAt,
        'notify_via' => $notifyVia
    ]);

    $_SESSION['success'] = 'Нову задачу успішно створено!';

    // Повертаємо користувача до того списку, у якому створювалася задача
    header('Location: ../pages/dashboard.php' . ($listId ? '?list_id=' . $listId : ''));
    exit;
} else {
    header('Location: ../pages/create.php');
    exit;
}