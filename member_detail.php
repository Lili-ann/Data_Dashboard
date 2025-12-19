<!-- Starting the session and connecting to the database then verify the login by using email-->
 

<?php
session_start();
require_once 'config.php';
 
// 1. Security Check
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

//2. Fetch Member ID from URL and Get Member Details
if (isset($_GET['id'])) {
    $id_requested = $_GET['id'];

    //for fetching user details and its role from database user and role tables
    //"LEFT JOIN" the `role` table so we still get the user even if no row in the role table exists for that user.
    $sql = "SELECT user.name, user.id, user.major, user.profile_pic, role.role 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        WHERE user.id = ?";

    // prepare and bind to avoid SQL injection  
    $stmt = $conn->prepare($sql);
    //this binds the requested ID as an integer parameter to avoid SQL injection
    $stmt->bind_param("i", $id_requested);
    //execute and get the result set of the query 
    $stmt->execute();
    $result = $stmt->get_result();

    //this check if user exists in the database 
    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        if (empty($member['role'])) {
            $member['role'] = 'Member';
        }
    } else {
        echo "User not found.";
        exit();
    }
} else {
    //if nthere's no ID provided in URL, go back to user_page.php 
    header("Location: user_page.php");
    exit();
}

//getting attendance records for this user from attendance and schedule tables
//this query selects attendance records for the user, joining `schedule` to get meeting details 
//and LEFT JOINing `venue` to retrieve the room name, if there's any.
// then order by meeting time in descending order (most recent first)
$att_sql = "SELECT 
                attendance.status, --attendance status (Present, Absent, Pending)
                schedule.meeting_name, --meeting name
                schedule.meeting_time, --meeting time
                venue.room_name -- Roomm Number
            FROM attendance -- attendance table
            JOIN schedule ON attendance.schedule_id = schedule.id -- join schedule table
            LEFT JOIN venue ON schedule.id = venue.schedule_id -- left join venue table to get room number
            WHERE attendance.user_id = ? --filter by user id
            ORDER BY schedule.meeting_time DESC"; //order by meeting time descending

$att_stmt = $conn->prepare($att_sql);
//this binds the user id to the prepared statement as integer parameter to avoid SQL injection
$att_stmt->bind_param("i", $id_requested);
//execute and fetch attendance result set
$att_stmt->execute();
//get the result set of attendance query
$att_result = $att_stmt->get_result();
?>

<!-- Member Detail Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header" style="justify-content: center;">
            <div class="header-title">Member Details</div>
        </div>

        <div class="content">

            <!-- Profile Section -->
            <div class="profile-section">
                <div class="profile-pic-box">
                    <?php 
                        //this checks if the database has another image. If empty, fallback to avatar1.
                        // Displaying profile picture
                        $pic_name = !empty($member['profile_pic']) ? $member['profile_pic'] : 'avatar1.jpg';
                        $img_src = 'uploads/' . $pic_name; 
                    ?>
                    <img src="<?php echo htmlspecialchars($img_src); ?>" 
                        alt="Profile Picture" 
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                </div>

                <div class="profile-info"> <!-- Displaying member information -->
                    <p><strong>Name :</strong> <?php echo htmlspecialchars(ucwords($member['name'])); ?></p>
                    <p><strong>ID :</strong> <?php echo htmlspecialchars($member['id']); ?></p>
                    <p><strong>Role :</strong> <?php echo htmlspecialchars(ucfirst($member['role'])); ?></p>
                    <p><strong>Major :</strong> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $member['major']))); ?></p>
                </div>
            </div>

            <div class="section-title">Attendance</div> 
            <!-- Attendance Records Section -->
            <?php 
            //checking if attendance records exist, loops through them and display
            if ($att_result->num_rows > 0): // Shows Attendance Records
                while($row = $att_result->fetch_assoc()):  
                    //format the meeting date for better readability which is like "Monday 01 Jan, 3:30pm"
                    $formatted_date = date('l d M, g:ia', strtotime($row['meeting_time']));
                    //room displa name check for cases where room_name is NULL or empty
                    $room_display = !empty($row['room_name']) ? $row['room_name'] : 'TBA';
            ?>
            <div class="card">
                <div class="card-left"> <!--Displaying meeting details-->
                    <span class="meeting-title"><?php echo htmlspecialchars($row['meeting_name']); ?></span>
                    <span class="meeting-time">Date: <?php echo $formatted_date; ?></span>
                    <span class="meeting-time">Room: <?php echo htmlspecialchars($room_display); ?></span>
                </div>
                
                <div class="card-right"> <!--Displaying attendance status-->
                  <?php  
                        $status_class = '';
                        if ($row['status'] == 'Present') {
                            $status_class = 'status-present';
                        } elseif ($row['status'] == 'Absent') {
                            $status_class = 'status-absent'; 
                        } else {
                            $status_class = 'status-pending'; 
                        }
                    ?> 
                    <div class="status-btn <?php echo $status_class; ?>"> <!--status badge-->
                        <?php echo htmlspecialchars($row['status']); ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            
            <?php else: ?>
                <p style="text-align: center; color: #777; margin-top: 20px;">No attendance history found.</p>
            <?php endif; ?>

        </div> 

        <div class="bottom-nav">
            <!-- Back to Members List -->
            <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-backward"></i> 
                <span>Back</span>
            </a>
        </div>

    </div>

</body>
</html>