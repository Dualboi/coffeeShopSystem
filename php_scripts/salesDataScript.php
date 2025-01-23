<?php
include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables
$saleDataItems = [];
$successMessage = "";
$errorMessage = "";

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Query to fetch and calculate the total sales data
$query = "
    SELECT 
        SUM(quantitySold) AS totalQuantitySold, 
        SUM(totalCosts) AS totalCost, 
        SUM(revenue) AS totalRevenue, 
        SUM(profit) AS totalProfit
    FROM sales
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    // Fetch the row with aggregated data
    $row = $result->fetch_assoc();

    // Prepare the data for display
    $saleDataItems[] = [
        'totalQuantitySold' => $row['totalQuantitySold'] ?? 0,
        'totalCost' => $row['totalCost'] ?? 0,
        'revenue' => $row['totalRevenue'] ?? 0,
        'profit' => $row['totalProfit'] ?? 0,
    ];
} else {
    $errorMessage = "No sales data found.";
}

// Close the database connection
$conn->close();
?>