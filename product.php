<?php
require_once "header.php";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once "db.php";

// Получаем уникальные жанры из базы данных для select
$genres_query = $conn->query("SELECT DISTINCT genre FROM products WHERE category = 'movies' AND genre IS NOT NULL");
$available_genres = [];
while ($row = $genres_query->fetch_assoc()) {
    $available_genres[] = $row['genre'];
}
?>

    <style>
        .filter-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .filter-title {
            color: white;
            margin-bottom: 25px;
            font-size: 28px;
            font-weight: 600;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            background: rgba(20,20,20,0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }

        .filter-form input,
        .filter-form select {
            padding: 12px 15px;
            border: 1px solid #333;
            background: #141414;
            color: white;
            border-radius: 4px;
            font-size: 14px;
        }

        .filter-form input {
            flex: 1;
            min-width: 200px;
        }

        .filter-form select {
            width: 200px;
        }

        .filter-form button {
            padding: 12px 25px;
            background: #e50914;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .filter-form button:hover {
            background: #b20710;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .movie-card {
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s;
            background: #141414;
        }

        .movie-card:hover {
            transform: scale(1.03);
        }

        .movie-poster {
            width: 100%;
            height: 320px;
            object-fit: cover;
        }

        .movie-info {
            padding: 15px;
        }

        .movie-title {
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .movie-meta {
            color: #b3b3b3;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .movie-rating {
            display: flex;
            align-items: center;
            color: #e50914;
            font-weight: bold;
        }

        .no-results {
            color: white;
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .movie-poster {
                height: 220px;
            }
        }
    </style>

    <main class="filter-container">
        <h2 class="filter-title">Поиск фильмов</h2>

        <form method="GET" class="filter-form">
            <input type="text" name="title" placeholder="Название фильма" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">
            <select name="genre">
                <option value="">Все жанры</option>
                <?php foreach ($available_genres as $genre): ?>
                    <option value="<?= htmlspecialchars($genre) ?>"
                        <?= ($_GET['genre'] ?? '') == $genre ? 'selected' : '' ?>>
                        <?= htmlspecialchars($genre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="year">
                <option value="">Все годы</option>
                <?php for($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?= $y ?>" <?= ($_GET['year'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit">Найти</button>
        </form>

        <div class="movies-grid">
            <?php
            $title = $_GET['title'] ?? '';
            $genre = $_GET['genre'] ?? '';
            $year = $_GET['year'] ?? '';
            $rating = $_GET['rating'] ?? '';

            $query = "SELECT * FROM products WHERE category = 'movies'";

            if (!empty($title)) {
                $title = $conn->real_escape_string($title);
                $query .= " AND title LIKE '%$title%'";
            }
            if (!empty($genre)) {
                $genre = $conn->real_escape_string($genre);
                $query .= " AND genre = '$genre'";
            }
            if (!empty($year)) {
                $query .= " AND year = " . intval($year);
            }
            if (!empty($rating)) {
                $query .= " AND rating >= " . floatval($rating);
            }

            // Для отладки можно вывести SQL запрос
            // echo "<pre>SQL: $query</pre>";

            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="movie-card">';
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '" class="movie-poster">';
                    echo '<div class="movie-info">';
                    echo '<h3 class="movie-title">' . htmlspecialchars($row['title']) . '</h3>';
                    echo '<div class="movie-meta">' . htmlspecialchars($row['genre']) . ' • ' . $row['year'] . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="no-results">По вашему запросу ничего не найдено. Попробуйте изменить параметры поиска.</div>';
            }
            ?>
        </div>
    </main>

<?php require_once "footer.php"; ?>