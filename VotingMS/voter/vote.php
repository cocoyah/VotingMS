<?php
session_start();
include("../config.php");

// Check if the user is logged in
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit;
}

$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
$user_id = $_SESSION['id']; // Assuming 'id' is the user_id

// Query to check if the user has already voted
$vote_check_query = "SELECT COUNT(*) AS vote_count FROM Vote WHERE user_id = '$user_id'";
$vote_check_result = mysqli_query($con, $vote_check_query);

if (!$vote_check_result) {
    echo "Error: " . mysqli_error($con);
    exit;
}

$row = mysqli_fetch_assoc($vote_check_result);
$has_voted = $row['vote_count'] > 0;

// Query to get positions and their respective candidates
$query = "
    SELECT position, firstname, lastname, candidate_id 
    FROM candidate 
    ORDER BY position, lastname, firstname
";

$result = mysqli_query($con, $query);

if (!$result) {
    echo "Error: " . mysqli_error($con);
    exit;
}

// Process the vote when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$has_voted) {
    // Collect the votes from the form
    $votes = [];
    foreach ($_POST as $position => $candidate_id) {
        if (!empty($candidate_id)) {
            $votes[$position] = $candidate_id;
        }
    }

    if (!empty($votes)) {
        // Insert the votes into the Vote table
        foreach ($votes as $position => $candidate_id) {
            $insert_query = "
                INSERT INTO Vote (user_id, candidate_id)
                VALUES ('$user_id', '$candidate_id')
            ";

            if (!mysqli_query($con, $insert_query)) {
                echo "Error: " . mysqli_error($con);
                exit;
            }
        }

        // Mark the user as having voted
        $_SESSION['has_voted'] = true;
        $has_voted = true;

        // Display a success message
        echo "
            <script>
                alert('Thank you for voting.');
                window.location.href = 'vote.php';
            </script>
        ";
        exit;
    } else {
        // Handle case where no votes were selected
        echo "
            <script>
                alert('Please select a candidate for each position.');
                window.location.href = 'vote.php';
            </script>
        ";
        exit;
    }
}
?>

<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/vote_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        .position-section {
            width: 50%;
            margin: 0 auto;
            margin-bottom: 20px;
            text-align: left;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            background-color: rgb(255, 255, 255);
        }

        .candidate {
            font-family: sans-serif;
            text-align: left;
            padding-left: 20px;
            font-size: 15px;
            padding-bottom: 20px;
        }

        .candidate label {
            padding-left: 10px;
        }

        .submit-vote button {
            background-color: rgb(217, 39, 34);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-family: sans-serif;
        }

        .submit-vote button:disabled {
            background-color: gray;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar-container">
            <div class="logo-con">
                <img class="logo" src="../vote_logo.png" alt="">
            </div>
            <div class="sidebar-menu">
                <ul class="sidebar-menu-list">
                    <li>
                        <a href="vote.php"><i class="fa fa-check-circle" aria-hidden="true"></i> Vote</a>
                    </li>
                    <li>
                        <a href="../logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i> Log out</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="body-container">
            <div class="voting-instruction">
                <p>
                    <?php if ($has_voted): ?>
                        You have already voted. Thank you for participating!
                    <?php else: ?>
                        Please select one candidate for each position listed below and click 'Submit Vote' to cast your vote.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Loop through each position and display its candidates -->
            <form action="" method="POST">
                <?php
                if (!$has_voted) {
                    $current_position = null;
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Check if we are starting a new position
                        if ($current_position !== $row['position']) {
                            if ($current_position !== null) {
                                echo "</div>"; // Close the previous position section
                            }
                            $current_position = $row['position'];
                            echo "<div class='position-section'>";
                            echo "<h3 style='margin-bottom: 20px; padding: 10px 15px; font-family: sans-serif; margin-bottom: 10px; color:rgb(255, 255, 255); background-color: rgb(72, 72, 72);'>" . htmlspecialchars($current_position) . "</h3>"; // Display the position name
                        }

                        // Display the candidate
                        echo "<div class='candidate'>";
                        echo "<input type='radio' id='candidate-" . $row['candidate_id'] . "' name='" . $row['position'] . "' value='" . $row['candidate_id'] . "'>";
                        echo "<label for='candidate-" . $row['candidate_id'] . "'>" . htmlspecialchars($row['firstname']) . " " . htmlspecialchars($row['lastname']) . "</label>";
                        echo "</div>";
                    }

                    echo "</div>"; // Close the last position section
                }
                ?>

                <div class="submit-vote">
                    <button type="submit" <?php echo $has_voted ? 'disabled' : ''; ?>>
                        <?php echo $has_voted ? 'Vote Submitted' : 'Submit Vote'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
