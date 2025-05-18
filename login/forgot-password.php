<?php
  require '../vendor/autoload.php';
  require '../includes/db.php';

  session_start();

  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $reset_code = rand(100000, 999999);

    $update = $pdo->prepare("UPDATE users SET reset_code = ? WHERE email = ?");
    $update->execute([$reset_code, $email]);

    $_SESSION['email'] = $email;

    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = 'true';
      $mail->Username = 'babaylangemini@gmail.com';
      $mail->Password = 'fsde mhxu jqud edvl';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('babaylangemini@gmail.com', 'Gregg Reiven Babaylan');
      $mail->addAddress($email, "THIS IS YOUR CLIENT");

      $mail->isHTML(true);
      $mail->Subject = "Password Reset Code";
      $mail->Body = "
          <p>Hello, This is your password reset code</p>

          <div>{$reset_code}</div>
        ";

      $mail->AltBody = "Hello, This is your password reset code: {$reset_code}";
      $mail->send();

      $_SESSION['email_sent'] = "true";
      $_SESSION['success'] = "Verification code has been sent to your email";
      header("Location: send-code.php");
      exit();
    } catch (Exception $e) {
      $_SESSION['Error'] = "Message could not be sent";
      header("Location: forgot-password.php");
      exit();
    }
  } else {
    $_SESSION['Error'] = "No user found with that email";
    header("Location: forgot-password.php");
    exit();
  }
}
?>


<!DOCTYPE html>     
<html>
    <head>
        <title>Forgot Password</title>
        <link rel="stylesheet" href="styles/styles.css">
        <meta charset="UTF-8">
    </head>
    <body>
    <div class="container">
    <div class="card">
      <style>
/* Reset & Base Styles */
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

      /* Card Container */
      .container {
        width: 100%;
        max-width: 420px;
      }

      .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 40px;
        text-align: center;
      }

      /* Logo & Header */
      .card img {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
        object-fit: contain;
      }

      .card h1 {
        font-size: 22px;
        color: #333;
        margin-bottom: 8px;
        font-weight: 600;
      }

      .card p {
        color: #666;
        font-size: 14px;
        margin-bottom: 24px;
      }

      /* Form Elements */
      .card input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        margin-bottom: 20px;
        transition: all 0.2s;
      }

      .card input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
      }

      /* Buttons */
      .sendcode-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg,rgb(228, 116, 47) 0%, #ff914d 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
      }

      .sendcode-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
      }

      /* Alert Messages */
      #alertMessage {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
      }

      /* Error Message */
      #alertMessage[style*="F74141"] {
        background-color: #ffebee;
        border: 1px solid #ffcdd2 !important;
      }

      /* Success Message */
      #alertMessage[style*="77DD77"] {
        background-color: #e8f5e9;
        border: 1px solid #c8e6c9 !important;
      }

      /* Responsive Adjustments */
      @media (max-width: 480px) {
        .card {
          padding: 30px 20px;
        }
        
        .card h1 {
          font-size: 20px;
        }
      }

      .site-logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ff914d;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .site-logo img {
        height: 40px;
        width: auto;
      }

      .footer-logo {
        font-size: 1.5rem;
        font-weight: bold;
        color: #ff914d;
        display: flex;
        align-items: center;
        gap: 10px;
      }
      
      .footer-logo img {
        height: 30px;
        width: auto;
      }
      </style>
      <img src="../images/logo.png" alt="" />
      <h1>Forgot Password</h1>
      <p id="alert_message">Please enter your email address</p>

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

      <form action="forgot-password.php" method="POST">
        <input type="email" placeholder="Email" name="email" required />
        <button type="submit" class = "sendcode-btn">Send code</button>
      </form>
    </div>
  </div>
    </body>
</html>