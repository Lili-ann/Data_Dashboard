<?php

$host = 'localhost';
$user ="root";
$password = "";
$dbname = "club_db";

// FIXED: Changed $database to $dbname
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>