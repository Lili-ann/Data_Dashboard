<?php
//initializing
session_start();
require_once 'config.php';


//verify if user is logged in by checking session variable 
// If not logged in, redirect to login page
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

//validate meeting id
//check if 'id' parameter is provided in the URL
//if not provided, redirect back to the meeting list page
if (!isset($_GET['id'])) {
    header("Location: meeting_list.php");
    exit();
}
$schedule_id = $_GET['id'];


// check if the current user is an admin for navigation and permissions purposes
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$isAdmin = ($role == 'admin'); 

//handling attendance form submission using POST method 
//this section handles both inserting new records and updating existing ones 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
    
    //this loops through each user and their selected attendance status
    foreach ($_POST['status'] as $user_id => $status_value) {
        
        //however, if the user left the dropdown as "-- Select --", skip this entry
        //to prevents inserting empty status values into the database
        if (empty($status_value)) {
            continue; 
        }

        //vhecking existing attendance record for the user and meeting
        //Query the database to see if an attendance record exists for this user and meeting from the attendance table
        $check_sql = "SELECT id FROM attendance WHERE user_id = ? AND schedule_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_id, $schedule_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            //updating existing record
            //if a record exists, update the status with the new value the admin selected
            $update_sql = "UPDATE attendance SET status = ? WHERE user_id = ? AND schedule_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sii", $status_value, $user_id, $schedule_id);
            $update_stmt->execute();
        } else {
            //inserting new attendance record
            //check if no record exists, create a new attendance entry
            $insert_sql = "INSERT INTO attendance (user_id, schedule_id, status) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iis", $user_id, $schedule_id, $status_value);
            $insert_stmt->execute();
        }
    }
    
    //success message to display to the admin after saving attendance
    $message = "Attendance saved successfully!";

    //logging the attendance update action
    //also records this action in the system logs 
    //it gets the user's name or use 'Unknown User' if not available
    $logger_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown User';
    $log_details = "Updated attendance for Meeting ID: " . $schedule_id;
    //calls the logging function in the config.php file
    if (function_exists('logActivity')) {
        logActivity($conn, $_SESSION['id'], $logger_name, 'Update Attendance', $log_details);
    }
}

//getting meeting details
//which are :meeting name, time and the venue (room name)
//using LEFT JOIN to handle cases where a meeting might not have a venue assigned
$meet_sql = "SELECT schedule.*, venue.room_name 
             FROM schedule 
             LEFT JOIN venue ON schedule.id = venue.schedule_id 
             WHERE schedule.id = ?";
$m_stmt = $conn->prepare($meet_sql);
$m_stmt->bind_param("i", $schedule_id);
$m_stmt->execute();
$meeting = $m_stmt->get_result()->fetch_assoc();

//check if the meeting exists; if not, display error message and exit
if (!$meeting) {
    echo "Meeting not found.";
    exit();
}

//getting all users and their attendance status for a meeting
// Uses LEFT JOINs to include users even if they don't have a role or attendance record yet
//display in alphabetical order for user name
$sql = "SELECT user.id, user.name, role.role, attendance.status 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        LEFT JOIN attendance ON user.id = attendance.user_id AND attendance.schedule_id = ?
        ORDER BY user.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$attendees_result = $stmt->get_result();

// gets the attendance status options for the dropdown menus
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
        
        <!-- Header Section: Navigation buttons and page title -->
        <div class="header">
            <a href="meeting_list.php" class="header-btn">Back</a>
            <div class="header-title">Attendance List</div>
            <!-- Logout button -->
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            
            <!-- Display success message if attendance was saved -->
            <?php if(isset($message)): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Meeting Details Card: Display meeting name, time, and room name -->
            <div class="card" style="margin-bottom: 25px; background: rgba(255,255,255,0.95);">
                <div class="card-left">
                    <span class="meeting-title" style="font-size: 20px; color: #8B0000;">
                        <?php echo htmlspecialchars($meeting['meeting_name']); ?>
                    </span>
                    <!-- Formatted meeting date and time-->
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
	    <!-- The Save Button (when you click it, the data will sent securely-->
	    <!-- Data Loop, which when we click save, it will called attendees_result to the person -->
            <form method="POST"> 
		 <!-- Data Loop, which when we click save, it will called attendees_result to the person -->
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
			    <!-- This allows you to know exactly whichstatus belongs to which person when you process the form -->
                            <select name="status[<?php echo $person['id']; ?>]">
                                <option value="">-- Select --</option>
				 <!-- It loops through a predefined list of statuses (Present, Absent, and Pending) -->
				<!-- The code checks if the person's current status ini the database matches the current optipn in the loop -->
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
		     <!-- when clicked, this sends all the dropdown selections to the server at once-->
                    <button type="submit" name="save_attendance" class="btn-save">Save Attendance</button>
                </div>

            </form>

        </div> 
        
        <!-- Bottom Navigation Bar:if it's different options based on roles -->
        <div class="bottom-nav">
             <!-- Admin Navigation: 3 items for admin tasks -->
             <?php if ($isAdmin): ?>
                
                <!-- Manage Members -->
                <a href="user_page.php" class="nav-item">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Manage<br>members</span>
                </a>

                <!-- Manage Schedule -->
                <a href="meeting_list.php" class="nav-item active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Manage<br>schedule</span>
                </a>

                <!-- View Activity Logs  -->
                <a href="admin_logs.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">View<br>Logs</span>
                </a>

            <!-- Manager/Member Navigation: 2 items for regular users -->
            <?php else: ?>
                
                <!-- Meeting List page -->
                <a href="meeting_list.php" class="nav-item active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Meeting<br>List</span>
                </a>
                
                <!-- Member List page-->
                <a href="user_page.php" class="nav-item">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-text">Member<br>List</span>
                </a>

            <?php endif; ?>
        </div>

    </div>

</body>
</html>