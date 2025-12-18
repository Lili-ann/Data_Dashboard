<?php
session_start();
require_once 'config.php';

// 1. SECURITY CHECK
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 2. GET MEETING ID
if (!isset($_GET['id'])) {
    header("Location: meeting_list.php");
    exit();
}
$schedule_id = $_GET['id'];

// --- FIXED ROLE LOGIC ---
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$isAdmin = ($role == 'admin'); 

// 3. HANDLE FORM SUBMISSION (Save/Update Attendance)
// 3. HANDLE FORM SUBMISSION (Save/Update Attendance)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
    
    foreach ($_POST['status'] as $user_id => $status_value) {
        
        // --- [FIX] SKIP EMPTY SELECTIONS ---
        // If the admin left it as "-- Select --", ignore this user.
        if (empty($status_value)) {
            continue; 
        }
        // -----------------------------------

        // Check if a record already exists for this user + meeting
        $check_sql = "SELECT id FROM attendance WHERE user_id = ? AND schedule_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $schedule_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Record exists -> UPDATE it
            $update_sql = "UPDATE attendance SET status = ? WHERE user_id = ? AND schedule_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sii", $status_value, $user_id, $schedule_id);
            $update_stmt->execute();
        } else {
            // No record -> INSERT new one
            $insert_sql = "INSERT INTO attendance (user_id, schedule_id, status) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iis", $user_id, $schedule_id, $status_value);
            $insert_stmt->execute();
        }
    }
    
    $message = "Attendance saved successfully!";

    // LOG THIS ACTION
    $logger_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown User';
    $log_details = "Updated attendance for Meeting ID: " . $schedule_id;
    if (function_exists('logActivity')) {
        logActivity($conn, $_SESSION['id'], $logger_name, 'Update Attendance', $log_details);
    }
}

// 4. FETCH MEETING DETAILS
$meet_sql = "SELECT schedule.*, venue.room_name 
             FROM schedule 
             LEFT JOIN venue ON schedule.id = venue.schedule_id 
             WHERE schedule.id = ?";
$m_stmt = $conn->prepare($meet_sql);
$m_stmt->bind_param("i", $schedule_id);
$m_stmt->execute();
$meeting = $m_stmt->get_result()->fetch_assoc();

if (!$meeting) {
    echo "Meeting not found.";
    exit();
}

// 5. FETCH ALL USERS + CURRENT ATTENDANCE
$sql = "SELECT user.id, user.name, role.role, attendance.status 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        LEFT JOIN attendance ON user.id = attendance.user_id AND attendance.schedule_id = ?
        ORDER BY user.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$attendees_result = $stmt->get_result();

$status_options = ['Present', 'Absent', 'Pending'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance List</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="meeting_list.php" class="header-btn">Back</a>
            <div class="header-title">Attendance List</div>
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            
            <?php if(isset($message)): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="card" style="margin-bottom: 25px; background: rgba(255,255,255,0.95);">
                <div class="card-left">
                    <span class="meeting-title" style="font-size: 20px; color: #8B0000;">
                        <?php echo htmlspecialchars($meeting['meeting_name']); ?>
                    </span>
                    <span class="meeting-time">
                        <?php echo date('d M, h:i A', strtotime($meeting['meeting_time'])); ?>
                    </span>
                </div>
                <div class="card-right">
                    <span class="room-text" style="font-size: 18px;">
                        <?php echo !empty($meeting['room_name']) ? htmlspecialchars($meeting['room_name']) : 'TBA'; ?>
                    </span>
                </div>
            </div>

            <form method="POST"> 
                <?php while($person = $attendees_result->fetch_assoc()): ?>
                <div class="dropdowncard">
                    
                    <div class="dropdowncard-left">
                        <span class="staff-name"><?php echo htmlspecialchars($person['name']); ?></span>
                        <span class="staff-id">
                            <?php echo !empty($person['role']) ? htmlspecialchars($person['role']) : 'Member'; ?>
                        </span>
                    </div>

                    <div class="dropdowncard-right">
                        <div class="select-wrapper">
                            <select name="status[<?php echo $person['id']; ?>]">
                                <option value="">-- Select --</option>
                                <?php foreach($status_options as $option): ?>
                                    <option value="<?php echo $option; ?>" 
                                        <?php echo ($person['status'] == $option) ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>

                <div class="save-btn-container">
                    <button type="submit" name="save_attendance" class="btn-save">Save Attendance</button>
                </div>

            </form>

        </div> 
        
        <div class="bottom-nav">
             <?php if ($isAdmin): // --- ADMIN NAV (3 Items) --- ?>
                
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

            <?php else: // --- MANAGER / MEMBER NAV (2 Items) --- ?>
                
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