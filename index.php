<?php include 'Header.php'; ?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
?>



    <style>
        main {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
            background-color: var(--primary-color);
        }

        .hero-title {
            font-size: 42px;
            text-align: center;
            margin-bottom: 40px;
            color: var(--text-color);
            font-weight: bold;
            background: linear-gradient(90deg, #e50914, #f01828);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .intro-section {
            font-size: 18px;
            line-height: 1.8;
            color: #d1d1d1;
            margin-bottom: 50px;
        }

        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 40px;
            color: var(--text-color);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: center;
            margin-bottom: 60px;
        }

        .feature-card {
            background: var(--dark-bg);
            border-radius: 8px;
            padding: 25px;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
            object-fit: contain;
        }

        .feature-card h3 {
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: 20px;
        }

        .feature-card p {
            color: #b3b3b3;
            font-size: 16px;
        }

        .advantages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            text-align: center;
            margin-bottom: 60px;
        }

        .advantage-card {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #333;
        }

        .advantage-card h3 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .movie-card {
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s;
        }

        .movie-card:hover {
            transform: scale(1.05);
        }

        .movie-poster {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .movie-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding: 15px;
            color: white;
        }

        .movie-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .movie-meta {
            font-size: 14px;
            color: #d1d1d1;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 32px;
            }

            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .movie-poster {
                height: 220px;
            }
        }
    </style>

    <main>
        <h1 class="hero-title">CinemaHub — Лучшие фильмы и сериалы в одном месте</h1>

        <section class="intro-section">
            <p>Добро пожаловать в CinemaHub — ваш персональный онлайн-кинотеатр с огромной коллекцией фильмов, сериалов и эксклюзивного контента. Мы собрали для вас лучшие киноленты со всего мира в превосходном качестве.</p>

            <p>Наша платформа предлагает уникальные возможности: просмотр в 4K и HDR, персональные рекомендации на основе ваших предпочтений, возможность создавать собственные списки для просмотра и синхронизацию между устройствами.</p>

            <p>CinemaHub — это не просто кинотеатр, это целая экосистема для киноманов. Мы регулярно обновляем нашу библиотеку, добавляя новые релизы, классику мирового кино и эксклюзивные проекты, которые вы не найдете больше нигде.</p>

            <p>Откройте для себя мир безграничного кино. Смотрите где угодно и когда угодно — на телевизоре, компьютере, планшете или смартфоне. Ваши любимые фильмы всегда под рукой.</p>

            <p><strong>Подключайтесь к CinemaHub сегодня — первый месяц бесплатно!</strong></p>
        </section>

        <section>
            <h2 class="section-title">Как это работает?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/3632/3632069.png" class="feature-icon" alt="Выбор фильма">
                    <h3>Выберите фильм</h3>
                    <p>Огромная библиотека фильмов и сериалов на любой вкус с удобной системой поиска</p>
                </div>
                <div class="feature-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/3161/3161837.png" class="feature-icon" alt="Просмотр">
                    <h3>Смотрите в лучшем качестве</h3>
                    <p>Доступно 4K, HDR и Dolby Atmos на поддерживаемых устройствах</p>
                </div>
                <div class="feature-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/3294/3294134.png" class="feature-icon" alt="Устройства">
                    <h3>На любом устройстве</h3>
                    <p>Телевизор, компьютер, планшет или смартфон — ваше кино всегда с вами</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="section-title">Почему выбирают нас?</h2>
            <div class="advantages-grid">
                <div class="advantage-card">
                    <h3>Эксклюзивный контент</h3>
                    <p>Фильмы и сериалы, которые вы не найдете на других платформах</p>
                </div>
                <div class="advantage-card">
                    <h3>Без рекламы</h3>
                    <p>Наслаждайтесь просмотром без перерывов на назойливую рекламу</p>
                </div>
                <div class="advantage-card">
                    <h3>Персонализация</h3>
                    <p>Умные рекомендации на основе ваших предпочтений</p>
                </div>
                <div class="advantage-card">
                    <h3>Семейный доступ</h3>
                    <p>До 5 профилей с индивидуальными рекомендациями</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="section-title">Популярные новинки</h2>
            <div class="movies-grid">
                <?php
                $result = $conn->query("SELECT * FROM products WHERE category = 'movies' ORDER BY id DESC LIMIT 6");
                while ($row = $result->fetch_assoc()):
                    ?>
                    <div class="movie-card">
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="movie-poster" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="movie-info">
                            <div class="movie-title"><?= htmlspecialchars($row['title']) ?></div>
                            <div class="movie-meta"><?= $row['genre'] ?> • <?= $row['year'] ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>


    </main>

<?php include 'Footer.php'; ?>