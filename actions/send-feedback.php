<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'other');
    $message = trim($_POST['message'] ?? '');

    $errors = [];

    if (empty($name)) {
        $errors[] = "Будь ласка, введіть ваше ім'я.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Будь ласка, введіть коректну email-адресу.";
    }

    if (empty($message)) {
        $errors[] = "Повідомлення не може бути порожнім.";
    }

    if (empty($errors)) {
        $to = "taskflowtodoserhii@gmail.com";

        $subjectTitles = [
            'idea'  => 'Пропозиція / Ідея',
            'bug'   => 'Повідомити про баг',
            'other' => 'Загальне запитання'
        ];
        $subjectText = $subjectTitles[$subject] ?? 'Загальне запитання';

        $emailSubject = 'Нове повідомлення з DiloFlow: ' . $subjectText;

        $body  = "<h2>Нове повідомлення з форми зворотного зв'язку DiloFlow</h2>";
        $body .= "<p><strong>Ім'я:</strong> " . htmlspecialchars($name) . "</p>";
        $body .= "<p><strong>Email для відповіді:</strong> " . htmlspecialchars($email) . "</p>";
        $body .= "<p><strong>Тема:</strong> " . htmlspecialchars($subjectText) . "</p>";
        $body .= "<p><strong>Повідомлення:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

        if (sendMail($to, $emailSubject, $body)) {
            $_SESSION['success'] = "Дякуємо, {$name}! Ваше повідомлення успішно надіслано.";
        } else {
            $_SESSION['errors'] = ["Не вдалося надіслати лист. Спробуйте пізніше або зверніться до адміністратора."];
        }
    } else {
        $_SESSION['errors'] = $errors;
    }
}

header('Location: ' . BASE_URL . '/pages/feedback.php');
exit;