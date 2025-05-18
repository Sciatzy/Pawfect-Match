<?php
require '../vendor/autoload.php';

try {
    // Initialize mPDF with minimal settings
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'default_font' => 'dejavusans',
        'default_font_size' => 10
    ]);

    // Simple test content
    $html = '<html><body>';
    $html .= '<h1>Test PDF</h1>';
    $html .= '<p>This is a test PDF generated at: ' . date('Y-m-d H:i:s') . '</p>';
    $html .= '</body></html>';

    // Write the content
    $mpdf->WriteHTML($html);

    // Output the PDF
    $mpdf->Output('test.pdf', 'D');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?> 