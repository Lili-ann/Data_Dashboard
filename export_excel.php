<?php
// Start session handling so we can access logged-in user info.
session_start();

// Load database connection and helper functions from config.
require_once 'config.php';

// SECURITY: Restrict this export to administrators only.
// If the current session does not represent an admin, redirect to the index.
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}

// Tell the browser this response is a CSV file download.
// The filename contains the current date for convenience.
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Club_Full_Database_' . date('Y-m-d') . '.csv');

// Open a writeable output stream that goes directly to the HTTP response.
$output = fopen('php://output', 'w');

// =========================================================
// SECTION 1: EXPORT MEMBER LIST
// =========================================================
// Write a simple title row to separate sections in the CSV.
fputcsv($output, array('--- MEMBER LIST ---'));

// Column headers for members block.
fputcsv($output, array('User ID', 'Name', 'Email', 'Major', 'Role', 'Profile Picture'));

// Query users and their role (if any). LEFT JOIN ensures users appear
// even if they don't have a role row.
$sql_users = "SELECT user.id, user.name, user.email, user.major, user.profile_pic, role.role 
              FROM user 
              LEFT JOIN role ON user.id = role.user_id 
              ORDER BY user.id ASC";
$result_users = $conn->query($sql_users);

// Iterate result set and write each user as a CSV row.
if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        // Default to 'Member' if there is no explicit role value.
        $role = !empty($row['role']) ? $row['role'] : 'Member';
        fputcsv($output, array(
            $row['id'],           // User ID
            $row['name'],         // Full name
            $row['email'],        // Email address
            $row['major'],        // Major/department
            $role,                // Role label (e.g. Admin, Member)
            $row['profile_pic']   // Filename or path of profile picture
        ));
    }
}

// Add a couple blank rows to visually separate sections in the CSV.
fputcsv($output, array(''));
fputcsv($output, array(''));


// =========================================================
// SECTION 2: EXPORT MEETING SCHEDULE
// =========================================================
fputcsv($output, array('--- MEETING SCHEDULE ---'));
fputcsv($output, array('Meeting ID', 'Meeting Name', 'Date & Time', 'Room / Venue'));

// Query schedule and attempt to include the venue room name.
// LEFT JOIN is used so that schedules without venue rows still appear.
$sql_schedule = "SELECT schedule.id, schedule.meeting_name, schedule.meeting_time, venue.room_name 
                 FROM schedule 
                 LEFT JOIN venue ON schedule.id = venue.schedule_id 
                 ORDER BY schedule.meeting_time DESC";
$result_schedule = $conn->query($sql_schedule);

if ($result_schedule->num_rows > 0) {
    while ($row = $result_schedule->fetch_assoc()) {
        // If no room name is provided, display TBA (to be announced).
        $room = !empty($row['room_name']) ? $row['room_name'] : 'TBA';
        fputcsv($output, array(
            $row['id'],            // Meeting unique id
            $row['meeting_name'],  // Human-friendly meeting title
            $row['meeting_time'],  // Date/time stored in DB
            $room                  // Room or venue name
        ));
    }
}

// Spacing between sections.
fputcsv($output, array(''));
fputcsv($output, array(''));


// =========================================================
// SECTION 3: EXPORT ATTENDANCE LOGS
// =========================================================
fputcsv($output, array('--- ATTENDANCE LOGS ---'));
fputcsv($output, array('Meeting Name', 'Meeting Date', 'Member Name', 'Status'));

// Join attendance records with schedule and user tables to get readable rows.
$sql_attendance = "SELECT schedule.meeting_name, schedule.meeting_time, user.name, attendance.status 
                   FROM attendance
                   JOIN schedule ON attendance.schedule_id = schedule.id
                   JOIN user ON attendance.user_id = user.id
                   ORDER BY schedule.meeting_time DESC, user.name ASC";
$result_attendance = $conn->query($sql_attendance);

if ($result_attendance->num_rows > 0) {
    while ($row = $result_attendance->fetch_assoc()) {
        fputcsv($output, array(
            $row['meeting_name'], // Meeting title
            $row['meeting_time'], // When the meeting occurred
            $row['name'],         // Attendee's full name
            $row['status']        // Attendance status (e.g. Present/Absent)
        ));
    }
}

// Optionally log the export action if helper exists. This can be useful
// for auditing who downloaded the full database and when.
if (function_exists('logActivity')) {
    logActivity($conn, $_SESSION['id'], $_SESSION['name'], 'Export', 'Downloaded full database.');
}

// Terminate script after streaming the CSV to the client.
exit();
?>