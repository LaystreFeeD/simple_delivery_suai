<?php
include_once '../../database/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trackingNumber = trim($_POST['trackingNumber'] ?? '');

    if (empty($trackingNumber)) {
        echo json_encode(["error" => "Введите номер отслеживания."]);
        exit;
    }

    $trackingInfo = trackOrder($trackingNumber);

    if ($trackingInfo) {
        echo json_encode($trackingInfo);
    } else {
        echo json_encode(["error" => "Информация об отслеживании не найдена."]);
    }
} else {
    echo json_encode(["error" => "Неподдерживаемый метод запроса."]);
}
