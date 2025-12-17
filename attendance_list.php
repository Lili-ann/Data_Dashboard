<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 1. GET MEETING ID FROM URL
$meeting_id = isset($_GET['id']) ? $_GET['id'] : 0;

// 2. MOCK DATABASE OF MEETINGS (Must match meeting_list.php)
$meetings_db = [
    0 => ['title' => 'Meeting 1', 'time' => '12 Oct, 10:00 AM', 'room' => 'Room 5'],
    1 => ['title' => 'Meeting 2', 'time' => '19 Oct, 02:00 PM', 'room' => 'Room 3'],
    2 => ['title' => 'Meeting 3', 'time' => '26 Oct, 09:00 AM', 'room' => 'Room 1'],
    3 => ['title' => 'Meeting 4', 'time' => '02 Nov, 04:00 PM', 'room' => 'Room 6'],
];

// Get the specific meeting data based on ID
$current_meeting = isset($meetings_db[$meeting_id]) ? $meetings_db[$meeting_id] : $meetings_db[0];


// 3. MOCK DATA: ATTENDEES LIST
$attendees = [
    ['id' => '22018390', 'name' => 'Student 1', 'role' => 'Member', 'status' => 'Present'],
    ['id' => '22018391', 'name' => 'Student 2', 'role' => 'Member', 'status' => 'Absent'],
    ['id' => '22018392', 'name' => 'Student 3', 'role' => 'Manager', 'status' => 'Present'],
    ['id' => '22018393', 'name' => 'Student 4', 'role' => 'Member', 'status' => 'Present'],
    ['id' => '22018394', 'name' => 'Student 5', 'role' => 'Member', 'status' => 'Present'],
];

// UPDATED: Only Present or Absent
$status_options = ['Present', 'Absent'];
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
            
            <div class="card" style="margin-bottom: 25px; background: rgba(255,255,255,0.95);">
                <div class="card-left">
                    <span class="meeting-title" style="font-size: 20px; color: #8B0000;"><?php echo $current_meeting['title']; ?></span>
                    <span class="meeting-time"><?php echo $current_meeting['time']; ?></span>
                </div>
                <div class="card-right">
                    <span class="room-text" style="font-size: 18px;"><?php echo $current_meeting['room']; ?></span>
                </div>
            </div>

            <form action="save_attendance.php" method="POST">
                
                <?php foreach($attendees as $person): ?>
                <div class="dropdowncard">
                    
                    <div class="dropdowncard-left">
                        <span class="staff-name"><?php echo $person['name']; ?></span>
                        <span class="staff-id"><?php echo $person['role']; ?></span>
                    </div>

                    <div class="dropdowncard-right">
                        <div class="select-wrapper">
                            <select name="status[<?php echo $person['id']; ?>]">
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
                <?php endforeach; ?>

                <div class="save-btn-container">
                    <button type="submit" class="btn-save">Save Attendance</button>
                </div>

            </form>

        </div> 
        
        <div class="bottom-nav">
            <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-user"></i>
                <span class="nav-text">Member<br>List</span>
            </a>
            
            <a href="meeting_list.php" class="nav-item active">
                <i class="fas fa-calendar-check"></i>
                <span class="nav-text">Meeting<br>List</span>
            </a>
        </div>

    </div>

</body>
</html>