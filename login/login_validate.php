<?php

session_start();

require '../includes/db.php';
require '../includes/jwt.php';


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($password, $user['password'])){
        $_SESSION['logged_in'] = true;
        $token = jwt_token($user['ID'], $user['firstname'], $user['lastname']);
        setcookie("token", $token, time() + 3600, "/", "", true, true);
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = "Credentials are incorrect";
        header("Location: login.php");
        exit();
    }
}

?>
