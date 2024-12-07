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
                        <!-- Total Profit Today (combined from both tables) -->
                        <div class="alert alert-success col-md-3 ml-4">
                            <p><b><large>Total Profit Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                include 'db_connect.php'; // Ensure the db_connect.php file is correctly configured
                                $today = date('Y-m-d');
                                
                                // Query for total profit from both laundry_list1 and laundry_list
                                $result1 = $conn->query("SELECT SUM(amount_total) AS amount 
                                                       FROM laundry_list1 
                                                       WHERE date(created_date) = '$today' AND status_pay = 1");

                                $result2 = $conn->query("SELECT SUM(total_amount) AS amount 
                                                       FROM laundry_list 
                                                       WHERE date(date_created) = '$today' AND pay_status = 1");

                                $totalProfit = 0;
                                if ($result1->num_rows > 0) {
                                    $row1 = $result1->fetch_assoc();
                                    $totalProfit += $row1['amount'];
                                }
                                if ($result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                    $totalProfit += $row2['amount'];
                                }

                                echo number_format($totalProfit, 2); // Combined Total Profit
                                ?>
                            </large></b></p>
                        </div>

                        <!-- Total Customers Today (combined from both tables) -->
                        <div class="alert alert-info col-md-3 ml-4">
                            <p><b><large>Total Customers Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                // Query for total customers from both laundry_list1 and laundry_list
                                $result1 = $conn->query("SELECT count(id) AS count 
                                                       FROM laundry_list1 
                                                       WHERE date(created_date) = '$today'");

                                $result2 = $conn->query("SELECT count(id) AS count 
                                                       FROM laundry_list 
                                                       WHERE date(date_created) = '$today'");

                                $totalCustomers = 0;
                                if ($result1->num_rows > 0) {
                                    $row1 = $result1->fetch_assoc();
                                    $totalCustomers += $row1['count'];
                                }
                                if ($result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                    $totalCustomers += $row2['count'];
                                }

                                echo number_format($totalCustomers); // Combined Total Customers
                                ?>
                            </large></b></p>
                        </div>

                        <!-- Total Claimed Laundry Today (combined from both tables) -->
                        <div class="alert alert-primary col-md-3 ml-4">
                            <p><b><large>Total Claimed Laundry Today</large></b></p>
                            <hr>
                            <p class="text-right"><b><large>
                                <?php 
                                // Query for total claimed laundry from both laundry_list1 and laundry_list
                                $result1 = $conn->query("SELECT count(id) AS count 
                                                       FROM laundry_list1 
                                                       WHERE status = 3 AND date(created_date) = '$today'");

                                $result2 = $conn->query("SELECT count(id) AS count 
                                                       FROM laundry_list 
                                                       WHERE status = 3 AND date(date_created) = '$today'");

                                $totalClaimed = 0;
                                if ($result1->num_rows > 0) {
                                    $row1 = $result1->fetch_assoc();
                                    $totalClaimed += $row1['count'];
                                }
                                if ($result2->num_rows > 0) {
                                    $row2 = $result2->fetch_assoc();
                                    $totalClaimed += $row2['count'];
                                }

                                echo number_format($totalClaimed); // Combined Total Claimed Laundry
                                ?>
                            </large></b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Monthly Sales</h3>
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="au-card m-b-30">
            <div class="au-card-inner">
                <h3 class="title-2 m-b-40">Weekly Sales</h3>
                <canvas id="weeklySalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('fetch_sales_data.php')
        .then(response => response.json())
        .then(data => {
            // Monthly Sales Chart
            const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: data.monthly.labels,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: data.monthly.data,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Weekly Sales Chart
            const weeklyCtx = document.getElementById('weeklySalesChart').getContext('2d');
            new Chart(weeklyCtx, {
                type: 'line',
                data: {
                    labels: data.weekly.labels,
                    datasets: [{
                        label: 'Weekly Sales',
                        data: data.weekly.data,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching sales data:', error));
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
