<?php
session_start();

// Ensure that the userID is set
if (!isset($_SESSION['userID'])) {
    echo "No userID found in session.";
    exit;  // Stop execution if userID is not set
}

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit;
}

include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables for the user's name
$forname = 'Guest';
$surname = '';
$email = '';
$streetAddress = '';
$postCode = '';
$city = '';
$roleType = '';

// Check if userID is in the session
if (isset($_SESSION['userID'])) {
    // Get the userID from the session
    $userID = $_SESSION['userID'];

    // Connect to the database
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Query to get the user's full information, including their role
    $query = "
SELECT 
    u.forname, u.surname, u.email, 
    a.streetAddress, a.postCode, a.city,
    r.roleType
FROM clientUserInfo u
JOIN address a ON u.addressID = a.addressID
JOIN roleType r ON u.roleTypeID = r.roleTypeID
WHERE u.userID = ?
";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID); // Bind the userID parameter to the query
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($fetchedForname, $fetchedSurname, $fetchedEmail, $fetchedStreetAddress, $fetchedPostCode, $fetchedCity, $fetchedRoleType);
    $stmt->fetch();

    // Check if a user was found and assign the fetched data to variables
    if ($stmt->num_rows > 0) {
        $stmt->fetch(); // Make sure to fetch data only if there are rows
        $forname = $fetchedForname;
        $surname = $fetchedSurname;
        $email = $fetchedEmail;
        $streetAddress = $fetchedStreetAddress;
        $postCode = $fetchedPostCode;
        $city = $fetchedCity;
        $roleType = $fetchedRoleType;  // Store the roleType
    } else {
        // This else block can help debug if no user is found with the given userID
        echo "No user found with this ID.";
    }


    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
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
    
    <div class="navbar">
        <a href="adminPage.php">Home</a>
        <a href="index.php">Logout</a>
        <a href="salesData.php">Sales data</a>
        <a href="inventory.php">Inventory</a>
        <a href="rota.php">Rota</a>
        <a href="wages.php">Wages</a>
    </div>
</header>
<h1 class="login_txt">Admin portal</h1>
<h3 class="login_txt">Hello, <?php echo htmlspecialchars($forname . ' ' . $surname); ?>! <i class="fa-regular fa-face-smile-beam"></i></h3><!-- create "hello admin *user" here!-->
<section>
        <article class="table">
            <!-- Title of the section is now included in the table header -->
            <table class="dataTable" border="1">
                <thead>
                    <tr>
                        <th colspan="2" class="table-header">User Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>First Name</th>
                        <td><?php echo htmlspecialchars($forname); ?></td>
                    </tr>
                    <tr>
                        <th>Last Name</th>
                        <td><?php echo htmlspecialchars($surname); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($email); ?></td>
                    </tr>
                    <tr>
                        <th>Street Address</th>
                        <td><?php echo htmlspecialchars($streetAddress); ?></td>
                    </tr>
                    <tr>
                        <th>Post Code</th>
                        <td><?php echo htmlspecialchars($postCode); ?></td>
                    </tr>
                    <tr>
                        <th>City</th>
                        <td><?php echo htmlspecialchars($city); ?></td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td><?php echo htmlspecialchars($roleType); ?></td> <!-- Display the roleType here -->
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
