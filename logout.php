<?php
session_start();  // Начать сессию, чтобы работать с ней

// Удаляем все данные сессии
session_unset();

// Уничтожаем саму сессию
session_destroy();

// Редирект на главную страницу или страницу входа
header("Location: index.php");
exit();
?>
