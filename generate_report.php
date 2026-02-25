<?php

require_once __DIR__ . '../libs/fpdf.php';
include __DIR__ . '/includes/db_connect.php';

header('Content-Type: application/pdf');
$report = $_GET['type'] ?? '';
date_default_timezone_set('Asia/Manila');
$dateGenerated = date("F j, Y, g:i a");

// Common Header Function
function addReportHeader($pdf, $title, $subtitle = '')
{
    $pdf->AddPage();
    $pdf->Image(__DIR__ . '../images/dcnet-it-solutions-high-resolution-logo-transparent.png', 10, 10, 30);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, $title, 0, 1, 'R');
    if (!empty($subtitle)) {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $subtitle, 0, 1, 'R');
    }
    global $dateGenerated;
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, "Generated on: $dateGenerated", 0, 1, 'R');
    $pdf->Ln(10);
}

$pdf = new FPDF();

switch ($report) {

    // ✅ 1. Active Customers
    case 'active_customers':
        $stmt = $conn->query("SELECT name, plan, contactNum, subscription_active FROM customers WHERE subscription_active = '1'");
        addReportHeader($pdf, 'Active Customers Report');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Plan', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Contact', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 12);
        while ($row = $stmt->fetch_assoc()) {
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(40, 10, $row['plan'], 1);
            $pdf->Cell(50, 10, $row['contactNum'], 1);
            $pdf->Cell(30, 10, 'Active', 1);
            $pdf->Ln();
        }
        break;

    // ✅ 2. Inactive Customers
    case 'inactive_customers':
        $stmt = $conn->query("SELECT name, plan, contactNum,subscription_active FROM customers WHERE subscription_active = '0'");
        addReportHeader($pdf, 'Inactive Customers Report');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Plan', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Contact', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 12);
        while ($row = $stmt->fetch_assoc()) {
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(40, 10, $row['plan'], 1);
            $pdf->Cell(50, 10, $row['contactNum'], 1);
            $pdf->Cell(30, 10, 'Inactive', 1);
            $pdf->Ln();
        }
        break;

    // ✅ 3. New Subscribers
    case 'new_subscribers':
        $from = $_GET['from_date'] ?? '';
        $to = $_GET['to_date'] ?? '';
        $stmt = $conn->prepare("SELECT name, plan, subscription_active, date_of_installation FROM customers WHERE date_of_installation BETWEEN ? AND ?");
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();
        addReportHeader($pdf, 'New Subscribers Report', "From $from to $to");
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Plan', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Installation Date', 1, 1, 'C', true);
        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(40, 10, $row['plan'], 1);
            $pdf->Cell(30, 10, $row['subscription_active'], 1);
            $pdf->Cell(50, 10, $row['date_of_installation'], 1);
            $pdf->Ln();
        }
        break;

    // ✅ 4–16. Add placeholders for other reports:
    case 'subscription_expiry':
    case 'customer_profiles':
    case 'plan_change_history':
case 'location_report':
    $location = $_GET['location'];
    $stmt = $conn->prepare("SELECT 
        customers.name AS name,
        customers.acountNum AS acountNum,
        customers.contactNum AS contactNum,
        customers.plan AS plan,
        customers.amount AS amount_due,
        customers.address AS address,
        customers.location AS location
    FROM customers
    WHERE location = ?");
    $stmt->bind_param("s", $location);
    $stmt->execute();
    $result = $stmt->get_result();

    addReportHeader($pdf, 'Customer Per Location Report', "Server Location: $location");

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(230, 230, 230);

    // Total width: 190 mm max
    $pdf->Cell(30, 8, 'Name', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Acct No.', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Contact No.', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Plan', 1, 0, 'C', true);
    $pdf->Cell(20, 8, 'Amount', 1, 0, 'C', true);
    $pdf->Cell(45, 8, 'Address', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Location', 1, 1, 'C', true); // Ends row

    $pdf->SetFont('Arial', '', 9);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 8, $row['name'], 1);
        $pdf->Cell(25, 8, $row['acountNum'], 1);
        $pdf->Cell(25, 8, $row['contactNum'], 1);
        $pdf->Cell(20, 8, $row['plan'], 1);
        $pdf->Cell(20, 8, number_format($row['amount_due'], 2), 1);
        $pdf->Cell(45, 8, $row['address'], 1);
        $pdf->Cell(25, 8, $row['location'], 1);
        $pdf->Ln();
    }
    break;

    case 'monthly_billing':
        // Get current month and year
        $month = date('m');
        $year = date('Y');

        // SQL query: Get all bills due this month
        $stmt = $conn->prepare("SELECT 
                                billing.reference_num AS ref_num,
                                customers.name AS name,
                                customers.amount AS amount,
                                billing.due_date AS due_date,
                                billing.status AS status
                            FROM billing
                            JOIN customers ON billing.customer_id = customers.id
                            WHERE MONTH(billing.due_date) = ? AND YEAR(billing.due_date) = ?
                            ORDER BY billing.due_date ASC");

        $stmt->bind_param("ss", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        // PDF header
        $reportTitle = "Monthly Billing Report - " . date("F Y");
        addReportHeader($pdf, $reportTitle);

        // Table headers
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(30, 10, 'Bill Ref No.', 1, 0, 'C', true);
        $pdf->Cell(65, 10, 'Customer Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Amount', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Due Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);

        // Table rows
        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['ref_num'], 1);
            $pdf->Cell(65, 10, $row['name'], 1);
            $pdf->Cell(30, 10, $row['amount'], 1);
            $pdf->Cell(35, 10, $row['due_date'], 1);
            $pdf->Cell(30, 10, $row['status'], 1);
            $pdf->Ln();
        }
        break;

    case 'unpaid_bills':
        // Get the date range from GET or default values
        $from = $_GET['from_date'] ?? date('Y-m-01'); // default to start of current month
        $to = $_GET['to_date'] ?? date('Y-m-t');       // default to end of current month

        $stmt = $conn->prepare("SELECT billing.reference_num as ref_num, customers.name as name, customers.amount as amount, billing.due_date as due_date, billing.status as status  
                            FROM billing 
                            JOIN customers ON billing.customer_id = customers.id
                            WHERE billing.status = 'Unpaid'
                              AND billing.due_date BETWEEN ? AND ?
                            ORDER BY billing.due_date ASC");
        $stmt->bind_param("ss", $from, $to); // bind start and end dates
        $stmt->execute();
        $result = $stmt->get_result();

        addReportHeader($pdf, 'Unpaid Bills Report', "From $from to $to");

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(35, 10, 'Bill Ref No.', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Name', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Amount', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Due Date', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Status', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(35, 10, $row['ref_num'], 1);
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(25, 10, $row['amount'], 1);
            $pdf->Cell(30, 10, $row['due_date'], 1);
            $pdf->Cell(40, 10, $row['status'], 1);
            $pdf->Ln();
        }
        break;
    case 'overdue_accounts':
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT 
                                billing.reference_num AS ref_num,
                                customers.name AS name,
                                customers.amount AS amount,
                                billing.due_date AS due_date,
                                billing.status AS status
                            FROM billing
                            JOIN customers ON billing.customer_id = customers.id
                            WHERE billing.status = 'Unpaid'
                            AND billing.due_date < ?
                            ORDER BY billing.due_date ASC");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();

        addReportHeader($pdf, 'Overdue Accounts Report', "As of $today");

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(30, 10, 'Bill Ref No.', 1, 0, 'C', true);
        $pdf->Cell(60, 10, 'Customer Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Amount Due', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Due Date', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(30, 10, $row['ref_num'], 1);
            $pdf->Cell(60, 10, $row['name'], 1);
            $pdf->Cell(30, 10, $row['amount'], 1);
            $pdf->Cell(40, 10, $row['due_date'], 1);
            $pdf->Cell(30, 10, $row['status'], 1);
            $pdf->Ln();
        }
        break;
    case 'payment_collection':
        $from = $_GET['from_date'] ?? date('Y-m-01'); // default to start of current month
        $to = $_GET['to_date'] ?? date('Y-m-t');       // default to end of current month
        $stmt = $conn->prepare("SELECT 
                                payments.reference_no AS payment_ref,
                                customers.name AS customer_name,
                                payments.amount AS amount_paid,
                                payments.payment_method AS method,
                                payments.payment_date AS date_paid
                            FROM payments
                            JOIN billing ON billing.id = payments.bill_id
                            JOIN customers ON billing.customer_id = customers.id
                            WHERE payments.payment_date BETWEEN ? AND ?
                            ORDER BY payments.payment_date ASC");
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();

        addReportHeader($pdf, 'Payment Collection Report', "From $from to $to");

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(35, 10, 'Payment Ref', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Customer Name', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Amount', 1, 0, 'C', true);
        $pdf->Cell(40, 10, 'Payment Method', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Date Paid', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(35, 10, $row['payment_ref'], 1);
            $pdf->Cell(50, 10, $row['customer_name'], 1);
            $pdf->Cell(30, 10, $row['amount_paid'], 1);
            $pdf->Cell(40, 10, $row['method'], 1);
            $pdf->Cell(35, 10, $row['date_paid'], 1);
            $pdf->Ln();
        }
        break;

    case 'payment_history':
        $from = $_GET['from_date'] ?? date('Y-m-01'); // default to start of current month
        $to = $_GET['to_date'] ?? date('Y-m-t');       // default to end of current month

        $stmt = $conn->prepare("SELECT 
                                billing.reference_num AS ref_num,
                                payments.reference_no AS payment_ref_num,
                                customers.name AS name,
                                customers.amount AS amount_due,
                                payments.amount AS amount_payed,
                                payments.payment_date AS payment_date,
                                payments.payment_method AS method
                            FROM payments
                            JOIN billing ON billing.id = payments.bill_id
                            JOIN customers ON billing.customer_id = customers.id
                            WHERE billing.status = 'Paid'
                            AND payments.payment_date BETWEEN ? AND ?
                            ORDER BY payments.payment_date ASC");
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $result = $stmt->get_result();

        addReportHeader($pdf, 'Payment History Report', "From $from to $to");

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(40, 10, 'Payment Ref No.', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Bill Ref No.', 1, 0, 'C', true);
        $pdf->Cell(50, 10, 'Customer Name', 1, 0, 'C', true);
        $pdf->Cell(35, 10, 'Amount Payed', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Payment Date', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 12);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(40, 10, $row['payment_ref_num'], 1);
            $pdf->Cell(35, 10, $row['ref_num'], 1);
            $pdf->Cell(50, 10, $row['name'], 1);
            $pdf->Cell(35, 10, $row['amount_payed'], 1);
            $pdf->Cell(30, 10, $row['payment_date'], 1);
            $pdf->Ln();
        }
        break;
    case 'billing_statement':
    // Get customer_id from POST or GET
    $customer_id = $_POST['customer_id'] ?? $_GET['customer_id'] ?? null;

    if (!$customer_id) {
        die("Customer ID is required.");
    }

    // Get customer and all unpaid billing info
    $stmt = $conn->prepare("
        SELECT 
            customers.name AS name,
            customers.address AS address,
            customers.amount AS monthly_amount,
            customers.date_of_installation AS subscription_date,
            customers.plan AS plan_name,
            billing.billing_date AS billing_date,
            billing.reference_num AS ref_num,
            billing.due_date AS due_date
        FROM customers
        INNER JOIN billing ON customers.id = billing.customer_id
        WHERE billing.status = 'unpaid' AND customers.id = ?
        ORDER BY billing.billing_date ASC
    ");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all billing records
    $billing_records = [];
    while ($row = $result->fetch_assoc()) {
        $billing_records[] = $row;
    }

    if (empty($billing_records)) {
        die("Customer not found or no unpaid billing records.");
    }

    // Use first record for customer info
    $customer = $billing_records[0];

    // PDF Output
    addReportHeader($pdf, 'Billing Statement', "Date: " . date('F j, Y'));

    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(5);
    $pdf->Cell(0, 10, "Customer Name: " . $customer['name'], 0, 1);
    $pdf->Cell(0, 10, "Address: " . $customer['address'], 0, 1);
    $pdf->Cell(0, 10, "Plan: " . $customer['plan_name'], 0, 1);
    $pdf->Cell(0, 10, "Subscription Date: " . $customer['subscription_date'], 0, 1);
    $pdf->Ln(5);

    // Table header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(230, 230, 230); // Light gray fill
    $pdf->Cell(30, 10, 'Bill Ref No.', 1, 0, 'C', true);
    $pdf->Cell(45, 10, 'Description', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Amount', 1, 0, 'C', true);
    $pdf->Cell(40, 10, 'Billing Date', 1, 0, 'C', true);
    $pdf->Cell(35, 10, 'Due Date', 1, 1, 'C', true);

    // Table body
    $pdf->SetFont('Arial', '', 12);
    foreach ($billing_records as $record) {
           $pdf->Cell(30, 10, $record['ref_num'], 1);
        $amount = number_format($record['monthly_amount'], 2);
        $pdf->Cell(45, 10, 'Monthly Internet Fee', 1);
        $pdf->Cell(40, 10, $amount, 1);
        $pdf->Cell(40, 10, $record['billing_date'], 1);
        $pdf->Cell(35, 10, $record['due_date'], 1);
        $pdf->Ln();
    }
    break;

    default:
        echo "Invalid or no report selected.";
        exit;
}

$pdf->Output(str_replace('_', '', $report) . "_report.pdf", 'I');
