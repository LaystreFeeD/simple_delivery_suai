<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="card-title text-center">Регистрация</h2>
                        <form id="registerForm">
                            <div class="form-group">
                                <label for="login">Логин</label>
                                <input type="text" class="form-control" id="login" name="login" required pattern="[A-Za-z0-9]+">
                            </div>
                            <div class="form-group">
                                <label for="email">Электронная почта</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Пароль</label>
                                <input type="password" class="form-control" id="password" name="password" required pattern="[A-Za-z0-9]+">
                            </div>
                            <div class="form-group">
                                <label for="confirmPassword">Подтверждение пароля</label>
                                <input type="password" class="form-control" id="confirmPassword" required pattern="[A-Za-z0-9]+">
                            </div>
                            <button type="submit" class="btn btn-success btn-block">Зарегистрироваться</button>
                            <div id="errorContainer" class="alert alert-danger mt-3" style="display: none;"></div>
                            <p class="text-center mt-2">Уже зарегистрированы? <a href="./login.php">Войти</a></p>
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
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                var login = $('#login').val();
                var email = $('#email').val();
                var password = $('#password').val();
                var confirmPassword = $('#confirmPassword').val();

                var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
                if (!emailRegex.test(email)) {
                    $('#errorContainer').show().text('Некорректный формат электронной почты.');
                    return;
                }

                if (password !== confirmPassword) {
                    $('#errorContainer').show().text('Пароли не совпадают.');
                    return;
                }

                $.ajax({
                    url: 'logic/register_logic.php',
                    type: 'POST',
                    data: {
                        login: login,
                        email: email,
                        password: password,
                        confirmPassword: confirmPassword
                    },
                    success: function(response) {
                        if (response === "success") {
                            window.location.href = './login.php';
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
