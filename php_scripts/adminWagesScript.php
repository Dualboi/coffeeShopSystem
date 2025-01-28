<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "php_scripts/db_connection.php";
include "php_scripts\wagesScript.php";

$totalPay = 0; // Total pay for all staff
$inventoryItems = []; // Array to store individual staff data
$errorMessage = "";

// Check if the user is an admin
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Query to fetch all staff, their total hours worked, and calculate their wages
    $query = "
    SELECT 
        CONCAT(u.forname, ' ', u.surname) AS staffName, 
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
    GROUP BY u.userID
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    // Populate $inventoryItems with staff details and calculate total pay
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $inventoryItems[] = [
                'staff' => $row['staffName'],
                'pay' => number_format($row['totalWage'], 2)
            ];
            $totalPay += $row['totalWage'];
        }
    } else {
        $errorMessage = "No staff data found.";
    }

    $stmt->close();
} else {
    $errorMessage = "You are not authorized to view this data.";
}

?>
