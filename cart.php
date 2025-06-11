<?php
require_once "header.php";
require_once "db.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];

// Удаление из корзины
if (isset($_GET['remove'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}

// Оформление заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['message'] = "Для оформления подписки необходимо авторизоваться.";
        header("Location: login.php");
        exit();
    }

    $userId = $_SESSION['user_id'];

    if (!empty($cart)) {
        $subscription = $cart[0];
        $subscriptionId = (int)($subscription['subscription_id'] ?? 0);

        // Рассчитываем дату окончания подписки (текущая дата + 1 месяц)
        $expiresDate = new DateTime();
        $expiresDate->add(new DateInterval('P1M')); // Добавляем 1 месяц
        $expires = $expiresDate->format('Y-m-d');

        try {
            // Обновляем данные пользователя
            $stmt = $conn->prepare("UPDATE users SET subscription_id = ?, subscription_expires = ? WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Ошибка подготовки запроса: " . $conn->error);
            }

            $stmt->bind_param("isi", $subscriptionId, $expires, $userId);

            if (!$stmt->execute()) {
                throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
            }

            $stmt->close();

            // Также можно добавить запись в таблицу заказов, если она есть
            // $orderStmt = $conn->prepare("INSERT INTO orders (user_id, subscription_id, order_date, expires_date) VALUES (?, ?, NOW(), ?)");
            // $orderStmt->bind_param("iis", $userId, $subscriptionId, $expires);
            // $orderStmt->execute();
            // $orderStmt->close();

            // Очищаем корзину
            $_SESSION['cart'] = [];
            $_SESSION['message'] = "Подписка успешно оформлена! Действует до " . $expiresDate->format('d.m.Y');

        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['message'] = "Произошла ошибка при оформлении подписки. Пожалуйста, попробуйте позже.";
        }
    } else {
        $_SESSION['message'] = "Корзина пуста. Выберите подписку для оформления.";
    }

    header("Location: cart.php");
    exit();
}
?>

    <style>
        .cart-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px;
            background: rgba(20, 20, 20, 0.8);
            border-radius: 12px;
            color: white;
        }

        .cart-item {
            background: #1f1f1f;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
        }

        .cart-item h3 {
            margin: 0;
        }

        .cart-item .price {
            font-size: 18px;
            font-weight: bold;
        }

        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .cart-actions button,
        .cart-actions a {
            padding: 10px 20px;
            background: #e50914;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }

        .cart-actions button:hover,
        .cart-actions a:hover {
            background: #b20710;
        }

        .success-message {
            color: #4CAF50;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .error-message {
            color: #e50914;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>

    <div class="cart-container">
        <h1>Корзина</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="<?= strpos($_SESSION['message'], 'ошибка') !== false ? 'error-message' : 'success-message' ?>">
                <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <p>Корзина пуста. <a href="product.php" style="color: #e50914;">Выбрать подписку</a></p>
        <?php else: ?>
            <?php foreach ($cart as $item): ?>
                <div class="cart-item">
                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                    <p class="price"><?= htmlspecialchars($item['price']) ?> ₽</p>
                    <p><?= nl2br(htmlspecialchars($item['features'])) ?></p>
                </div>
            <?php endforeach; ?>

            <div class="cart-actions">
                <form method="post">
                    <button type="submit" name="checkout">Оформить подписку</button>
                </form>
                <a href="cart.php?remove=1">Удалить</a>
            </div>
        <?php endif; ?>
    </div>

<?php require_once "footer.php"; ?>