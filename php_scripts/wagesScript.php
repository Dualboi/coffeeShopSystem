<?php
session_start();

include "php_scripts/db_connection.php";

$totalWage = 0;
$userDetails = [];
$errorMessage = "";

// Check if the user is logged in
if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Query to fetch users first name, last name, total hours worked and calculate wages by role type.
    $query = "
    SELECT 
        u.forname, 
        u.surname, 
        IFNULL(SUM(
            TIME_TO_SEC(r.hoursWorked) / 3600 * 
            CASE rt.roleType
                WHEN 'Barista' THEN 12
                WHEN 'Manager' THEN 17
                WHEN 'Waiter' THEN 12
                WHEN 'Chef' THEN 13
                ELSE 0
            END
        ), 0) AS totalWage
    FROM clientUserInfo u
    LEFT JOIN rota r ON u.userID = r.userID
    LEFT JOIN roleType rt ON r.roleTypeID = rt.roleTypeID
    WHERE u.userID = ?
    GROUP BY u.userID
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($forname, $surname, $fetchedTotalWage);

    // Fetch data and populate variables
    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        $totalWage = $fetchedTotalWage;
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
