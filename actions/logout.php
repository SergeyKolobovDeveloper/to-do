<?php
session_start();

$_SESSION = [];

session_destroy();

header('Location: /to-do/index.php');
exit;