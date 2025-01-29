<?php
include 'connection.php'; // Include the database connection file
// Define constants for HTTP status codes
define('STATUS_SUCCESS', 200);
define('STATUS_ERROR', 500);
define('STATUS_UNPROCESSABLE', 422);
/**
 * Sends a JSON response with a given status and message.
 *
 * @param int $status The HTTP status code.
 * @param string $message The message to send in the response.
 */
function respond($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit; // Terminate the script after sending the response
}
/**
 * Validates the input data for required fields.
 *
 * @param array $data The input data from the POST request.
 * @return bool True if all required fields are present, false otherwise.
 */
function validateInput($data) {
    return !empty($data['first_name']) && !empty($data['last_name']) && 
           !empty($data['birth_date']) && !empty($data['gender']) && 
           !empty($data['email']) && !empty($data['phone_number']);
}
/**
 * Saves a user record to the database (either insert or update).
 *
 * @param mysqli $conn The database connection.
 * @param array $data The user data to save.
 */
function saveUser ($conn, $data) {
    $id = $data['id'] ?? null; // Get the user ID if it exists
    // Prepare SQL query for either updating or inserting a user
    $query = $id 
        ? "UPDATE users SET first_name=?, last_name=?, birth_date=?, gender=?, email=?, phone_number=? WHERE id=?"
        : "INSERT INTO users (first_name, last_name, birth_date, gender, email, phone_number) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query); // Prepare the SQL statement
    // Bind parameters based on whether we are updating or inserting
    if ($id) {
        $stmt->bind_param('ssssssi', $data['first_name'], $data['last_name'], $data['birth_date'], $data['gender'], $data['email'], $data['phone_number'], $id);
    } else {
        $stmt->bind_param('ssssss', $data['first_name'], $data['last_name'], $data['birth_date'], $data['gender'], $data['email'], $data['phone_number']);
    }
    // Execute the statement and respond based on the outcome
    if ($stmt->execute()) {
        respond(STATUS_SUCCESS, 'User  saved successfully.');
    } else {
        respond(STATUS_ERROR, 'Database error: ' . $conn->error);
    }
}
/**
 * Deletes a user record from the database.
 *
 * @param mysqli $conn The database connection.
 * @param int $id The ID of the user to delete.
 */
function deleteUser ($conn, $id) {
    $query = "DELETE FROM users WHERE id = ?"; // Prepare the delete query
    $stmt = $conn->prepare($query); // Prepare the SQL statement
    $stmt->bind_param('i', $id); // Bind the user ID parameter
    // Execute the statement and respond based on the outcome
    if ($stmt->execute()) {
        respond(STATUS_SUCCESS, 'User  deleted successfully.');
    } else {
        respond(STATUS_ERROR, 'Database error: ' . $conn->error);
    }
}
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; // Get the action from the POST data
    // Handle different actions based on the value of 'action'
    switch ($action) {
        case 'save':
            // Validate input data before saving
            if (!validateInput($_POST)) {
                respond(STATUS_UNPROCESSABLE, 'All fields are required.');
            }
            saveUser ($conn, $_POST); // Call the function to save the user
            break;
        case 'delete':
            $id = $_POST['id'] ?? null; // Get the user ID for deletion
            if (!$id) {
                respond(STATUS_UNPROCESSABLE, 'User  ID is required for deletion.');
            }
            deleteUser ($conn, $id); // Call the function to delete the user
            break;
        default:
            respond(STATUS_UNPROCESSABLE, 'Invalid action.'); // Handle invalid actions
    }
}