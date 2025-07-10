<?php
require 'vendor/autoload.php'; // Make sure Dompdf is installed via Composer

use Dompdf\Dompdf;

include 'db.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM sales_reports WHERE report_date BETWEEN ? AND ? ORDER BY report_date DESC");
$stmt->execute([$from, $to]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;">Sawasdee Mini Mart - Sales Report</h2>
<p><strong>From:</strong> ' . $from . ' <strong>To:</strong> ' . $to . '</p>
<table border="1" width="100%" cellspacing="0" cellpadding="4">
  <thead>
    <tr>
      <th>Date</th>
      <th>Cashier</th>
      <th>Product</th>
      <th>Category</th>
      <th>Qty</th>
      <th>Total (RM)</th>
      <th>Payment</th>
    </tr>
  </thead>
  <tbody>';

foreach ($reports as $row) {
    $html .= '<tr>
      <td>' . $row['report_date'] . '</td>
      <td>' . htmlspecialchars($row['cashier']) . '</td>
      <td>' . htmlspecialchars($row['title']) . '</td>
      <td>' . htmlspecialchars($row['category']) . '</td>
      <td>' . $row['qty'] . '</td>
      <td>' . number_format($row['amount'], 2) . '</td>
      <td>' . htmlspecialchars($row['payment_method']) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("sales_report_" . $from . "_to_" . $to . ".pdf", ["Attachment" => false]);
