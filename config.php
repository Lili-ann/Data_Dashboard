<?php
//configuring the database connection

//inserting the database connection details
$host = 'localhost';        
$user ="root";              
$password = "";             
$dbname = "club_db";        // Database name

//Create a connection to the MySQL database - club_db
$conn = new mysqli($host, $user, $password, $dbname);

//verifies if the connection was successful
if ($conn->connect_error) {
//display error message and stops execution, if connection fails
    die("Connection failed: " . $conn->connect_error);
}

//function to log user activities
//this function records user actions into the database activity_log table acting as history.
function logActivity($conn, $user_id, $user_name, $action, $details) {
//tells the SQL to insert a new row into the activity_log table
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, user_name, action, details) VALUES (?, ?, ?, ?)");
    
//"isss" means: Integer, String, String, String, which defines the datatypes of the placeholders
    $stmt->bind_param("isss", $user_id, $user_name, $action, $details);
    
    $stmt->execute();   //sends the data to the database to be saved
    $stmt->close();     // close the task and free memory
}
?>



