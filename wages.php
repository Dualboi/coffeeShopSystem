<?php
session_start();

include "php_scripts\wagesScript.php";
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
            <a href="<?php echo isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ? 'adminPage.php' : 'ClientPage.php'; ?>">Home</a>
            <a href="index.php">Logout</a>
            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']): ?>
                <a href="salesData.php"> Sales data </a>
            <?php endif; ?>
            <a href="inventory.php">Inventory</a>
            <a href="rota.php">Rota</a>
            <a href="wages.php">Wages</a>
        </div>
    </header>
    <h1 class="login_txt">Wage Dashboard</h1>
    <section>
    <article class="table">
            <!-- Title of the section is now included in the table header -->
            <table class="dataTable" border="1">
                <thead>
                    <tr>
                        <th colspan="2" class="table-header">Wages For <?php echo htmlspecialchars($forname . ' ' . $surname); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Your Wage This Month</th>
                        <td>£<?php echo htmlspecialchars($totalWage); ?></td>
                    </tr>
                </tbody>

            </table>
        </article>
    </section>
    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>
</body>

</html>