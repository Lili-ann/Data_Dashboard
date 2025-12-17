<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
$logs = [
    ["name" => "Stella", "action" => "borrowed", "book" => "Physics CAMBRIDGE"],
    ["name" => "Lilian", "action" => "borrowed", "book" => "Biology CAMBRIDGE"],
    ["name" => "Rafdah", "action" => "returned", "book" => "CompSci CAMBRIDGE"],
    ["name" => "JJ", "action" => "returned", "book" => "Mechanics CAMBRIDGE"],
    ["name" => "Alvino", "action" => "borrowed", "book" => "Pure Maths CAMBRIDGE"],
    ["name" => "Kenny", "action" => "borrowed", "book" => "Biology CAMBRIDGE"],
    ["name" => "Shandy", "action" => "returned", "book" => "Business CAMBRIDGE"],
];
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body class="user-body">
    <div class="overlay"></div>
    <div class="user-container">
        
        <div class="header">
            <div class="header-btn" style="cursor: default;"></div>
            Admin Dashboard  
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            <h2 class="section-title">Logs</h2>

            <div class="logs-box">

                <?php foreach($logs as $item): ?>
                    <div class="log-line">
                        <span class="darkred-name"><?php echo $item['name']; ?></span>
                        
                        <?php echo $item['action']; ?> "<?php echo $item['book']; ?>"
                        
                    </div>
                <?php endforeach; ?>

            </div>
            

        </div>
        <div class="bottom-nav">
            <a href="manage_roles.php" class="nav-item">
                <i class="fas fa-user-cog"></i> 
                <span>Manage Roles</span>
            </a>

            <a href="modify_data.php" class="nav-item">
                <i class="fas fa-pen-to-square"></i>
                <span>Modify Data</span>
            </a>

            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>

        </div>

    </div>
    
</body>

</html>