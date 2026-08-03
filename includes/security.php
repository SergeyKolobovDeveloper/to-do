<?php

define('CIPHER_METHOD', 'aes-256-cbc');

function encryptText(string $plainText): string 
{
    if (empty($plainText)) {
        return '';
    }

    $key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'DefaultFallbackSecretKey32Bytes!';

    $ivLength = openssl_cipher_iv_length(CIPHER_METHOD);

    $iv = openssl_random_pseudo_bytes($ivLength);

    $encrypted = openssl_encrypt($plainText, CIPHER_METHOD, $key, 0, $iv);

    return base64_encode($encrypted . '::' . $iv);
}

function decryptText(string $cipherText): string 
{
    if (empty($cipherText)) {
        return '';
    }

    $key = defined('ENCRYPTION_KEY') ? ENCRYPTION_KEY : 'DefaultFallbackSecretKey32Bytes!';

    $data = base64_decode($cipherText);

    if (strpos($data, '::') !== false) {
        list($encryptedData, $iv) = explode('::', $data, 2);

        return openssl_decrypt($encryptedData, CIPHER_METHOD, $key, 0, $iv) ?: '';
    }

    return $cipherText;
}