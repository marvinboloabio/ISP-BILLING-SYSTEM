<?php
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['customer_id']; // Correct field name from hidden input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']); // Account No.
    $contact = trim($_POST['edit_contact_no']);
    $location = trim($_POST['edit_location']);
    $address = trim($_POST['address']);
    $plan = trim($_POST['plan']);
    $amount = trim($_POST['amount']);
    $installation_date = trim($_POST['installation_date']);
    $active = isset($_POST['subscription_active']) ? (int)$_POST['subscription_active'] : 0;

    // Validate required fields
    if (empty($name) || empty($contact) || empty($location) || empty($address) || empty($plan) || empty($amount)) {
        echo 'All fields are required.';
        exit;
    }

    // Validate contact number
    if (!ctype_digit($contact) || strlen($contact) < 7 || strlen($contact) > 15) {
        echo 'Invalid contact number.';
        exit;
    }

    // Validate amount
    if (!is_numeric($amount)) {
        echo 'Invalid amount.';
        exit;
    }

    // Validate date
    if (!empty($installation_date)) {
        $d = DateTime::createFromFormat('Y-m-d', $installation_date);
        if (!$d || $d->format('Y-m-d') !== $installation_date) {
            echo 'Invalid installation date format.';
            exit;
        }
    }

    // Check for duplicate account number (email)
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE acountNum = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo 'Account number already exists.';
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
    }

    // Update customer
    $stmt = $conn->prepare("UPDATE customers 
        SET name = ?, acountNum = ?, contactNum = ?, location = ?, address = ?, plan = ?, amount = ?, date_of_installation = ?, subscription_active = ? 
        WHERE id = ?");

    $stmt->bind_param("ssssssdssi", $name, $email, $contact, $location, $address, $plan, $amount, $installation_date, $active, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Failed to update customer: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
