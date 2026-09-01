<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

$logFile = __DIR__ . '/../telegram_log.txt';
$time = date('Y-m-d H:i:s');

// Отримуємо сирі дані від Telegram
$content = file_get_contents("php://input");

$logData = "=== [{$time}] НОВИЙ ЗАПИТ ВІД TELEGRAM ===\n";
$logData .= "Raw Content: " . ($content ?: "ПОРОЖНЬО") . "\n";

$update = json_decode($content, true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'] ?? null;
    $text   = trim($update['message']['text'] ?? '');
    
    $logData .= "Chat ID: {$chatId}\n";
    $logData .= "Text: {$text}\n";

    if (strpos($text, '/start') === 0) {
        $parts  = explode(' ', $text);
        $userId = isset($parts[1]) ? intval($parts[1]) : 0;
        
        $logData .= "Отримано User ID з кнопки: {$userId}\n";

        if ($userId > 0 && $chatId) {
            try {
                $sql  = 'UPDATE `users` SET `telegram_chat_id` = :chat_id WHERE `id` = :user_id';
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['chat_id' => $chatId, 'user_id' => $userId]);
                
                $rows = $stmt->rowCount();
                $logData .= "Результат БД: Оновлено рядків = {$rows}\n";
            } catch (Exception $e) {
                $logData .= "Помилка БД: " . $e->getMessage() . "\n";
            }
        } else {
            $logData .= "Помилка: Не передано user_id (текст: '{$text}')\n";
        }
    }
}

$logData .= "=========================================\n\n";

file_put_contents($logFile, $logData, FILE_APPEND);

http_response_code(200);
echo "OK";