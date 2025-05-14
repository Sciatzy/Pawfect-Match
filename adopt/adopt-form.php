<?php
session_start();
require_once '../includes/db.php';
require '../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$pet_id = $_POST['pet_id'];

// Check if user is logged in
if (!isset($_COOKIE['token'])) {
    // Save the pet_id in session for redirection after login
    if (isset($pet_id)) {
        $_SESSION['intended_pet_id'] = $_GET['pet_id'];
    }
    $_SESSION['error'] = "You must be logged in to adopt a pet.";
    header('Location: ../login/login.php');
    exit();
}

// If pet_id is provided in URL, fetch pet details
$pet = null;
if (isset($pet_id)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pets WHERE pets_id = ?");
        $stmt->execute([$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Silently ignore errors, pet info will just not be displayed
    }
}
$secret_key = $_ENV['JWT_SECRET'];
$token = $_COOKIE['token'];
$decoded = JWT::decode($token, keyOrKeyArray: new Key($secret_key, 'HS256'));

// Get user information for pre-filling the form
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
    <title>Adoption Application - Pawfect Match</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --primary-hover: #ff5252;
            --secondary-color: #4CAF50;
            --text-color: #333;
            --light-text: #666;
            --border-color: #ddd;
            --light-bg: #f9f9f9;
            --error-color: #f44336;
            --success-color: #4CAF50;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--light-bg);
            padding: 0;
            margin: 0;
        }
        
        .header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-weight: 900;
            color: var(--text-color);
        }
        
        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .pet-preview {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .pet-preview img {
            width: 120px;
            height: 120px;
            border-radius: 10px;
            object-fit: cover;
            margin-right: 20px;
        }
        
        .pet-preview-details h2 {
            margin: 0 0 10px 0;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .pet-preview-details p {
            margin: 5px 0;
            color: var(--light-text);
        }
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 900;
        }
        
        .form-section {
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }
        
        .form-section:last-of-type {
            border-bottom: none;
        }
        
        h2 {
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-family: 'Montserrat', sans-serif;
            font-size: 1rem;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            margin-top: 5px;
        }
        
        .checkbox-group label {
            flex: 1;
        }
        
        .submit-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .submit-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
        }
        
        a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .required-note {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--light-text);
        }
        
        @media (max-width: 600px) {
            .pet-preview {
                flex-direction: column;
                text-align: center;
            }
            
            .pet-preview img {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Pawfect Match</h1>
    </div>
    
    <div class="container">
        <?php if ($pet): ?>
        <div class="pet-preview">
            <img src="../admin/<?= htmlspecialchars($pet['image_path']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>">
            <div class="pet-preview-details">
                <h2>Adopting <?= htmlspecialchars($pet['name']) ?></h2>
                <p><strong>Age:</strong> <?= htmlspecialchars($pet['age']) ?> years</p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
                <p><strong>Weight:</strong> <?= htmlspecialchars($pet['weight']) ?> kg</p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="form-container">
            <h1>Adoption Application</h1>
            
            <form id="adoptionForm" action="process-application.php" method="POST">
                <input type="hidden" name="pet_id" id="petId" value="<?= isset($_GET['pet_id']) ? htmlspecialchars($_GET['pet_id']) : '' ?>">
                
                <div class="form-section">
                    <h2>Your Information</h2>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name*</label>
                        <input type="text" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email*</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone*</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Full Address*</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Your Home</h2>
                    
                    <div class="form-group">
                        <label for="housing_type">Housing Type*</label>
                        <select id="housing_type" name="housing_type" required>
                            <option value="">Select...</option>
                            <option value="apartment">Apartment</option>
                            <option value="house">House</option>
                            <option value="condo">Condo</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="yard_size">Yard Size*</label>
                        <select id="yard_size" name="yard_size" required>
                            <option value="">Select...</option>
                            <option value="none">No Yard</option>
                            <option value="small">Small Yard</option>
                            <option value="medium">Medium Yard</option>
                            <option value="large">Large Yard</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2>Pet Experience</h2>
                    
                    <div class="form-group">
                        <label for="pet_experience">Previous Pet Experience*</label>
                        <select id="pet_experience" name="pet_experience" required>
                            <option value="">Select...</option>
                            <option value="none">No Experience</option>
                            <option value="some">Some Experience</option>
                            <option value="experienced">Very Experienced</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="hours_alone">Hours Pet Would Be Alone Daily*</label>
                        <select id="hours_alone" name="hours_alone" required>
                            <option value="">Select...</option>
                            <option value="<4">Less than 4 hours</option>
                            <option value="4-8">4-8 hours</option>
                            <option value=">8">More than 8 hours</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="comments">Reason to adopt</label>
                    <textarea id="comments" name="comments"></textarea>
                </div>
                
                <div class="form-group checkbox-group">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">I agree to the <a href="#">Terms of Adoption</a>*</label>
                </div>
                <input type="hidden" value="<?= $pet_id?>" name="pet_id">
                
                <button type="submit" class="submit-btn">Submit Application</button>
            </form>
            
            <p class="required-note">* Required fields</p>
        </div>
    </div>
</body>
</html>