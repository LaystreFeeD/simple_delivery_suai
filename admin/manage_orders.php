<?php
include_once '../database/database.php';

session_start();

if (!isset($_SESSION['user_id']) || !isAdmin($_SESSION['user_id'])) {
    header('Location: ../authentication/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление Заказами</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=4148de1f-092c-4659-b47b-6d2a44bfa05d&lang=ru_RU" type="text/javascript"></script>
</head>

<body>
    <div class="container">
        <h2 class="mt-4">Управление Заказами</h2>
        <table class="table mt-4" id="ordersTable">
            <thead>
                <tr>
                    <th>Номер заказа</th>
                    <th>Статус</th>
                    <th>Позиция</th>
                    <th>Удаление</th>
                </tr>
            </thead>
            <tbody>
                <!-- Заказы будут загружены сюда -->
            </tbody>
        </table>
    </div>

    <!-- Модальное окно для обновления позиции -->
    <div id="updateLocationModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Обновить Позицию Заказа</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="map" style="width: 100%; height: 400px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveLocation">Сохранить Позицию</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        var myMap;
        var myPlacemark;
        var selectedOrderId;

        ymaps.ready();

        function initMap(latitude, longitude) {
            if (!myMap) {
                myMap = new ymaps.Map("map", {
                    center: [latitude, longitude],
                    zoom: 14
                });
                myMap.events.add('click', function(e) {
                    var coords = e.get('coords');
                    if (myPlacemark) {
                        myPlacemark.geometry.setCoordinates(coords);
                    } else {
                        myPlacemark = new ymaps.Placemark(coords);
                        myMap.geoObjects.add(myPlacemark);
                    }
                });
            } else {
                if (!myPlacemark || !myPlacemark.geometry.getCoordinates().every(function(coord, index) {
                        return coord === (index === 0 ? latitude : longitude);
                    })) {
                    myMap.setCenter([latitude, longitude], 14);
                }
            }

            if (myPlacemark) {
                myMap.geoObjects.remove(myPlacemark);
            }

            myPlacemark = new ymaps.Placemark([latitude, longitude]);
            myMap.geoObjects.add(myPlacemark);
        }

        $(document).ready(function() {
            loadOrders();

            function loadOrders() {
                $.ajax({
                    url: '../admin/logic/manage_orders_logic.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'getOrders'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#ordersTable tbody').html(response.data);
                        } else {
                            alert('Произошла ошибка при загрузке заказов');
                        }
                    }
                });
            }

            $('#ordersTable').on('click', '.deleteOrder', function() {
                if (confirm('Вы уверены, что хотите удалить этот заказ?')) {
                    var orderId = $(this).data('order-id');
                    $.ajax({
                        url: '../admin/logic/manage_orders_logic.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'deleteOrder',
                            orderId: orderId
                        },
                        success: function(response) {
                            if (response.success) {
                                loadOrders();
                                alert(`Заказ ${orderId} удален!`);
                            } else {
                                alert('Произошла ошибка при удалении заказа');
                            }
                        }
                    });
                }
            });

            $('#ordersTable').on('change', '.order-status', function() {
                var orderId = $(this).data('order-id');
                var newStatus = $(this).val();

                $.ajax({
                    url: '../admin/logic/manage_orders_logic.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'updateOrderStatus',
                        orderId: orderId,
                        newStatus: newStatus
                    },
                    success: function(response) {
                        if (!response.success) {
                            alert('Произошла ошибка при обновлении статуса заказа');
                        }
                        alert(`Статус заказа ${orderId} обновлен`);
                    }
                });
            });

            $('#ordersTable').on('click', '.openMapModal', function() {
                selectedOrderId = $(this).data('order-id');
                $.ajax({
                    url: '../admin/logic/manage_orders_logic.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'getOrderLocation',
                        orderId: selectedOrderId
                    },
                    success: function(response) {
                        if (response.success && response.data[0].current_latitude && response.data[0].current_longitude) {

                            initMap(response.data[0].current_latitude, response.data[0].current_longitude);
                            $('#updateLocationModal').modal('show');
                        }
                    },
                    error: function(jx, _, e) {
                        console.log(jx);
                    }
                });
            });

            $('#saveLocation').click(function() {
                if (myPlacemark) {
                    var coords = myPlacemark.geometry.getCoordinates();
                    $.ajax({
                        url: '../admin/logic/manage_orders_logic.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'updateOrderLocation',
                            orderId: selectedOrderId,
                            latitude: coords[0],
                            longitude: coords[1]
                        },
                        success: function(response) {
                            if (!response.success) {
                                alert('Произошла ошибка при сохранении новой позиции');
                            }
                            $('#updateLocationModal').modal('hide');
                            alert(`Позиция заказа ${selectedOrderId} обновлена`);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
