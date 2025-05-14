<?php

session_start();

require '../includes/db.php';
require '../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if(!isset($_COOKIE['token'])){
    header('../login/login.php');
    exit();
}

$decoded = JWT::decode($_COOKIE['token'], new Key($_ENV['JWT_SECRET'], 'HS256'));

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $housingtype = $_POST['housing_type'];
    $yardsize = $_POST['yard_size'];
    $petexperience = $_POST['pet_experience'];
    $hoursalone = $_POST['hours_alone'];
    $comment = $_POST['comments'];
    $pet_id = $_POST['pet_id'];
    $user_id = $decoded->data->user_id;

    $stmt = $pdo->prepare("INSERT INTO adopters(user_id, full_name, email, phone, address, housing_type, yard_size, pet_experience, hours_alone, pet_id, comments) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$user_id, $fullname, $email, $phone, $address, $housingtype, $yardsize, $petexperience, $hoursalone, $pet_id, $comment]);

header("location: thank-you.php");
exit();
}