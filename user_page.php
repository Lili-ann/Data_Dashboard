<?php
session_start();
require_once 'config.php';

// 1. Security Check
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// 2. DEFINE PERMISSIONS (Case-Insensitive)
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';

// Admin: Can edit users + Needs back button to Manage Roles
$can_edit = ($role == 'admin');

// Manager: Needs back button to Meeting List
$is_manager = ($role == 'manager');

// 3. Fetch Users
$sql = "SELECT user.id, user.name, role.role 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        ORDER BY user.id ASC";
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

    <div class="user-container">
        
        <div class="header">
            <div class="header-btn" style="cursor: default;"></div>
            Members List    
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div id="search-container" style="display: none; padding: 10px 20px 20px 20px;">
            <input type="text" id="searchInput" onkeyup="filterMembers()" 
                   placeholder="Search name or ID..." 
                   style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 16px;">
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
                    <span class="student-id">ID: <?php echo htmlspecialchars($row['id']); ?></span>
                </a>

                <div class="card-right" style="display: flex; align-items: center; gap: 10px;">
                    <span class="role-badge <?php echo strtolower($displayRole); ?>">
                        <?php echo htmlspecialchars($displayRole); ?>
                    </span>

                    <?php if ($can_edit): ?>
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" style="color: #800000; font-size: 1.2rem; padding: 5px;" title="Edit Member">
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
            
            <?php if ($can_edit): ?>
                <a href="manage_roles.php" class="nav-item">
                    <i class="fa-solid fa-backward"></i> 
                    <span>Back</span>
                </a>
            <?php endif; ?>

            <?php if ($is_manager): ?>
                <a href="meeting_list.php" class="nav-item">
                    <i class="fa-solid fa-backward"></i> 
                    <span>Back</span>
                </a>
            <?php endif; ?>

            <a href="javascript:void(0);" onclick="toggleSearch()" id="searchNavBtn" class="nav-item">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </a>

        </div>

    </div>

    <script>
    function toggleSearch() {
        var container = document.getElementById("search-container");
        var btn = document.getElementById("searchNavBtn");
        var input = document.getElementById("searchInput");

        if (container.style.display === "none") {
            container.style.display = "block";
            btn.classList.add("active"); // Highlight button
            input.focus();
        } else {
            container.style.display = "none";
            btn.classList.remove("active"); // Dim button
        }
    }

    function filterMembers() {
        var input, filter, cards, name, id, i;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        cards = document.getElementsByClassName("card");

        for (i = 0; i < cards.length; i++) {
            name = cards[i].getElementsByClassName("student-name")[0];
            id = cards[i].getElementsByClassName("student-id")[0];
            
            if (name || id) {
                var nameText = name.textContent || name.innerText;
                var idText = id.textContent || id.innerText;

                if (nameText.toUpperCase().indexOf(filter) > -1 || idText.toUpperCase().indexOf(filter) > -1) {
                    cards[i].style.display = ""; 
                } else {
                    cards[i].style.display = "none"; 
                }
            }
        }
    }
    </script>
</body>
</html>