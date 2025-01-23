<?php
include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables
$totalWage = 0; // Initialize the $totalWage variable to avoid undefined warnings
$userDetails = [];
$successMessage = "";
$errorMessage = "";

// Check if userID is in the session
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    // Connect to the database
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Query to fetch user details and calculate total wages
    $query = "
        SELECT 
            u.forname, u.surname, 
            SUM(r.hoursWorked * w.wage) AS totalWage
        FROM clientUserInfo u
        JOIN rota r ON u.userID = r.userID
        JOIN wages w ON u.wagesID = w.wagesID
        WHERE u.userID = ?
        GROUP BY u.userID
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($forname, $surname, $fetchedTotalWage);
    $stmt->fetch();

    // Check if user details are found
    if ($stmt->num_rows > 0) {
        $totalWage = $fetchedTotalWage; // Assign the fetched total wage
        $userDetails = [
            "forname" => $forname,
            "surname" => $surname,
            "totalWage" => $totalWage
        ];
    } else {
        $errorMessage = "No user data found.";
    }

    $stmt->close();
    $conn->close();
} else {
    $errorMessage = "User is not logged in.";
}

?>
