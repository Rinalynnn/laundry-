<?php
include 'db_connect.php';

// Fetch Monthly Sales
$monthly_query = $conn->query("
    SELECT MONTHNAME(date_created) AS month, 
           SUM(amount_tendered) AS total_sales 
    FROM laundry_list 
    WHERE YEAR(date_created) = YEAR(CURDATE()) 
    GROUP BY MONTH(date_created) 
    ORDER BY MONTH(date_created);
");
$monthly_data = [];
while ($row = $monthly_query->fetch_assoc()) {
    $monthly_data['labels'][] = $row['month'];
    $monthly_data['data'][] = $row['total_sales'];
}

// Fetch Weekly Sales
$weekly_query = $conn->query("
    SELECT WEEK(date_created) AS week, 
           SUM(amount_tendered) AS total_sales 
    FROM laundry_list 
    WHERE YEAR(date_created) = YEAR(CURDATE()) 
    GROUP BY WEEK(date_created) 
    ORDER BY WEEK(date_created);
");
$weekly_data = [];
while ($row = $weekly_query->fetch_assoc()) {
    $weekly_data['labels'][] = 'Week ' . $row['week'];
    $weekly_data['data'][] = $row['total_sales'];
}

// Return JSON
echo json_encode(['monthly' => $monthly_data, 'weekly' => $weekly_data]);
?>
