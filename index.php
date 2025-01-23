<!DOCTYPE html>
<html lang="en">

<head>
    <title>Coffee Shop System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/4a263b5a4b.js" crossorigin="anonymous"></script>
    <style>
</style>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <header>
        <h2 class="logo">coffee.co</h2>
        <h3></h3>
        <!--<div class="navbar">
        <a href="index.php">Home</a>
        <a href="index.php">Logout</a>
        <a href="inventory.php">Inventory</a>
        <a href="rota.php">Rota</a>
        <a href="wages.php">Wages</a>
    </div>!-->
    </header>
    <h1 class="login_txt">Sign up to get started today!</h1>
    <p class="about"> The complete coffee shop management system with built in inventory management, rota managment, and wage calculation.</p>
    <section>
        <article>
            <div>
                <form method="POST" action="login.php">
                    <div class="email" for="email">Email:</div>
                    <input type="email" id="email" name="email" required>
                    <br>
                    <div class="password" for="password">Password:</div>
                    <input type="password" id="password" name="password" required>
                    <br>
                    <input class="login" type="submit" value="Login">
                </form>
            </div>
            <h3 class="newRegister">Not already a user? Register here!</h2>
                <button class="newRegister">
                    <a href="register.php">Register</a>
                </button>
        </article>
    </section>

    <footer class="mainfooter">
        <a class="logofooter"><i class="fa-regular fa-copyright"></i> 2025 coffee.co All Rights Reserved.</a>
    </footer>

    <?php
    include "php_scripts\db_connection.php";
    // Get the single instance of the database connection
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Close the connection when done
    $conn->close();
    ?>
</body>

</html>