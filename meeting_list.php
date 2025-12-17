<?php
session_start();
require_once 'config.php';

// 1. Security Check
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Check if user is Manager or Admin (Adjust 'Manager' if your DB uses lowercase)
$isAdmin = (isset($_SESSION['role']) && ($_SESSION['role'] == 'Manager' || $_SESSION['role'] == 'Admin'));

// 2. FETCH REAL MEETINGS FROM DB
// We order by ID DESC so the newest meetings show first
$sql = "SELECT * FROM schedule ORDER BY meeting_time DESC";
$result = $conn->query($sql);
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
        
        <div class="header" style="position: relative; display: flex; justify-content: flex-end; align-items: center;">
            
            <span style="position: absolute; left: 50%; transform: translateX(-50%); font-weight: bold; font-size: 1.2rem; color: #ffffff;">
                Meeting List
            </span>
            
            <a href="logout.php" class="header-btn logout">Logout</a>

        </div>

        <div class="content">
            
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                
                <div class="card">
                    
                    <a href="attendance_list.php?id=<?php echo $row['id']; ?>" style="text-decoration: none; flex: 1; display: flex; color: inherit;">
                        <div class="card-left">
                            <span class="meeting-title"><?php echo htmlspecialchars($row['meeting_name']); ?></span>
                            <span class="meeting-time">
                                <?php echo date('d M, h:i A', strtotime($row['meeting_time'])); ?>
                            </span>
                        </div>
                    </a>

                    <div class="card-right">
                        <span class="room-text">
                            <i class="fa-solid fa-location-dot" style="margin-right:5px; color:#800000;"></i>
                            <?php echo htmlspecialchars($row['room']); ?>
                        </span>
                    </div>

                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; margin-top: 20px;">No meetings found in database.</p>
            <?php endif; ?>

        </div> 

        <div class="bottom-nav">
             <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-user"></i>
                <span class="nav-text">Member<br>List</span>
            </a>
            <a href="#" class="nav-item active">
                <i class="fas fa-calendar-check"></i>
                <span class="nav-text">Meeting<br>List</span>
            </a>
        </div>

    </div>

</body>
</html>