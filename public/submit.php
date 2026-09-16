<?php

require_once __DIR__.'/../src/config.php';
require_once __DIR__.'/../src/captcha.php';
require_once __DIR__.'/../src/mailer.php';


function jsonResponse(array $data, int $statusCode = 200): void{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
}

   $body = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $name    = trim($body['name']    ?? '');
    $phone   = trim($body['phone']   ?? '');
    $email   = trim($body['email']   ?? '');
    $message = trim($body['message'] ?? '');
    $token   = trim($body['token']   ?? '');

    // Валидация полей
    if (!$name || !$email || !$message || !$phone) {
        jsonResponse([
            'success' => false,
            'message' => 'Заполните все поля',
        ], 400);
    }

    // Проверка капчи
    $captchaResult = verifyCaptcha($token);

    if (
        !$captchaResult ||
        empty($captchaResult['success']) ||
        ($captchaResult['score'] ?? 0) < 0.9
    ) {
        jsonResponse([
            'success' => false,
            'message' => 'Есть подозрение что Вы - бот',
        ], 403);
    }

    error_log('captcha success: ' . json_encode($captchaResult));

    // Отправка письма
    $sent = sendMail($name, $email, $phone, $message);

    if ($sent) {
        jsonResponse([
            'success' => true,
            'message' => 'Запрос успешно отправлен',
        ]);
    } else {
        jsonResponse([
            'success' => false,
            'message' => 'Ошибка отправки письма',
        ], 500);
    }
