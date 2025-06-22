<?php
// db_config.php

$host = "127.0.0.1"; // or your host
$db_user = "root"; // your database username
$db_pass = ""; // your database password
$db_name = "ppdb_smk";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // For testing connection
?>