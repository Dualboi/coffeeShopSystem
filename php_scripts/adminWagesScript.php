<?php
include "php_scripts/db_connection.php"; // Include your database connection

// Initialize variables
$inventoryItems = []; // To store wage data for all employees
$errorMessage = "";

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch wage data for all employees (admin only)
if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
    // Query to get the staff details, wages, and total wages based on their role
    $query = "
        SELECT 
            u.forname, 
            u.surname, 
            rt.roleType,
            w.wage,
            IFNULL(SUM(TIME_TO_SEC(r.hoursWorked) / 3600 * w.wage), 0) AS allWagesTotal
        FROM clientUserInfo u
        LEFT JOIN rota r ON u.userID = r.userID
        LEFT JOIN wages w ON u.wagesID = w.wagesID
        LEFT JOIN roletype rt ON r.roleTypeID = rt.roleTypeID
        GROUP BY u.userID, rt.roleType, w.wage
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data and populate $inventoryItems array
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $inventoryItems[] = [
                'staff' => $row['forname'] . ' ' . $row['surname'] . ' (' . $row['roleType'] . ')',
                'wages' => number_format($row['wage'], 2),
                'allWagesTotal' => number_format($row['allWagesTotal'], 2)
            ];
        }
    } else {
        $errorMessage = "No wage data found.";
    }

    $stmt->close();
} else {
    $errorMessage = "You are not authorized to view this data.";
}

$conn->close();
?>