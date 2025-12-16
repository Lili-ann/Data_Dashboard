<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// MOCK DATA 
$members = [
    ['name' => 'Student 1', 'id' => 'ID', 'role' => 'Admin'],
    ['name' => 'Student 2', 'id' => 'ID', 'role' => 'Manager'],
    ['name' => 'Student 3', 'id' => 'ID', 'role' => 'Manager'],
    ['name' => 'Student 3', 'id' => 'ID', 'role' => 'Member'],
    ['name' => 'Student 4', 'id' => 'ID', 'role' => 'Member'], // Saya tambah satu biar kelihatan scrollnya
]; 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- This cdnjs.cloudfare is used instead of manually downloading all font awesome icons -->
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <div class="header-btn" style="cursor: default;"></div>
            Members List    
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            <?php 
            // Kita kasih index angka manual agar bisa ditangkap di halaman detail
            // key 'id_data' ini ceritanya ID dari database
            $index = 1; 
            foreach($members as $member): 
                $member_id = $index++; // Simulasi ID 1, 2, 3, dst
            ?>
            
            <a href="member_detail.php?id=<?php echo $member_id; ?>" style="text-decoration:none; color:inherit;">
                <div class="card">
                    <div class="card-left">
                        <span class="student-name"><?php echo $member['name']; ?></span>
                        <span class="student-id"><?php echo $member['id']; ?></span>
                    </div>
                    <div class="card-right">
                        <?php echo $member['role']; ?>
                    </div>
                </div>
            </a>
            
            <?php endforeach; ?>
        </div>

        <div class="bottom-nav">
        </div>

    </div>

</body>
</html>