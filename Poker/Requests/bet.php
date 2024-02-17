<?php
    $host = 'localhost';
    $db = 'poker';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);

    session_start();
    $username = $_SESSION['username'];
    $betValue = $_POST['bet'];

    $sql = "UPDATE utenti SET balance = balance - ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$betValue, $username]);

    $sql = "SELECT balance FROM utenti WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $newBalance = $stmt->fetch()['balance'];

    echo $newBalance;
?>