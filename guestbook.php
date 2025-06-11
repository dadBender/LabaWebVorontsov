<?php
require_once "header.php";

$file = "guestbook.txt";
$success = "";
$error = "";

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name && $message) {
        $entry = [
            'name' => $name,
            'message' => $message,
            'date' => date('Y-m-d H:i:s')
        ];

        // Добавим запись в файл
        file_put_contents($file, json_encode($entry) . PHP_EOL, FILE_APPEND);
        $success = "Сообщение добавлено!";
    } else {
        $error = "Пожалуйста, заполните все поля.";
    }
}

// Получаем все записи
$entries = [];
$wordCounts = [];

if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $entries[] = $data;

            // Подсчёт слов
            $words = explode(' ', mb_strtolower(strip_tags($data['message'])));
            foreach ($words as $word) {
                $word = preg_replace('/[^а-яa-z0-9]+/iu', '', $word); // очищаем от символов
                if ($word) {
                    $wordCounts[$word] = ($wordCounts[$word] ?? 0) + 1;
                }
            }
        }
    }
}
?>

<style>
    .guestbook-container {
        max-width: 800px;
        margin: 40px auto;
        padding: 30px;
        background-color: rgba(0, 0, 0, 0.8);
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        color: #fff;
        font-family: 'Arial', sans-serif;
    }

    .guestbook-container h2 {
        text-align: center;
        margin-bottom: 25px;
        font-size: 28px;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .guestbook-container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 40px;
    }

    .guestbook-container input,
    .guestbook-container textarea {
        padding: 12px;
        border: 2px solid #444;
        border-radius: 8px;
        background: #333;
        color: white;
        font-size: 16px;
    }

    .guestbook-container button {
        background-color: #e74c3c;
        color: white;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .guestbook-container button:hover {
        background-color: #c0392b;
    }

    .entry {
        margin-bottom: 20px;
        padding: 20px;
        border-bottom: 1px solid #444;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    .entry strong {
        color: #f1c40f;
        font-size: 18px;
    }

    .entry small {
        color: #bdc3c7;
        font-size: 14px;
    }

    .entry p {
        font-size: 16px;
        margin-top: 10px;
        line-height: 1.5;
    }

    .success {
        color: #2ecc71;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }

    .error {
        color: #e74c3c;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }

    .wordcount {
        margin-top: 40px;
        padding: 20px;
        background: #333;
        border-radius: 8px;
        font-size: 16px;
        color: #ecf0f1;
    }

    .wordcount h4 {
        margin-bottom: 20px;
        color: #f1c40f;
        font-size: 20px;
        text-transform: uppercase;
    }

    .wordcount strong {
        color: #e74c3c;
    }
</style>


<div class="guestbook-container">
    <h2>Гостевая книга</h2>

    <?php if ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Ваше имя" required>
        <textarea name="message" placeholder="Ваше сообщение" rows="4" required></textarea>
        <button type="submit">Оставить сообщение</button>
    </form>

    <?php if (!empty($entries)): ?>
        <h3>Сообщения:</h3>
        <?php foreach (array_reverse($entries) as $entry): ?>
            <div class="entry">
                <strong><?= htmlspecialchars($entry['name']) ?></strong> —
                <small><?= htmlspecialchars($entry['date']) ?></small>
                <p><?= nl2br(htmlspecialchars($entry['message'])) ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Пока нет сообщений.</p>
    <?php endif; ?>

    <?php if (!empty($wordCounts)): ?>
        <div class="wordcount">
            <h4>Статистика слов:</h4>
            <?php
            arsort($wordCounts);
            foreach ($wordCounts as $word => $count) {
                echo "<strong>$word</strong>: $count<br>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once "footer.php"; ?>
