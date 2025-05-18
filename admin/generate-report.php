<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

// Get all rescued strays with reporter and rescuer info
$stmt = $pdo->prepare("
    SELECT s.*, 
           u.firstname AS reporter_firstname, u.lastname AS reporter_lastname, u.email AS reporter_email,
           r.firstname AS rescuer_firstname, r.lastname AS rescuer_lastname, r.email AS rescuer_email
    FROM strays s
    LEFT JOIN users u ON s.reporter_id = u.ID
    LEFT JOIN users r ON s.rescued_by = r.ID
    WHERE s.rescued_date IS NOT NULL
    ORDER BY s.rescued_date DESC
");
$stmt->execute();
$rescued_strays = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PDF
$mpdf = new \Mpdf\Mpdf([
    'margin_left' => 20,
    'margin_right' => 20,
    'margin_top' => 20,
    'margin_bottom' => 20,
]);

// Add logo and title
$mpdf->Image('../images/logo.png', 10, 10, 50);
$mpdf->SetFont('Arial', 'B', 24);
$mpdf->Cell(0, 20, 'Rescued Strays Report', 0, 1, 'C');
$mpdf->Ln(10);

// Add summary
$mpdf->SetFont('Arial', 'B', 14);
$mpdf->Cell(0, 10, 'Summary', 0, 1);
$mpdf->SetFont('Arial', '', 12);
$mpdf->Cell(0, 10, 'Total Rescued Strays: ' . count($rescued_strays), 0, 1);
$mpdf->Cell(0, 10, 'Report Generated: ' . date('F d, Y'), 0, 1);
$mpdf->Ln(10);

// Add rescued strays details
$mpdf->SetFont('Arial', 'B', 14);
$mpdf->Cell(0, 10, 'Rescued Strays Details', 0, 1);
$mpdf->Ln(5);

foreach ($rescued_strays as $stray) {
    $mpdf->SetFont('Arial', 'B', 12);
    $mpdf->Cell(0, 10, 'Stray ID: ' . $stray['ID'], 0, 1);
    
    $mpdf->SetFont('Arial', '', 11);
    $mpdf->Cell(40, 8, 'Name:', 0);
    $mpdf->Cell(0, 8, $stray['name'], 0, 1);
    
    $mpdf->Cell(40, 8, 'Type:', 0);
    $mpdf->Cell(0, 8, $stray['animal_type'], 0, 1);
    
    $mpdf->Cell(40, 8, 'Gender:', 0);
    $mpdf->Cell(0, 8, ucfirst($stray['gender']), 0, 1);
    
    $mpdf->Cell(40, 8, 'Location:', 0);
    $mpdf->Cell(0, 8, $stray['location'], 0, 1);
    
    $mpdf->Cell(40, 8, 'Rescued Date:', 0);
    $mpdf->Cell(0, 8, date('F d, Y', strtotime($stray['rescued_date'])), 0, 1);
    
    $mpdf->Cell(40, 8, 'Reporter:', 0);
    $mpdf->Cell(0, 8, $stray['reporter_firstname'] . ' ' . $stray['reporter_lastname'], 0, 1);
    
    if (!empty($stray['rescuer_firstname'])) {
        $mpdf->Cell(40, 8, 'Rescuer:', 0);
        $mpdf->Cell(0, 8, $stray['rescuer_firstname'] . ' ' . $stray['rescuer_lastname'], 0, 1);
    }
    
    $mpdf->Ln(5);
    $mpdf->MultiCell(0, 8, 'Description: ' . $stray['description'], 0);
    $mpdf->Ln(10);
    
    // Add a separator line between entries
    $mpdf->Line(20, $mpdf->y, 190, $mpdf->y);
    $mpdf->Ln(10);
}

// Add footer
$mpdf->SetY(-15);
$mpdf->SetFont('Arial', 'I', 8);
$mpdf->Cell(0, 10, 'Page ' . $mpdf->PageNo() . ' of {nb}', 0, 0, 'C');

// Output PDF
$mpdf->Output('Rescued_Strays_Report.pdf', 'D');
?>