<?php
require_once 'db.php';

header('Content-Type: application/json');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function getRandomCaptcha($conn, $formType) {
    $stmt = $conn->prepare("SELECT id, image_path, answer FROM captcha_images ORDER BY RAND() LIMIT 1");
    if ($stmt && $stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $captcha = $result->fetch_assoc();
            $cleanAnswer = mb_strtolower(trim($captcha['answer']));
            $_SESSION[$formType . '_captcha_answer'] = $cleanAnswer;
            $_SESSION[$formType . '_captcha_id'] = $captcha['id'];
            return $captcha;
        }
    }
    return false;
}

$formType = isset($_GET['form']) && in_array($_GET['form'], ['login', 'register']) ? $_GET['form'] : 'login';
$captcha = getRandomCaptcha($conn, $formType);

if ($captcha) {
    echo json_encode([
        'success' => true,
        'image_path' => $captcha['image_path'],
        'form_type' => $formType
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Не удалось загрузить CAPTCHA',
        'default_image' => 'images/captcha/default.jpg'
    ]);
}