<?php
session_start();
require_once 'config.php';

// 1. Security Check
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
$can_edit = ($role == 'admin');
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

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header" style="position: relative; justify-content: space-between;">
            
            <div style="display: flex; align-items: center; min-width: 80px;">
                <?php if ($can_edit): // Admin -> Admin Hub ?>
                    <a href="admin_page.php" class="header-btn">
                        <i class="fas fa-chevron-left" style="margin-right: 5px;"></i> Back
                    </a>
                <?php elseif ($is_manager): // Manager -> Meeting List ?>
                    <a href="meeting_list.php" class="header-btn">
                        <i class="fas fa-chevron-left" style="margin-right: 5px;"></i> Back
                    </a>
                <?php else: ?>
                    <?php endif; ?>
            </div>

            <div class="header-title" style="position: absolute; left: 50%; transform: translateX(-50%); white-space: nowrap;">
                Members List
            </div>  

            <div style="display: flex; align-items: center; gap: 15px; min-width: 80px; justify-content: flex-end;">
                <a href="javascript:void(0);" onclick="toggleSearch()" class="header-btn" style="min-width: auto; padding: 5px;">
                    <i class="fas fa-search" style="font-size: 18px;"></i>
                </a>
                
                <a href="logout.php" class="header-btn logout" style="min-width: auto;">Logout</a>
            </div>
        </div>

        <div id="search-container" style="display: none; padding: 10px 20px 20px 20px; background: rgba(255,255,255,0.5);">
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
                        <a href="edit_member.php?id=<?php echo $row['id']; ?>" style="color: #800000; font-size: 1.2rem; padding: 5px;">
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
            <?php if ($can_edit): // --- ADMIN NAV (3 Items with Icons) --- ?>
                
                <a href="user_page.php" class="nav-item active">
                    <i class="fas fa-users-cog"></i>
                    <span class="nav-text">Manage<br>members</span>
                </a>

                <a href="meeting_list.php" class="nav-item">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-text">Manage<br>schedule</span>
                </a>

                <a href="admin_logs.php" class="nav-item">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">View<br>Logs</span>
                </a>

            <?php else: // --- USER/MANAGER NAV --- ?>
                
                <?php if ($is_manager): ?>
                    <a href="meeting_list.php" class="nav-item">
                        <i class="fas fa-calendar-check"></i>
                        <span class="nav-text">Meeting<br>List</span>
                    </a>
                <?php endif; ?>
                
                <a href="user_page.php" class="nav-item active">
                    <i class="fa-solid fa-user"></i>
                    <span class="nav-text">Members<br>List</span>
                </a>
                

            <?php endif; ?>
        </div>

    </div>

    <script>
    function toggleSearch() {
        var container = document.getElementById("search-container");
        var input = document.getElementById("searchInput");
        if (container.style.display === "none") {
            container.style.display = "block";
            input.focus();
        } else {
            container.style.display = "none";
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