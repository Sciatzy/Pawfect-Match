<?php

session_start();

require '../includes/db.php'

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Pet - Pawfect Match</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .header {
            background-color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header a {
            color: black    ;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo {
            height: 30px;
            width: auto;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Same styles as before, just simplified form groups */
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, select, textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
        }
        .submit-btn {
            background: #ee7721;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="pet-list.php">← Back to List</a>
        <a href="../login/index.php">
            <img src="../images/logo.png" alt="Pawfect Match Logo" class="header-logo">
            Pawfect Match
        </a>
    </div>
    <div class="container">
        <h1>Post a Pet</h1>
        <form action="process-pet.php" method="POST" enctype="multipart/form-data">
            <!-- Required Fields -->
            <div class="form-group">
                <label for="name">Pet Name*</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="weight">Weight (kg)*</label>
                <input type="number" id="weight" name="weight" step="0.1" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="age">Age (years)*</label>
                <input type="number" id="age" name="age" min="0" max="30" required>
            </div>
            
            <div class="form-group">
                <label for="gender">Gender*</label>
                <select id="gender" name="gender" required>
                    <option value="">Select...</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description*</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Pet Photo*</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            
            <button type="submit" class="submit-btn">Submit</button>
        </form>
    </div>
</body>
</html>