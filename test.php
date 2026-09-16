<?php
require_once __DIR__ . '/src/config.php';
require_once __DIR__.'/src/captcha.php';
require_once __DIR__ . '/src/mailer.php';

var_dump(env('PORT'));
var_dump(env('MAIL_HOST', 'default-host'));
var_dump($_ENV);
var_dump(verifyCaptcha('fake-token-12345'));

echo "Пробую отправить...\n";

$ok = sendMail(
    'Тест Тестов',
    'test@example.com',
    '+7 999 000-00-00',
    'Это тестовое сообщение из mailer.php'
);

var_dump($ok);