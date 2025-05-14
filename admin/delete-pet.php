<?php
require '../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pets_id'])) {
    $pets_id = $_POST['pets_id'];

    // Optionally: Only allow admins (check session or user role)
    if (!isset($_SESSION['ID'])) {
        header('Location: ../login.php');
        exit;
    }

    // Delete pet from DB
    $stmt = $pdo->prepare("DELETE FROM pets WHERE pets_id = ?");
    $stmt->execute([$pets_id]);

    // Redirect back to pet list
    header("Location: pet-list.php");
    exit;
} else {
    header("Location: pet-list.php");
    exit;
}
