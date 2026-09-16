<?php

require_once __DIR__.'/config.php';

function verifyCaptcha(string $token): ?array {

    $secretKey = env('CAPTCHA_SECRET_KEY');
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    if($token === ''){
        error_log('Token is empty');
        return null;
    }

    $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'secret'   => $secretKey,
                'response' => $token,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($error){
        error_log('Captcha error '.$error);
        return null;
    }

    if($httpCode !== 200){
        error_log('Http code = '. $httpCode);
        return null;
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        error_log('Captcha: invalid JSON response: ' . $response);
        return null;
    }

    return $data;
}