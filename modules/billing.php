<?php require_once '../includes/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Billing - ISP System</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f4f6f9;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .card-header {
      background: linear-gradient(to right, #4f46e5, #6366f1);
      color: #fff;
      padding: 1rem 1.5rem;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }

    .btn-add {
      background: #fff;
      color: #4f46e5;
      font-weight: 600;
      border: 2px solid #4f46e5;
      transition: 0.3s ease;
    }

    .btn-add:hover {
      background: #4f46e5;
      color: #fff;
    }

    .table thead {
      background: #f1f5f9;
    }

    .table td,
    .table th {
      vertical-align: middle;
    }

    .modal-content {
      border-radius: 16px;
    }

    .modal-header {
      background: #4f46e5;
      color: white;
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
    }

    .form-label {
      font-weight: 500;
    }

    .badge-status {
      cursor: pointer;
      user-select: none;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <a class="navbar-brand font-weight-bold text-primary" href="dashboard.php">📡 ISP Billing</a>
    <div class="ml-auto">
      <a href="../logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>
  </nav>

  <!-- Main Container -->
  <div class="container-fluid" style="max-width: 100%; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <h2 class="mb-0">💳 Billing Management</h2>

      <div>
        <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#addBillModal">
          ➕ Add Bill
        </button>
        <button id="sendRemindersBtn" class="btn btn-warning">
          📤 Send SMS Reminders
        </button>
      </div>
    </div>

    <div id="bill-alert-box" class="mb-3"></div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover" id="billingTable" style="width:100%;">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Reference No.</th>
            <th>Customer</th>
            <th>Contact</th>
            <th>Plan</th>
            <th>Billing Date</th>
            <th>Due Date</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="billData"></tbody>
      </table>
    </div>
  </div>

  <!-- Add Bill Modal -->
  <div class="modal fade" id="addBillModal" tabindex="-1" role="dialog" aria-labelledby="addBillLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <form id="addBillForm" class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Add New Bill</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Reference Number</label>
              <input type="text" name="reference_num" class="form-control" readonly>
            </div>
            <div class="form-group col-md-6">
              <label>Customer</label>
              <select name="customer_id" class="form-control" required>
                <option value="">-- Select --</option>
                <?php
                $res = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
                while ($row = $res->fetch_assoc()) {
                  echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Billing Date</label>
              <input type="date" name="billing_date" id = "billing_date" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
              <label>Due Date</label>
              <input type="date" name="due_date" id = "due_date" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">💾 Save Bill</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">✖ Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Bill Modal -->
  <div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <form id="editBillForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Bill</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="bill_id" id="edit_bill_id" />
          <div class="form-group">
            <label class="form-label">Reference Number</label>
            <input type="text" name="ref_num" id="edit_ref_num" class="form-control" readonly>
          </div>
          <div class="form-group">
            <label for="edit_customer_id" class="form-label">Customer</label>
            <select name="customer_id" id="edit_customer_id" class="form-control" required>
              <option value="">-- Select --</option>
              <?php
              // Same list as add form
              $res = $conn->query("SELECT id, name FROM customers ORDER BY name ASC");
              while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
              }
              ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Billing Date</label>
            <input type="date" name="billing_date" id="edit_billing_date" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" id="edit_due_date" class="form-control" required>
          </div>
          <!-- JS Scripts 
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-control" required>
              <option value="unpaid">Unpaid</option>
              <option value="paid">Paid</option>
            </select>
          </div>-->

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Bill</button>
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap4.min.js"></script>

  <!-- DataTables Buttons for export -->
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

  <script>
    let billingTable;

    $(document).ready(function () {

      $('#billingTable').on('click', '.cancel-btn', function () {
        const billId = $(this).data('id');

        if (confirm('Are you sure you want to cancel this bill?')) {
          $.ajax({
            url: '../ajax/cancel_bill.php',
            method: 'POST',
            data: { id: billId },
            dataType: 'json',
            success: function (response) {
              alert(response.message);
              $('#billingTable').DataTable().ajax.reload();
            },
            error: function () {
              alert('An error occurred while cancelling the bill.');
            }
          });
        }
      });

      $('#sendRemindersBtn').on('click', function () {
        $.ajax({
          url: '../ajax/send_bulk_sms.php',
          method: 'POST',
          dataType: 'json',
          success: function (response) {
            alert(response.message); // ✅ Now shows: "Reminders sent successfully."
          },
          error: function (xhr, status, error) {
            alert('Error sending reminders');
            console.error(error);
          }
        });
      });

      $('#addBillModal').on('show.bs.modal', function () {
        $.ajax({
          url: '../ajax/generate_ref.php',
          method: 'GET',
          dataType: 'json',
          success: function (response) {
            $('input[name="reference_num"]').val(response.reference);
          },
          error: function () {
            console.error('Failed to fetch reference number');
          }
        });
      });

      billingTable = $('#billingTable').DataTable({
        ajax: {
          url: '../ajax/fetch_bills.php',
          dataSrc: 'data'
        },
        columns: [
          {
            data: 'id'
          },
          { data: 'reference_num' },
          { data: 'customer_name' },
          { data: 'contact' },
          { data: 'plan' },
          { data: 'billing_date' },
          { data: 'due_date' },
          {
            data: 'amount',
            render: function (data) {
              return '₱' + parseFloat(data).toFixed(2);
            }
          },
          {
            data: 'status',
            render: function (data, type, row) {
              let badgeClass = '';

              switch (data.toLowerCase()) {
                case 'paid':
                  badgeClass = 'badge-success';
                  break;
                case 'unpaid':
                  badgeClass = 'badge-danger';
                  break;
                case 'cancelled':
                  badgeClass = 'badge-secondary';
                  break;
                default:
                  badgeClass = 'badge-light';
              }

              return `
    <span class="badge badge-status ${badgeClass}" style="cursor:pointer;" data-id="${row.id}" data-status="${data}">
      ${data.charAt(0).toUpperCase() + data.slice(1)}
    </span>
  `;
            }
          },
          {
            data: null,
            orderable: false,
            render: function (data, type, row) {
              if (row.status === 'unpaid') {
                return `
        <button class="btn btn-sm btn-primary btn-edit" data-id="${row.id}" style="margin-left: 5px;">
          ✏️ Edit
        </button>
      `;
              } else {
                return ''; // No button for paid or cancelled
              }
            }
          }
        ],
        order: [[2, 'desc']],
        lengthMenu: [10, 25, 50, 100],
        dom: 'Bfltip',
        buttons: [
          'copy', 'csv', 'excel', 'pdf', 'print'
        ]
      });

      // Add Bill Form Submit with Billing vs Due Date Validation + Confirmation
      $('#addBillForm').submit(function (e) {
        e.preventDefault();

        const billingRaw = $('#billing_date').val();
        const dueRaw = $('#due_date').val();

        // Convert strings like '24/06/2025' to Date objects safely
        const [bdDay, bdMonth, bdYear] = billingRaw.split('/');
        const [ddDay, ddMonth, ddYear] = dueRaw.split('/');

        const billingDate = new Date(`${bdYear}-${bdMonth}-${bdDay}`);
        const dueDate = new Date(`${ddYear}-${ddMonth}-${ddDay}`);

        if (isNaN(billingDate.getTime()) || isNaN(dueDate.getTime())) {
          $('#bill-alert-box').html('<div class="alert alert-danger">❌ Please provide valid billing and due dates.</div>');
          return;
        }

        if (billingDate > dueDate) {
          $('#bill-alert-box').html('<div class="alert alert-danger">❌ Billing date cannot be after due date.</div>');
          return;
        }

        // 🔔 Confirmation prompt
        if (!confirm("Are you sure you want to save this bill?")) {
          return;
        }

        // Submit form
        $.post('../ajax/add_bill.php', $(this).serialize(), function (response) {
          if (response.trim() === 'success') {
            $('#addBillModal').modal('hide');
            $('#addBillForm')[0].reset();
            billingTable.ajax.reload();
            $('#bill-alert-box').html('<div class="alert alert-success">✅ Bill added successfully!</div>');
          } else {
            $('#bill-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
          }
        });
      });



      // Click Edit Button - load bill data to modal
      $('#billingTable tbody').on('click', '.btn-edit', function () {
        let id = $(this).data('id');
        $.getJSON('../ajax/get_bill.php?id=' + id, function (bill) {
          if (bill) {
            $('#edit_bill_id').val(bill.id);
            $('#edit_ref_num').val(bill.reference_num);
            $('#edit_customer_id').val(bill.customer_id);
            $('#edit_billing_date').val(bill.billing_date);
            $('#edit_due_date').val(bill.due_date);
            $('#edit_status').val(bill.status);
            $('#editBillModal').modal('show');
          } else {
            alert('Bill not found');
          }
        });
      });

      // Edit Bill Form Submit
      $('#editBillForm').submit(function (e) {
        e.preventDefault();
        $.post('../ajax/edit_bill.php', $(this).serialize(), function (response) {
          if (response.trim() === 'success') {
            $('#editBillModal').modal('hide');
            billingTable.ajax.reload();
            $('#bill-alert-box').html('<div class="alert alert-success">✅ Bill updated successfully!</div>');
          } else {
            $('#bill-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
          }
        });
      });

      // Delete bill
      $('#billingTable tbody').on('click', '.btn-delete', function () {
        if (!confirm('Are you sure you want to delete this bill?')) return;
        let id = $(this).data('id');
        $.post('../ajax/delete_bill.php', { id }, function (response) {
          if (response.trim() === 'success') {
            billingTable.ajax.reload();
            $('#bill-alert-box').html('<div class="alert alert-success">✅ Bill deleted successfully!</div>');
          } else {
            $('#bill-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
          }
        });
      });

      // Toggle status on badge click
      $('#billingTable tbody').on('click', '.badge-status', function () {
        let id = $(this).data('id');
        let currentStatus = $(this).data('status');
        let newStatus = currentStatus === 'paid' ? 'unpaid' : 'paid';

        $.post('../ajax/toggle_status.php', { id, status: newStatus }, function (response) {
          if (response.trim() === 'success') {
            billingTable.ajax.reload();
            $('#bill-alert-box').html('<div class="alert alert-success">✅ Status updated successfully!</div>');
          } else {
            $('#bill-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
          }
        });
      });

    });
  </script>

</body>

</html>