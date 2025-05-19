<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/apache/logs/error.log');

try {
    error_log("Starting Adopted Pets PDF generation process");
    
    // Get adopted pets data
    $query = "
    SELECT 
        ad.adoption_id,
        ad.adoption_date,
        a.adopter_id,
        a.housing_type,
        a.yard_size,
        a.pet_experience,
        a.hours_alone,
        a.comments,
        p.name as pet_name,
        p.gender,
        p.age,
        p.weight,
        u.firstname,
        u.lastname,
        u.email,
        a.phone,
        a.address
    FROM adoptions ad
    INNER JOIN adopters a ON ad.adopter_id = a.adopter_id
    INNER JOIN pets p ON ad.pet_id = p.pets_id
    INNER JOIN users u ON a.user_id = u.ID
    WHERE ad.app_status = 'approved'
    ORDER BY ad.adoption_date DESC";
    
    $adopted_pets = $pdo->query($query)->fetchAll();
    error_log("Found " . count($adopted_pets) . " adopted pets");

    // Initialize mPDF with custom settings
    $mpdf = new \Mpdf\Mpdf([
        'margin_top' => 35,
        'margin_header' => 10
    ]);
    
    // Define the header with the logo
    $logoPath = '../assets/images/logo.png'; // Update with your actual logo path
    
    // Set header template with logo
    $mpdf->SetHTMLHeader('
        <div style="text-align: center; border-bottom: 1px solid #4a4a4a; padding-bottom: 10px; margin-bottom: 10px;">
            <table width="100%">
                <tr>
                    <td width="20%" style="text-align: left;">
                        <img src="' . $logoPath . '" width="70" alt="Pet Adoption Logo">
                    </td>
                    <td width="60%" style="text-align: center; font-size: 20px; font-weight: bold; color: #444;">
                        Adopted Pets Report
                    </td>
                    <td width="20%" style="text-align: right; font-style: italic; font-size: 12px;">
                        Generated: ' . date('F d, Y') . '
                    </td>
                </tr>
            </table>
        </div>
    ');
    
    // Set footer
    $mpdf->SetHTMLFooter('
        <table width="100%">
            <tr>
                <td width="33%" style="font-size: 12px;">{DATE j-m-Y}</td>
                <td width="33%" align="center" style="font-size: 12px;">Page {PAGENO} of {nbpg}</td>
                <td width="33%" style="text-align: right; font-size: 12px;">Pet Adoption Center</td>
            </tr>
        </table>
    ');
    
    // Introduction content
    $html = '
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            color: #333;
        }
        .adoption-record {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #4CAF50;
        }
        .pet-info {
            background-color: #edf7ed;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .adopter-info {
            background-color: #e8f0fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .adoption-details {
            background-color: #fff9e6;
            padding: 10px;
            border-radius: 5px;
        }
        h2, h3 {
            color: #4a4a4a;
        }
        h4 {
            margin-bottom: 5px;
            color: #2e7d32;
        }
        .summary {
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
    
    <div class="summary">
        <h2>Adoption Summary</h2>
        <p>Total Adoptions: <strong>' . count($adopted_pets) . '</strong></p>
        <p>This report lists all approved pet adoptions from our system, including details about the pets and their new owners.</p>
    </div>
    ';
    
    // Add each adoption record
    foreach ($adopted_pets as $pet) {
        $html .= '
        <div class="adoption-record">
            <h3>Adoption #' . $pet['adoption_id'] . '</h3>
            
            <div class="pet-info">
                <h4>🐾 Pet Information</h4>
                <table width="100%">
                    <tr>
                        <td width="25%"><strong>Name:</strong></td>
                        <td>' . htmlspecialchars($pet['pet_name']) . '</td>
                        <td width="25%"><strong>Gender:</strong></td>
                        <td>' . htmlspecialchars($pet['gender']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Age:</strong></td>
                        <td>' . htmlspecialchars($pet['age']) . ' years</td>
                        <td><strong>Weight:</strong></td>
                        <td>' . htmlspecialchars($pet['weight']) . ' kg</td>
                    </tr>
                </table>
            </div>
            
            <div class="adopter-info">
                <h4>👤 Adopter Information</h4>
                <table width="100%">
                    <tr>
                        <td width="25%"><strong>Name:</strong></td>
                        <td colspan="3">' . htmlspecialchars($pet['firstname'] . ' ' . $pet['lastname']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>' . htmlspecialchars($pet['email']) . '</td>
                        <td width="25%"><strong>Phone:</strong></td>
                        <td>' . htmlspecialchars($pet['phone']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td colspan="3">' . htmlspecialchars($pet['address']) . '</td>
                    </tr>
                </table>
            </div>
            
            <div class="adoption-details">
                <h4>📋 Adoption Details</h4>
                <table width="100%">
                    <tr>
                        <td width="25%"><strong>Date:</strong></td>
                        <td>' . date('F d, Y', strtotime($pet['adoption_date'])) . '</td>
                        <td width="25%"><strong>Housing Type:</strong></td>
                        <td>' . htmlspecialchars($pet['housing_type']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Yard Size:</strong></td>
                        <td>' . htmlspecialchars($pet['yard_size']) . '</td>
                        <td><strong>Hours Alone:</strong></td>
                        <td>' . htmlspecialchars($pet['hours_alone']) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Pet Experience:</strong></td>
                        <td colspan="3">' . htmlspecialchars($pet['pet_experience']) . '</td>
                    </tr>
                </table>
                <p><strong>Comments:</strong> ' . htmlspecialchars($pet['comments']) . '</p>
            </div>
        </div>
        ';
    }

    // Write HTML to the PDF
    $mpdf->WriteHTML($html);

    // Output PDF
    $mpdf->Output('Adopted_Pets_Report.pdf', 'D');
    error_log("PDF generated and sent to browser successfully");

} catch (Exception $e) {
    error_log("PDF Generation Error: " . $e->getMessage());
    error_log("Error File: " . $e->getFile());
    error_log("Error Line: " . $e->getLine());
    error_log("Stack Trace: " . $e->getTraceAsString());
    
    echo 'Error generating PDF: ' . $e->getMessage();
}
?>