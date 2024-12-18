<?php
    session_start();
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/reg_style.css">
    </head>

    <body>
        <div class="container">
            
        <?php

            include("config.php");

            // Get user data
            if (isset($_POST['submit'])) {
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $role = 'voter'; // Set role as a string

                // Verify the unique email
                $verify_query = mysqli_query($con, "SELECT email FROM user WHERE email = '$email'");

                if (mysqli_num_rows($verify_query) != 0) {
                    echo "<div class='message'>
                            <p>This email is already used, try another one!</p>
                        </div>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                } else {
                    // Insert user data into the database
                    $insert_query = "INSERT INTO user (firstname, lastname, email, password, role) 
                                     VALUES ('$firstname', '$lastname', '$email', '$password', '$role')";
                    if (mysqli_query($con, $insert_query)) {
                        echo "
                        <script>
                            console.log('Registration Successful!');
                            alert('Registration Successful! Click OK to proceed to login.');
                            window.location.href = 'login.php'; 
                        </script>
                        ";
                    } else {
                        echo "<div class='message'>
                                <p>Error occurred during registration!</p>
                            </div>";
                    }
                }
            } else {
            ?>
            <form action="" method="POST">
                <div class="login-header">
                    <img class="logo" src="vote_logo.png" alt=""></img>
                    <h1>Create an account</h1>
                </div>
                <div>
                    <input type="text" name="firstname" placeholder="First Name" required>
                </div>
                <div>
                    <input type="text" name="lastname" placeholder="Last Name" required>
                </div>
                <div>
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="submit">
                    <input type="submit" name="submit" value="Register"></input>
                </div>
                <div class="register-link">
                    <p>Already have an account? <a href="login.php"><b>Login</b></a></p>
                </div>
            </form>
            <?php
            } // Close the else block
        ?>
        </div>
    </body>
</html>
