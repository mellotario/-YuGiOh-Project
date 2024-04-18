<?php
// Include the database connection file
include_once 'includes/connect.php';

// Function to fetch table values as options for dropdown
function fetchTableValues($db, $table)
{
    $stmt = $db->prepare("SELECT * FROM $table LIMIT 50");
    $stmt->execute();
    $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($values)) {
        echo "<p>No data found in the table.</p>";
        return;
    }

    echo "<table id='$table' class='data-table' style='display: none;'>";
    echo "<thead>";
    foreach ($values[0] as $key => $value) {
        echo "<th>$key</th>";
    }
    echo "<th>Action</th>";
    echo "<th>Delete</th>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($values as $value) {
        echo "<tr>";
        foreach ($value as $key => $val) {
            echo "<td><span>$val</span><input type='text' class='edit-input' style='display: none;' value='$val'></td>";
        }
        echo "<td><button class='edit-btn'>Edit</button></td>";
        echo "<td><button class='delete-btn'>Delete</button></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    if (count($values) > 50) {
        echo "<button class='show-more-btn'>Show More</button>";
    }
}


?>


<main>
    <h2>Admin Page</h2>
    <table border="1">
        <tr>
            <th>Table Name</th>
            <th>Action</th>
        </tr>
        <?php
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($tables as $table) : ?>
            <tr>
                <td><?php echo $table; ?></td>
                <td>
                    <div class="dropdown">
                        <button class="edit-btn">Show</button>
                        <?php fetchTableValues($db, $table); ?>
                        <div class="add-value-container" style="display: none;">

                            <input type="text" name="username" id="username_<?php echo $table; ?>" placeholder="Enter username">
                            <input type="email" name="email" id="email_<?php echo $table; ?>" placeholder="Enter email">
                            <input type="password" name="password" id="password_<?php echo $table; ?>" placeholder="Enter password">

                            <?php if ($table === 'users') : ?>
                                <button onclick="addUser('<?php echo $table; ?>')">Confirm User</button>
                            <?php endif; ?>
                        </div>
                        <?php if ($table === 'users') : ?>
                            <a href="#" class="add-value-btn" onclick="showAddUserInputs(this)">Add User</a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<style>
    /* Dropdown button */
    .dropbtn {
        background-color: #3498db;
        color: white;
        padding: 10px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    /* Dropdown content (hidden by default) */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        z-index: 1;
    }

    /* Links inside the dropdown */
    .dropdown-content a {
        color: black;
        padding: 10px;
        text-decoration: none;
        display: block;
    }

    /* Change color of dropdown links on hover */
    .dropdown-content a:hover {
        background-color: #ddd;
    }

    /* Add Value button style */
    .add-value-btn {
        color: #3498db;
        padding: 10px;
        text-decoration: none;
        display: block;
        border-top: 1px solid #ddd;
    }

    /* Add Value button style on hover */
    .add-value-btn:hover {
        background-color: #f9f9f9;
    }
</style>

<script>

    
    // Add event listener to handle edit button click and add value button click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('edit-btn')) {
            var table = event.target.parentElement.querySelector('.data-table');
            if (table) {
                // Toggle the display of the table
                table.style.display = table.style.display === 'table' ? 'none' : 'table';

                // Hide other tables
                var allTables = document.querySelectorAll('.data-table');
                allTables.forEach(function(t) {
                    if (t !== table) {
                        t.style.display = 'none';
                    }
                });

                // Change the button text to "OK" or "Cancel" and make cells editable
                var rows = table.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td');
                    cells.forEach(function(cell, index) {
                        var span = cell.querySelector('span');
                        var input = cell.querySelector('input');
                        if (span && input) {
                            if (index !== cells.length - 1) { // Skip the last column (action column)
                                if (span.style.display === 'none') {
                                    span.style.display = 'inline';
                                    input.style.display = 'none';
                                } else {
                                    span.style.display = 'none';
                                    input.style.display = 'inline';
                                    input.value = span.textContent;
                                }
                            }
                        }
                    });
                    var editBtn = row.querySelector('.edit-btn');
                    if (editBtn) {
                        if (editBtn.textContent === 'Edit') {
                            editBtn.textContent = 'OK';
                        } else {
                            editBtn.textContent = 'Edit';
                        }
                    }
                });
            }
        } else if (event.target.classList.contains('add-value-btn')) {
            let tableName = event.target.closest('.dropdown').querySelector('.dropbtn').innerHTML;
            // Perform necessary action (e.g., open modal to add a new value to the table)
            console.log('Add Value button clicked for table: ' + tableName);
        }
    });


    // Add event listener to handle show more button click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('show-more-btn')) {
            var table = event.target.previousElementSibling;
            var rows = table.querySelectorAll('tr');
            var startIndex = rows.length - 1; // Exclude the header row
            var endIndex = startIndex + 20; // Show the next 20 rows

            for (var i = startIndex; i < endIndex && i < rows.length; i++) {
                rows[i].style.display = 'table-row';
            }

            // Hide the "Show More" button if all rows are displayed
            if (endIndex >= rows.length) {
                event.target.style.display = 'none';
            }
        }
    });

    document.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON' && event.target.textContent === 'Delete') {
            var row = event.target.closest('tr');
            var id = row.querySelector('td:first-child span').textContent.trim();
            var tableName = row.closest('table').id; // Use the id attribute of the table

            // Send an AJAX request to delete the record
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Remove the row from the table on successful deletion
                    row.remove();
                    alert('Record deleted successfully');
                } else {
                    alert('Error deleting record');
                }
            };
            xhr.send(JSON.stringify({
                table: tableName,
                id: id
            }));
        }
    });


    // Function to show the inputs for adding a new user
    function showAddUserInputs(button) {
        var container = button.parentElement.querySelector('.add-value-container');
        if (container) {
            container.style.display = 'block';
        }
    }

    // Function to add a new user to the specified table
    function addUser(tableName) {
        // Get the input values
        var username = document.getElementById('username_users').value;
        var email = document.getElementById('email_users').value;
        var password = document.getElementById('password_users').value;



        if (username === '' || email === '' || password === '') {
            console.error('Empty values detected');
            return;
        }

        console.log(username, email, password);

        // Create a FormData object with the input values
        var formData = new FormData();
        formData.append('username', username);
        formData.append('email', email);
        formData.append('password', password);

        // Send an AJAX request to add the user
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'add_user.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('User added successfully');
            } else {
                alert('Error adding user');
            }
        };
        xhr.send(formData);

        // Reset the input fields
        usernameInput.value = '';
        emailInput.value = '';
        passwordInput.value = '';

        // Hide the inputs
        var container = document.querySelector('.add-value-container');
        if (container) {
            container.style.display = 'none';
        }
    }


    document.addEventListener('click', function(event) {
    if (event.target.classList.contains('edit-btn') && event.target.textContent === 'OK') {
        var row = event.target.closest('tr');
        var cells = row.querySelectorAll('td');

        // Extract the updated values from the input fields
        var updatedValues = [];
        cells.forEach(function(cell, index) {
            var span = cell.querySelector('span');
            var input = cell.querySelector('input');
            if (span && input) {
                if (index !== cells.length - 2) { // Skip the last two columns (action columns)
                    span.textContent = input.value;
                    updatedValues.push(input.value);
                }
            }
        });

        // Perform an AJAX request to update the user in the database
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_user.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('User updated successfully');
            } else {
                alert('Error updating user');
            }
        };
        xhr.send(JSON.stringify({
            id: cells[0].querySelector('span').textContent.trim(),
            updatedValues: updatedValues
        }));

        // Change the button text back to "Edit"
        event.target.textContent = 'Edit';

        // Hide the input fields
        cells.forEach(function(cell) {
            var span = cell.querySelector('span');
            var input = cell.querySelector('input');
            if (span && input) {
                span.style.display = 'inline';
                input.style.display = 'none';
            }
        });
    }
});

</script>