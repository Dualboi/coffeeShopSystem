<?php
include "db_connection.php";

// Get the single instance of the database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];
$forname = $_POST['forname'];
$surname = $_POST['surname'];
$streetAddress = $_POST['streetAddress'];
$postCode = $_POST['postCode'];
$city = $_POST['city'];
$roleTypeID = $_POST['roleType'];  // Get roleTypeID from the form

// Check if the email belongs to an admin
$isAdmin = false;
if (preg_match('/@coffee\.co(\.[a-z]+)?$/i', $email)) {
    $isAdmin = true;
}

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert the address into the `address` table
$addressQuery = "INSERT INTO address (streetAddress, postCode, city) VALUES (?, ?, ?)";
$addressStmt = $conn->prepare($addressQuery);
$addressStmt->bind_param("sss", $streetAddress, $postCode, $city);
$addressStmt->execute();

// Get the ID of the newly created address
$addressID = $conn->insert_id;

// Insert the user into the `clientUserInfo` table, including the isAdmin and roleTypeID
$isAdminValue = $isAdmin ? 1 : 0; // Convert boolean to integer
$userQuery = "INSERT INTO clientUserInfo (addressID, password, email, forname, surname, isAdmin, roleTypeID) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("issssii", $addressID, $hashedPassword, $email, $forname, $surname, $isAdminValue, $roleTypeID);
$userStmt->execute();

// Redirect based on account type
if ($isAdmin) {
    header("Location: ../index.php");
} else {
    header("Location: ../index.php");
}

// Close the statements and the database connection
$addressStmt->close();
$userStmt->close();
$conn->close();
?>
