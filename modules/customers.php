<?php
session_start();
// Normally you'd check if user is logged in here
// include your DB connection file
include '../includes/db_connect.php';

// Fetch customers from database with pagination
$limit = 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Count total customers for pagination
$totalQuery = $conn->query("SELECT COUNT(*) as total FROM customers");
$totalRow = $totalQuery->fetch_assoc();
$total_customers = $totalRow['total'];

// Fetch customers data
$sql = "SELECT * FROM customers ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Customers - ISP Billing</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <style>
    body {
      background: #f5f7fa;
      font-family: 'Inter', sans-serif;
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .table-actions button {
      margin-right: 5px;
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
  <div class="container-fluid my-4 py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-xl-11 col-xxl-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h3>Customers</h3>
          <button class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">Add New Customer</button>
        </div>

        <div class="card p-3 shadow-sm border-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="thead-light">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Account No.</th>
                  <th>Contact</th>
                  <th>Location</th>
                  <th>Address</th>
                  <th>Date of Installation</th>
                  <th>Plan</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($row['id']) ?></td>
                      <td><?= htmlspecialchars($row['name']) ?></td>
                      <td><?= htmlspecialchars($row['acountNum']) ?></td>
                      <td><?= htmlspecialchars($row['contactNum']) ?></td>
                      <td><?= htmlspecialchars($row['location']) ?></td>
                      <td><?= htmlspecialchars($row['address']) ?></td>
                      <td><?= htmlspecialchars($row['date_of_installation']) ?></td>
                      <td><?= htmlspecialchars($row['plan']) ?></td>
                      <td><?= htmlspecialchars($row['amount']) ?></td>
                      <td>
                        <?php if ($row['subscription_active']): ?>
                          <span class="badge badge-success">Active</span>
                        <?php else: ?>
                          <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-info edit-btn" data-id="<?= $row['id'] ?>"
                          data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                          data-email="<?= htmlspecialchars($row['acountNum'], ENT_QUOTES) ?>"
                          data-contact="<?= htmlspecialchars($row['contactNum'], ENT_QUOTES) ?>"
                            data-location="<?= htmlspecialchars($row['location'], ENT_QUOTES) ?>"
                          data-address="<?= htmlspecialchars($row['address'], ENT_QUOTES) ?>"
                           data-date="<?= htmlspecialchars($row['date_of_installation'], ENT_QUOTES) ?>"
                           data-plan="<?= htmlspecialchars($row['plan'], ENT_QUOTES) ?>"
                           data-amount="<?= htmlspecialchars($row['amount'], ENT_QUOTES) ?>"
                          data-active="<?= $row['subscription_active'] ?>">Edit</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="10" class="text-center">No customers found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav>
            <ul class="pagination justify-content-center">
              <?php
              $totalPages = ceil($total_customers / $limit);
              for ($i = 1; $i <= $totalPages; $i++):
                ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>


  <!-- Add Customer Modal -->
  <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="addCustomerForm" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Customer</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div id="add-alert-box"></div>
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="form-group">
            <label for="email">Account No.</label>
            <input type="text" id="email" name="email" class="form-control" />
          </div>

          <div class="form-group">
            <label>Contact No</label>
            <input type="text" name="contact_no" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Server Location</label>
            <select id="location" name="location" class="form-control" required>
              <option value="Surallah">Surallah</option>
              <option value="T'boli">T'boli</option>
              <option value="Lake Sebu">Lake Sebu</option>
            </select>
          </div>
          <div class="form-group">
            <label>Full Adress</label>
            <textarea name="address" class="form-control" rows="3" required></textarea>
          </div>


          <div class="form-group">
            <label>Date of Installation</label>
            <input type="date" name="installation_date" class="form-control" required />
          </div>
          <div class="form-group">
            <label>Plan</label>
            <select id="plan" name="plan" class="form-control" required>
              <option value="">-- Select Plan --</option>
              <option value="20mbps">20mbps</option>
              <option value="25mbps">25mbps</option>
              <option value="30mbps">30mbps</option>
              <option value="50mbps">50mbps</option>
            </select>
          </div>
          <div class="form-group">
            <label>Amount</label>
            <input type="number" id="amount" name="amount" class="form-control" step="0.01" required />
          </div>
          <div class="form-group">
            <label>Subscription Active</label>
            <select name="subscription_active" class="form-control">
              <option value="1" selected>Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Add Customer</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editCustomerForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Customer</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="edit-alert-box"></div>

        <!-- Hidden ID -->
        <input type="hidden" name="customer_id" id="edit-customer-id" />

        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" id="edit-name" class="form-control" required />
        </div>
        <div class="form-group">
          <label for="edit-email">Account No.</label>
          <input type="text" id="edit-email" name="email" class="form-control" />
        </div>
        <div class="form-group">
          <label>Contact No</label>
          <input type="text" name="edit_contact_no" id="edit_contact_no" class="form-control" required />
        </div>
        <div class="form-group">
          <label>Server Location</label>
          <select id="edit-location" name="edit_location" class="form-control" required>
            <option value="Surallah">Surallah</option>
            <option value="T'boli">T'boli</option>
            <option value="Lake Sebu">Lake Sebu</option>
          </select>
        </div>
        <div class="form-group">
          <label>Full Address</label>
          <textarea name="address" id="edit-address" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
          <label>Date of Installation</label>
          <input type="date" name="installation_date" id="edit-installation-date" class="form-control" required />
        </div>
        <div class="form-group">
          <label>Plan</label>
          <select id="edit-plan" name="plan" class="form-control" required>
            <option value="">-- Select Plan --</option>
            <option value="20mbps">20mbps</option>
            <option value="25mbps">25mbps</option>
            <option value="30mbps">30mbps</option>
            <option value="50mbps">50mbps</option>
          </select>
        </div>
        <div class="form-group">
          <label>Amount</label>
          <input type="number" id="edit-amount" name="amount" class="form-control" step="0.01" required />
        </div>
        <div class="form-group">
          <label>Subscription Active</label>
          <select name="subscription_active" id="edit-subscription-active" class="form-control">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Update Customer</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>


  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script>
    $(function () {

      const planSelect = document.getElementById('plan');
      const amountInput = document.getElementById('amount');

      const prices = {
        "20mbps": 1000,
        "25mbps": 1300,
        "30mbps": 2000,
        "50mbps": 3500,
      };

      planSelect.addEventListener('change', function () {
        const selectedPlan = this.value;
        if (prices[selectedPlan]) {
          amountInput.value = prices[selectedPlan];
          amountInput.readOnly = true;  // Prevent manual editing if you want
        } else {
          amountInput.value = '';
          amountInput.readOnly = false; // Allow input if no plan selected
        }
      });

            const editPlanSelect = document.getElementById('edit-plan');
      const EditAmountInput = document.getElementById('edit-amount');

      const editPrices = {
        "20mbps": 1000,
        "25mbps": 1300,
        "30mbps": 2000,
        "50mbps": 3500,
      };

      editPlanSelect.addEventListener('change', function () {
        const editSelectedPlan = this.value;
        if (editPrices[editSelectedPlan]) {
          EditAmountInput.value = editPrices[editSelectedPlan];
          EditAmountInput.readOnly = true;  // Prevent manual editing if you want
        } else {
          EditAmountInput.value = '';
          EditAmountInput.readOnly = false; // Allow input if no plan selected
        }
      });

// Fill edit form on clicking edit button
$('.edit-btn').on('click', function () {
  const btn = $(this);
  const id = btn.data('id');
  const name = btn.data('name');
  const email = btn.data('email'); // Account number
  const contact_no = btn.data('contact'); // Assuming `data-contact` is set
  const location = btn.data('location');
  const address = btn.data('address');
  const installation_date = btn.data('date');
  const plan = btn.data('plan');
  const amount = btn.data('amount');
  const active = btn.data('active');

  const modal = $('#editCustomerModal');
  modal.find('input[name="customer_id"]').val(id);
  modal.find('input[name="name"]').val(name);
  modal.find('input[name="email"]').val(email);
  modal.find('input[name="edit_contact_no"]').val(contact_no);
  modal.find('select[name="edit_location"]').val(location);
  modal.find('textarea[name="address"]').val(address);
  modal.find('input[name="installation_date"]').val(installation_date);
  modal.find('select[name="plan"]').val(plan);
  modal.find('input[name="amount"]').val(amount);
  modal.find('select[name="subscription_active"]').val(active);

  modal.modal('show');
});


      // Add Customer form submit
      $('#addCustomerForm').submit(function (e) {
        e.preventDefault();
        $.post('../ajax/add_customer.php', $(this).serialize(), function (response) {
          if (response.trim() === 'success') {
            location.reload();
          } else {
            $('#add-alert-box').html('<div class="alert alert-danger">' + response + '</div>');
          }
        });
      });

      // Edit Customer form submit
      $('#editCustomerForm').submit(function (e) {
        e.preventDefault();
        $.post('../ajax/edit_customer.php', $(this).serialize(), function (response) {
          if (response.trim() === 'success') {
            location.reload();
          } else {
            $('#edit-alert-box').html('<div class="alert alert-danger">' + response + '</div>');
          }
        });
      });

      // Delete customer button
      $('.delete-btn').click(function () {
        if (confirm('Are you sure you want to delete this customer?')) {
          const id = $(this).data('id');
          $.post('ajax/delete_customer.php', { id: id }, function (response) {
            if (response.trim() === 'success') {
              location.reload();
            } else {
              alert('Error deleting customer: ' + response);
            }
          });
        }
      });

    });
  </script>
</body>

</html>