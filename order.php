<?php
require_once "header.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "db.php";  // Подключаем файл с настройками подключения к базе данных

// Проверка авторизации

if (!isset($_SESSION['user_id'])) {
    echo "<p style='text-align:center; margin-top: 40px;'>Пожалуйста, <a href='#' onclick=\"document.getElementById('loginModal').style.display='block'\">войдите</a>, чтобы сделать заказ.</p>";
    require_once "footer.php";
    exit();
}

// Получаем активные подписки из базы данных с использованием mysqli
$sql = "SELECT id, name, price, features FROM subscriptions WHERE active = 1";
$result = $conn->query($sql);

// Проверка, есть ли данные в базе
if ($result->num_rows > 0) {
    $subscriptions = $result->fetch_all(MYSQLI_ASSOC);  // Получаем все подписки в массив
} else {
    $subscriptions = [];  // Если подписок нет, создаём пустой массив
}

// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscription_id'])) {
    $subId = (int)$_POST['subscription_id'];

    // Находим подписку в полученных данных
    foreach ($subscriptions as $sub) {
        if ($sub['id'] == $subId) {
            // Добавляем подписку в корзину сессии
            $_SESSION['cart'] = [
                [
                    'subscription_id' => $sub['id'],
                    'name' => $sub['name'],
                    'price' => $sub['price'],
                    'features' => $sub['features'],
                ]
            ];
            header("Location: cart.php");  // Перенаправляем на страницу корзины
            exit();
        }
    }
}

 // Подключаем шапку страницы
?>

<style>
    .subscriptions-container {
        max-width: 900px;
        margin: 50px auto;
        padding: 40px;
        background: rgba(20, 20, 20, 0.8);
        border-radius: 12px;
        color: white;
    }

    .subscription-card {
        background: #141414;
        border: 1px solid #333;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
    }

    .subscription-info h3 {
        margin: 0 0 10px;
    }

    .subscription-info p {
        margin: 5px 0;
        white-space: pre-line; /* сохраняем переносы строк */
    }

    .subscription-info .price {
        font-size: 20px;
        font-weight: bold;
    }

    .subscription-form button {
        margin-top: 10px;
        padding: 10px 20px;
        background: #e50914;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .subscription-form button:hover {
        background: #b20710;
    }
</style>

<div class="subscriptions-container">
    <h1>Выберите подписку</h1>
    <?php foreach ($subscriptions as $sub): ?>
        <div class="subscription-card">
            <div class="subscription-info">
                <h3><?= htmlspecialchars($sub['name']) ?></h3>
                <p class="price"><?= htmlspecialchars($sub['price']) ?> ₽</p>
                <p><?= nl2br(htmlspecialchars($sub['features'])) ?></p>
                <form class="subscription-form" method="post">
                    <input type="hidden" name="subscription_id" value="<?= $sub['id'] ?>">
                    <button type="submit">Добавить в корзину</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once "footer.php"; ?>
