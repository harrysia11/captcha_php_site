<?php

require_once __DIR__.'/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail(string $name, string $email, string $phone, string $message): bool{

    $mail = new PHPMailer(true);

    try{
        $mail->isSMTP();
        $mail->Host       = env('MAIL_HOST');
        $mail->Port       = (int) env('MAIL_PORT');
        $mail->SMTPAuth   = true;
        $mail->Username   = env('MAIL_USER');
        $mail->Password   = env('MAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // или ENCRYPTION_SMTPS для 465
        $mail->CharSet    = 'UTF-8';

        // Отправитель и получатель
        $mail->setFrom(env('MAIL_USER'), $name);
        $mail->addAddress(env('MAIL_BOX_ADDRESS'));
        $mail->addReplyTo($email, $name);

        // Содержимое
        $mail->Subject = 'Почта с сайта';
        $mail->Body    = "{$message}\n\nтел: {$phone}\n{$name}";

        $mail->send();
        return true;
    }catch(Exception $e){
        error_log('Mail transport error '.$e->getMessage());
        error_log('PHPMailer details: ' . $mail->ErrorInfo);
        return false;
    }
}