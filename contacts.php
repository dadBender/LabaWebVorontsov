<?php
require_once "header.php";

$successMessage = "";
$errorMessage = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $to = "support@cinemahub.com"; // Замените на реальный email
        $subject = "Обращение в поддержку CinemaHub от $name";
        $body = "Имя: $name\nEmail: $email\n\nСообщение:\n$message";
        $headers = "From: $email";

        if (mail($to, $subject, $body, $headers)) {
            $successMessage = "Ваше сообщение отправлено! Мы ответим вам в течение 24 часов.";
        } else {
            $errorMessage = "Произошла ошибка при отправке. Пожалуйста, попробуйте позже.";
        }
    } else {
        $errorMessage = "Пожалуйста, заполните все обязательные поля.";
    }
}
?>

    <style>
        .contact-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 40px;
            background: rgba(20, 20, 20, 0.8);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            color: white;
        }

        .contact-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            position: relative;
        }

        .contact-title:after {
            content: "";
            display: block;
            width: 100px;
            height: 4px;
            background: #e50914;
            margin: 15px auto 0;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-form input,
        .contact-form textarea {
            padding: 12px 15px;
            background: #141414;
            border: 1px solid #333;
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }

        .contact-form textarea {
            min-height: 150px;
            resize: vertical;
        }

        .contact-form button {
            background: #e50914;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }

        .contact-form button:hover {
            background: #b20710;
        }

        .success-message {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 8px;
        }

        .error-message {
            text-align: center;
            color: #e50914;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(229, 9, 20, 0.1);
            border-radius: 8px;
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #333;
        }

        .contact-info h3 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        .contact-info p {
            margin-bottom: 10px;
            color: #b3b3b3;
        }

        @media (max-width: 768px) {
            .contact-container {
                padding: 20px;
            }

            .contact-title {
                font-size: 26px;
            }
        }
    </style>

    <div class="contact-container">
        <h1 class="contact-title">Служба поддержки</h1>

        <?php if ($successMessage): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
        <?php endif; ?>

        <form method="post" class="contact-form">
            <input type="text" name="name" placeholder="Ваше имя" required>
            <input type="email" name="email" placeholder="Ваш email" required>
            <textarea name="message" placeholder="Опишите вашу проблему или вопрос" required></textarea>
            <button type="submit">Отправить сообщение</button>
        </form>

        <div class="contact-info">
            <h3>Другие способы связи</h3>
            <p><strong>Email:</strong> support@cinemahub.com</p>
            <p><strong>Телефон:</strong> +7 (800) 123-45-67</p>
            <p><strong>Часы работы:</strong> Круглосуточно, 7 дней в неделю</p>
            <p><strong>Чат поддержки:</strong> Доступен в личном кабинете</p>
        </div>
    </div>

<?php require_once "footer.php"; ?>