<?php
session_start();
include_once '../../database/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login) || empty($password)) {
        echo "Пожалуйста, заполните все поля.";
        exit;
    }
    $userId = loginUser($login, $password);
    if ($userId) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['login'] = $login;
        echo "success";
    } else {
        echo "Неверные учетные данные.";
    }
} else {
    echo "Метод не поддерживается.";
}
