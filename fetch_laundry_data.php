<?php
// db_connect.php - Make sure to include this to connect to the database
include 'db_connect.php';

// Fetch data for the Total Profit, Total Customers, and Total Claimed Laundry Today
$today = date('Y-m-d');
$profitQuery = $conn->query("SELECT SUM(total_amount) AS amount FROM laundry_list WHERE pay_status = 1 AND DATE(date_created) = '$today'");
$customerQuery = $conn->query("SELECT COUNT(id) AS count FROM laundry_list WHERE DATE(date_created) = '$today'");
$claimedQuery = $conn->query("SELECT COUNT(id) AS count FROM laundry_list WHERE status = 3 AND DATE(date_created) = '$today'");

// Prepare the data for JSON response
$data = [
    'totalProfit' => $profitQuery->num_rows > 0 ? number_format($profitQuery->fetch_array()['amount'], 2) : "0.00",
    'totalCustomers' => $customerQuery->num_rows > 0 ? number_format($customerQuery->fetch_array()['count']) : "0",
    'totalClaimed' => $claimedQuery->num_rows > 0 ? number_format($claimedQuery->fetch_array()['count']) : "0"
];

// Send the data back as a JSON response
echo json_encode($data);
?>
