<?php
require_once __DIR__ . '../libs/fpdf.php';
include __DIR__ . '/includes/db_connect.php';

if (!isset($_GET['id'])) {
  die("Payment ID is required.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("
  SELECT p.*, c.name AS customer_name, b.billing_date
  FROM payments p
  LEFT JOIN customers c ON p.customer_id = c.id
  LEFT JOIN billing b ON p.bill_id = b.id
  WHERE p.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
  die("Payment not found.");
}

$row = $result->fetch_assoc();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 20);

// Logo
$pdf->Image('images/dcnet-it-solutions-high-resolution-logo-transparent.png', 10, 10, 30); // adjust logo path
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, 'DCNet IT SOLUTIONS', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, 'Tubi-ala Surallah South Cotabato', 0, 1, 'C');
$pdf->Ln(10);

// Draw a line
$pdf->SetDrawColor(0, 0, 0);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(5);

// Receipt Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Official Payment Receipt', 0, 1, 'C');
$pdf->Ln(5);

// Payment Details Box
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(190, 10, "Payment Details", 0, 1, 'L', true);

// Details Table
function receiptRow($pdf, $label, $value) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(50, 10, $label, 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(140, 10, $value, 0, 1);
}


receiptRow($pdf, 'Date Paid:', $row['payment_date']);
receiptRow($pdf, 'Customer Name:', $row['customer_name']);
receiptRow($pdf, 'Billing Date:', $row['billing_date']);
receiptRow($pdf, 'Amount Paid:', 'PHP ' . number_format($row['amount'], 2));
receiptRow($pdf, 'Payment Method:', $row['payment_method']);
receiptRow($pdf, 'Reference No:', $row['reference_no']);

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 11);
$pdf->MultiCell(0, 10, "Thank you for your payment. This receipt serves as proof of payment. Please keep it for your records.", 0, 'L');

$pdf->Ln(15);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "________________________", 0, 1, 'R');
$pdf->Cell(0, 5, "Authorized Signature", 0, 1, 'R');

$pdf->Output("receipt_{$row['id']}.pdf", 'I');
?>
