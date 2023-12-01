<?php
include_once '../../database/database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action == 'getOrderLocation') {
        $orderId = $_POST['orderId'] ?? 0;
        if ($orderId) {
            $location = trackOrder($orderId);
            echo json_encode(['success' => true, 'data' => $location]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка получения местоположения']);
        }
    } elseif ($action == 'deleteOrder') {
        $orderId = $_POST['orderId'] ?? 0;

        if ($orderId) {
            deleteOrder($orderId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка удаления заказа']);
        }
    } elseif ($action == 'getOrders') {
        $orders = getAllOrders();
        $ordersHtml = '';
        foreach ($orders as $order) {
            $ordersHtml .= "<tr>
                                <td>{$order['id']}</td>
                                <td>
                                    <select class='form-control order-status' data-order-id='{$order['id']}'>
                                        <option value='В обработке'" . ($order['status'] == 'В обработке' ? ' selected' : '') . ">В обработке</option>
                                        <option value='Отправлен'" . ($order['status'] == 'Отправлен' ? ' selected' : '') . ">Отправлен</option>
                                        <option value='Доставлен'" . ($order['status'] == 'Доставлен' ? ' selected' : '') . ">Доставлен</option>
                                    </select>
                                </td>
                                <td><button class='btn btn-primary openMapModal' data-order-id='{$order['id']}'>Изменить Позицию</button></td>
                                <td><button class='btn btn-danger deleteOrder' data-order-id='{$order['id']}'>Удалить</button></td>
                            </tr>";
        }
        echo json_encode(['success' => true, 'data' => $ordersHtml]);
    } elseif ($action == 'updateOrderStatus') {
        $orderId = $_POST['orderId'] ?? 0;
        $newStatus = $_POST['newStatus'] ?? '';

        if ($orderId && $newStatus) {
            updateOrderStatus($orderId, $newStatus);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка обновления статуса заказа']);
        }
    } elseif ($action == 'updateOrderLocation') {
        $orderId = $_POST['orderId'] ?? 0;
        $latitude = $_POST['latitude'] ?? 0;
        $longitude = $_POST['longitude'] ?? 0;

        if ($orderId && $latitude && $longitude) {
            updateOrderLocation($orderId, $latitude, $longitude);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка обновления местоположения']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Неподдерживаемый метод запроса']);
}
