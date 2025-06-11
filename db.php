<?php

$host = "localhost";
$user = "root";
$password = "2468";
$database = "vanyalab4";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>
