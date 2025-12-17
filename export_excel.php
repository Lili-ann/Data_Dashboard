<?php
session_start();
require_once 'config.php';

// 1. SECURITY: Only Admins allowed
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. SET HEADERS
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Club_Full_Database_' . date('Y-m-d') . '.csv');

// 3. OPEN OUTPUT STREAM
$output = fopen('php://output', 'w');

// =========================================================
// SECTION 1: MEMBERS LIST
// =========================================================
// Add a Title Row
fputcsv($output, array('--- MEMBER LIST ---'));
// Add Column Headers
fputcsv($output, array('User ID', 'Name', 'Email', 'Major', 'Role', 'Profile Picture'));

$sql_users = "SELECT user.id, user.name, user.email, user.major, user.profile_pic, role.role 
              FROM user 
              LEFT JOIN role ON user.id = role.user_id 
              ORDER BY user.id ASC";
$result_users = $conn->query($sql_users);

if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $role = !empty($row['role']) ? $row['role'] : 'Member';
        fputcsv($output, array(
            $row['id'], 
            $row['name'], 
            $row['email'], 
            $row['major'], 
            $role, 
            $row['profile_pic']
        ));
    }
}

// Add blank rows for spacing
fputcsv($output, array(''));
fputcsv($output, array(''));


// =========================================================
// SECTION 2: MEETING SCHEDULE
// =========================================================
fputcsv($output, array('--- MEETING SCHEDULE ---'));
fputcsv($output, array('Meeting ID', 'Meeting Name', 'Date & Time', 'Room / Venue'));

// Join with Venue table to get room names
$sql_schedule = "SELECT schedule.id, schedule.meeting_name, schedule.meeting_time, venue.room_name 
                 FROM schedule 
                 LEFT JOIN venue ON schedule.id = venue.schedule_id 
                 ORDER BY schedule.meeting_time DESC";
$result_schedule = $conn->query($sql_schedule);

if ($result_schedule->num_rows > 0) {
    while ($row = $result_schedule->fetch_assoc()) {
        $room = !empty($row['room_name']) ? $row['room_name'] : 'TBA';
        fputcsv($output, array(
            $row['id'],
            $row['meeting_name'],
            $row['meeting_time'],
            $room
        ));
    }
}

// Add blank rows for spacing
fputcsv($output, array(''));
fputcsv($output, array(''));


// =========================================================
// SECTION 3: ATTENDANCE RECORDS
// =========================================================
fputcsv($output, array('--- ATTENDANCE LOGS ---'));
fputcsv($output, array('Meeting Name', 'Meeting Date', 'Member Name', 'Status'));

// Join everything: Schedule + Attendance + User
$sql_attendance = "SELECT schedule.meeting_name, schedule.meeting_time, user.name, attendance.status 
                   FROM attendance
                   JOIN schedule ON attendance.schedule_id = schedule.id
                   JOIN user ON attendance.user_id = user.id
                   ORDER BY schedule.meeting_time DESC, user.name ASC";
$result_attendance = $conn->query($sql_attendance);

if ($result_attendance->num_rows > 0) {
    while ($row = $result_attendance->fetch_assoc()) {
        fputcsv($output, array(
            $row['meeting_name'],
            $row['meeting_time'],
            $row['name'],
            $row['status']
        ));
    }
}

// Log the action
if (function_exists('logActivity')) {
    logActivity($conn, $_SESSION['id'], $_SESSION['name'], 'Export', 'Downloaded full database.');
}

exit();
?>