<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');

// MOCK DATA MEETING
// We use the index (0, 1, 2, 3) as the ID to pass to the next page
$meetings = [
    ['title' => 'Meeting 1', 'time' => '12 Oct, 10:00 AM', 'room' => 'Room 5'],
    ['title' => 'Meeting 2', 'time' => '19 Oct, 02:00 PM', 'room' => 'Room 3'],
    ['title' => 'Meeting 3', 'time' => '26 Oct, 09:00 AM', 'room' => 'Room 1'],
    ['title' => 'Meeting 4', 'time' => '02 Nov, 04:00 PM', 'room' => 'Room 6'],
];
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
            <a href="user_page.php" class="header-btn">Back</a>
            Meeting List
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <form action="save_meetings.php" method="POST">
            <div class="content">
                
                <?php foreach($meetings as $index => $meet): ?>
                <div class="card">
                    
                    <a href="attendance_list.php?id=<?php echo $index; ?>" style="text-decoration: none; flex: 1; display: flex;">
                        <div class="card-left">
                            <input type="hidden" name="meetings[<?php echo $index; ?>][title]" value="<?php echo $meet['title']; ?>">
                            <span class="meeting-title"><?php echo $meet['title']; ?></span>
                            <span class="meeting-time"><?php echo $meet['time']; ?></span>
                        </div>
                    </a>

                    <div class="card-right">
                        <?php if ($isAdmin): ?>
                            <select name="meetings[<?php echo $index; ?>][room]" class="dropdowncard">
                                <?php 
                                $rooms = ['Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5', 'Room 6'];
                                foreach($rooms as $r): 
                                ?>
                                    <option value="<?php echo $r; ?>" <?php echo ($meet['room'] == $r) ? 'selected' : ''; ?>>
                                        <?php echo $r; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fas fa-chevron-down select-icon"></i>
                        <?php else: ?>
                            <span class="room-text">
                                <?php echo $meet['room']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div> 

            <?php if ($isAdmin): ?>
                <div class="save-button-container">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            <?php endif; ?>

        </form>

        <div class="bottom-nav">
             <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-user"></i>
                <span class="nav-text">Member<br>List</span>
            </a>
            <a href="#" class="nav-item active">
                <i class="fas fa-calendar-check"></i>
                <span class="nav-text">Meeting<br>List</span>
            </a>
        </div>

    </div>

</body>
</html>