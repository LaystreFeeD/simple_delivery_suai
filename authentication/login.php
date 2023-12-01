<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="card-title text-center">Авторизация</h2>
                        <form id="loginForm">
                            <div class="form-group">
                                <label for="login">Логин</label>
                                <input type="text" class="form-control" id="login" name="login" required pattern="[A-Za-z0-9]+">
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required pattern="[A-Za-z0-9]+">
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Войти</button>
                            <div id="errorContainer" class="alert alert-danger mt-3" style="display: none;"></div>
                            <p class="text-center mt-2">Нет в системе? <a href="./register.php">Регистрация</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                var login = $('#login').val();
                var password = $('#password').val();

                $.ajax({
                    url: 'logic/login_logic.php',
                    type: 'POST',
                    data: {
                        login: login,
                        password: password
                    },
                    success: function(response) {
                        if (response == "success") {
                            window.location.href = '../main/index.php';
                        } else {
                            $('#errorContainer').show().text(response);
                        }
                    },
                    error: function() {
                        $('#errorContainer').show().text('Ошибка выполнения запроса.');
                    }
                });
            });
        });
    </script>

</body>

</html>
