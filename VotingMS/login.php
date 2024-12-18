<?php
    session_start();
?>

<html>
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login_style.css">
</head>

<body>
    <div class="container">
    <?php

        include("config.php");
        
        if (isset($_POST['submit'])) {
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $password = mysqli_real_escape_string($con, $_POST['password']);

            // Verify credentials
            $result = mysqli_query($con, "SELECT * FROM user WHERE email='$email' AND password='$password'") or die("Select Error");
            $row = mysqli_fetch_assoc($result);

            if (is_array($row) && !empty($row)) {
                $_SESSION['valid'] = $row['email'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['id'] = $row['user_id'];
                $_SESSION['role'] = $row['role']; // Store the role in the session

                // Redirect based on role
                if ($row['role'] === 'voter') {
                    header("Location: voter/vote.php");
                } elseif ($row['role'] === 'admin') {
                    header("Location: admin/result.php");
                } else {
                    echo "
                        <script>
                            alert('Invalid role. Please contact the administrator.');
                            window.location.href = 'login.php';
                        </script>
                    ";
                }
            } else {
                echo "
                    <script>
                        console.log('Login Unsuccessful!');
                        alert('Incorrect Username or Password! Go back to login page.');
                        window.location.href = 'login.php';
                    </script>
                ";
            }
        } else {
        ?>
        
        <form action="" method="POST">
            <div class="login-header">
                <img class="logo" src="vote_logo.png" alt=""></img>
                <h1>Login to your account</h1>
            </div>
            <div>
                <input type="text" name="email" placeholder="Email" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="submit">
                <input type="submit" name="submit" value="Login"></input>
            </div>
            <div class="register-link">
                <p>Don't have an account yet? <a href="registration.php"><b>Register Now</b></a></p>
            </div>
        </form>
    <?php
        }
    ?>
    </div>
</body>
</html>
