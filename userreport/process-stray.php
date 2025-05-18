<?php
session_start();
require_once '../includes/db.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Check if user is logged in
if (!isset($_COOKIE['token'])) {
    header('Location: ../login/login.php');
    exit();
}

$secret_key = $_ENV['JWT_SECRET'];
$token = $_COOKIE['token'];
$decoded = JWT::decode($token, keyOrKeyArray: new Key($secret_key, 'HS256'));

// Get user ID from token
$user_id = $decoded->data->user_id;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'animal_type', 'gender', 'description', 'location', 'urgency'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Please fill all required fields");
        }
    }

    // Process image upload
    $uploadDir = __DIR__ . '/uploads/'; // Full path to uploads directory
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Check if file was uploaded properly
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading file");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $fileType = $_FILES['image']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG and PNG images are allowed");
    }

    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    $webPath = 'uploads/' . $filename; // Web-accessible path

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        die("Image upload failed");
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO strays 
            (reporter_id, name, animal_type, gender, age, weight, description, location, urgency, image_path, contact_info, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        
        $stmt->execute([
            $user_id,
            htmlspecialchars($_POST['name']),
            htmlspecialchars($_POST['animal_type']),
            htmlspecialchars($_POST['gender']),
            !empty($_POST['age']) ? intval($_POST['age']) : null,
            !empty($_POST['weight']) ? floatval($_POST['weight']) : null,
            htmlspecialchars($_POST['description']),
            htmlspecialchars($_POST['location']),
            htmlspecialchars($_POST['urgency']),
            $webPath,
            !empty($_POST['contact_info']) ? htmlspecialchars($_POST['contact_info']) : null
        ]);
        
        header('Location: stray-reports.php');
        exit();
    } catch (PDOException $e) {
        // If database insertion fails, delete the uploaded file
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        die("Error saving report: " . $e->getMessage());
    }
} else {
    // If not a POST request, redirect to the form page
    header('Location: report-stray.php');
    exit();
}
?>