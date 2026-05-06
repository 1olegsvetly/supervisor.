<?php
// Start session with explicit parameters before any output
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');

// Try to set session save path to current dir if default fails
$sessionPath = __DIR__ . '/../sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0755, true);
}
if (is_writable($sessionPath)) {
    ini_set('session.save_path', $sessionPath);
}

session_start();

require_once '../functions.php';

// Debug mode - log everything
$debug_log = [];
$debug_log['start'] = 'Login script started at ' . date('Y-m-d H:i:s');
$debug_log['request_method'] = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
$debug_log['raw_POST'] = file_get_contents('php://input');
$debug_log['POST'] = $_POST;
$debug_log['SESSION_before'] = $_SESSION;
$debug_log['session_id'] = session_id();
$debug_log['session_save_path'] = session_save_path();
$debug_log['session_status'] = session_status();

// Initialize config explicitly
$config = getConfig();
$debug_log['config_loaded'] = true;
$debug_log['admin_login_from_config'] = $config['admin_login'] ?? 'NOT_SET';
$debug_log['admin_password_from_config'] = $config['admin_password'] ?? 'NOT_SET';

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = $_POST['password'] ?? '';
    
    $debug_log['input_login'] = $login;
    $debug_log['input_password'] = str_repeat('*', strlen($password));
    $debug_log['config_admin_login'] = $config['admin_login'] ?? 'NOT_SET';
    $debug_log['config_admin_password'] = $config['admin_password'] ?? 'NOT_SET';
    
    $expectedLogin = $config['admin_login'] ?? '';
    $expectedPassword = $config['admin_password'] ?? '';
    
    $debug_log['login_match'] = ($login === $expectedLogin);
    $debug_log['password_match'] = ($password === $expectedPassword);
    
    if (!empty($login) && !empty($password) && $login === $expectedLogin && $password === $expectedPassword) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_login_time'] = time();
        $debug_log['auth_result'] = 'SUCCESS';
        $debug_log['SESSION_after'] = $_SESSION;
        $debug_log['session_regenerated'] = session_regenerate_id(true);
        
        // Save debug log before redirect
        $logFile = __DIR__ . '/login_debug.log';
        $writeResult = file_put_contents($logFile, json_encode($debug_log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $debug_log['log_write_result'] = $writeResult;
        
        // Redirect to index.php in same directory
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль";
        $debug_log['auth_result'] = 'FAILED';
        $debug_log['error'] = $error;
        $debug_log['reason'] = 'Credentials mismatch or empty fields';
    }
}

// Save debug log on page load too
$logFile = __DIR__ . '/login_debug.log';
$writeResult = file_put_contents($logFile, json_encode($debug_log, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
$debug_log['final_log_write'] = $writeResult;
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
        <form action="login.php" method="post" class="login-form">
            <input type="text" name="login" placeholder="Логин" class="form-input" required autocomplete="username">
            <input type="password" name="password" placeholder="Пароль" class="form-input" required autocomplete="current-password">
            <button type="submit" class="btn btn--primary">Войти</button>
        </form>
    </div>
</body>
</html>
