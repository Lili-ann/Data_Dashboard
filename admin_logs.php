<?php
session_start();
require_once 'config.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 2. FETCH REAL LOGS FROM DATABASE
$sql = "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 50";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="admin_page.php" class="header-btn">Back</a>
            
            <div class="header-title">Admin Logs</div>
            
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">            
            <div class="logs-box">
                
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        
                        <div class="log-line">
                            <span style="float: right; font-size: 11px; color: #666; margin-top: 2px;">
                                <?php echo date('d M, H:i', strtotime($row['created_at'])); ?>
                            </span>

                            <span class="darkred-name">
                                <?php echo htmlspecialchars($row['user_name']); ?>
                            </span>

                            <span style="color: #222; font-weight: bold; font-size: 14px; margin-right: 5px;">
                                [<?php echo htmlspecialchars($row['action']); ?>]
                            </span>

                            <div style="color: #555; font-size: 13px; margin-top: 2px; padding-left: 5px;">
                                <?php echo htmlspecialchars($row['details']); ?>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; padding-top: 20px; color: #666;">No activity recorded yet.</p>
                <?php endif; ?>

            </div>
        </div>

        <div class="bottom-nav">
            <a href="admin_page.php" class="nav-item">
                <i class="fas fa-users-cog"></i>
                <span class="nav-text">Manage<br>members</span>
            </a>

            <a href="meeting_list.php" class="nav-item">
                <i class="fas fa-calendar-check"></i>
                <span class="nav-text">Manage<br>schedule</span>
            </a>

            <a href="admin_logs.php" class="nav-item active">
                <i class="fas fa-file-alt"></i>
                <span class="nav-text">View<br>Logs</span>
            </a>
        </div>

    </div>
</body>
</html>