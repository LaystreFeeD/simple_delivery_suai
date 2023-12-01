<?php
include_once '../../database/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $recipientName = trim($_POST['recipientName'] ?? '');
    $weight = $_POST['weight'] ?? 0;
    $dimensions = trim($_POST['dimensions'] ?? '');
    $latitude = $_POST['lat'] ?? 0.0;
    $longitude = $_POST['lon'] ?? 0.0;
    $userId = $_SESSION['user_id'] ?? null;

    if (empty($recipientName) || $weight <= 0 || empty($dimensions) || $latitude == 0.0 || $longitude == 0.0) {
        echo "Необходимо заполнить все поля и установить метку на карте.";
        exit;
    }

    $status = 'В обработке';

    $orderId = createOrder($userId, $recipientName, $latitude, $longitude, $weight, $dimensions, $status);

    if ($orderId) {
        echo $orderId;
    } else {
        echo "Ошибка при создании заказа.";
    }
} else {
    echo "Неподдерживаемый метод запроса.";
}
