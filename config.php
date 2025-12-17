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

// --- Helper Function to Log Activity ---
function logActivity($conn, $user_id, $user_name, $action, $details) {
    // This prepares the SQL to insert a new row into your activity_log table
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, user_name, action, details) VALUES (?, ?, ?, ?)");
    
    // "isss" means: Integer, String, String, String
    $stmt->bind_param("isss", $user_id, $user_name, $action, $details);
    
    $stmt->execute();
    $stmt->close();
}


?>