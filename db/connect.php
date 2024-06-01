<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pranburi";

$dsn = "mysql:host=$servername;dbname=$dbname";

try {
    $conn = new PDO($dsn, $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";
    session_start();

    require_once("default_admin.php");

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
