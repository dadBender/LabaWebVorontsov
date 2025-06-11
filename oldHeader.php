<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Настройки безопасности ДО старта сессии
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Lax');

// Старт сессии
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Подключаем файл с соединением к БД
require_once 'db.php';

/**
 * Безопасное выполнение SQL запроса
 */
function safeQuery($conn, $sql, $params = [], $types = "") {
    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Ошибка подготовки запроса: " . $conn->error);
        }

        if (!empty($params)) {
            if (!$stmt->bind_param($types, ...$params)) {
                throw new Exception("Ошибка привязки параметров: " . $stmt->error);
            }
        }

        if (!$stmt->execute()) {
            throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
        }

        return $stmt;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Валидация входных данных
 */
function validateInput($data, $maxLength = 255) {
    if (!isset($data)) {
        return false;
    }

    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

    if (strlen($data) > $maxLength) {
        return false;
    }

    return $data;
}

/**
 * Генерация CAPTCHA
 */
function generateCaptcha($conn, $formType = 'login') {
    // Получаем случайную CAPTCHA из базы данных
    $stmt = safeQuery($conn, "SELECT id, image_path, answer FROM captcha_images ORDER BY RAND() LIMIT 1");

    if ($stmt) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $captcha = $result->fetch_assoc();

            // Нормализуем ответ: удаляем пробелы и приводим к нижнему регистру
            $cleanAnswer = mb_strtolower(trim($captcha['answer']));

            // Сохраняем в сессии с учетом типа формы
            $_SESSION[$formType . '_captcha_answer'] = $cleanAnswer;
            $_SESSION[$formType . '_captcha_id'] = $captcha['id'];

            return $captcha;
        }
    }

    // Заглушка на случай ошибки
    $_SESSION[$formType . '_captcha_answer'] = 'default';
    return [
        'image_path' => 'images/captcha/default.jpg',
        'answer' => 'default'
    ];
}

/**
 * Проверка CAPTCHA
 */
function verifyCaptcha($userAnswer, $formType = 'login') {
    $sessionKey = $formType . '_captcha_answer';

    if (empty($_SESSION[$sessionKey])) {
        error_log("CAPTCHA check failed: no stored answer for form type '$formType'");
        return false;
    }

    if (empty($userAnswer)) {
        error_log("CAPTCHA check failed: empty user input for form type '$formType'");
        return false;
    }

    // Нормализуем ввод пользователя
    $userInput = mb_strtolower(trim($userAnswer));
    $correctAnswer = $_SESSION[$sessionKey];

    return $userInput === $correctAnswer;
}

// --- Главная логика ---

$login_error = '';
$register_error = '';

// Генерируем CAPTCHA для форм
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !empty($login_error) || !empty($register_error)) {
    $currentCaptcha = generateCaptcha($conn, 'login');
    $registerCaptcha = generateCaptcha($conn, 'register');
}

// Обработка входа
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Инициализация попыток входа
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    // Задержка при множественных попытках
    if ($_SESSION['login_attempts'] > 2) {
        sleep(min($_SESSION['login_attempts'], 10));
    }

    // Валидация данных
    $login = validateInput($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $captchaAnswer = $_POST['captcha_answer'] ?? '';

    // Проверка CAPTCHA
    if (!verifyCaptcha($captchaAnswer, 'login')) {
        $login_error = "Неверный код с картинки";
        $currentCaptcha = generateCaptcha($conn, 'login');
    } else {
        // CAPTCHA верна, проверяем логин/пароль
        $stmt = safeQuery($conn,
            "SELECT id, login, name, password FROM users WHERE login = ?",
            [$login],
            "s"
        );

        if ($stmt) {
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    // Успешный вход
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_login'] = $user['login'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['login_attempts'] = 0;

                    session_regenerate_id(true);
                    header("Location: profile.php");
                    exit();
                }
            }

            $login_error = "Неверный логин или пароль";
            $stmt->close();
        } else {
            $login_error = "Ошибка системы. Пожалуйста, попробуйте позже.";
        }
    }

    if ($login_error) {
        $_SESSION['login_attempts']++;
        $currentCaptcha = generateCaptcha($conn, 'login');
    }
}

// Если капча ещё не сгенерирована, генерируем
if (!isset($currentCaptcha)) {
    $currentCaptcha = generateCaptcha($conn, 'login');
}
if (!isset($registerCaptcha)) {
    $registerCaptcha = generateCaptcha($conn, 'register');
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaHub - Онлайн кинотеатр</title>
    <style>
        :root {
            --primary-color: #141414;
            --secondary-color: #e50914;
            --text-color: #fff;
            --hover-color: #b20710;
            --dark-bg: #181818;
            --light-bg: #222;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: var(--primary-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .wrapper {
            display: flex;
            flex-direction: column;
        }

        header {
            background: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
            padding: 20px 50px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        header.scrolled {
            background-color: var(--primary-color);
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: var(--secondary-color);
            text-decoration: none;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        nav a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
            position: relative;
        }

        nav a:hover {
            color: var(--secondary-color);
        }

        nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--secondary-color);
            transition: width 0.3s;
        }

        nav a:hover::after {
            width: 100%;
        }

        .auth-btn {
            background-color: var(--secondary-color);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .auth-btn:hover {
            background-color: var(--hover-color);
            color: white;
        }

        /* Модальные окна */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: var(--dark-bg);
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            position: relative;
            animation: modalFadeIn 0.3s;
        }

        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            color: #aaa;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: var(--text-color);
        }

        .modal h3 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            background-color: var(--light-bg);
            color: var(--text-color);
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: var(--hover-color);
        }

        .modal-switch {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .modal-switch a {
            color: var(--secondary-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .modal-switch a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: var(--secondary-color);
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        /* CAPTCHA стили */
        .captcha-container {
            margin: 15px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .captcha-image {
            margin-bottom: 10px;
            border: 1px solid var(--light-bg);
            border-radius: 4px;
            max-width: 100%;
            height: auto;
        }

        .captcha-reload {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
            text-decoration: underline;
        }

        .captcha-reload:hover {
            color: var(--hover-color);
        }

        footer {
            background-color: var(--dark-bg);
            padding: 30px 0;
            text-align: center;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            nav {
                margin-top: 15px;
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .modal-content {
                margin: 20% auto;
                width: 90%;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <header id="mainHeader">
        <a href="index.php" class="logo">CinemaHub</a>
        <nav>
            <a href="index.php">Главная</a>
            <a href="gallery.php">Наши фильмы</a>
            <a href="product.php">Поиск</a>
            <a href="order.php">Подписки</a>
            <a href="cart.php">Корзина</a>
            <a href="contacts.php">Обратная связь</a>
            <a href="guestbook.php">Отзывы</a>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="profile.php">Профиль</a>
                <a href="logout.php" class="auth-btn">Выйти</a>
            <?php else: ?>
                <a href="#" id="openLogin" class="auth-btn">Войти</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Модалка входа -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h3>Вход в аккаунт</h3>
            <?php if (isset($login_error)): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="text" name="login" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль" minlength="6" required>

                <!-- CAPTCHA блок -->
                <div class="captcha-container">
                    <img src="<?php echo htmlspecialchars($currentCaptcha['image_path']); ?>" alt="CAPTCHA" class="captcha-image" id="captchaImage">
                    <input type="text" name="captcha_answer" placeholder="Введите текст с картинки" required>

                </div>

                <input type="submit" value="Войти">
            </form>
            <div class="modal-switch">
                Нет аккаунта? <a href="#" id="openRegisterFromLogin">Зарегистрироваться</a>
            </div>
        </div>
    </div>

    <!-- Модалка регистрации -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('registerModal')">&times;</span>
            <h3>Регистрация</h3>
            <?php if (isset($register_error)): ?>
                <div class="error-message"><?php echo $register_error; ?></div>
            <?php endif; ?>
            <form method="post" action="register.php">
                <input type="text" name="login" placeholder="Логин мин 8" required>
                <input type="text" name="name" placeholder="Ваше имя" required>
                <input type="tel" name="phone" placeholder="Телефон формат +7 (xxx) xxx xx-xx" pattern="[\+]\d{1}\s[\(]\d{3}[\)]\s\d{3}[\-]\d{2}[\-]\d{2}" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="password" placeholder="Пароль мин 8" minlength="8" required>

                <!-- CAPTCHA блок -->
                <div class="captcha-container">
                    <img src="<?php echo htmlspecialchars($registerCaptcha['image_path']); ?>" alt="CAPTCHA" class="captcha-image" id="registerCaptchaImage">
                    <input type="text" name="captcha_answer" placeholder="Введите текст с картинки" required>

                </div>

                <input type="submit" value="Зарегистрироваться">
            </form>
            <div class="modal-switch">
                Уже есть аккаунт? <a href="#" id="openLoginFromRegister">Войти</a>
            </div>
        </div>
    </div>
</div>

<script>
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    // Обновление CAPTCHA
    function reloadCaptcha(formType) {
        const imageId = formType === 'login' ? 'captchaImage' : 'registerCaptchaImage';
        const imgElement = document.getElementById(imageId);
        imgElement.src = imgElement.src.split('?')[0] + '?t=' + new Date().getTime();
    }

    document.getElementById("openLogin")?.addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("loginModal").style.display = "block";
    });

    document.getElementById("openRegisterFromLogin")?.addEventListener("click", function(e) {
        e.preventDefault();
        closeModal('loginModal');
        document.getElementById("registerModal").style.display = "block";
    });

    document.getElementById("openLoginFromRegister")?.addEventListener("click", function(e) {
        e.preventDefault();
        closeModal('registerModal');
        document.getElementById("loginModal").style.display = "block";
    });

    window.onclick = function(event) {
        if (event.target.classList.contains("modal")) {
            event.target.style.display = "none";
        }
    };

    <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
    window.addEventListener('DOMContentLoaded', function() {
        document.getElementById('loginModal').style.display = 'block';
    });
    <?php endif; ?>
</script>
</body>
</html>