<?php
session_start();
require '../includes/db.php'; // Includes your database connection

// Check if user is logged in
if (!isset($_SESSION['ID'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'weight', 'age', 'gender', 'description'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Please fill all required fields");
        }
    }

    // Process image upload
    $uploadDir = '../uploads/pets/'; // Changed to use a proper relative path
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
    $webPath = '/uploads/pets/' . $filename; // Web-accessible path with leading slash

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        die("Image upload failed");
    }

    // Insert into database
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO pets 
            (user_id, name, weight, age, gender, description, image_path, size, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'M', NOW())"
        );
        
        $stmt->execute([
            $_SESSION['ID'],
            htmlspecialchars($_POST['name']),
            floatval($_POST['weight']),
            intval($_POST['age']),
            $_POST['gender'],
            htmlspecialchars($_POST['description']),
            $webPath // Use the web-accessible path
        ]);
        
        header('Location: pet-list.php');
        exit();
    } catch (PDOException $e) {
        // If database insertion fails, delete the uploaded file
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        die("Error saving pet: " . $e->getMessage());
    }
} else {
    // If not a POST request, redirect to the form page
    header('Location: post-pet.html');
    exit();
}
?>