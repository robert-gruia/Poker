<!-- Register Page -->
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/indexStyle.css">
    <link rel="icon" href="../Images/Icon.ico">
    <title>Inserimento Dati</title>
</head>


<body>
    <div class="login-box">
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>" id="dataForm">
            <div class="main">
                <p class="sign" align="center">Register</p>
                <form class="form1">
                    <input class="un " type="text" name="username" id="username" align="center" placeholder="Username">
                    <input class="un " type="email" name="email" id="email" align="center" placeholder="Email">
                    <input class="pass" type="password" name="password" id="password" align="center"
                        placeholder="Password">
                    <a class="submit" align="center" onclick="document.getElementById('dataForm').submit()">Register</a>
                    <p>
                        <?php
                        // Verifica se il form è stato inviato
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            // Recupera i dati dal form
                            $user = $_POST["username"];
                            $email = $_POST["email"];
                            $password = $_POST["password"];
                            $passMd5 = md5($password);

                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "poker";

                            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                $message = "Formato email non valido";
                            } else {
                                $conn = new mysqli($servername, $username, $password, $dbname);


                                if ($conn->connect_error) {
                                    die("Connessione fallita: " . $conn->connect_error);
                                }
                                $sql = "SELECT * FROM utenti WHERE username = '$user'";
                                $result = $conn->query($sql);
                                if (mysqli_num_rows($result) > 0) {
                                    echo "Username already exists";
                                    $conn->close();
                                } else {
                                    $sql = "INSERT INTO utenti (username, email, password, balance) VALUES ('$user', '$email', '$passMd5', 10000)";

                                    if ($conn->query($sql) === TRUE) {
                                        session_start();
                                        $_SESSION["username"] = $user;
                                        $conn->close();
                                        header("Location: poker.php");
                                    } else {
                                        $message = "Errore durante l'inserimento dei dati: " . $conn->error;
                                    }

                                }
                            }
                        }
                        ?>
                    </p>

                    <p class="register" align="center"><a href="index.php">Login</p>

                </form>
            </div>


</body>

</html>