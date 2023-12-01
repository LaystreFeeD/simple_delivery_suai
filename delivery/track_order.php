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
    <title>Отслеживание Посылки</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://api-maps.yandex.ru/2.1/?apikey=4148de1f-092c-4659-b47b-6d2a44bfa05d&lang=ru_RU" type="text/javascript"></script>
</head>

<body>

    <div class="container">
        <h2 class="mt-4">Отслеживание Посылки</h2>
        <form id="trackOrderForm">
            <div class="form-group">
                <label for="trackingNumber">Номер отслеживания</label>
                <input type="text" class="form-control" id="trackingNumber" name="trackingNumber" required>
            </div>
            <button type="submit" class="btn btn-success">Отследить</button>
        </form>
        <div id="trackingResult" class="mt-3"></div>
        <div id="map" style="width: 100%; height: 400px;"></div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        var myMap;
        var myPlacemark;
        var updateInterval;

        ymaps.ready();

        function initMap(latitude, longitude) {
            if (!myMap) {
                myMap = new ymaps.Map("map", {
                    center: [latitude, longitude],
                    zoom: 14
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

        function updateTrackingInfo(trackingNumber) {
            $.ajax({
                url: '../delivery/logic/track_order_logic.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    trackingNumber: trackingNumber
                },
                success: function(response) {
                    if (response.error) {
                        $('#trackingResult').html('<div class="alert alert-danger">' + response.error + '</div>');
                        $('#map').hide();
                        clearInterval(updateInterval);
                    } else {
                        var trackingInfo = response[0];
                        var trackingStatus = '<div class="alert alert-primary" style="font-size: larger;">Статус: ' + trackingInfo.status + '</div>';
                        $('#trackingResult').html(trackingStatus);

                        initMap(trackingInfo.current_latitude, trackingInfo.current_longitude);
                        $('#map').show();
                    }
                },
                error: function() {
                    $('#trackingResult').html('<div class="alert alert-danger">Ошибка при отслеживании.</div>');
                    $('#map').hide();
                    clearInterval(updateInterval);
                }
            });
        }

        $(document).ready(function() {
            var urlParams = new URLSearchParams(window.location.search);
            var trackingNumber = urlParams.get('trackingNumber');

            if (trackingNumber) {
                $('#trackingNumber').val(trackingNumber);
                updateTrackingInfo(trackingNumber);
                updateInterval = setInterval(function() {
                    updateTrackingInfo(trackingNumber);
                }, 5000);
            }

            $('#trackOrderForm').on('submit', function(e) {
                clearInterval(updateInterval);
                e.preventDefault();
                var trackingNumber = $('#trackingNumber').val();

                updateTrackingInfo(trackingNumber);
                updateInterval = setInterval(function() {
                    updateTrackingInfo(trackingNumber);
                }, 5000);
            });
        });
    </script>

</body>

</html>
