<?php
session_start();
require_once 'config.php';

// 1. SECURITY: Only Admins allowed
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. HANDLE FORM SUBMISSION
if (isset($_POST['create_meeting'])) {
    $meeting_name = $_POST['meeting_name'];
    $room = $_POST['room'];
    $date = $_POST['date']; // YYYY-MM-DD
    $time = $_POST['time']; // HH:MM

    // Combine Date and Time for MySQL (YYYY-MM-DD HH:MM:SS)
    $final_datetime = $date . ' ' . $time . ':00';

    // --- STEP 1: Insert into 'schedule' table (WITHOUT ROOM) ---
    $stmt = $conn->prepare("INSERT INTO schedule (meeting_name, meeting_time) VALUES (?, ?)");
    $stmt->bind_param("ss", $meeting_name, $final_datetime);

    if ($stmt->execute()) {
        
        // --- STEP 2: Get the ID of the meeting we just created ---
        $new_schedule_id = $conn->insert_id;
        $stmt->close();

        // --- STEP 3: Insert into 'venue' table ---
        // We use the $new_schedule_id to link the room to the meeting
        $venue_stmt = $conn->prepare("INSERT INTO venue (schedule_id, room_name) VALUES (?, ?)");
        $venue_stmt->bind_param("is", $new_schedule_id, $room);
        
        if ($venue_stmt->execute()) {
            
            // (Optional) Log this action
            if (function_exists('logActivity')) {
                logActivity($conn, $_SESSION['id'], $_SESSION['name'], 'Create Meeting', "Created: $meeting_name");
            }

            $venue_stmt->close();
            header("Location: meeting_list.php");
            exit();

        } else {
            $error_msg = "Meeting created, but failed to save Room details.";
        }

    } else {
        $error_msg = "Error creating meeting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meeting</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="meeting_list.php" class="header-btn">Back</a>
            <div class="header-title">Create Meeting</div>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            
            <?php if(isset($error_msg)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Meeting</span>
                    </div>
                    <div class="dropdowncard-right">
                        <input type="text" name="meeting_name" placeholder="e.g. Monthly Sync" required
                               style="border: none; border-bottom: 2px solid #8B0000; background: rgba(0,0,0,0.03); 
                                      text-align: right; font-weight: bold; font-size: 15px; outline: none; 
                                      width: 160px; padding: 5px; color: #333;">
                    </div>
                </div>

                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Room</span>
                    </div>
                    <div class="dropdowncard-right">
                         <input type="text" name="room" placeholder="e.g. Room A" required
                               style="border: none; border-bottom: 2px solid #8B0000; background: rgba(0,0,0,0.03); 
                                      text-align: right; font-weight: bold; font-size: 15px; outline: none; 
                                      width: 160px; padding: 5px; color: #333;">
                    </div>
                </div>

                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Date</span>
                    </div>
                    <div class="dropdowncard-right">
                        <input type="date" name="date" required
                               style="border: 1px solid #ddd; border-radius: 5px; padding: 5px; font-family: 'Poppins', sans-serif;">
                    </div>
                </div>

                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Time</span>
                    </div>
                    <div class="dropdowncard-right">
                        <input type="time" name="time" required
                               style="border: 1px solid #ddd; border-radius: 5px; padding: 5px; font-family: 'Poppins', sans-serif;">
                    </div>
                </div>

                <div class="save-btn-container">
                    <button type="submit" name="create_meeting" class="btn-save">Save Meeting</button>
                </div>

            </form>

        </div> 

        <div class="bottom-nav">
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
        </div>

    </div>

</body>
</html>