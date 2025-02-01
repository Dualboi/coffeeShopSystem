<?php
session_start();
?>
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
        <!--<div class="navbar">
        <a href="index.php">Home</a>
        <a href="index.php">Logout</a>
        <a href="inventory.php">Inventory</a>
        <a href="rota.php">Rota</a>
        <a href="wages.php">Wages</a>
    </div>!-->
    </header>
    <h1 class="login_txt">Register now</h1>
    <section>
        <article class="register">
            <div id="registration-form">
                <h2 class="registerh2">Register</h2>
                <form method="POST" action="php_scripts/register_user.php">
                    <!-- Email Input -->
                    <div for="email">Email:</div>
                    <input type="email" id="email" name="email" required><br><br>

                    <!-- Displaying error message for email  -->
                    <?php if (isset($_SESSION['errorMessage'])) {
                        echo '<p style="color: red;">' . $_SESSION['errorMessage'] . '</p>';
                        unset($_SESSION['errorMessage']);  // Clear the error message after displaying
                    } ?>

                    <!-- Password Input -->
                    <div for="password">Password:</div>
                    <input type="password" id="password" name="password" required><br><br>

                    <!-- Displaying Password Error Message -->
                    <?php
                    if (isset($_SESSION['passwordErr'])) {
                        echo '<p style="color: red;">' . $_SESSION['passwordErr'] . '</p><br>';
                        unset($_SESSION['passwordErr']);  // Clear the error message after displaying
                    }
                    ?>

                    <!-- First Name Input -->
                    <div for="forname">First Name:</div>
                    <input type="text" id="forname" name="forname" required><br><br>

                    <!-- Last Name Input -->
                    <div for="surname">Last Name:</div>
                    <input type="text" id="surname" name="surname" required><br><br>

                    <?php

                    // Check if there are errors to display
                    if (isset($_SESSION['errors'])) {
                        echo '<ul style="color: red;">';
                        foreach ($_SESSION['errors'] as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul>';
                        unset($_SESSION['errors']);  // Clear errors after displaying
                    }
                    ?>


                    <h3 class="addressHeader">Address</h3>

                    <!-- Street Address Input -->
                    <div for="streetAddress">Street Address:</div>
                    <input type="text" id="streetAddress" name="streetAddress" required><br><br>

                    <!-- Post Code Input -->
                    <div for="postCode">Post Code:</div>
                    <input type="text" id="postCode" name="postCode" required><br><br>

                    <!-- City Input -->
                    <div for="city">City:</div>
                    <input type="text" id="city" name="city" required><br><br>

                    <!-- Role Dropdown -->
                    <div for="roleType">Role:</div>
                    <select class="role_dropdown" name="roleType" id="roleType" required>
                        <!-- Options populated dynamically from the database -->
                        <?php
                        // Connect to the database
                        include "php_scripts/db_connection.php";
                        $db = Database::getInstance();
                        $conn = $db->getConnection();

                        // Fetch role types from the database
                        $query = "SELECT roleTypeID, roleType FROM roletype";  // Updated to match the correct table name
                        $result = $conn->query($query);

                        // Populate the dropdown options dynamically
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['roleTypeID'] . '">' . htmlspecialchars($row['roleType']) . '</option>';
                        }

                        // Close the connection
                        $conn->close();
                        ?>
                    </select><br><br>

                    <!-- Submit Button -->
                    <input class="register" type="submit" value="Register">

                    <!-- Link to Login Page -->
                    <div class="backtologintxt">Forgot you are actually already a user? Login here!</div>
                    <button class="backtologin"><a class="backtologin" href="index.php">Login here</a></button>
                </form>
            </div>
        </article>
    </section>


    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>

</body>

</html>