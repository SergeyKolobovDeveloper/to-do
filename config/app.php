<?php
if (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1'], true)) {
    define('BASE_URL', 'http://localhost/to-do');
} else {
    define('BASE_URL', 'https://taskflow-todo.infinityfreeapp.com');
}

define('TELEGRAM_BOT_TOKEN', 'Token_Telegram');