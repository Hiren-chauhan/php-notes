<?php
// Database connection settings
$servername = "localhost"; // Database server name
$username = "root";         // Database username
$password = "";             // Database password
$dbname = "crud_demo";      // Database name
// Create a new mysqli instance for database connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check for connection errors
if ($conn->connect_error) {
    // Log the error message (optional: you can log this to a file instead of displaying it)
    error_log("Connection failed: " . $conn->connect_error);
    // Terminate the script and display a user-friendly message
    die("Connection to the database failed. Please try again later.");
}
// Optionally, set the character set for the connection
$conn->set_charset("utf8mb4"); // Use utf8mb4 for better Unicode support
// You can also define a function to close the connection when done
// function closeConnection($conn) {
//     if ($conn) {
//         $conn->close(); // Close the database connection
//     }
// }
// The connection is now established and can be used for queries
?>