
cinnamon🐰
<?php
  session_start();

  require '../includes/db.php';

  if(!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) ||  !$_SESSION['reset_code_verified']){
    header("Location: send-code.php");
    exit();
  }

  // Password validation function
  function validatePassword($password) {
    // Check minimum length
    if (strlen($password) < 8) {
      return "Password must be at least 8 characters long";
    }
    
    // Check for uppercase
    if (!preg_match('/[A-Z]/', $password)) {
      return "Password must contain at least one uppercase letter";
    }
    
    // Check for lowercase
    if (!preg_match('/[a-z]/', $password)) {
      return "Password must contain at least one lowercase letter";
    }
    
    // Check for number
    if (!preg_match('/[0-9]/', $password)) {
      return "Password must contain at least one number";
    }
    
    // Check for special character
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
      return "Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)";
    }
    
    return true;
  }

  if($_SERVER['REQUEST_METHOD'] === "POST"){
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate the new password
    $validation_result = validatePassword($new_password);
    if($validation_result !== true) {
      $_SESSION['error'] = $validation_result;
      header("Location: new-password.php");
      exit();
    }

    if($new_password === $confirm_password){
      $hashedPassword = password_hash($new_password, PASSWORD_BCRYPT);

      $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
      $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);

      unset($_SESSION['reset_email']);
      unset($_SESSION['reset_code_verified']);

      $_SESSION['success'] = "Your password has been reset successfully. You can now log in with your new password.";
      header("Location: login.php");
      exit();
    } else {
      $_SESSION['error'] = "Passwords do not match. Please try again.";
      header("Location: new-password.php");
      exit();
    }
  }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Create new password</title>
        <link rel="stylesheet" href="styles/style.css">
        <meta charset="UTF-8">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            }
            body {
                background-color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
            }
            .container {
                width: 100%;
                max-width: 420px;
            }
            .card {
                background: white;
                border-radius: 14px;
                box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10);
                padding: 40px 32px 32px 32px;
                text-align: center;
                transition: box-shadow 0.2s;
            }
            .card img {
                width: 80px;
                height: 80px;
                margin-bottom: 20px;
                object-fit: contain;
            }
            .card h3 {
                color: #ff914d;
                margin-bottom: 10px;
                font-weight: 700;
            }
            .card p {
                color: #666;
                margin-bottom: 24px;
            }
            .card input {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 14px;
                margin-bottom: 20px;
                transition: border-color 0.2s, box-shadow 0.2s;
                background: #fafafa;
            }
            .card input:focus {
                outline: none;
                border-color: #ff914d;
                box-shadow: 0 0 0 2px #ff914d33;
            }
            .card button {
                width: 100%;
                padding: 12px;
                background: linear-gradient(135deg, #ff914d 0%, #e87e3c 100%);
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
                margin-top: 8px;
            }
            .card button:hover {
                background: linear-gradient(135deg, #e87e3c 0%, #ff914d 100%);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(255, 145, 77, 0.15);
            }
            #alertMessage {
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
            }
            #alertMessage[style*="F74141"] {
                background-color: #ffebee;
                border: 1px solid #ffcdd2 !important;
                color: #F74141;
            }
            #alertMessage[style*="77DD77"] {
                background-color: #e8f5e9;
                border: 1px solid #c8e6c9 !important;
                color: #77DD77;
            }
            @media (max-width: 480px) {
                .card {
                    padding: 30px 10px;
                }
            }
            /* Custom popup styles */
            .custom-popup {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                z-index: 1000;
                text-align: center;
                min-width: 300px;
            }
            .custom-popup h3 {
                margin-bottom: 15px;
                color: #333;
            }
            .custom-popup p {
                margin-bottom: 20px;
                color: #666;
            }
            .custom-popup button {
                background: #ff914d;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-weight: 500;
            }
            .custom-popup button:hover {
                background: #e87e3c;
            }
            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
        </style>
    </head>
    <body>
    <div class="container">
    <div class="card">
      <img src="../images/logo.png" alt="" />
      <h3 style="text-align: center">New Password</h3>
      <?php if (isset($_SESSION['error'])): ?>
        <p id="alertMessage" style="border: 1px solid #F74141; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #F74141;">
          <?= $_SESSION['error'];
          unset($_SESSION['error']); ?>
        </p>
      <?php endif; ?>

      <?php if (isset($_SESSION['success'])): ?>
        <p id="alertMessage" style="border: 1px solid #77DD77; padding: 0.5rem 1rem !important; border-radius: 0.25rem; display: block; color: #77DD77;">
          <?= $_SESSION['success'];
          unset($_SESSION['success']); ?>
        </p>
      <?php endif; ?>
      <form action="new-password.php" method="POST" id="newPasswordForm" onsubmit="return validateForm()">
        <input type="password" name="new_password" id="password" placeholder="New Password" required>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter Password" required />
        
        <button type="submit">Change password</button>
      </form>
    </div>
  </div>

  <!-- Custom Popup -->
  <div class="overlay" id="overlay"></div>
  <div class="custom-popup" id="customPopup">
    <h3>Password Requirements</h3>
    <p id="popupMessage"></p>
    <button onclick="closePopup()">OK</button>
  </div>

  <script>
    function showPopup(message) {
      document.getElementById('popupMessage').textContent = message;
      document.getElementById('customPopup').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
    }

    function closePopup() {
      document.getElementById('customPopup').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
    }

    function validateForm() {
      const password = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;
      
      // Check length
      if (password.length < 😎 {
        showPopup('Password must be at least 8 characters long');
        return false;
      }
      
      // Check uppercase
      if (!/[A-Z]/.test(password)) {
        showPopup('Password must contain at least one uppercase letter');
        return false;
      }
      
      // Check lowercase
      if (!/[a-z]/.test(password)) {
        showPopup('Password must contain at least one lowercase letter');
        return false;
      }
      
      // Check number
      if (!/[0-9]/.test(password)) {
        showPopup('Password must contain at least one number');
        return false;
      }
      
      // Check special character
      if (!/[!@#$%^&*()\-_=+{};:,<.>]/.test(password)) {
        showPopup('Password must contain at least one special character (!@#$%^&*()-_=+{};:,<.>)');
        return false;
      }
      
      if (password !== confirmPassword) {
        showPopup('Passwords do not match');
        return false;
      }
      
      return true;
    }
  </script>
</body>
</html>