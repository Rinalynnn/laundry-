<style>
    .card-body {
        padding: 20px;
    }
    .alert {
        border-radius: 8px;
    }
    .alert p {
        margin: 0;
    }
    .alert .text-right {
        text-align: right;
    }
</style>

<div class="container-fluid">
    <div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Total Profit Today -->
                        <div class="alert alert-success col-md-3 ml-4">
                            <p><b><large>Total Profit Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                include 'db_connect1.php'; // Make sure the db_connect.php file is correctly configured
                                $today = date('Y-m-d');
                                $result = $conn->query("SELECT SUM(amount_total) AS amount, 
                                                       COUNT(id) AS count, 
                                                       SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) AS claimed
                                                       FROM laundry_list1 
                                                       WHERE date(created_date) = '$today' AND status_pay = 1");

                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    echo number_format($row['amount'], 2); // Total Profit
                                } else {
                                    echo "0.00";
                                }
                                ?>
                            </large></b></p>
                        </div>

                        <!-- Total Customers Today -->
                        <div class="alert alert-info col-md-3 ml-4">
                            <p><b><large>Total Customers Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                include 'db_connect1.php'; // Same as above, ensures the connection is in place
                                $result = $conn->query("SELECT count(id) AS count FROM laundry_list1 
                                                       WHERE date(created_date) = '$today'");
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    echo number_format($row['count']);
                                } else {
                                    echo "0";
                                }
                                ?>
                            </large></b></p>
                        </div>

                        <!-- Total Claimed Laundry Today -->
                        <div class="alert alert-primary col-md-3 ml-4">
                            <p><b><large>Total Claimed Laundry Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                include 'db_connect1.php'; // Ensure db connection is included
                                $result = $conn->query("SELECT count(id) AS count FROM laundry_list1 
                                                       WHERE status = 3 AND date(created_date) = '$today'");
                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    echo number_format($row['count']);
                                } else {
                                    echo "0";
                                }
                                ?>
                            </large></b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


