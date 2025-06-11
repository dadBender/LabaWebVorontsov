<style>
    .big-footer {
        background-color: #141414;
        color: #fff;
        padding: 60px 40px 40px;
        border-top: 1px solid #333;
    }

    .footer-columns {
        display: flex;
        justify-content: space-between;
        gap: 40px;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-column {
        flex: 1;
        min-width: 200px;
    }

    .footer-column h3 {
        margin-bottom: 20px;
        font-size: 16px;
        color: #fff;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .footer-column ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-column ul li {
        margin-bottom: 12px;
    }

    .footer-column ul li a,
    .footer-column p,
    .footer-column a {
        color: #b3b3b3;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
    }

    .footer-column ul li a:hover,
    .footer-column a:hover {
        color: #fff;
    }

    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .social-links a {
        display: inline-block;
        width: 36px;
        height: 36px;
        background-color: #333;
        border-radius: 50%;
        text-align: center;
        line-height: 36px;
        transition: background-color 0.3s ease;
    }

    .social-links a:hover {
        background-color: #e50914;
    }

    .app-badges {
        margin-top: 25px;
    }

    .app-badges img {
        height: 40px;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .copyright {
        margin-top: 50px;
        text-align: center;
        color: #777;
        font-size: 12px;
        padding-top: 20px;
        border-top: 1px solid #333;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .language-selector {
        margin-top: 20px;
    }

    .language-selector select {
        background-color: #000;
        color: #fff;
        border: 1px solid #333;
        padding: 8px 15px;
        border-radius: 4px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .footer-columns {
            gap: 30px;
        }

        .footer-column {
            min-width: 150px;
        }
    }
</style>

<footer class="big-footer">
    <div class="footer-columns">
        <div class="footer-column">
            <h3>Меню</h3>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="gallery.php">Фильмы</a></li>
                <li><a href="product.php">Сериалы</a></li>
                <li><a href="order.php">Подписка</a></li>
                <li><a href="cart.php">Мой список</a></li>
                <li><a href="contacts.php">Контакты</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>О нас</h3>
            <ul>
                <li><a href="#">О компании</a></li>
                <li><a href="#">Вакансии</a></li>
                <li><a href="#">Для партнёров</a></li>
                <li><a href="#">Реклама</a></li>
                <li><a href="#">Подарочные карты</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Помощь</h3>
            <ul>
                <li><a href="#">Центр поддержки</a></li>
                <li><a href="#">Способы оплаты</a></li>
                <li><a href="#">Устройства</a></li>
                <li><a href="#">Конфиденциальность</a></li>
                <li><a href="#">Настройки cookies</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Скачайте приложение</h3>
            <div class="app-badges">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Download_on_the_App_Store_Badge.svg/1200px-Download_on_the_App_Store_Badge.svg.png" alt="App Store">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Google_Play_Store_badge_EN.svg/1200px-Google_Play_Store_badge_EN.svg.png" alt="Google Play">
            </div>

            <h3 style="margin-top: 25px;">Мы в соцсетях</h3>
            <div class="social-links">
                <a href="#" title="VK">VK</a>
                <a href="#" title="Telegram">TG</a>
                <a href="#" title="YouTube">YT</a>
                <a href="#" title="Twitter">TW</a>
            </div>
        </div>
    </div>

    <div class="copyright">
        © <?= date('Y') ?> CinemaHub. Все права защищены.
    </div>
</footer>