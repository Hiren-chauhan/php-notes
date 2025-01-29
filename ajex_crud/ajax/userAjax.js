$(document).ready(function () {
    // Show alert message (Success/Warning/Danger/Info)
    function showAlert(message, alertType = 'success') {
        const alertBox = $('#alertBox');
        const alertMessage = $('#alertMessage');
        alertMessage.text(message);
        // Remove existing alert classes and add new alert type
        alertBox.removeClass('d-none alert-success alert-danger alert-warning alert-info')
                .addClass(`alert-${alertType}`)
                .addClass('show');
        // Hide the alert after 4 seconds
        setTimeout(() => {
            alertBox.removeClass('show').addClass('d-none');
        }, 4000);
    }
    // Reset form fields and validation state
    function resetForm() {
        $('#userdata')[0].reset();
        $('#userdata').removeClass('was-validated');
        $('#id').val('');  // Reset the hidden ID field
    }
    // Refresh the user table data by making an AJAX request
    function refreshUserTable() {
        $.ajax({
            url: 'ajax/fetch_users.php',  // Endpoint to fetch user data
            method: 'GET',
            success: function (data) {
                // Update table body with new data
                $('table tbody').html(data);
                initializeEventListeners();  // Reinitialize event listeners for dynamic content
            },
            error: function () {
                showAlert("Failed to refresh the user table. Please try again.", 'danger');
            }
        });
    }
    // Initialize event listeners for dynamically created buttons (edit and delete)
    function initializeEventListeners() {
        // Edit Button Click - Populate form with user data for editing
        $('.edit-button').off('click').on('click', function () {
            const userData = $(this).data();
            $('#id').val(userData.id);
            $('#first_name').val(userData.first_name);
            $('#last_name').val(userData.last_name);
            $('#birth_date').val(userData.birth_date);
            $('#gender').val(userData.gender);
            $('#email').val(userData.email);
            $('#phone_number').val(userData.phone_number);
            $('#userModal').modal('show');  // Show the modal for editing
        });
        // Delete Button Click - Delete user via AJAX
        $('.delete-button').off('click').on('click', function () {
            const userId = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "ajax/controller.php",  // Endpoint to handle the delete action
                data: { action: 'delete', id: userId },
                success: function (response) {
                    const res = JSON.parse(response);
                    if (res.status === 200) {
                        showAlert("User deleted successfully!", 'danger');
                        refreshUserTable();  // Refresh the table after deletion
                    } else {
                        showAlert("Error: " + res.message, 'danger');
                    }
                },
                error: function () {
                    showAlert("An error occurred while deleting the user.", 'danger');
                }
            });
        });
    }
    // Handle form submission for creating or updating a user
    $('#userdata').on('submit', function (e) {
        e.preventDefault();  // Prevent the default form submission
        if (!this.checkValidity()) {
            e.stopPropagation();  // Stop propagation if form is invalid
            $(this).addClass('was-validated');  // Show validation feedback
            return;
        }
        const formData = new FormData(this);
        formData.append('action', 'save');  // Add action type (save)
        $.ajax({
            type: 'POST',
            url: 'ajax/controller.php',  // Endpoint to handle the save action
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === 200) {
                    showAlert("User saved successfully!", 'success');
                    $('#userModal').modal('hide');  // Close the modal after saving
                    resetForm();  // Reset the form fields
                    refreshUserTable();  // Refresh the user table
                } else {
                    showAlert("Error: " + res.message, 'danger');
                }
            },
            error: function () {
                showAlert("An error occurred while saving the user.", 'danger');
            }
        });
    });
    // Initialize the DataTable for enhanced table features (search, pagination, etc.)
    $('#example').DataTable();
    // Initialize event listeners and refresh the table on page load
    initializeEventListeners();
    refreshUserTable();
});
