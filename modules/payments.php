<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payments</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-size: 15px;
        }

        .navbar {
            padding: 0.75rem 1.5rem;
        }

        h2 {
            font-weight: 600;
            color: #333;
        }

        .container-fluid {
            max-width: 100%;
            margin: 0 auto;
            padding-left: 20px;
            padding-right: 20px;
        }

        .btn-primary,
        .btn-success {
            border-radius: 6px;
        }

        .btn-primary:hover,
        .btn-success:hover {
            opacity: 0.9;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body .form-group label {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .table thead {
            background-color: #343a40;
            color: #fff;
        }

        .receipt-btn {
            margin-right: 5px;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 1rem;
        }

        #payment-alert-box {
            margin-bottom: 15px;
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

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>💰 Payments</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addPaymentModal">➕ Add Payment</button>
        </div>

        <div id="payment-alert-box"></div>

        <div class="table-responsive" style="overflow-x: auto;">
            <table id="paymentsTable" class="table table-bordered table-hover display nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Billing Ref No.</th>
                        <th>Customer</th>
                        <th>Billing Date</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Payment Ref No.</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <form id="addPaymentForm" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row d-none">
                        <div class="col">
                            <input type="text" name="bill_id" id="bill_id" class="form-control">
                            <input type="text" name="customer_id" id="customer_id" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Payment Ref No.</label>
                            <input type="text" name="reference_no" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bill Ref No.</label>
                            <input type="text" name="bill_reference_num" id="bill_reference_num" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Billing Date</label>
                            <input type="date" name="billing_date" id="billing_date" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Amount Due</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Payment Amount</label>
                            <input type="number" step="0.01" name="pay_amount" id="pay_amount" class="form-control"
                                required>
                            <div class="invalid-feedback">Amount must match the amount due.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="GCash">GCash</option>
                            <option value="PayMaya">PayMaya</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">💾 Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">✖ Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editPaymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <form id="editPaymentForm" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row d-none">
                        <div class="col">
                            <input type="hidden" name="payment_id" id="edit_payment_id" class="form-control">
                            <input type="hidden" name="bill_id" id="edit_bill_id" class="form-control">
                            <input type="hidden" name="customer_id" id="edit_customer_id" class="form-control">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Payment Ref No.</label>
                            <input type="text" name="reference_no" id="edit_reference_no" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Bill Ref No.</label>
                            <input type="text" name="edit_bill_reference_num" id="edit_bill_reference_num"
                                class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" id="edit_customer_name" class="form-control"
                                readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Billing Date</label>
                            <input type="date" name="billing_date" id="edit_billing_date" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Amount Due</label>
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control"
                                readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Payment Amount</label>
                            <input type="number" step="0.01" name="pay_amount" id="edit_pay_amount" class="form-control"
                                required>
                            <div class="invalid-feedback">Amount must match the amount due.</div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Payment Date</label>
                            <input type="date" name="payment_date" id="edit_payment_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="edit_payment_method" class="form-control" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="GCash">GCash</option>
                            <option value="PayMaya">PayMaya</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary text-white">💾 Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">✖ Cancel</button>
                </div>
            </form>
        </div>
    </div>


    <!-- jQuery first -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Popper.js (required for Bootstrap 4) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

    <!-- Bootstrap 4.5.2 JS without integrity -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        let paymentsTable;

        $(document).ready(function () {
            $('#paymentsTable').on('click', '.btn-edit-payment', function () {
                const id = $(this).data('id');
                // Build the URL (ensure this path is correct relative to your page)
                const url = '../ajax/get_payment.php?id=' + encodeURIComponent(id);

                $.getJSON(url)
                    .done(function (payment) {
                        console.log('Received payment data:', payment);
                        if (payment) {
                            // Populate fields. Make sure these selectors match input IDs in your modal.
                            $('#edit_payment_id').val(payment.id);
                            $('#edit_bill_id').val(payment.bill_id);
                            $('#edit_customer_id').val(payment.customer_id);
                            $('#edit_reference_no').val(payment.reference_no);
                            $('#edit_bill_reference_num').val(payment.bill_ref);
                            $('#edit_customer_name').val(payment.customer_name);
                            $('#edit_billing_date').val(payment.billing_date);
                            $('#edit_due_date').val(payment.due_date);
                            $('#edit_amount').val(payment.amount);
                            $('#edit_pay_amount').val(payment.pay_amount);
                            $('#edit_payment_date').val(payment.payment_date);
                            $('#edit_payment_method').val(payment.payment_method);

                            // Show the modal
                            $('#editPaymentModal').modal('show');
                        } else {
                            console.warn('get_payment returned null or empty');
                            alert('Payment not found');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching payment:', textStatus, errorThrown);
                        // Log the actual response text if useful:
                        console.error('Response text:', jqXHR.responseText);
                        alert('Error fetching payment data');
                    });
            });

                                 /*   $('#edit_bill_reference_num').on('blur', function () {
                const edit_bill_reference_num = $(this).val().trim();
                if (edit_bill_reference_num === '') return;

                $.ajax({
                    url: '../ajax/get_edit_bill_by_reference.php', // adjust path if needed
                    method: 'GET',
                    data: { edit_bill_reference_num: edit_bill_reference_num },
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            alert('Bill Reference not found.');
                            $('#edit_customer_name').val('');
                            $('#edit_customer_id').val('');
                            $('#amount').val('');
                            $('#edit_billing_date').val('');
                            $('#edit_bill_id').val('');
                            $('#edit_due_date').val('');
                        } else {
                            $('#edit_customer_name').val(data.customer_name);
                            $('#edit_customer_id').val(data.customer_id);
                            $('#edit_billing_date').val(data.billing_date);
                            $('#edit_due_date').val(data.due_date);
                            $('#edit_amount').val(data.amount_due);
                            $('#edit_bill_id').val(data.bill_id);
                        }
                    },
                    error: function () {
                        alert('Error fetching bill data.');
                    }
                });
            });**/

            $('#pay_amount').on('input', function () {
                let due = parseFloat($('#amount').val()) || 0;
                let pay = parseFloat($(this).val()) || 0;

                if (pay !== due) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                } else {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                }
            });
            $('#edit_pay_amount').on('input', function () {
                let due = parseFloat($('#edit_amount').val()) || 0;
                let pay = parseFloat($(this).val()) || 0;

                if (pay !== due) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                } else {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                }
            });
            $('#addPaymentModal').on('show.bs.modal', function () {
                $.ajax({
                    url: '../ajax/generate_payment_ref.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        $('input[name="reference_no"]').val(response.reference);
                    },
                    error: function () {
                        console.error('Failed to fetch reference number');
                    }
                });
            });
            $('#bill_reference_num').on('blur', function () {
                const bill_reference_num = $(this).val().trim();
                if (bill_reference_num === '') return;

                $.ajax({
                    url: '../ajax/get_bill_by_reference.php', // adjust path if needed
                    method: 'GET',
                    data: { bill_reference_num: bill_reference_num },
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            alert('Bill Reference not found.');
                            $('#customer_name').val('');
                            $('#customer_id').val('');
                            $('#amount').val('');
                            $('#billing_date').val('');
                            $('#bill_id').val('');
                            $('#due_date').val('');
                        } else {
                            $('#customer_name').val(data.customer_name);
                            $('#customer_id').val(data.customer_id);
                            $('#billing_date').val(data.billing_date);
                            $('#due_date').val(data.due_date);
                            $('#amount').val(data.amount_due);
                            $('#bill_id').val(data.bill_id);
                        }
                    },
                    error: function () {
                        alert('Error fetching bill data.');
                    }
                });
            });

            paymentsTable = $('#paymentsTable').DataTable({
                ajax: {
                    url: '../ajax/fetch_payments.php',
                    dataSrc: function (json) {
                        console.log('AJAX success, data:', json);
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.error('AJAX error:', xhr.responseText);
                        alert('Failed to load payments data. Check console for errors.');
                    }
                },
                columns: [
                    { data: null, render: (data, type, row, meta) => meta.row + 1 },
                    { data: 'bill_ref' },
                    { data: 'customer_name' },
                    { data: 'billing_date' },
                    { data: 'payment_date' },
                    {
                        data: 'amount',
                        render: data => '₱' + parseFloat(data).toFixed(2)
                    },
                    { data: 'payment_method' },
                    { data: 'reference_no' },
                    {
                        data: 'id',
                        orderable: false,
                        render: function (id, type, row) {
                            return `
      <button class="btn btn-sm btn-primary btn-edit-payment" data-id="${id}">
        ✏️ Edit
      </button>
      <a href="../generate_reciept.php?id=${id}" target="_blank" class="btn btn-sm btn-success receipt-btn" style="margin-left:5px;">
        🧾 Receipt
      </a>
    `;
                        }
                    }
                ],
                order: [[3, 'desc']],
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });


            $('#addPaymentForm').submit(function (e) {
                e.preventDefault();

                const due = parseFloat($('#amount').val());
                const payment = parseFloat($('#pay_amount').val());

                if (isNaN(due) || isNaN(payment)) {
                    alert('Amount Due and Payment Amount must be valid numbers.');
                    return;
                }

                if (due !== payment) {
                    alert('❌ Payment Amount must match Amount Due exactly.');
                    return;
                }

                if (!confirm('Are you sure you want to add this payment?')) {
                    return;
                }

                $.post('../ajax/add_payment.php', $(this).serialize(), function (response) {
                    if (!isNaN(response)) {
                        $('#addPaymentModal').modal('hide');
                        $('#addPaymentForm')[0].reset();
                        paymentsTable.ajax.reload();
                        $('#payment-alert-box').html('<div class="alert alert-success">✅ Payment recorded successfully.</div>');
                        window.open('../generate_reciept.php?id=' + response, '_blank');
                    } else {
                        $('#payment-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
                    }
                });
            });

            $('#editPaymentForm').submit(function (e) {
                e.preventDefault();
                const due = parseFloat($('#edit_amount').val());
                const payment = parseFloat($('#edit_pay_amount').val());
                if (isNaN(due) || isNaN(payment)) {
                    alert('Amount Due and Payment Amount must be valid numbers.');
                    return;
                }

                if (due !== payment) {
                    alert('❌ Payment Amount must match Amount Due exactly.');
                    return;
                }

                if (!confirm('Are you sure you want to update this payment?')) {
                    return;
                }
                $.post('../ajax/edit_payment.php', $(this).serialize(), function (response) {
                    if (response.trim() === 'success') {
                        $('#editPaymentModal').modal('hide');
                        paymentsTable.ajax.reload();
                        $('#payment-alert-box').html('<div class="alert alert-success">✅ Payment updated successfully!</div>');
                    } else {
                        $('#payment-alert-box').html('<div class="alert alert-danger">❌ ' + response + '</div>');
                    }
                });
            });

        });
    </script>

</body>

</html>