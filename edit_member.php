<?php
session_start();
require_once 'config.php';

// 1. SECURITY: Only Admins allowed
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. GET ID
if (!isset($_GET['id'])) {
    header("Location: user_page.php");
    exit();
}
$edit_id = $_GET['id'];

// 3. FETCH DATA (User + Role)
$sql = "SELECT user.*, role.role FROM user LEFT JOIN role ON user.id = role.user_id WHERE user.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $edit_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}

// Default values if empty
$current_role = !empty($user['role']) ? $user['role'] : 'Member';
$pic_name = !empty($user['profile_pic']) ? $user['profile_pic'] : 'avatar1.jpg';
$img_src = 'uploads/' . $pic_name; 

// 4. HANDLE UPDATE FORM SUBMISSION
if (isset($_POST['update_member'])) {
    $new_name = $_POST['name'];
    $new_major = $_POST['major'];
    $new_role = $_POST['role'];
    
    // A. Update User Info (Name & Major)
    $stmt = $conn->prepare("UPDATE user SET name = ?, major = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_name, $new_major, $edit_id);
    $stmt->execute();
    $stmt->close();

    // B. Update Role (Check if exists first)
    $check_stmt = $conn->prepare("SELECT id FROM role WHERE user_id = ?");
    $check_stmt->bind_param("i", $edit_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing
        $r_stmt = $conn->prepare("UPDATE role SET role = ? WHERE user_id = ?");
        $r_stmt->bind_param("si", $new_role, $edit_id);
    } else {
        // Insert new
        $r_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
        $r_stmt->bind_param("is", $edit_id, $new_role);
    }
    $r_stmt->execute();
    $r_stmt->close();
    
    // Redirect back to list
    header("Location: user_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">

    <div class="overlay"></div>

    <div class="user-container">
        
        <div class="header">
            <a href="user_page.php" class="header-btn">Back</a>
            Edit Member
            <div class="header-btn" style="width: 60px;"></div>
        </div>

        <div class="content">
            
            <div class="profile-section" style="justify-content: center; margin-bottom: 30px;">
                <div class="profile-pic-box">
                    <img src="<?php echo htmlspecialchars($img_src); ?>" 
                        alt="Profile" 
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                </div>
                 <div class="profile-info">
                    <p style="font-size: 18px;"><strong>ID:</strong> <?php echo $user['id']; ?></p>
                 </div>
            </div>

            <form method="POST">
                
                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Full Name</span>
                    </div>
                    <div class="dropdowncard-right" style="flex: 1; display: flex; justify-content: flex-end;">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" 
                               style="border: none; border-bottom: 2px solid #8B0000; background: rgba(0,0,0,0.03); 
                                      text-align: right; font-weight: bold; font-size: 16px; outline: none; 
                                      width: 100%; max-width: 200px; padding: 5px; color: #333;">
                        
                        <i class="fas fa-pen" style="font-size: 12px; color: #8B0000; margin-left: 8px; margin-top: 8px;"></i>
                    </div>
                </div>

                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Major</span>
                    </div>
                    <div class="dropdowncard-right">
                        <div class="select-wrapper" style="width: 200px;"> 
                            <select name="major">
                                <option value="software engineering" <?php echo ($user['major'] == 'software engineering') ? 'selected' : ''; ?>>Software Eng.</option>
                                <option value="data science" <?php echo ($user['major'] == 'data science') ? 'selected' : ''; ?>>Data Science</option>
                                <option value="digital business" <?php echo ($user['major'] == 'digital business') ? 'selected' : ''; ?>>Digital Business</option>
                                <option value="information technology" <?php echo ($user['major'] == 'information technology') ? 'selected' : ''; ?>>Information Tech</option>
                                <option value="accounting" <?php echo ($user['major'] == 'accounting') ? 'selected' : ''; ?>>Accounting</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="dropdowncard">
                    <div class="dropdowncard-left">
                        <span class="staff-name">Role</span>
                    </div>
                    <div class="dropdowncard-right">
                        <div class="select-wrapper">
                            <select name="role">
                                <option value="Member" <?php echo ($current_role == 'Member') ? 'selected' : ''; ?>>Member</option>
                                <option value="Manager" <?php echo ($current_role == 'Manager') ? 'selected' : ''; ?>>Manager</option>
                                <option value="Admin" <?php echo ($current_role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="save-btn-container">
                    <button type="submit" name="update_member" class="btn-save">Save Changes</button>
                </div>

            </form>

        </div> 

        <div class="bottom-nav">
            <a href="user_page.php" class="nav-item">
                <i class="fa-solid fa-backward"></i> 
                <span>Cancel</span>
            </a>
        </div>

    </div>

</body>
</html>