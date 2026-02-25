<?php
header('Content-Type: application/json');

require_once '../includes/db_connect.php'; // adjust path if needed

// Fetch total customers
$customers_result = $conn->query("SELECT COUNT(*) AS total FROM customers");
$total_customers = $customers_result->fetch_assoc()['total'];

// Fetch monthly revenue
$revenue_result = $conn->query("SELECT SUM(amount) AS total FROM payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())");
$monthly_revenue = $revenue_result->fetch_assoc()['total'] ?? 0;

$overall_to_be_collected = $conn->query("SELECT SUM(amount) AS total FROM customers WHERE subscription_active = '1'")->fetch_assoc()['total'] ?? 0;

// Fetch pending bills
$pending_bills_result = $conn->query("SELECT COUNT(*) AS total FROM billing WHERE status = 'Unpaid'");
$pending_bills = $pending_bills_result->fetch_assoc()['total'];

// Fetch subscriptions
$active_sub_result = $conn->query("SELECT COUNT(*) AS total FROM customers WHERE subscription_active = '1'");
$active_subscriptions = $active_sub_result->fetch_assoc()['total'];

$inactive_sub_result = $conn->query("SELECT COUNT(*) AS total FROM customers WHERE subscription_active = '0'");
$inactive_subscriptions = $inactive_sub_result->fetch_assoc()['total'];

// Return as JSON
$data = [
  'total_customers' => $total_customers,
  'monthly_revenue' => $monthly_revenue,
  'pending_bills' => $pending_bills,
  'active_subscriptions' => $active_subscriptions,
  'inactive_subscriptions' => $inactive_subscriptions,
  'overall_to_be_collected' => $overall_to_be_collected
];

echo json_encode($data);

$conn->close();
