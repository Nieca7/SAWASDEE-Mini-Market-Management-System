<?php
require 'vendor/autoload.php'; // Dompdf must be installed via Composer
use Dompdf\Dompdf;

$id = $_GET['id'] ?? null;
if (!$id) {
    echo 'Missing invoice ID.';
    exit();
}

// Simulate fetching invoice data (replace with real DB logic)
$html = '<h2>Invoice ID: #' . htmlspecialchars($id) . '</h2>';
$html .= '<p>Example invoice content here. Replace with your dynamic HTML from invoice.php.</p>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Invoice_' . $id . '.pdf', ['Attachment' => true]);
?>