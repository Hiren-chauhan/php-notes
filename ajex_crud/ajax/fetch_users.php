<?php
// Include database connection file
include 'connection.php';
// Query to fetch all users in descending order by ID
$query = "SELECT * FROM users ORDER BY id DESC;";
// Execute the query and handle potential errors
$result = $conn->query($query);
// Check if the query was successful, if not, display an error message
if (!$result) {
    die("Database query failed: " . $conn->error);
}
// Loop through the fetched rows and display them in an HTML table
while ($row = $result->fetch_assoc()) {
    // Ensure data is safely output by using htmlspecialchars to prevent XSS attacks
    echo "<tr>
            <td>" . htmlspecialchars($row['id']) . "</td>
            <td>" . htmlspecialchars($row['first_name']) . "</td>
            <td>" . htmlspecialchars($row['last_name']) . "</td>
            <td>" . htmlspecialchars($row['birth_date']) . "</td>
            <td>" . htmlspecialchars($row['gender']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['phone_number']) . "</td>
            <td>
                <!-- Edit Button -->
                <button class='btn btn-sm btn-info edit-button' 
                        data-id='" . htmlspecialchars($row['id']) . "'
                        data-first_name='" . htmlspecialchars($row['first_name']) . "'
                        data-last_name='" . htmlspecialchars($row['last_name']) . "'
                        data-birth_date='" . htmlspecialchars($row['birth_date']) . "'
                        data-gender='" . htmlspecialchars($row['gender']) . "'
                        data-email='" . htmlspecialchars($row['email']) . "'
                        data-phone_number='" . htmlspecialchars($row['phone_number']) . "'>
                    Edit
                </button>
                <!-- Delete Button -->
                <button class='btn btn-sm btn-danger delete-button' data-id='" . htmlspecialchars($row['id']) . "'>Delete</button>
            </td>
        </tr>";
}
?>
