<?php

if (!defined('ENCRYPTION_KEY')) {
    require_once __DIR__ . '/../config/env.php';
}

define('CIPHER_METHOD', 'aes-256-cbc');

/**
 * Отримання та сувора перевірка ключа шифрування
 */
function getEncryptionKey(): string 
{
    // Перевіряємо, чи існує константа і чи вона не порожня
    if (!defined('ENCRYPTION_KEY') || empty(ENCRYPTION_KEY)) {
        throw new Exception("Критична помилка безпеки, напишіть в пдітримку ");
    }

    return ENCRYPTION_KEY;
}

function encryptText(string $plainText): string 
{
    if (empty($plainText)) {
        return '';
    }

    // Сувора перевірка ключа: якщо ключа немає — викидає помилку і НЕ пише в БД
    $key = getEncryptionKey();

    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);
    $iv = openssl_random_pseudo_bytes($ivLength);

    // Чисте бінарне шифрування
    $encrypted = openssl_encrypt($plainText, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    // Зберігаємо у форматі: [base64_data]::[hex_iv]
    return base64_encode($encrypted) . '::' . bin2hex($iv);
}

function decryptText(string $cipherText): string 
{
    if (empty($cipherText)) {
        return '';
    }

    // Отримуємо ключ через сувору перевірку
    try {
        $key = getEncryptionKey();
    } catch (Exception $e) {
        // Якщо ключ зламаний, повертаємо початковий текст або помилку, щоб не викливати crash
        return '[Помилка]';
    }

    $expectedIvLength = openssl_cipher_iv_length(CIPHER_METHOD);

    if (strpos($cipherText, '::') !== false) {
        $parts = explode('::', $cipherText, 2);
        if (count($parts) === 2) {
            $encryptedData = base64_decode($parts[0], true);
            $iv = @hex2bin($parts[1]);

            if ($encryptedData !== false && $iv !== false && strlen($iv) === $expectedIvLength) {
                $decrypted = openssl_decrypt($encryptedData, CIPHER_METHOD, $key, OPENSSL_RAW_DATA, $iv);
                if ($decrypted !== false && mb_check_encoding($decrypted, 'UTF-8')) {
                    return $decrypted;
                }
            }
        }
    }

    $decodedData = base64_decode($cipherText, true);
    if ($decodedData !== false && strpos($decodedData, '::') !== false) {
        list($encryptedData, $iv) = explode('::', $decodedData, 2);

        if (strlen($iv) === $expectedIvLength) {
            $decrypted = openssl_decrypt($encryptedData, CIPHER_METHOD, $key, 0, $iv);
            if ($decrypted !== false && mb_check_encoding($decrypted, 'UTF-8')) {
                return $decrypted;
            }
        }
    }

    return $cipherText;
}