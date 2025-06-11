<?php
require_once "db.php";
require_once "Header.php";
?>

    <style>
        .slider-container {
            max-width: 1200px;
            margin: 40px auto;
            position: relative;
            overflow: hidden;
            padding: 0 40px;
        }

        .slider-track {
            display: flex;
            gap: 20px;
            transition: transform 0.5s ease-in-out;
        }

        .slider-card {
            min-width: calc(20% - 16px);
            box-sizing: border-box;
            position: relative;
            transition: transform 0.3s ease;
        }

        .slider-card:hover {
            transform: scale(1.05);
        }

        .slider-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .movie-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 15px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            border-radius: 0 0 8px 8px;
        }

        .movie-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .movie-meta {
            font-size: 14px;
            color: #ccc;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(20,20,20,0.7);
            color: #fff;
            border: none;
            width: 40px;
            height: 60px;
            cursor: pointer;
            font-size: 24px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .slider-btn:hover {
            background: rgba(229,9,20,0.8);
        }

        .slider-btn.left {
            left: 0;
        }

        .slider-btn.right {
            right: 0;
        }

        .section-title {
            text-align: center;
            font-size: 32px;
            margin: 40px 0 30px;
            color: white;
            position: relative;
        }

        .section-title:after {
            content: "";
            display: block;
            width: 100px;
            height: 4px;
            background: #e50914;
            margin: 15px auto 0;
        }

        @media (max-width: 1024px) {
            .slider-card {
                min-width: calc(25% - 15px);
            }
        }

        @media (max-width: 768px) {
            .slider-card {
                min-width: calc(33.33% - 13px);
            }

            .slider-card img {
                height: 250px;
            }
        }

        @media (max-width: 576px) {
            .slider-card {
                min-width: calc(50% - 10px);
            }

            .slider-container {
                padding: 0 20px;
            }
        }
    </style>

    <h2 class="section-title">Популярные фильмы</h2>

    <div class="slider-container">
        <div class="slider-track" id="sliderTrack">
            <?php
            $stmt = $conn->query("SELECT * FROM products WHERE category = 'movies' ORDER BY RAND() LIMIT 10");

            while ($row = $stmt->fetch_assoc()) {
                echo '<div class="slider-card">';
                if (!empty($row['image'])) {
                    echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                } else {
                    echo '<img src="images/movie-placeholder.jpg" alt="Постер фильма">';
                }
                echo '<div class="movie-info">';
                echo '<div class="movie-title">' . htmlspecialchars($row['title']) . '</div>';
                echo '<div class="movie-meta">' . htmlspecialchars($row['genre']) . ' • ' . $row['year'] . '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <button class="slider-btn left" onclick="prevSlide()">‹</button>
        <button class="slider-btn right" onclick="nextSlide()">›</button>
    </div>

    <script>
        let currentSlide = 0;
        const track = document.getElementById('sliderTrack');
        const slides = track.children;
        const visibleSlides = 5; // Количество видимых слайдов
        const slideWidth = slides[0].offsetWidth + 20; // Ширина слайда + gap

        function updateSliderPosition() {
            track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
        }

        function prevSlide() {
            if (currentSlide > 0) {
                currentSlide--;
                updateSliderPosition();
            }
        }

        function nextSlide() {
            if (currentSlide < slides.length - visibleSlides) {
                currentSlide++;
                updateSliderPosition();
            }
        }

        // Автоматическое определение количества видимых слайдов
        function updateVisibleSlides() {
            const containerWidth = document.querySelector('.slider-container').offsetWidth;
            const cardWidth = slides[0].offsetWidth;
            visibleSlides = Math.floor(containerWidth / cardWidth);
        }

        window.addEventListener('resize', function() {
            updateVisibleSlides();
            updateSliderPosition();
        });

        // Инициализация
        updateVisibleSlides();
    </script>

<?php require_once "Footer.php"; ?>