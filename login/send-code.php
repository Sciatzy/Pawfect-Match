<?php
  session_start();

  require '../includes/db.php';

  if($_SERVER['REQUEST_METHOD'] === "POST"){
    $enteredCode = $_POST['code'];
    $enteredCode = (int)$enteredCode;
    $email = $_SESSION['email'];

    if(!isset($_SESSION['email'])){
      $_SESSION['error'] = "No email session found, please try again.";
      header("Location: forgot-password.php");
      exit();
    }

    $stmt  = $pdo->prepare("SELECT reset_code FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if($user){
      if($enteredCode === (int)$user['reset_code']){
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_code_verified'] = true;
        
        header("Location: new-password.php");
        exit();
      } else {
        $_SESSION['error'] = "Invalid code. Please try again.";
      }
    } else {
      $_SESSION['error'] = "No user found with that email.";
    }
  }
?>



<!DOCTYPE html>
<html>
    <head>
        <title>Send Code</title>
        <link rel="stylesheet" href="styles/style.css">
        <meta charset="UTF-8">
    </head>
    <body>
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

      .card button,
      .sendcode-btn {
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

      .card button:hover,
      .sendcode-btn:hover {
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

      </style>
    <div class="container">
    <div class="card">
      <img src="../images/logo.png" alt="" />
      <h3 style="text-align: center">Enter code</h3>
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
      <form action="send-code.php" method="POST">
        <input type="number" placeholder="Enter code" maxlength="6" name="code"/>
        <button>Submit</button>
      </form>
    </div>
  </div>

    </body>
</html>