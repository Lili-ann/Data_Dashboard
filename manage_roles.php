<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// MOCK DATA 
$members = [
    ['id' => '22018390', 'name' => 'Staff 1', 'role' => 'Admin'],
    ['id' => '22018391', 'name' => 'Staff 2', 'role' => 'Manager'],
    ['id' => '22018392', 'name' => 'Staff 3', 'role' => 'Manager'],
    ['id' => '22018393', 'name' => 'Staff 4', 'role' => 'Member'],
    ['id' => '22018394', 'name' => 'Staff 5', 'role' => 'Member'], 
]; 

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
            <a href="admin_page.php" class="header-btn">Back</a>
            Manage Roles
            <a href="logout.php" class="header-btn logout">Logout</a>
        </div>

        <div class="content">
            <form action="save_roles.php" method="POST">
                
                <?php foreach($members as $index => $member): ?>
                <div class="dropdowncard">
                    
                    <div class="dropdowncard-left">
                        <span class="staff-name"><?php echo $member['name']; ?></span>
                        <span class="staff-id">ID: <?php echo $member['id']; ?></span>
                    </div>

                    <div class="dropdowncard-right">
                        <div class="select-wrapper">
                            <select name="role[<?php echo $member['id']; ?>]">
                                <?php foreach($available_roles as $role): ?>
                                    <option value="<?php echo $role; ?>" 
                                        <?php echo ($member['role'] == $role) ? 'selected' : ''; ?>>
                                        <?php echo $role; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="save-btn-container">
                    <button type="submit" class="btn-save">Save</button>
                </div>

            </form>
        </div> 

        <div class="bottom-nav">
            <a href="#" class="nav-item">
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