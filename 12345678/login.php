<?php
session_start();
require 'db.php';
if (isset($_SESSION['login'])) {
    header('Location: index.php');
    exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        $error_message = 'Пожалуйста, заполните все поля';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, login, pass_hash FROM applications WHERE login = ? LIMIT 1");
            $stmt->execute([$login]);
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['pass_hash'])) {

                $_SESSION['login'] = $user['login'];
                $_SESSION['uid'] = $user['id'];
                header('Location: index.php');
                exit();
            } else {
                $error_message = 'Неверный логин или пароль';
            }
        } catch (PDOException $e) {
            error_log('Ошибка входа: ' . $e->getMessage());
            $error_message = 'Произошла ошибка при входе. Пожалуйста, попробуйте позже.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2 class="form-title">Вход в систему</h2>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            
            <form method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="login" class="form-label">Логин</label>
                    <input type="text" class="form-control" id="login" name="login" required 
                           value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>