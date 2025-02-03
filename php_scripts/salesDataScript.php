<?php
include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables
$saleDataItems = [];
$successMessage = "";
$errorMessage = "";

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Query to fetch total sales data
$query = "
    SELECT 
        COALESCE(SUM(quantitySold), 0) AS totalQuantitySold, 
        COALESCE(SUM(totalCosts), 0) AS totalCost, 
        COALESCE(SUM(revenue), 0) AS totalRevenue, 
        COALESCE(SUM(profit), 0) AS totalProfit
    FROM sales
";

$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();

    // Prepare the data for display
    $saleDataItems[] = [
        'totalQuantitySold' => $row['totalQuantitySold'],
        'totalCost' => $row['totalCost'],
        'revenue' => $row['totalRevenue'],
        'profit' => $row['totalProfit'],
    ];
} else {
    $errorMessage = "No sales data found.";
}

$conn->close();
?>
