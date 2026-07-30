<?php
if($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'){
    define('BASE_URL', '/to-do');
} else {
    define('BASE_URL', '');
}