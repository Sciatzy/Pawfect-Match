<?php
require_once '../includes/db.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

session_start();

// Check for JWT token
if (!isset($_COOKIE['token'])) {
    header('Location: ../login/login.php');
    exit();
}

$secret_key = $_ENV['JWT_SECRET'];
$token = $_COOKIE['token'];

try {
    $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
    $user_id = $decoded->data->user_id ?? null;
    if (!$user_id) {
        die('Unauthorized: Invalid user.');
    }
} catch (Exception $e) {
    header('Location: ../login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = $_POST['report_id'];
    try {
        $stmt = $pdo->prepare("UPDATE strays SET status = 'rescued', rescued_date = NOW(), rescued_by = ? WHERE stray_id = ?");      
         $stmt->execute([$user_id, $report_id]);
        $_SESSION['success'] = "Stray has been marked as rescued successfully.";
    } catch (PDOException $e) {
        error_log("Error marking stray as rescued: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while updating the stray status.";
    }
    header("Location: stray-reports.php");
    exit();
} else {
    header("Location: stray-reports.php");
    exit();
}
?>