<?php
session_start();
require_once 'config.php';

// 1. SECURITY: Only Admins allowed
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
// Check if user is Admin (case-insensitive)
$role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
if ($role !== 'admin') {
    // If not admin, send them back to their home
    header("Location: user_page.php");
    exit();
}

// 2. HANDLE FORM SUBMISSION (Save Roles)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_roles'])) {
    
    // Loop through the submitted roles
    // The form sends: $_POST['role'][USER_ID] = 'NEW_ROLE'
    foreach ($_POST['role'] as $user_id => $new_role) {
        
        // A. Check if user already has a role row
        $check_sql = "SELECT id FROM role WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $result_check = $check_stmt->get_result();

        if ($result_check->num_rows > 0) {
            // B. UPDATE existing role
            $update_sql = "UPDATE role SET role = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_role, $user_id);
            $update_stmt->execute();
        } else {
            // C. INSERT new role (if they were just a default member with no row)
            $insert_sql = "INSERT INTO role (user_id, role) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("is", $user_id, $new_role);
            $insert_stmt->execute();
        }
    }
    $success_msg = "Roles updated successfully!";
}

// 3. FETCH REAL MEMBERS FROM DB
$sql = "SELECT user.id, user.name, role.role 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        ORDER BY user.id ASC";
$members_result = $conn->query($sql);

// Available roles for the dropdown
$available_roles = ['Admin', 'Manager', 'Member'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="user_page.php" class="header-btn">Back</a>
            Manage Roles
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            
            <?php if(isset($success_msg)): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <?php 
                if ($members_result->num_rows > 0):
                    while($member = $members_result->fetch_assoc()): 
                        // If role is NULL in database, treat as 'Member'
                        $current_role = !empty($member['role']) ? $member['role'] : 'Member';
                ?>
                <div class="dropdowncard">
                    
                    <div class="dropdowncard-left">
                        <span class="staff-name"><?php echo htmlspecialchars($member['name']); ?></span>
                        <span class="staff-id">ID: <?php echo htmlspecialchars($member['id']); ?></span>
                    </div>

                    <div class="dropdowncard-right">
                        <div class="select-wrapper">
                            <select name="role[<?php echo $member['id']; ?>]">
                                <?php foreach($available_roles as $role_opt): ?>
                                    <option value="<?php echo $role_opt; ?>" 
                                        <?php echo ($current_role == $role_opt) ? 'selected' : ''; ?>>
                                        <?php echo $role_opt; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile; 
                else:
                    echo "<p style='text-align:center; color:#666;'>No members found.</p>";
                endif;
                ?>

                <div class="save-btn-container">
                    <button type="submit" name="save_roles" class="btn-save">Save</button>
                </div>

            </form>
        </div> 

        <div class="bottom-nav">
            <a href="#" class="nav-item active">
                <i class="fas fa-user-cog"></i> 
                <span>Manage Roles</span>
            </a>

            <a href="user_page.php" class="nav-item">
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