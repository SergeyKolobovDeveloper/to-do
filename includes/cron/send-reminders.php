<?php
date_default_timezone_set('UTC'); // вказано за світовим часом UTC

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/mail.php';

if (file_exists(__DIR__ . '/../includes/security.php')) {
    require_once __DIR__ . '/../includes/security.php';
}

try {
    $currentUtcTime = date('Y-m-d H:i:s');
    $logHeader      = "[" . $currentUtcTime . "] ";

    // Шукаємо задачі, у яких настав час нагадування і вони ще не були відправлені
    $sql = "SELECT t.*, u.email, u.telegram_chat_id 
            FROM tasks t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.remind_at IS NOT NULL 
              AND t.remind_at <= :utc_time 
              AND t.is_reminded = 0 
              AND t.notify_via != 'none'";
              
    $data = $pdo->prepare($sql);
    $data->execute(['utc_time' => $currentUtcTime]);
    $tasks = $data->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tasks)) {
        echo "Немає задач для надсилання нагадувань.\n";
        exit;
    }

    $sentCount = 0;

    foreach ($tasks as $task) {
        $rawTitle = $task['title'];
        if (function_exists('decryptText')) {
            $title = decryptText($rawTitle);
        } else {
            $title = $rawTitle;
        }

        $title   = htmlspecialchars($title);
        $dueDate = $task['due_date'] ? $task['due_date'] : 'Не вказано';

        // 1. Відправка на пошту 
        if ($task['notify_via'] === 'email' || $task['notify_via'] === 'both') {
            if (!empty($task['email'])) {
                $messageText  = "🔔 <b>Нагадування про задачу DiloFlow</b><br><br>";
                $messageText .= "📌 <b>Задача:</b> {$title}<br>";
                $messageText .= "⏰ <b>Дедлайн:</b> {$dueDate}<br><br>";
                $messageText .= "Гарного та продуктивного дня!";

                $subject    = '🔔 Нагадування про задачу: ' . $title;
                $mailResult = sendMail($task['email'], $subject, $messageText);

                if ($mailResult) {
                    $logMsg = $logHeader . "✅ Лист успішно відправлено на " . $task['email'] . "\n";
                    echo $logMsg;
                    file_put_contents(__DIR__ . '/../cron_log.txt', $logMsg, FILE_APPEND);
                } else {
                    $logMsg = $logHeader . "❌ Помилка відправки листа на " . $task['email'] . "\n";
                    echo $logMsg;
                    file_put_contents(__DIR__ . '/../cron_log.txt', $logMsg, FILE_APPEND);
                }
            }
        }
		// 2. Відправка на Telegram
        if ($task['notify_via'] === 'telegram' || $task['notify_via'] === 'both') {
            if (!empty($task['telegram_chat_id']) && defined('TELEGRAM_BOT_TOKEN')) {
                
                $tgMessage  = "🔔 <b>Нагадування про задачу DiloFlow</b>\n\n";
                $tgMessage .= "📌 <b>Задача:</b> {$title}\n";
                $tgMessage .= "⏰ <b>Дедлайн:</b> {$dueDate}\n\n";
                $tgMessage .= "Гарного та продуктивного дня!";
        
                // 💡 ОБХІД DNS-БЛОКУВАННЯ INFINITYFREE: використовуємо пряму IP-адресу Telegram
                $telegramIp = "149.154.167.220";
                $url = "https://" . $telegramIp . "/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
                
                $telegramData = [
                    'chat_id'    => $task['telegram_chat_id'],
                    'text'       => $tgMessage,
                    'parse_mode' => 'HTML'
                ];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($telegramData));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                // Передаємо заголовок Host, щоб Telegram правильно обробив запит та SSL
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Host: api.telegram.org']);
                
                // Вимикаємо перевірку SSL для сумісності з IP
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                $tgResult = curl_exec($ch);
                $curlErr  = curl_error($ch);
                curl_close($ch);

                // Перевіряємо відповідь від Telegram
                $tgResponseData = json_decode($tgResult, true);

                if ($tgResult !== false && isset($tgResponseData['ok']) && $tgResponseData['ok'] === true) {
                    $logMsg = $logHeader . "✅ Повідомлення в Telegram успішно надіслано (chat_id: {$task['telegram_chat_id']})!\n";
                    echo $logMsg;
                    file_put_contents(__DIR__ . '/../cron_log.txt', $logMsg, FILE_APPEND);
                } else {
                    $logMsg = $logHeader . "❌ Помилка відправки в Telegram: " . ($curlErr ? $curlErr : $tgResult) . "\n";
                    echo $logMsg;
                    file_put_contents(__DIR__ . '/../cron_log.txt', $logMsg, FILE_APPEND);
                }
            }
        }

        // 3. Помічаємо задачу як «сповіщену»
        $updateDate = $pdo->prepare('UPDATE tasks SET `is_reminded` = 1 WHERE `id` = :id');
        $updateDate->execute(['id' => $task['id']]);
        
        $sentCount++;
    }

    echo "<br><b>Успішно оброблено задач: {$sentCount}</b>\n";

} catch (Exception $e) {
    $errorMsg = "[" . date('Y-m-d H:i:s') . "] Помилка при виконанні Cron: " . $e->getMessage() . "\n";
    echo $errorMsg;
    file_put_contents(__DIR__ . '/../cron_log.txt', $errorMsg, FILE_APPEND);
}