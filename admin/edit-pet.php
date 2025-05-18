<?php
require '../includes/db.php';
session_start();

if (!isset($_COOKIE['token'])) {
    header('Location: ../login.php');
    exit;
}

$pets_id = $_GET['pets_id'] ?? null;
if (!$pets_id) {
    header('Location: admin-petlist.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pets WHERE pets_id = ?");
$stmt->execute([$pets_id]);
$pet = $stmt->fetch();

if (!$pet) {
    echo "Pet not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $gender = $_POST['gender'];
    $description = $_POST['description'];

    $update = $pdo->prepare("UPDATE pets SET name = ?, age = ?, weight = ?, gender = ?, description = ? WHERE pets_id = ?");
    $update->execute([$name, $age, $weight, $gender, $description, $pets_id]);

    header('Location: pet-list.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pet - Pawfect Match</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e54c8;
            --secondary-color: #8f94fb;
            --accent-color: #ff6b6b;
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: var(--white);
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .header-links {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-logo {
            height: 30px;
            width: auto;
        }

        .edit-form {
            background: var(--white);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-title {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0.75rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 84, 200, 0.25);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-links">
                <a href="pet-list.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Pet List
                </a>
                <a href="../login/index.php">
                    <img src="../images/logo.png" alt="Pawfect Match Logo" class="header-logo">
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="edit-form">
            <h2 class="form-title">Edit Pet: <?= htmlspecialchars($pet['name']) ?></h2>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($pet['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Age (years)</label>
                    <input type="number" name="age" class="form-control" value="<?= $pet['age'] ?>" step="0.1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" class="form-control" value="<?= $pet['weight'] ?>" step="0.1" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="Male" <?= $pet['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $pet['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($pet['description']) ?></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Pet</button>
                    <a href="pet-list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
