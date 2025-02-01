<?php
include "db_connection.php";

// Start session for storing errors
session_start();

// Get the database connection
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
$roleTypeID = $_POST['roleType']; 

// Check if the email belongs to an admin
$isAdmin = preg_match('/@coffee\.co(\.[a-z]+)?$/i', $email) ? 1 : 0;

// Initialize an array to store all errors
$errors = [];

// Password validation
$passwordRegex = '/^(?=.*[A-Z]).{9,}$/'; // At least 9 characters, one uppercase letter
if (!preg_match($passwordRegex, $password)) {
    $errors[] = "Password must be at least 9 characters long and contain at least one uppercase letter.";
}

// Email validation (matches trigger logic)
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || 
    !preg_match('/^[a-zA-Z0-9._%+-]+@([a-zA-Z0-9.-]+\.(com|net|org|edu|gov|mil|info|io))$/', $email)) {
    $errors[] = "Invalid email format: must be a standard email format with valid TLDs.";
}
if ($isAdmin && !str_ends_with($email, "@coffee.co")) {
    $errors[] = "Invalid email for admin: must end with @coffee.co.";
}

// Name validation (matches trigger logic)
if (!preg_match('/^[A-Za-z]+([-\'][A-Za-z]+)*$/', $forname)) {
    $errors[] = "First Name can only contain letters, apostrophes, and hyphens.";
}
if (!preg_match('/^[A-Za-z]+([-\'][A-Za-z]+)*$/', $surname)) {
    $errors[] = "Last Name can only contain letters, apostrophes, and hyphens.";
}

// If any errors exist, store them in session and stop execution
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: ../register.php");
    exit();
}

// Start a transaction (only if no validation errors)
$conn->begin_transaction();

try {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert address into the address table
    $addressQuery = "INSERT INTO address (streetAddress, postCode, city) VALUES (?, ?, ?)";
    $addressStmt = $conn->prepare($addressQuery);
    $addressStmt->bind_param("sss", $streetAddress, $postCode, $city);
    $addressStmt->execute();
    $addressID = $conn->insert_id;

    // Insert user into clientUserInfo (Triggers validate email & names)
    $userQuery = "INSERT INTO clientUserInfo (addressID, password, email, forname, surname, isAdmin, roleTypeID) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $userStmt = $conn->prepare($userQuery);
    $userStmt->bind_param("issssii", $addressID, $hashedPassword, $email, $forname, $surname, $isAdmin, $roleTypeID);
    $userStmt->execute();

    // If everything is successful, commit transaction
    $conn->commit();
    $_SESSION['successMessage'] = "Registration successful!";
    header("Location: ../index.php");
    exit();

} catch (mysqli_sql_exception $e) {
    // Rollback transaction if an error occurs
    $conn->rollback();

    // Collect database-trigger validation errors
    $dbErrors = explode('. ', $e->getMessage());
    $errors = array_merge($errors, $dbErrors); // Merge with existing validation errors

    // Store all errors in session and redirect
    $_SESSION['errors'] = $errors;
    header("Location: ../register.php");
    exit();
}

// Close database connections
if (isset($addressStmt)) $addressStmt->close();
if (isset($userStmt)) $userStmt->close();
$conn->close();
?>
