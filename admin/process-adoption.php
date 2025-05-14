<?php
session_start();

require '../includes/db.php';

if (!isset($_COOKIE['token'])) {
    header('Location: ../login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adoption_id = $_POST['adoption_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        // Update adoption status to approved
        $stmt = $pdo->prepare("UPDATE adopters SET status = 'approved', application_date = CURRENT_TIMESTAMP WHERE adopter_id = ?");
        $stmt->execute([$adoption_id]);

        // Get the pet_id from the adoption
        $stmt = $pdo->prepare("SELECT pet_id FROM adopters WHERE adopter_id = ?");
        $stmt->execute([$adoption_id]);
        $pet_id = $stmt->fetchColumn();

        // Update pet status to adopted
        $stmt = $pdo->prepare("UPDATE pets SET status = 'adopted' WHERE pets_id = ?");
        $stmt->execute([$pet_id]);

        header('Location: pending-adoptions.php?status=approved');
        exit();
    } elseif ($action === 'reject') {
        // Update adoption status to rejected
        $stmt = $pdo->prepare("UPDATE adoptions SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$adoption_id]);
        header('Location: pending-adoptions.php?status=rejected');
        exit();
    }
} else {
    header('Location: pending-adoptions.php');
    exit();
}