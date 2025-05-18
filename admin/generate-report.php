<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/jwt.php';
require_once '../vendor/autoload.php';

$type = isset($_GET['type']) ? $_GET['type'] : (isset($_POST['type']) ? $_POST['type'] : null);
$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id']) ? $_POST['id'] : null);

if (!$type || !$id) {
    die("Missing report type or id.");
}

if ($type === 'stray') {
    // Get stray report details
    $stmt = $pdo->prepare("
        SELECT sr.*, u.firstname, u.lastname, u.email 
        FROM strays sr 
        JOIN users u ON sr.reported_by = u.ID 
        WHERE sr.ID = ? AND sr.rescued_date IS NOT NULL
    ");
    $stmt->execute([$id]);
    $stray = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stray) {
        die("Stray report not found");
    }

    // Create PDF
    $mpdf = new \Mpdf\Mpdf([
        'margin_left' => 20,
        'margin_right' => 20,
        'margin_top' => 20,
        'margin_bottom' => 20,
    ]);

    // Add logo
    $mpdf->Image('../images/logo.png', 10, 10, 50);

    // Add title
    $mpdf->SetFont('Arial', 'B', 24);
    $mpdf->Cell(0, 20, 'Rescued Stray Report', 0, 1, 'C');
    $mpdf->Ln(10);

    // Add stray details
    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(40, 10, 'Report ID:', 0);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->Cell(0, 10, $stray['ID'], 0, 1);

    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(40, 10, 'Location:', 0);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->Cell(0, 10, $stray['location'], 0, 1);

    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(40, 10, 'Reported Date:', 0);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->Cell(0, 10, date('F d, Y', strtotime($stray['report_date'])), 0, 1);

    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(40, 10, 'Rescued Date:', 0);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->Cell(0, 10, date('F d, Y', strtotime($stray['rescued_date'])), 0, 1);

    $mpdf->Ln(10);
    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(0, 10, 'Description:', 0, 1);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->MultiCell(0, 10, $stray['description']);

    $mpdf->Ln(10);
    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(0, 10, 'Reporter Information:', 0, 1);
    $mpdf->SetFont('Arial', '', 12);
    $mpdf->Cell(40, 10, 'Name:', 0);
    $mpdf->Cell(0, 10, $stray['firstname'] . ' ' . $stray['lastname'], 0, 1);
    $mpdf->Cell(40, 10, 'Email:', 0);
    $mpdf->Cell(0, 10, $stray['email'], 0, 1);

    // Add footer
    $mpdf->SetY(-30);
    $mpdf->SetFont('Arial', 'I', 8);
    $mpdf->Cell(0, 10, 'Generated on ' . date('F d, Y H:i:s'), 0, 0, 'C');
    $mpdf->Cell(0, 10, 'Page ' . $mpdf->PageNo(), 0, 0, 'R');

    // Output PDF
    $mpdf->Output('Rescued_Stray_Report_' . $stray['ID'] . '.pdf', 'D');
} else {
    die("Invalid report type");
}
?> 