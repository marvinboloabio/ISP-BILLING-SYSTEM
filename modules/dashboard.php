<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - ISP Billing</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Inter', sans-serif;
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    .dashboard-header {
      margin: 30px 0;
    }
    .quick-links .btn {
      margin-right: 15px;
      margin-bottom: 15px;
      min-width: 120px;
      font-weight: 600;
      border-radius: 8px;
    }
  </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <a class="navbar-brand font-weight-bold text-primary" href="dashboard.php">📡 ISP Billing</a>
        <div class="ml-auto">
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>

<div class="container">
  <div class="dashboard-header text-center">
    <h2>Welcome, Admin</h2>
    <p class="text-muted">Dashboard overview</p>
  </div>

  <div class="row text-center">
  <div class="col-md-3 mb-4">
    <div class="card p-4">
      <h5>Total Customers</h5>
      <h3 class="text-primary" id="total-customers">Loading...</h3>
    </div>
  </div>
    <!-- New card for Overall To Be Collected -->
  <div class="col-md-3 mb-4">
    <div class="card p-4">
      <h5>Total Receivables</h5>
      <h3 class="text-danger" id="overall-collected">Loading...</h3>
    </div>
  </div>
  <div class="col-md-3 mb-4">
    <div class="card p-4">
      <h5>Monthly Revenue</h5>
      <h3 class="text-success" id="monthly-revenue">Loading...</h3>
    </div>
  </div>
  <div class="col-md-3 mb-4">
    <div class="card p-4">
      <h5>Pending Bills</h5>
      <h3 class="text-warning" id="pending-bills">Loading...</h3>
    </div>
  </div>
  <div class="col-md-3 mb-4">
    <div class="card p-4">
      <h5>Subscriptions</h5>
      <p class="mb-1"><span class="text-success font-weight-bold" id="active-subs">Loading...</span> Active</p>
      <p><span class="text-danger font-weight-bold" id="inactive-subs">Loading...</span> Inactive</p>
    </div>
  </div>
</div>

  <div class="quick-links text-center my-4">
    <a href="customers.php" class="btn btn-primary">Customers</a>
    <a href="billing.php" class="btn btn-info">Billing</a>
    <a href="payments.php" class="btn btn-success">Payments</a>
    <a href="reports.php" class="btn btn-secondary">Reports</a>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
  $(document).ready(function () {
    $.ajax({
      url: '../ajax/get_dashboard_data.php',
      method: 'GET',
      dataType: 'json',
      success: function (data) {
        $('#total-customers').text(data.total_customers);
        $('#monthly-revenue').text('₱' + parseFloat(data.monthly_revenue).toLocaleString('en-PH', { minimumFractionDigits: 2 }));
        $('#pending-bills').text(data.pending_bills);
        $('#active-subs').text(data.active_subscriptions);
        $('#inactive-subs').text(data.inactive_subscriptions);
         $('#overall-collected').text(data.overall_to_be_collected);
       
      },
      error: function () {
        alert('Failed to load dashboard data.');
      }
    });
  });
</script>

</body>
</html>
