<?php
session_start();

require '../includes/db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_COOKIE['token'])) {
    header('Location: ../login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adoption_id = $_POST['adoption_id'];
    $action = $_POST['action'];

    // Get adoption details including user email and pet name
    $stmt = $pdo->prepare("
        SELECT a.*, p.name as pet_name, u.email, CONCAT(u.firstname, ' ', u.lastname) as full_name 
        FROM adopters a 
        JOIN pets p ON a.pet_id = p.pets_id 
        JOIN users u ON a.user_id = u.ID 
        WHERE a.adopter_id = ?
    ");
    $stmt->execute([$adoption_id]);
    $adoption = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($action === 'approve') {
        // Update adoption status to approved in adopters table
        $stmt = $pdo->prepare("UPDATE adopters SET status = 'approved', application_date = CURRENT_TIMESTAMP WHERE adopter_id = ?");
        $stmt->execute([$adoption_id]);

        // Get the pet_id from the adoption
        $stmt = $pdo->prepare("SELECT pet_id FROM adopters WHERE adopter_id = ?");
        $stmt->execute([$adoption_id]);
        $pet_id = $stmt->fetchColumn();

        // Update pet status to adopted
        $stmt = $pdo->prepare("UPDATE pets SET status = 'adopted' WHERE pets_id = ?");
        $stmt->execute([$pet_id]);

        // Insert into adoptions table with app_status as 'approved'
        $stmt = $pdo->prepare("
            INSERT INTO adoptions (adopter_id, pet_id, adoption_date, app_status) 
            VALUES (?, ?, CURRENT_TIMESTAMP, 'approved')
        ");
        $stmt->execute([$adoption_id, $pet_id]);

        // Send approval email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'babaylangemini@gmail.com';
            $mail->Password = 'fsde mhxu jqud edvl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('babaylangemini@gmail.com', 'Pawfect Match');
            $mail->addAddress($adoption['email'], $adoption['full_name']);

            $mail->isHTML(true);
            $mail->Subject = "Adoption Application Approved - Pawfect Match";
            $mail->Body = "
                <h2>Congratulations! Your adoption application has been approved!</h2>
                <p>Dear {$adoption['full_name']},</p>
                <p>We are pleased to inform you that your application to adopt {$adoption['pet_name']} has been approved!</p>
                <p>Please visit our shelter to complete the adoption process and take your new furry friend home.</p>
                <p>If you have any questions, please don't hesitate to contact us.</p>
                <br>
                <p>Best regards,</p>
                <p>The Pawfect Match Team</p>
            ";
            $mail->AltBody = "Congratulations! Your adoption application for {$adoption['pet_name']} has been approved!";

            $mail->send();
        } catch (Exception $e) {
            // Log the error but continue with the approval process
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        header('Location: pending-adoptions.php?status=approved');
        exit();
    } elseif ($action === 'reject') {
        // Update adoption status to rejected
        $stmt = $pdo->prepare("UPDATE adopters SET status = 'rejected' WHERE adopter_id = ?");
        $stmt->execute([$adoption_id]);

        // Send rejection email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'babaylangemini@gmail.com';
            $mail->Password = 'fsde mhxu jqud edvl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('babaylangemini@gmail.com', 'Pawfect Match');
            $mail->addAddress($adoption['email'], $adoption['full_name']);

            $mail->isHTML(true);
            $mail->Subject = "Adoption Application Status - Pawfect Match";
            $mail->Body = "
                <h2>Adoption Application Status Update</h2>
                <p>Dear {$adoption['full_name']},</p>
                <p>We regret to inform you that your application to adopt {$adoption['pet_name']} has not been approved at this time.</p>
                <p>We appreciate your interest in adoption and encourage you to consider other pets in our shelter that might be a better match for your situation.</p>
                <p>If you have any questions about this decision, please feel free to contact us.</p>
                <br>
                <p>Best regards,</p>
                <p>The Pawfect Match Team</p>
            ";
            $mail->AltBody = "We regret to inform you that your application to adopt {$adoption['pet_name']} has not been approved at this time.";

            $mail->send();
        } catch (Exception $e) {
            // Log the error but continue with the rejection process
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        header('Location: pending-adoptions.php?status=rejected');
        exit();
    }
} else {
    header('Location: pending-adoptions.php');
    exit();
}