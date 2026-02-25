<?php
require_once '../includes/db_connect.php';

$bill_id = $_POST['bill_id'];
$customer_id = $_POST['customer_id'];
$amount = $_POST['pay_amount'];
$payment_method = $_POST['payment_method'];
$reference_no = $_POST['reference_no'];
$payment_date = $_POST['payment_date'];

// Check if the bill_id already exists in payments table
$check = $conn->prepare("SELECT id FROM payments WHERE bill_id = ?");
$check->bind_param("i", $bill_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "Error: This bill has already been paid.";
    $check->close();
    exit;
}
$check->close();

// Insert into payments table
$stmt = $conn->prepare("INSERT INTO payments (bill_id, customer_id, amount, payment_method, reference_no, payment_date) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iidsss", $bill_id, $customer_id, $amount, $payment_method, $reference_no, $payment_date);

if ($stmt->execute()) {
    // Update billing status to 'Paid'
    $update = $conn->prepare("UPDATE billing SET status = 'Paid' WHERE id = ?");
    $update->bind_param("i", $bill_id);
    $update->execute();
    $update->close();

    // Fetch the original bill dates
    $original_bill = $conn->prepare("SELECT billing_date, due_date FROM billing WHERE id = ?");
    $original_bill->bind_param("i", $bill_id);
    $original_bill->execute();
    $original_bill->bind_result($orig_billing_date, $orig_due_date);

    if ($original_bill->fetch()) {
        $original_bill->close();

        // Generate new billing dates
        $new_billing_date = date('Y-m-d', strtotime("+1 month", strtotime($orig_billing_date)));
        $new_due_date = date('Y-m-d', strtotime("+1 month", strtotime($orig_due_date)));

        // Get the latest reference number
        $ref_query = $conn->query("SELECT reference_num FROM billing ORDER BY id DESC LIMIT 1");
        $latest_ref = $ref_query->fetch_assoc()['reference_num'];

        // Extract numeric part and increment
        $latest_number = intval(substr($latest_ref, 5)); // from BILL-0001 to 1
        $new_number = $latest_number + 1;
        $new_reference_num = "BILL-" . str_pad($new_number, 4, "0", STR_PAD_LEFT); // e.g., BILL-0002

        // Insert new billing
        $new_bill = $conn->prepare("INSERT INTO billing (customer_id, reference_num, billing_date, due_date, status) VALUES (?, ?, ?, ?, 'Unpaid')");
        $new_bill->bind_param("isss", $customer_id, $new_reference_num, $new_billing_date, $new_due_date);
        $new_bill->execute();
        $new_bill->close();

    } else {
        $original_bill->close();
        echo "Error: Original bill not found.";
    }

    echo $stmt->insert_id; // Return payment ID
} else {
    echo "Payment insert failed: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
