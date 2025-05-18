<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Pawfect Match</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff914d;
            --secondary-color: #e87e3c;
            --text-color: #333;
            --light-bg: #f8f9fa;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: var(--white);
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .site-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .site-logo img {
            height: 40px;
            width: auto;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            flex: 1;
        }

        .thank-you-card {
            background: var(--white);
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 2rem;
        }

        .thank-you-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .thank-you-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .thank-you-message {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 175px;
            justify-content: center;
        }

        .btn-home:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            color: white;
        }

        .footer {
            background: var(--white);
            padding: 1.5rem;
            text-align: center;
            margin-top: auto;
        }

        .footer-logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }

        .footer-logo img {
            height: 30px;
            width: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="../login/index.php" class="site-logo">
                <img src="../images/logo.png" alt="Pawfect Match Logo">
                Pawfect Match
            </a>
        </div>
    </div>

    <div class="container">
        <div class="thank-you-card">
            <div class="thank-you-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="thank-you-title">Thank You!</h1>
            <div class="thank-you-message">
                <p>Your adoption application has been submitted successfully.</p>
                <p>Our team will review your application and contact you within 3-5 business days.</p>
            </div>
            <a href="../login/index.php" class="btn-home">
                <i class="fas fa-home"></i>
                Back to Home
            </a>
        </div>
    </div>

    <div class="footer">
        <div class="footer-logo">
            <img src="../images/logo.png" alt="Pawfect Match Logo">
            Pawfect Match
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>