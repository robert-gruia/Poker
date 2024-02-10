<!-- Login Page -->
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/indexStyle.css">
    <link rel="icon" href="../Images/Icon.ico">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <title>Login</title>
</head>


<body>

    <div class="login-box">
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="dataForm">
            <div class="main">
                <p class="sign" align="center">Sign in</p>
                <form class="form1">
                    <input class="un " type="text" name="username" id="username" align="center"
                        placeholder="Username/Email">
                    <input class="pass" type="password" name="password" id="password" align="center"
                        placeholder="Password">
                    <a class="submit" align="center">Sign in</a>



                    <p>
                        <?php
                        $ROOT = getcwd();
                        // Verifica se il form è stato inviato
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Recupera i dati dal form
                            $email_username = $_POST["username"];
                            $password = $_POST["password"];
                            $message = "";
                            $passMd5 = md5($password);

                            // Connessione al database
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "poker";
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            if ($conn->connect_error) {
                                die("Connessione fallita: " . $conn->connect_error);
                            }

                            $sql = "SELECT username FROM utenti WHERE email = '$email_username' OR username = '$email_username' AND password = '$passMd5'";
                            $result = $conn->query($sql);
                            $row = $result->fetch_assoc();
                            if ($result->num_rows > 0) {
                                //$message = "Dati inseriti con successo nel database!";
                                $userID = $conn->query("SELECT ID FROM utenti WHERE username = '$email_username' OR email = '$email_username'")->fetch_assoc()["ID"];
                                $_SESSION["username"] = $row["username"];
                                header("Location: poker.php");
                            } else {
                                echo "Login Error";
                            }
                            $conn->close();
                        }
                        ?>
                    </p>
                    <p class="register" align="center"><a href="register.php">Register</p>
            </div>
        </form>
    </div>

</body>

</html>