<?php
require_once '../functions.php';
session_start();

if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    
    if ($login === $config['admin_login'] && $password === $config['admin_password']) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            text-align: center;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .error { color: var(--error); margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-box glass">
        <h1 class="section-title">Вход</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST" class="login-form">
            <input type="text" name="login" placeholder="Логин" class="form-input" required>
            <input type="password" name="password" placeholder="Пароль" class="form-input" required>
            <button type="submit" class="btn btn--primary">Войти</button>
        </form>
    </div>
</body>
</html>
