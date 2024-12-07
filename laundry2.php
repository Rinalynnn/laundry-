<?php 
include 'db_connect1.php';

// Set the correct time zone for your location
date_default_timezone_set('Asia/Manila'); // Adjust to your local time zone
?>
<div class="container-fluid">
    <div class="col-lg-12"> 
        <div class="card">
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-12">     
                        <button class="col-sm-3 float-right btn btn-primary btn-sm" type="button" id="new_laundry"><i class="fa fa-plus"></i> New Laundry</button>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">     
                        <table class="table table-bordered" id="laundry-list">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Queue</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Amount Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Adjusted SQL query based on your actual table structure
                                $list = $conn->query("SELECT id, custom_name, queu, stat, amount_total, created_date FROM laundry_list1 ORDER BY stat ASC, id ASC");
                                while($row = $list->fetch_assoc()):
                                    // Ensure the date format is correct using strtotime and PHP date formatting
                                    $formatted_date = date("M d, Y", strtotime($row['created_date']));
                                ?>
                                <tr>
                                    <td class=""><?php echo $formatted_date ?></td>
                                    <td class="text-right"><?php echo $row['queu'] ?></td>
                                    <td class=""><?php echo ucwords($row['custom_name']) ?></td>
                                    <?php if($row['stat'] == 0): ?>
                                        <td class="text-center"><span class="badge badge-secondary">Pending</span></td>
                                    <?php elseif($row['stat'] == 1): ?>
                                        <td class="text-center"><span class="badge badge-primary">Processing</span></td>
                                    <?php elseif($row['stat'] == 2): ?>
                                        <td class="text-center"><span class="badge badge-info">Ready to be Claim</span></td>
                                    <?php elseif($row['stat'] == 3): ?>
                                        <td class="text-center"><span class="badge badge-success">Claimed</span></td>
                                    <?php endif; ?>
                                    <td class="text-right"><?php echo number_format($row['amount_total'], 2) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit_laundry" data-id="<?php echo $row['id'] ?>">Edit</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm delete_laundry" data-id="<?php echo $row['id'] ?>">Delete</button>
                                        <button type="button" class="btn btn-outline-success btn-sm print_receipt" data-id="<?php echo $row['id'] ?>">Print Receipt</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>  
    </div>  
</div>

<!-- Printable Modern Receipt -->
<div id="receipt" style="display:none; font-family: 'Arial', sans-serif; background-color: #f9f9f9; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto;">
    <div style="text-align: center; padding-bottom: 20px;">
        <!-- Logo -->
        <img src="logo.jpg" alt="Habit Laba Laundry" style="max-width: 120px; margin-bottom: 15px;">
        <h2 style="color: #00796B; font-size: 32px; font-weight: bold; margin-bottom: 5px;">Habit Laba Laundry</h2>
        <p style="font-size: 16px; color: #757575; font-weight: 600;">Your Laundry, Our Priority</p>
    </div>

    <div style="margin-top: 30px; font-size: 16px; color: #424242;">
        <p><strong>Customer Name:</strong> <span id="receipt_customer_name" style="font-weight: bold;"></span></p>
        <p><strong>Status:</strong> <span id="receipt_status"></span></p>
        <p><strong>Date:</strong> <span id="receipt_date"></span></p>
        <p><strong>Receipt Date:</strong> <span id="current_date" style="font-weight: bold;"></span></p>
    </div>

    <div style="margin-top: 30px; border-top: 2px solid #00796B; padding-top: 15px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 8px; background-color: #00796B; color: white; text-align: left;">Category</th>
                    <th style="padding: 8px; background-color: #00796B; color: white; text-align: left;">Weight</th>
                    <th style="padding: 8px; background-color: #00796B; color: white; text-align: left;">Unit Price</th>
                    <th style="padding: 8px; background-color: #00796B; color: white; text-align: left;">Amount</th>
                </tr>
            </thead>
            <tbody id="receipt_items">
                <!-- Items will be dynamically added here -->
            </tbody>
        </table>
    </div>

    <div style="margin-top: 25px; text-align: right; font-size: 18px; color: #00796B; font-weight: bold;">
        <p><strong>Total: </strong><span id="receipt_total" style="font-size: 22px;"></span></p>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 14px; color: #757575;">
        <p>Thank you for choosing Habit Laba Laundry!</p>
        <p>We look forward to serving you again!</p>
    </div>
</div>

<script>
    // Handle Print Receipt
    $('.print_receipt').click(function(){
        var laundryId = $(this).attr('data-id');
        $.ajax({
            url: 'ajax.php?action=get_laundry_details',
            method: 'POST',
            data: {id: laundryId},
            success: function(response){
                var data = JSON.parse(response);
                
                // Set customer name, status, and date
                $('#receipt_customer_name').text(data.custom_name);
                $('#receipt_status').text(data.stat);
                $('#receipt_date').text(data.created_date);

                // Add current date (the date of printing)
                var currentDate = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                $('#current_date').text(currentDate);
                
                // Populate the items table
                var itemsHTML = '';
                data.items.forEach(function(item) {
                    itemsHTML += `<tr>
                                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">${item.category}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">${item.weight}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">${item.unit_price}</td>
                                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">${item.amount}</td>
                                </tr>`;
                });
                $('#receipt_items').html(itemsHTML);
                
                // Set total amount
                $('#receipt_total').text(data.amount_total);

                // Print the receipt
                var receiptContent = document.getElementById('receipt').innerHTML;
                var newWindow = window.open('', '', 'height=600, width=800');
                newWindow.document.write('<html><head><title>Receipt</title>');
                newWindow.document.write('<style>body {font-family: Arial, sans-serif;}</style>');
                newWindow.document.write('</head><body>');
                newWindow.document.write(receiptContent);
                newWindow.document.write('</body></html>');
                newWindow.document.close();
                newWindow.print();
            }
        });
    });

    // Other JavaScript code
    $('#new_laundry').click(function(){
        uni_modal('New Laundry', 'manage_laundry2.php', 'mid-large');
    });

    $('.edit_laundry').click(function(){
        uni_modal('Edit Laundry', 'manage_laundry2.php?id=' + $(this).attr('data-id'), 'mid-large');
    });

    $('.delete_laundry').click
    $('.delete_laundry').click(function(){
        _conf("Are you sure to remove this data from the list?", "delete_laundry", [$(this).attr('data-id')]);
    });

    $('#laundry-list').dataTable();

    function delete_laundry(id){
        start_load(); // Start loading animation
        $.ajax({
            url: 'ajax.php?action=delete_laundry',
            method: 'POST',
            data: {id: id},
            success: function(resp){
                if(resp == 1){
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload(); // Reload the page after deletion
                    }, 1500);
                } else {
                    alert_toast("Failed to delete the data", 'error');
                }
            }
        });
    }
</script>
