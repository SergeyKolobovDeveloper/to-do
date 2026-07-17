<?php
session_start();

unset($_SERVER['user']);

header('Location: /to-do/index.php');
exit;