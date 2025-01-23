<?php
session_start();
include "php_scripts/db_connection.php";

// Get the single instance of the database connection
$db = Database::getInstance();
$conn = $db->getConnection();

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare the query to find the user by email
$query = "SELECT userID, password, isAdmin FROM clientUserInfo WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($userID, $storedPassword, $isAdmin);
$stmt->fetch();

// Check if the email exists and the password matches
if ($stmt->num_rows > 0 && password_verify($password, $storedPassword)) {
    // Password is correct, set session variables
    $_SESSION['userID'] = $userID;
    $_SESSION['email'] = $email;
    $_SESSION['isAdmin'] = $isAdmin; // Ensure this is being set correctly

    // Redirect based on role
    if ($isAdmin == 1) {
        header("Location: adminPage.php");
    } else {
        header("Location: ClientPage.php");
    }
    exit;
} else {
    // Authentication failed
    echo "<script>alert('Invalid email or password'); window.location.href='index.php';</script>";
}

$stmt->close();
$conn->close();
?>
