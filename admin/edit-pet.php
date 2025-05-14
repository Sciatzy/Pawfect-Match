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
<html>
<head><title>Edit Pet</title></head>
<body>
    <h2>Edit Pet: <?= htmlspecialchars($pet['name']) ?></h2>
    <form method="POST">
        <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($pet['name']) ?>" required></label><br>
        <label>Age: <input type="number" name="age" value="<?= $pet['age'] ?>" required></label><br>
        <label>Weight: <input type="number" name="weight" value="<?= $pet['weight'] ?>" required></label><br>
        <label>Gender:
            <select name="gender">
                <option value="Male" <?= $pet['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $pet['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </label><br>
        <label>Description:<br>
            <textarea name="description" rows="5" cols="40"><?= htmlspecialchars($pet['description']) ?></textarea>
        </label><br><br>
        <button type="submit">Update Pet</button>
    </form>
</body>
</html>
