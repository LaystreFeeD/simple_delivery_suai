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
    <title>Профиль Пользователя</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <h2 class="mt-4">Профиль Пользователя</h2>
        <div id="userInfo" class="mt-3"></div>
        <h3>Ваши посылки:</h3>
        <ul id="userOrders" class="list-group mt-3"></ul>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajax({
                url: '../user/logic/profile_logic.php',
                type: 'POST',
                data: {
                    action: 'getUserInfo'
                },
                success: function(response) {
                    $('#userInfo').html('<p>Имя: ' + response.login + '</p><p>Email: ' + response.email + '</p>');
                }
            });

            $.ajax({
                url: '../user/logic/profile_logic.php',
                type: 'POST',
                data: {
                    action: 'getUserOrders'
                },
                success: function(response) {
                    response.forEach(function(order) {
                        $('#userOrders').append('<li class="list-group-item">Посылка №' + order.id + ' Для ' + order.recipient_name +
                            ' - <a href="../delivery/track_order.php?trackingNumber=' + order.id + '">Отследить</a></li>');
                    });
                }
            });
        });
    </script>

</body>

</html>
