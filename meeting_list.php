<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// MOCK DATA MEETING
$meetings = [
    ['title' => 'Meeting 1', 'time' => 'Date & Time', 'room' => 'Room 5'],
    ['title' => 'Meeting 2', 'time' => 'Date & Time', 'room' => 'Room 3'],
    ['title' => 'Meeting 3', 'time' => 'Date & Time', 'room' => 'Room 1'],
    ['title' => 'Meeting 4', 'time' => 'Date & Time', 'room' => 'Room 6'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting List</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="index.php" class="header-btn">Back</a>
            
            <div class="header-title">Meeting List</div>
            
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            <?php foreach($meetings as $meet): ?>
            <div class="card">
                <div class="card-left">
                    <span class="meeting-title"><?php echo $meet['title']; ?></span>
                    <span class="meeting-time"><?php echo $meet['time']; ?></span>
                </div>
                <div class="card-right">
                    <?php echo $meet['room']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div> 

        <div class="bottom-nav">
            <a href="user_page.php" class="nav-item">
                <span class="nav-text">Member<br>List</span>
            </a>
            
            <a href="meeting_list.php" class="nav-item active">
                <span class="nav-text">Meeting<br>List</span>
            </a>
        </div>

    </div>

</body>
</html>