<?php
include("../config.php");

// Handle candidate addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $position = mysqli_real_escape_string($con, $_POST['position']);

    // If the "Other" option is selected, get the custom position
    if ($position === 'Other' && isset($_POST['custom_position']) && !empty($_POST['custom_position'])) {
        $position = mysqli_real_escape_string($con, $_POST['custom_position']);
    }

    // Check if the candidate already exists
    $verify_query = mysqli_query($con, "SELECT * FROM candidate WHERE firstname = '$firstname' AND lastname = '$lastname' AND position = '$position'");
    if (mysqli_num_rows($verify_query) > 0) {
        echo "<script>
                alert('This candidate is already registered for this position!');
                window.location.href = 'candidates.php';
              </script>";
    } else {
        // Insert into the database
        $insert_query = "INSERT INTO candidate (firstname, lastname, position) VALUES ('$firstname', '$lastname', '$position')";
        if (mysqli_query($con, $insert_query)) {
            echo "<script>
                    alert('Candidate successfully added!');
                    window.location.href = 'candidates.php';
                  </script>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}

// Handle candidate update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $firstname = mysqli_real_escape_string($con, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($con, $_POST['lastname']);
    $position = mysqli_real_escape_string($con, $_POST['position']);

    // If "Other" is selected, use the custom position entered by the admin
    if ($position === 'Other' && isset($_POST['custom_position']) && !empty($_POST['custom_position'])) {
        $position = mysqli_real_escape_string($con, $_POST['custom_position']);
    }

    $update_query = "UPDATE candidate SET firstname='$firstname', lastname='$lastname', position='$position' WHERE candidate_id=$id";
    if (mysqli_query($con, $update_query)) {
        echo "<script>
                alert('Candidate updated successfully!');
                window.location.href = 'candidates.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Handle candidate deletion
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($con, $_GET['delete_id']);

    $delete_query = "DELETE FROM candidate WHERE candidate_id=$id";
    if (mysqli_query($con, $delete_query)) {
        echo "<script>
                alert('Candidate deleted successfully!');
                window.location.href = 'candidates.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/canddts_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        // Open the popup for adding a new candidate
        function openPopupForm() {
            document.getElementById('popup-form-container').style.display = 'flex';
        }

        // Close the popup for adding a new candidate
        function closePopupForm() {
            document.getElementById('popup-form-container').style.display = 'none';
        }

        // Open the update candidate popup with existing data
        function openUpdatePopup(id, firstname, lastname, position) {
            document.getElementById('update-popup-form-container').style.display = 'flex';
            document.getElementById('update-id').value = id;
            document.getElementById('update-firstname').value = firstname;
            document.getElementById('update-lastname').value = lastname;
            document.getElementById('update-position').value = position;

            // Toggle visibility for custom position input if "Other" is selected
            toggleCustomPositionInput('update-position', 'custom-position-container');
        }

        // Close the update candidate popup
        function closeUpdatePopup() {
            document.getElementById('update-popup-form-container').style.display = 'none';
        }

        // Toggle visibility of the custom position input based on "Other" selection
        function toggleCustomPositionInput(selectId, containerId) {
            var positionSelect = document.getElementById(selectId);
            var customPositionContainer = document.getElementById(containerId);
            
            if (positionSelect.value === 'Other') {
                customPositionContainer.style.display = 'block';  // Show the input field
            } else {
                customPositionContainer.style.display = 'none';  // Hide the input field
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar-container">
            <div class="logo-con">
                <img class="logo" src="../vote_logo.png" alt=""></img>
            </div>
            <div class="sidebar-menu">
                <ul class="sidebar-menu-list">
                    <li><a href="result.php"><i class="fa fa-pie-chart" aria-hidden="true"></i>Result</a></li>
                    <li><a href="voters.php"><i class="fa fa-users" aria-hidden="true"></i>Voters</a></li>
                    <li><a href="candidates.php"><i class="fa fa-user" aria-hidden="true"></i>Candidates</a></li>
                    <li><a href="../logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Log out</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Body -->
        <div class="body-container">
            <div class="topnav">
                <div class="body-header">
                    <p>Candidates List</p>
                </div>
            </div>
            <br>
            <div class="add-candidate">
                <a href="#" onclick="openPopupForm()"><i class="fa fa-plus" aria-hidden="true"></i>Add Candidate</a>
            </div>

            <!-- Table to Display Candidates -->
            <div class="users-table">
                <table>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                    <!-- Fetch Candidates from the Database -->
                    <?php
                    $result = mysqli_query($con, "SELECT * FROM candidate");
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "
                        <tr>
                            <td>{$row['firstname']}</td>
                            <td>{$row['lastname']}</td>
                            <td>{$row['position']}</td>
                            <td>
                                <button onclick=\"openUpdatePopup({$row['candidate_id']}, '{$row['firstname']}', '{$row['lastname']}', '{$row['position']}')\">Update</button>
                                <a href='candidates.php?delete_id={$row['candidate_id']}' onclick=\"return confirm('Are you sure you want to delete this candidate?');\">Delete</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Candidate Popup Form -->
    <div id="popup-form-container" class="popup-form-container" style="display:none;">
        <div class="popup-form">
            <form action="candidates.php" method="POST">
                <h2>Add Candidate</h2>
                <input type="text" name="firstname" placeholder="First Name" required>
                <input type="text" name="lastname" placeholder="Last Name" required>
                <select name="position" id="position" required onchange="toggleCustomPositionInput('position', 'custom-position-container')">
                    <option value="">Select Position</option>
                    <option value="President">President</option>
                    <option value="Vice President">Vice President</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Treasurer">Treasurer</option>
                    <option value="Other">Other</option>
                </select>
                <!-- Custom Position Input (Initially Hidden) -->
                <div id="custom-position-container" style="display: none;">
                    <input type="text" name="custom_position" placeholder="Enter custom position">
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel" onclick="closePopupForm()">Cancel</button>
                    <button type="submit" name="submit" class="save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Candidate Popup Form -->
    <div id="update-popup-form-container" class="popup-form-container" style="display:none;">
        <div class="popup-form">
            <form action="candidates.php" method="POST">
                <h2>Update Candidate</h2>
                <input type="hidden" name="id" id="update-id">
                <input type="text" name="firstname" id="update-firstname" placeholder="First Name" required>
                <input type="text" name="lastname" id="update-lastname" placeholder="Last Name" required>
                <select name="position" id="update-position" required onchange="toggleCustomPositionInput('update-position', 'custom-position-container')">
                    <option value="President">President</option>
                    <option value="Vice President">Vice President</option>
                    <option value="Secretary">Secretary</option>
                    <option value="Treasurer">Treasurer</option>
                    <option value="Other">Other</option>
                </select>
                <!-- Custom Position Input (Initially Hidden) -->
                <div id="custom-position-container" style="display: none;">
                    <input type="text" name="custom_position" placeholder="Enter custom position">
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel" onclick="closeUpdatePopup()">Cancel</button>
                    <button type="submit" name="update" class="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
