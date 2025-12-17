<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT user.id, user.name, role.role 
        FROM user 
        JOIN role ON user.id = role.user_id";
$result = $conn->query($sql);
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
           // 2. CHECK IF USERS EXIST
            if ($result->num_rows > 0) {
                // 3. LOOP THROUGH EACH USER
                while($row = $result->fetch_assoc()) { 
                    $displayRole = !empty($row['role']) ? $row['role'] : 'Member';
            ?>
            
            <a href="member_detail.php?id=<?php echo $row['id']; ?>" style="text-decoration:none; color:inherit;">
                <div class="card">
                    <div class="card-left">
                        <span class="student-name"><?php echo $row['name']; ?></span>
                        <span class="student-id"><?php echo $row['id']; ?></span>
                    </div>

                    <div class="card-right">
                       <span class="role-badge <?php echo strtolower($displayRole); ?>">
                            <?php echo htmlspecialchars($displayRole); ?>
                        </span>
                    </div>
                </div>
            </a>
            
          <?php 
                } 
            } else {
                echo "<p style='text-align:center; margin-top:20px;'>No members found.</p>";
            }
            ?>
        </div>

        <div class="bottom-nav">
            <a href="#" class="nav-item"><i class="fas fa-home"></i><span>Home</span></a>
             <a href="#" class="nav-item"><i class="fas fa-search"></i><span>Search</span></a>
             <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>

    </div>
</body>
</html>