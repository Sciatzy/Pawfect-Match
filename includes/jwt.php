<?php

require_once '../vendor/autoload.php';
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$secret_key = $_ENV['JWT_SECRET'];

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwt_token($user_id, $firstname, $lastname){
    $jwt_secret = $_ENV['JWT_SECRET'];
    $payload = JWT::encode(
        array(
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'data' => array(
                'user_id' => $user_id,
                'name' => $firstname . ' ' . $lastname
            )
        ),
            $jwt_secret,
            'HS256'
    );

    return $payload;
}