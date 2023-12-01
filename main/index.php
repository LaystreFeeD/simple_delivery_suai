<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../authentication/login.php');
    exit;
}

$login = $_SESSION["login"];

include_once '../database/database.php';

$isAdmin = isAdmin($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная Страница</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">SimpleDelivery</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="../main/index.php">Главная <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../delivery/create_order.php">Отправить посылку</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../delivery/track_order.php">Отследить посылку</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../user/profile.php">Профиль <?php echo "(" . $login . ")" ?> </a>
                </li>
                <?php if ($isAdmin) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/index.php">Админ панель</a>
                    </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="../authentication/logic/logout.php">Выйти</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="jumbotron">
            <h1 class="display-4">Добро пожаловать в SimpleDelivery!</h1>
            <p class="lead">Надежная и быстрая доставка ваших посылок.</p>
            <hr class="my-4">
            <p>Используйте наш сервис для отправки и отслеживания посылок по всему миру.</p>
            <a class="btn btn-primary btn-lg" href="../delivery/create_order.php" role="button">Отправить посылку</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>
