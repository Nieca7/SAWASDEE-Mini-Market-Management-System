<?php
// Dummy CSV export for demonstration
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="sales_report.csv"');
echo "Date,Cashier,Product,Quantity,Total\n";
echo "2025-05-23,Ali,Milk,3,18.00\n";
?>