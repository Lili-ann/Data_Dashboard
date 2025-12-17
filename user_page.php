<?php
session_start();
require_once 'config.php';

// 1. Security Check
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 2. Fetch Users and their Roles
$sql = "SELECT user.id, user.name, role.role 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id";
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
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) { 
                    $displayRole = !empty($row['role']) ? $row['role'] : 'Member';
            ?>
            
            <div class="card">
                <a href="member_detail.php?id=<?php echo $row['id']; ?>" class="card-left" style="text-decoration:none; color:inherit; flex: 1;">
                    <span class="student-name"><?php echo htmlspecialchars($row['name']); ?></span>
                    <span class="student-id"><strong>ID :</strong> <?php echo htmlspecialchars($row['id']); ?></span>
                </a>

                <div class="card-right" style="display: flex; align-items: center; gap: 10px;">
                    <span class="role-badge <?php echo strtolower($displayRole); ?>">
                        <?php echo htmlspecialchars($displayRole); ?>
                    </span>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Manager'): ?>
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" style="color: #790707ff; font-size: 1.1rem; padding: 5px;" title="Edit Member">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php 
                } 
            } else {
                echo "<p style='text-align:center; margin-top:20px; color: #666;'>No members found.</p>";
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