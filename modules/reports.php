<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate Reports</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #e0f7fa, #f1f8e9);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .navbar {
            padding: 0.75rem 1.5rem;
        }


        .container-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .report-card {
            width: 100%;
            max-width: 700px;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }

        .card-header {
            background: linear-gradient(to right, #007bff, #00bcd4);
            color: white;
            padding: 1.5rem;
        }

        .card-header h4 {
            font-weight: 600;
        }

        .form-section label {
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        select:focus,
        input:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <a class="navbar-brand font-weight-bold text-primary" href="dashboard.php">📡 ISP Billing</a>
        <div class="ms-auto">
            <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </nav>
    <div class="container-center">
        <div class="card report-card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-clipboard-data me-2"></i> Generate Reports</h4>
            </div>
            <div class="card-body">
                <form id="reportForm" method="GET" action="../generate_report.php" target="_blank">

                    <div class="form-section mb-4">
                        <label for="reportType" class="form-label">📋 Select Report Type</label>
                        <select class="form-select" id="reportType" name="type" required>
                            <optgroup label="📄 Customer & Subscription Reports">
                                <option value="active_customers">Active Customers</option>
                                <option value="inactive_customers">Inactive/Disconnected Customers</option>
                                <option value="new_subscribers">New Subscribers</option>
                                <option value="customer_profiles">Customer Profiles</option>
                                <option value="location_report">Customer Location Report</option>
                            </optgroup>
                            <optgroup label="💸 Billing & Payment Reports">
                                <option value="monthly_billing">Monthly Billing</option>
                                <option value="unpaid_bills">Unpaid Bills</option>
                                <option value="overdue_accounts">Overdue Accounts</option>
                                <option value="payment_collection">Payment Collection</option>
                                <option value="payment_history">Payment History</option>
                                <option value="billing_statement">Billing Statement</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Dynamic Filters -->
                    <div id="filtersContainer" class="form-section"></div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS & jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const filters = {
            new_subscribers: `
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" class="form-control" required>
          </div>
        </div>
      `,
            subscription_expiry: `
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" required>
          </div>
        </div>
      `,
            payment_collection: `
        <div class="row">
          <div class="col-md-6">
            <label class="form-label">Collection From</label>
            <input type="date" name="from_date" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">To</label>
            <input type="date" name="to_date" class="form-control" required>
          </div>
        </div>
      `,
            location_report: `
        <div class="mb-3">
          <label class="form-label">Server Location</label>
           <select id="location" name="location" class="form-control" required>
              <option value="Surallah">Surallah</option>
              <option value="T'boli">T'boli</option>
              <option value="Lake Sebu">Lake Sebu</option>
            </select>
        </div>
      `,
            unpaid_bills: `
  <div class="row">
    <div class="col-md-6">
      <label class="form-label">From Date</label>
      <input type="date" name="from_date" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">To Date</label>
      <input type="date" name="to_date" class="form-control" required>
    </div>
  </div>
`,
            payment_history: `
  <div class="row">
    <div class="col-md-6">
      <label class="form-label">From Date</label>
      <input type="date" name="from_date" class="form-control" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">To Date</label>
      <input type="date" name="to_date" class="form-control" required>
    </div>
  </div>
`,
            billing_statement: `
        <div class="mb-3">
          <label class="form-label">Customer</label>
          <select name="customer_id" class="form-control" required>
  <option value="">-- Select Customer --</option>
  <?php
    include '../includes/db_connect.php';
    $query = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
    while ($row = $query->fetch_assoc()):
  ?>
    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
  <?php endwhile; ?>
</select>
        </div>
      `
        };

        $('#reportType').on('change', function () {
            const selected = $(this).val();
            $('#filtersContainer').html(filters[selected] || '');
        });
    </script>
</body>

</html>