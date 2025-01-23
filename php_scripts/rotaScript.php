<?php
include "php_scripts/db_connection.php"; // Include your database connection
// Initialize variables
$shiftDetails = [];
$successMessage = "";
$errorMessage = "";
$employees = []; // Fetch employees from the database
$roleTypes = []; // Fetch role types from the database

// Connect to the database
$db = Database::getInstance();
$conn = $db->getConnection();

// SQL Query to fetch employees
$query = "SELECT userID, forname, surname FROM clientUserInfo";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
} else {
    $errorMessage = "No employees found.";
}

// SQL Query to fetch role types for the select field
$query = "SELECT roleTypeID, roleType FROM roleType";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $roleTypes[] = $row;
    }
} else {
    $errorMessage = "No role types found.";
}

$stmt->close();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add New Shift
    if (isset($_POST['shift_date'], $_POST['start_time'], $_POST['end_time'], $_POST['employee_assigned'], $_POST['role_type'])) {
        $shiftDate = $_POST['shift_date'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $employeeID = $_POST['employee_assigned'];
        $roleTypeID = $_POST['role_type']; // Get the role type ID

        // Combine date and time for the DATETIME format
        $shiftStartTime = $shiftDate . ' ' . $startTime;
        $shiftEndTime = $shiftDate . ' ' . $endTime;

        // Check if the user has a wagesID assigned
        $query = "SELECT wagesID FROM clientUserInfo WHERE userID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $employeeID);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($wagesID);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            // If no wagesID is assigned, assign a default one
            if (!$wagesID) {
                $defaultWage = 10.00; // Default wage value
                $defaultWagesID = null;

                // Check if the default wage exists in the wages table
                $query = "SELECT wagesID FROM wages WHERE wage = ?";
                $stmtDefault = $conn->prepare($query);
                $stmtDefault->bind_param('d', $defaultWage);
                $stmtDefault->execute();
                $stmtDefault->store_result();
                $stmtDefault->bind_result($defaultWagesID);
                $stmtDefault->fetch();

                if ($stmtDefault->num_rows == 0) {
                    // Insert a default wage into the wages table if it doesn't exist
                    $query = "INSERT INTO wages (wage) VALUES (?)";
                    $stmtInsertWage = $conn->prepare($query);
                    $stmtInsertWage->bind_param('d', $defaultWage);
                    $stmtInsertWage->execute();
                    $defaultWagesID = $stmtInsertWage->insert_id; // Get the ID of the inserted wage
                    $stmtInsertWage->close();
                }

                $stmtDefault->close();

                // Assign the default wagesID to the user
                $query = "UPDATE clientUserInfo SET wagesID = ? WHERE userID = ?";
                $stmtUpdate = $conn->prepare($query);
                $stmtUpdate->bind_param('ii', $defaultWagesID, $employeeID);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }
        }

        $stmt->close();

        // Insert the new shift into the database
        $query = "
            INSERT INTO rota (userID, roleTypeID, shiftDate, shiftStartTime, shiftEndTime)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iisss', $employeeID, $roleTypeID, $shiftDate, $shiftStartTime, $shiftEndTime);

        if ($stmt->execute()) {
            $successMessage = "New shift added successfully.";
        } else {
            $errorMessage = "Failed to add new shift: " . $conn->error;
        }
        $stmt->close();
    }

    // Update Shift
    if (isset($_POST['shift_date_update'], $_POST['start_time_update'], $_POST['end_time_update'], $_POST['shift_id_update'])) {
        $shiftDate = $_POST['shift_date_update'];
        $startTime = $_POST['start_time_update'];
        $endTime = $_POST['end_time_update'];
        $rotaID = $_POST['shift_id_update'];

        // Combine date and time for the DATETIME format
        $shiftStartTime = $shiftDate . ' ' . $startTime;
        $shiftEndTime = $shiftDate . ' ' . $endTime;

        // Update shift in the database
        $query = "
            UPDATE rota
            SET shiftDate = ?, shiftStartTime = ?, shiftEndTime = ?
            WHERE rotaID = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssi', $shiftDate, $shiftStartTime, $shiftEndTime, $rotaID);

        if ($stmt->execute()) {
            $successMessage = "Shift updated successfully.";
        } else {
            $errorMessage = "Failed to update shift: " . $conn->error;
        }
        $stmt->close();
    }

    // Delete Shift
    if (isset($_POST['shift_id_delete'])) {
        $rotaID = $_POST['shift_id_delete'];

        // Delete the shift from the database
        $query = "DELETE FROM rota WHERE rotaID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $rotaID);

        if ($stmt->execute()) {
            $successMessage = "Shift deleted successfully.";
        } else {
            $errorMessage = "Failed to delete shift: " . $conn->error;
        }
        $stmt->close();
    }
}

// SQL Query to fetch shift details
$query = "
    SELECT 
        r.rotaID, 
        u.forname, 
        u.surname, 
        rt.roleType, 
        r.shiftDate, 
        r.shiftStartTime, 
        r.shiftEndTime, 
        r.hoursWorked
    FROM rota r
    JOIN clientUserInfo u ON r.userID = u.userID
    JOIN roleType rt ON r.roleTypeID = rt.roleTypeID
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shiftDetails[] = $row;
    }
} else {
    $errorMessage = "No shift details found.";
}

$conn->close();
?>
