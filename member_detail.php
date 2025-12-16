<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 1. Tangkap ID dari URL (yang dikirim dari user_page.php)
$id_requested = isset($_GET['id']) ? $_GET['id'] : 1; // Default ke 1 kalo error

// 2. MOCK DATA MEMBER (Harus sama urutannya dengan user_page biar datanya nyambung)
// Array key (1, 2, 3) disini berperan sebagai ID Database
$members_db = [
    1 => ['name' => 'Student 1', 'id' => '25001', 'role' => 'Admin',   'faculty' => 'Computer Science'],
    2 => ['name' => 'Student 2', 'id' => '25002', 'role' => 'Manager', 'faculty' => 'Business'],
    3 => ['name' => 'Student 3', 'id' => '25003', 'role' => 'Manager', 'faculty' => 'Engineering'],
    4 => ['name' => 'Student 3', 'id' => '25004', 'role' => 'Member',  'faculty' => 'Arts'],
    5 => ['name' => 'Student 4', 'id' => '25005', 'role' => 'Member',  'faculty' => 'Medicine'],
];

// Ambil data sesuai ID
$current_member = isset($members_db[$id_requested]) ? $members_db[$id_requested] : $members_db[1];

// 3. MOCK DATA ATTENDANCE (List Absensi)
$attendance_list = [
    ['meeting' => 'Meeting 1', 'date' => '12 Oct 2025', 'room' => 'Room 5', 'status' => 'Present'],
    ['meeting' => 'Meeting 2', 'date' => '19 Oct 2025', 'room' => 'Room 3', 'status' => 'Upcoming'],
];

?>

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
            
            <div class="profile-section">
                <div class="profile-pic-box">
                    <i class="fas fa-user-tie"></i>
                </div>

                <div class="profile-info">
                    <p><strong>Name :</strong> <?php echo $current_member['name']; ?></p>
                    <p><strong>ID :</strong> <?php echo $current_member['id']; ?></p>
                    <p><strong>Role :</strong> <?php echo $current_member['role']; ?></p>
                    <p><strong>Faculty :</strong> <?php echo $current_member['faculty']; ?></p>
                </div>
            </div>

            <div class="section-title">Attendance</div>

            <?php foreach($attendance_list as $att): ?>
            <div class="card">
                <div class="card-left">
                    <span class="meeting-title"><?php echo $att['meeting']; ?></span>
                    <span class="meeting-time">Date: <?php echo $att['date']; ?></span>
                    <span class="meeting-time">Room: <?php echo $att['room']; ?></span>
                </div>
                
                <div class="card-right">
                    <div class="status-btn <?php echo ($att['status'] == 'Present') ? 'status-present' : 'status-upcoming'; ?>">
                        <?php echo $att['status']; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div> 

        <div class="bottom-nav">
            <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-backward"></i> 
                <span>Back</span>
            </a>
        </div>

    </div>

</body>
</html>