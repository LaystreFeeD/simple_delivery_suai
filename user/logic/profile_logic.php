<?php
include_once '../../database/database.php';

session_start();

header('Content-Type: application/json');

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = trim($_POST['action'] ?? '');

    if ($action == 'getUserInfo') {
        $userInfo = getUserInfo($userId);
        if ($userInfo) {
            echo json_encode($userInfo);
        } else {
            echo json_encode(["error" => "Информация о пользователе не найдена"]);
        }
    } elseif ($action == 'getUserOrders') {
        $userOrders = getUserOrders($userId);
        echo json_encode($userOrders);
    } else {
        echo json_encode(["error" => "Неизвестное действие"]);
    }
} else {
    echo json_encode(["error" => "Неподдерживаемый метод запроса"]);
}
