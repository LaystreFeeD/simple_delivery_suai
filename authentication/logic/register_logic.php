<?php
include_once '../../database/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');


    if (empty($login) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "Пожалуйста, заполните все поля.";
        exit;
    }

    if ($password !== $confirmPassword) {
        echo "Пароли не совпадают.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Некорректный формат электронной почты.";
        exit;
    }

    if (userExists($login)) {
        echo "Пользователь с таким логином уже существует.";
        exit;
    }

    registerUser($login, $email, $password);
    echo "success";
} else {
    echo "Метод не поддерживается.";
}
