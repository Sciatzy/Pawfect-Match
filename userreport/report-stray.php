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
    $_SESSION['redirect_after_login'] = '/draft/userreport/report-stray.php';
    header('Location: ../login/login.php');
    exit();
}

$secret_key = $_ENV['JWT_SECRET'];
$token = $_COOKIE['token'];
$decoded = JWT::decode($token, keyOrKeyArray: new Key($secret_key, 'HS256'));

// Get user information
$user_id = $decoded->data->user_id;
$user = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Silently ignore errors
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a Stray Animal</title>
    <style>
        :root {
            --urgent: #ff6b6b;
            --high: #ffa502;
            --medium: #feca57;
            --low: #1dd1a1;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            text-align: center;
            position: relative;
            margin-bottom: 40px;
        }
        
        .header h1 {
            margin: 0;
            font-weight: 900;
            color:  #ff914d;
        }
        
        .back-link {
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .back-link:hover {
            text-decoration: underline;
            color: var(--urgent);
        }
        
        .back-arrow {
            font-size: 1.3rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .report-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .required:after {
            content: " *";
            color: var(--urgent);
        }
        
        input[type="text"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: inherit;
            font-size: 16px;
        }
        
        input[type="file"] {
            padding: 10px 0;
        }
        
        .urgency-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        
        .urgency-option {
            flex: 1;
            text-align: center;
        }
        
        .urgency-option input {
            display: none;
        }
        
        .urgency-option label {
            display: block;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: normal;
        }
        
        .urgency-option input:checked + label {
            font-weight: bold;
            transform: scale(1.05);
        }
        
        #urgent:checked + label { background: var(--urgent); color: white; }
        #high:checked + label { background: var(--high); color: white; }
        #medium:checked + label { background: var(--medium); }
        #low:checked + label { background: var(--low); color: white; }
        
        .submit-btn {
            background-color: #ff914d;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }
        
        .submit-btn:hover {
            background-color: #ff5252;
        }
        
        .image-preview {
            max-width: 300px;
            max-height: 300px;
            margin-top: 15px;
            display: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../login/index.php" class="back-link">
            <span class="back-arrow">←</span>
            Back to Home
        </a>
        <h1>Pawfect Match</h1>
    </div>

    <div class="container">
        <h1>Report a Stray Animal in Need</h1>
        
        <form class="report-form" action="process-stray.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ID" value="<?= $user_id ?>">
            
            <!-- Animal Information -->
            <div class="form-group">
                <label for="name" class="required">Animal's Name (if known)</label>
                <input type="text" id="name" name="name" placeholder="E.g. Brownie or Unknown">
            </div>
            
            <div class="form-group">
                <label for="animal-type" class="required">Animal Type</label>
                <select id="animal-type" name="animal_type" required>
                    <option value="">Select...</option>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="gender" class="required">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select...</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="unknown">Unknown</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="age">Approximate Age</label>
                <input type="number" id="age" name="age" min="0" max="30" placeholder="In years">
            </div>
            
            <div class="form-group">
                <label for="weight">Approximate Weight (kg)</label>
                <input type="number" id="weight" name="weight" step="0.1" min="0" placeholder="In kilograms">
            </div>
            
            <!-- Situation Details -->
            <div class="form-group">
                <label for="description" class="required">Description of Situation</label>
                <textarea id="description" name="description" rows="5" required 
                          placeholder="Describe the animal's condition, behavior, and exact location details"></textarea>
            </div>
            
            <div class="form-group">
                <label for="location" class="required">Exact Location</label>
                <input type="text" id="location" name="location" required 
                       placeholder="Street address, landmarks, or GPS coordinates">
            </div>
            
            <!-- Urgency Level -->
            <div class="form-group">
                <label class="required">Urgency Level</label>
                <div class="urgency-options">
                    <div class="urgency-option">
                        <input type="radio" id="urgent" name="urgency" value="urgent" required>
                        <label for="urgent">Urgent</label>
                    </div>
                    <div class="urgency-option">
                        <input type="radio" id="high" name="urgency" value="high">
                        <label for="high">High</label>
                    </div>
                    <div class="urgency-option">
                        <input type="radio" id="medium" name="urgency" value="medium" checked>
                        <label for="medium">Medium</label>
                    </div>
                    <div class="urgency-option">
                        <input type="radio" id="low" name="urgency" value="low">
                        <label for="low">Low</label>
                    </div>
                </div>
            </div>
            
            <!-- Photo Upload -->
            <div class="form-group">
                <label for="image" class="required">Photo of the Animal</label>
                <input type="file" id="image" name="image" accept="image/*" required
                       onchange="previewImage(this)">
                <img id="image-preview" class="image-preview" alt="Image preview">
            </div>
            
            <!-- Contact Information -->
            <div class="form-group">
                <label for="contact_info">Your Contact Information</label>
                <input type="tel" id="contact_info" name="contact_info" 
                       placeholder="Phone number (optional)">
                <small>For volunteers who want to help directly</small>
            </div>
            
            <button type="submit" class="submit-btn">Submit Report</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const file = input.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
