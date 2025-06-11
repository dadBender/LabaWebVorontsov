<?php
require_once "header.php";
require_once "db.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo "<p style='text-align:center; margin-top: 40px;'>Пожалуйста, <a href='#' onclick=\"document.getElementById('loginModal').style.display='block'\">войдите</a>, чтобы просмотреть личный кабинет.</p>";
    require_once "footer.php";
    exit();
}

// Обработка отмены подписки
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_subscription'])) {
    $userId = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET subscription_id = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $_SESSION['subscription_message'] = "Подписка успешно отменена!";
    } else {
        $_SESSION['subscription_message'] = "Ошибка при отмене подписки: " . $conn->error;
    }
    $stmt->close();

    header("Location: profile.php");
    exit();
}

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['profile_error'] = "Неверный формат email";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $phone, $email, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $_SESSION['profile_success'] = "Данные успешно обновлены!";
        } else {
            $_SESSION['profile_error'] = "Ошибка при обновлении данных";
        }
        $stmt->close();
    }

    header("Location: profile.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Получение информации о пользователе
$stmt = $conn->prepare("SELECT login, name, phone, email, registration_date, subscription_id FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<p style='text-align:center; margin-top:40px;'>Пользователь не найден.</p>";
    require_once "footer.php";
    exit();
}

// Получение информации о подписке пользователя
$subscription = null;
if (!empty($user['subscription_id'])) {
    $stmt = $conn->prepare("
        SELECT id, name, price, features 
        FROM subscriptions 
        WHERE id = ? AND active = 1
    ");
    $stmt->bind_param("i", $user['subscription_id']);
    $stmt->execute();
    $subscription = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

    <style>
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background-color: #1e1e1e;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
            color: #fff;
            font-family: 'Arial', sans-serif;
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 32px;
            color: #e74c3c;
        }

        .profile-info {
            margin-bottom: 40px;
            font-size: 18px;
        }

        .profile-info p {
            margin: 8px 0;
        }

        .profile-info strong {
            color: #f39c12;
        }

        .subscription-info {
            background-color: #34495e;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }

        .subscription-info p {
            font-size: 18px;
            color: #ecf0f1;
        }

        .subscription-info strong {
            color: #f39c12;
        }

        .no-subscription {
            text-align: center;
            font-style: italic;
            color: #bdc3c7;
            margin-top: 20px;
            font-size: 20px;
        }

        .subscriptions-table, .users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
        }

        .subscriptions-table th, .users-table th, .subscriptions-table td, .users-table td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .subscriptions-table th, .users-table th {
            background-color: #e0e0e0;
        }

        .no-subscription {
            text-align: center;
            font-style: italic;
            color: #888;
        }

        /* Стили для формы редактирования */
        .edit-form {
            margin-top: 30px;
            padding: 20px;
            background-color: #2c3e50;
            border-radius: 8px;
        }

        .edit-form h3 {
            margin-top: 0;
            color: #f39c12;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #f39c12;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #444;
            background-color: #34495e;
            color: #fff;
        }

        .btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #c0392b;
        }

        .success-message {
            color: #2ecc71;
            text-align: center;
            margin: 15px 0;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            margin: 15px 0;
        }

        .subscription-actions {
            margin-top: 15px;
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }

        .subscription-message {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .subscription-success {
            background-color: #2ecc71;
            color: white;
        }

        .subscription-error {
            background-color: #e74c3c;
            color: white;
        }
    </style>

    <div class="profile-container">
        <h2>Личный кабинет</h2>

        <?php if (isset($_SESSION['profile_error'])): ?>
            <div class="error-message"><?= $_SESSION['profile_error'] ?></div>
            <?php unset($_SESSION['profile_error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['profile_success'])): ?>
            <div class="success-message"><?= $_SESSION['profile_success'] ?></div>
            <?php unset($_SESSION['profile_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['subscription_message'])): ?>
            <div class="subscription-message <?= strpos($_SESSION['subscription_message'], 'успешно') !== false ? 'subscription-success' : 'subscription-error' ?>">
                <?= $_SESSION['subscription_message'] ?>
            </div>
            <?php unset($_SESSION['subscription_message']); ?>
        <?php endif; ?>

        <div class="profile-info">
            <p><strong>Логин:</strong> <?= htmlspecialchars($user['login']) ?></p>
            <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Дата регистрации:</strong> <?= htmlspecialchars($user['registration_date']) ?></p>
        </div>

        <div class="edit-form">
            <h3>Редактировать информацию</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Имя:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Телефон:</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn">Сохранить изменения</button>
            </form>
        </div>

        <h3>Информация о вашей подписке</h3>

        <?php if ($subscription): ?>
            <div class="subscription-info">
                <p><strong>Название подписки:</strong> <?= htmlspecialchars($subscription['name']) ?></p>
                <p><strong>Цена:</strong> <?= htmlspecialchars($subscription['price']) ?> руб.</p>
                <p><strong>Особенности:</strong> <?= nl2br(htmlspecialchars($subscription['features'])) ?></p>

                <div class="subscription-actions">
                    <form method="POST" action="" onsubmit="return confirm('Вы уверены, что хотите отменить подписку?');">
                        <button type="submit" name="cancel_subscription" class="btn-cancel">Отменить подписку</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="no-subscription">У вас нет активной подписки.</p>
        <?php endif; ?>

        <h3>Доступные подписки</h3>

        <table class="subscriptions-table">
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
                <th>Особенности</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT * FROM subscriptions");
            $stmt->execute();
            $subscriptions = $stmt->get_result();

            while ($row = $subscriptions->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['features'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

<?php require_once "footer.php"; ?>