<?php
include("../config.php");

// Handle voter deletion
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']); // Sanitize the ID

    // Prepare and execute the DELETE query
    $delete_query = "DELETE FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
                alert('User deleted successfully!');
                window.location.href = 'voters.php';
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
        <link rel="stylesheet" href="../css/vtrs_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .delete{
            background-color: rgb(68, 68, 68); 
            border: none;
            color: white;
            text-align: center;
            text-decoration: none;
            padding: 10px 20px;
            margin-right: 10px;
            font-size: 14px;
            border-radius: 10px;
            margin-top: -5px;
            margin-bottom: -5px;   
        }
    </style>
    </head>

    <body>
        <div class="container">
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
            <div class="body-container">    
                <div class="topnav">
                    <div class="body-header">
                        <p>User List</p>
                    </div>
                </div>
                <br>
                <div class="users-table">
                    <table>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                        <!-- Fetch Voters from the Database -->
                        <?php
                        $result = mysqli_query($con, "SELECT * FROM user");
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <tr>
                                <td>" . htmlspecialchars($row['firstname']) . "</td>
                                <td>" . htmlspecialchars($row['lastname']) . "</td>
                                <td>" . htmlspecialchars($row['email']) . "</td>
                                <td>" . htmlspecialchars($row['role']) . "</td>
                                <td>
                                    <a class='delete' href='voters.php?delete_id=" . $row['user_id'] . "' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </table>
                </div>  
            </div>
        </div>
    </body>
</html>
