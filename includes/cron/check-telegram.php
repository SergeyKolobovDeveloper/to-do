<?php
// Опитування Telegram API (Long Polling) із підтримкою OFFSET для нових користувачів

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';

$logFile   = __DIR__ . '/../cron_tg_log.txt';
$offsetFile = __DIR__ . '/../last_tg_offset.txt'; // Файл для збереження ID останнього прочитаного оновлення
$time      = date('Y-m-d H:i:s');
$logData   = "=== [{$time}] ЗАПУСК CHECK-TELEGRAM ===\n";

if (!defined('TELEGRAM_BOT_TOKEN') || empty(TELEGRAM_BOT_TOKEN)) {
    $logData .= "ПОМИЛКА: TELEGRAM_BOT_TOKEN не визначено\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    exit("TOKEN ERROR");
}

// 1. Зчитуємо останній offset (якщо є)
$offset = 0;
if (file_exists($offsetFile)) {
    $offset = (int)file_get_contents($offsetFile);
}

// 2. Звертаємося до Telegram API з IP-адресою та параметром offset
$telegramIp = "149.154.167.220";
$url = "https://" . $telegramIp . "/bot" . TELEGRAM_BOT_TOKEN . "/getUpdates?offset=" . $offset;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: api.telegram.org']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false) {
    $logData .= "cURL Помилка: {$curlErr}\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    exit("CURL ERROR");
}

$data = json_decode($response, true);

if (!isset($data['ok']) || $data['ok'] !== true) {
    $logData .= "Telegram API Відповідь: " . $response . "\n";
    file_put_contents($logFile, $logData, FILE_APPEND);
    exit("API ERROR");
}

$updates = $data['result'] ?? [];
$processedCount = 0;
$maxUpdateId = $offset;

foreach ($updates as $update) {
    $updateId = $update['update_id'];
    if ($updateId >= $maxUpdateId) {
        $maxUpdateId = $updateId + 1; // Оновлюємо offset для наступного запиту
    }

    if (isset($update['message'])) {
        $chatId = $update['message']['chat']['id'] ?? null;
        $text   = trim($update['message']['text'] ?? '');

        if (preg_match('/^\/start\s+(\d+)$/', $text, $matches)) {
            $userId = (int)$matches[1];

            if ($userId > 0 && $chatId) {
                try {
                    $sql  = 'UPDATE `users` SET `telegram_chat_id` = :chat_id WHERE `id` = :user_id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['chat_id' => $chatId, 'user_id' => $userId]);

                    $rows = $stmt->rowCount();
                    $logData .= "УСПІХ! Chat ID: {$chatId} збережено для User ID: {$userId}. Оновлено рядків: {$rows}\n";
                    $processedCount++;
                } catch (Exception $e) {
                    $logData .= "Помилка БД: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}

// Зберігаємо новий offset у файл
if ($maxUpdateId > $offset) {
    file_put_contents($offsetFile, $maxUpdateId);
}

$logData .= "Оброблено нових запитів: {$processedCount}\n";
$logData .= "=======================================\n\n";

file_put_contents($logFile, $logData, FILE_APPEND);
echo "OK: PROCESSED {$processedCount}";