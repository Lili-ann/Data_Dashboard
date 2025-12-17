<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Check if user is Manager or Admin (Useful if you add edit buttons later)
$isAdmin = (isset($_SESSION['role']) && (strtolower($_SESSION['role']) == 'manager' || strtolower($_SESSION['role']) == 'admin'));

// 2. FETCH REAL MEETINGS + VENUES (Using JOIN)
// We join 'venue' onto 'schedule' using the schedule ID.
$sql = "SELECT schedule.*, venue.room_name 
        FROM schedule 
        LEFT JOIN venue ON schedule.id = venue.schedule_id 
        ORDER BY schedule.meeting_time DESC";
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
        
        <div class="header">
            <?php if ($isAdmin): ?>
                <a href="admin_page.php" class="header-btn">Back</a>
            <?php else: ?>
                 <a href="index.php" class="header-btn">Back</a>
            <?php endif; ?>
            
            <div class="header-title">Meeting List</div>
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
                            <?php 
                                echo !empty($row['room_name']) ? htmlspecialchars($row['room_name']) : 'TBA'; 
                            ?>
                        </span>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; margin-top: 20px;">No meetings found.</p>
            <?php endif; ?>

            <?php if ($isAdmin): ?>
                <div class="save-btn-container" style="margin-top: 20px; padding-bottom: 20px;">
                    <a href="create_meeting.php" class="btn-save" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-plus" style="margin-right: 8px;"></i> Create New Meeting
                    </a>
                </div>
            <?php endif; ?>

        </div> 

        <div class="bottom-nav">
             <?php if ($isAdmin): // --- ADMIN NAV WITH ICONS --- ?>
                
                <a href="user_page.php" class="nav-item">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Manage<br>members</span>
                </a>

                <a href="meeting_list.php" class="nav-item active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Manage<br>schedule</span>
                </a>

                <a href="admin_logs.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">View<br>Logs</span>
                </a>

            <?php else: // --- USER/MANAGER NAV --- ?>
                <a href="meeting_list.php" class="nav-item active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Meeting<br>List</span>
                </a>
                <a href="user_page.php" class="nav-item">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-text">Member<br>List</span>
                </a>

            <?php endif; ?>
        </div>

    </div>

</body>
</html>