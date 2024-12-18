<?php
include("../config.php");

// Query to count votes for each candidate
$query = "
    SELECT 
        c.firstname AS candidate_firstname, 
        c.lastname AS candidate_lastname, 
        c.position AS candidate_position, 
        COUNT(v.candidate_id) AS vote_count
    FROM 
        candidate c
    LEFT JOIN 
        vote v ON c.candidate_id = v.candidate_id
    GROUP BY 
        c.candidate_id
    ORDER BY 
        c.position, c.lastname, c.firstname
";

$result = mysqli_query($con, $query);

if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/result_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
    <body>
        <div class="container">

            <div class="sidebar-container">
                <div class="logo-con">
                    <img class="logo" src="../vote_logo.png" alt="">
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
                        <p>Votes Tally</p>
                    </div>
                </div>
                <br>
                <div class="users-table">
                    <table>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Position</th>
                            <th>Vote Count</th>
                        </tr>
                        <?php
                        // Loop through query results and display each candidate's vote count
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                                <tr>
                                    <td>" . htmlspecialchars($row['candidate_firstname']) . " " . htmlspecialchars($row['candidate_lastname']) . "</td>
                                    <td>" . htmlspecialchars($row['candidate_position']) . "</td>
                                    <td>" . htmlspecialchars($row['vote_count']) . "</td>
                                </tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
