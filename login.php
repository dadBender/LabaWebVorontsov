<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'secret') {
        $_SESSION['is_admin'] = true;
        header('Location: /VanyaLaba6/admin/dashboard.php');
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Вход</title></head>
<body>
<h2>Вход в админку</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    <label>Логин: <input type="text" name="username"></label><br>
    <label>Пароль: <input type="password" name="password"></label><br>
    <input type="submit" value="Войти">
</form>
</body>
</html>
