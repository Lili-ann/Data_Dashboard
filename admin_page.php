<?php
session_start();
// 1. Security Check: Only Admins allowed
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        <!-- Header makes use of a standard style that all headers in all pages use from style.css -->
        <div class="header" style="justify-content: center;">
            <div class="header-title">ADMIN HUB</div>
        </div>

        <div class="content" style="display: flex; flex-direction: column; justify-content: center; padding-top: 50px;">
    
        <!-- href leads to the necessary pages that the buttons are supposed to route the Admins towards -->
    <a href="user_page.php" class="card" style="text-decoration: none; cursor: pointer;">
        <div class="card-left">
            <span class="student-name">Manage Members</span>
        </div>
    </a>

    <a href="meeting_list.php" class="card" style="text-decoration: none; cursor: pointer;">
        <div class="card-left">
            <span class="student-name">Manage Schedule/Attendance</span>
        </div>
    </a>

    <a href="admin_logs.php" class="card" style="text-decoration: none; cursor: pointer;">
        <div class="card-left">
            <span class="student-name">View Logs</span>
        </div>
    </a>

    <a href="export_excel.php" class="card" style="text-decoration: none; cursor: pointer;">
        <div class="card-left">
            <span class="student-name">Download Whole Database</span>
        </div>
    </a>

</div>

        <div class="bottom-nav">
             <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
             </a>
        </div>

    </div>

</body>
</html>