<?php
session_start();
include "php_scripts\salesDataScript.php";
// Debugging session variables
// Uncomment during debugging to verify session data
// echo "<pre>"; print_r($_SESSION); echo "</pre>";

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit;
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Shop System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="stylesheet.css">
    <script src="https://kit.fontawesome.com/4a263b5a4b.js" crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <h2 class="logo">coffee.co</h2>
        <div class="navbar">
            <a href="adminPage.php">Home</a>
            <a href="index.php">Logout</a>
            <a href="salesData.php">Sales data</a>
            <a href="inventory.php">Inventory</a>
            <a href="rota.php">Rota</a>
            <a href="wages.php">Wages</a>
        </div>
    </header>
    <h1 class="login_txt">Sales Dashboard</h1>
    <section class="inventory-section">
        <article class="inventory-table">
            <table class="inventory-dataTable" border="1">
                <thead>
                    <tr>
                        <th colspan="5" class="inventory-table-header">Current Inventory</th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>Total quantity sold</th>
                        <th>Total costs</th>
                        <th>Revenue</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($saleDataItems)): ?>
                        <?php foreach ($saleDataItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['totalQuantitySold']); ?></td>
                                <td>£<?php echo htmlspecialchars($item['totalCost']); ?></td>
                                <td>£<?php echo htmlspecialchars($item['revenue']); ?></td>
                                <td>£<?php echo htmlspecialchars($item['profit']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No sales data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </article>
    </section>
    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>
</body>

</html>