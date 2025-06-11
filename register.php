<?php
session_start();
require_once 'db.php';

unset($_SESSION['register_error']);
unset($_SESSION['register_warning']);



$login = trim($_POST['login'] ?? '');
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$redirectBack = strtok($_SERVER['HTTP_REFERER'], '?') . "#register-modal";

$captchaAnswer = trim($_POST['captcha_answer'] ?? '');

if (!isset($_SESSION['captcha_text']) || strtolower($captchaAnswer) !== strtolower($_SESSION['captcha_text'])) {
    $_SESSION['register_error'] = "Неверная CAPTCHA";
    header("Location: $redirectBack");
    exit();
}

// Проверка на пустые поля
if (empty($login) || empty($name) || empty($phone) || empty($email) || empty($password)) {
    $_SESSION['register_warning'] = "Все поля обязательны для заполнения";
    header("Location: $redirectBack");
    exit();
}

// Валидация
if (!preg_match('/^[a-zA-Z0-9_]{6,20}$/', $login)) {
    $_SESSION['register_warning'] = "Логин должен содержать 6-20 латинских букв, цифр или _";
    header("Location: $redirectBack");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_warning'] = "Некорректный формат email";
    header("Location: $redirectBack");
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['register_warning'] = "Пароль должен быть не менее 8 символов";
    header("Location: $redirectBack");
    exit();
}

// Проверка дубликата
$stmt = $conn->prepare("SELECT id FROM users WHERE login = ? OR email = ?");
$stmt->bind_param("ss", $login, $email);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['register_error'] = "Пользователь с таким логином или email уже существует";
    header("Location: $redirectBack");
    exit();
}

// Регистрация
try {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $reg_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO users (login, password, name, phone, email, registration_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $login, $hashed_password, $name, $phone, $email, $reg_date);

    if ($stmt->execute()) {
        $_SESSION['register_success'] = true;
        header("Location: registration_success.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        $_SESSION['register_error'] = "Пользователь с такими данными уже существует";
    } else {
        $_SESSION['register_error'] = "Ошибка базы данных: " . $e->getMessage();
    }
    header("Location: $redirectBack");
    exit();
}
