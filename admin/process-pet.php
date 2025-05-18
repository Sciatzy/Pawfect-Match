<?php
session_start();
require '../includes/db.php'; // Uses your local DB credentials

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'weight', 'age', 'gender', 'description'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            die("Please fill all required fields");
        }
    }
    
    // Process image upload
    $uploadDir = __DIR__ . '/uploads/pets/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
   
    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG and PNG images are allowed");
    }
    
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        die("Image upload failed");
    }
    
    // Insert into database
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO pets
            (name, weight, age, gender, description, image_path)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
       
        $stmt->execute([
            htmlspecialchars($_POST['name']),
            floatval($_POST['weight']),
            intval($_POST['age']),
            $_POST['gender'],
            htmlspecialchars($_POST['description']),
            'uploads/pets/' . $filename
        ]);
       
        header('Location: pet-list.php');
        exit();
    } catch (PDOException $e) {
        unlink($targetPath); // Delete uploaded file on error
        die("Error saving pet: " . $e->getMessage());
    }
} else {
    header('Location: post-pet.php');
    exit();
}
?>