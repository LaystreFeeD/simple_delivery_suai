<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../authentication/login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отправление Посылки</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=4148de1f-092c-4659-b47b-6d2a44bfa05d&lang=ru_RU" type="text/javascript"></script>
</head>

<body>

    <div class="container">
        <h2 class="mt-4">Отправление Посылки</h2>
        <form id="createOrderForm">
            <div class="form-group">
                <label for="recipientName">Имя получателя</label>
                <input type="text" class="form-control" id="recipientName" name="recipientName" required>
            </div>
            <div class="form-group">
                <label for="weight">Вес (кг)</label>
                <input type="number" class="form-control" id="weight" name="weight" required>
            </div>
            <div class="form-group">
                <label for="dimensions">Размеры (см)</label>
                <input type="text" class="form-control" id="dimensions" name="dimensions" required>
            </div>
            <div id="map" style="width: 100%; height: 400px;"></div>
            <button type="submit" class="btn btn-success mt-3">Отправить посылку</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        var myMap;
        var myPlacemark;

        ymaps.ready(init);

        function init() {
            myMap = new ymaps.Map("map", {
                center: [59.930151313488544, 30.294416608403747],
                zoom: 14,
            });

            myMap.events.add('click', function(e) {
                var coords = e.get('coords');

                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                } else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                }
            });
        }

        function createPlacemark(coords) {
            return new ymaps.Placemark(coords, {}, {
                draggable: true
            });
        }

        $('#createOrderForm').on('submit', function(e) {
            e.preventDefault();

            if (!myPlacemark) {
                alert('Пожалуйста, укажите местоположение на карте.');
                return;
            }

            var recipientName = $('#recipientName').val();
            var weight = $('#weight').val();
            var dimensions = $('#dimensions').val();
            var coords = myPlacemark.geometry.getCoordinates();

            $.ajax({
                url: 'logic/create_order_logic.php',
                type: 'POST',
                data: {
                    recipientName: recipientName,
                    weight: weight,
                    dimensions: dimensions,
                    lat: coords[0],
                    lon: coords[1]
                },
                success: function(response) {
                    if (!isNaN(parseInt(response))) {
                        var orderId = parseInt(response);
                        alert(`Заказ успешно создан с номером ${orderId}.`);
                        window.location.href = '../main/index.php';
                    } else {
                        alert(response);
                    }
                },
                error: function() {
                    alert('Ошибка выполнения запроса.');
                }
            });
        });
    </script>

</body>

</html>
