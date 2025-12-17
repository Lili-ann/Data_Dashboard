<?php

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    // $role = $_POST['role'];
    $major = $_POST['major'];

    // 1. RANDOM AVATAR LOGIC (Updated for 6 avatars)
    $avatar_list = [
        'avatar1.jpg', 
        'avatar2.jpg', 
        'avatar3.jpg', 
        'avatar4.jpg', 
        'avatar5.jpg', 
        'avatar6.jpg'
    ];
    
    // Pick one random key
    $random_key = array_rand($avatar_list);
    // Get the filename (e.g., 'avatar6.png')
    $profile_pic = $avatar_list[$random_key];

    // 1. CHECK EMAIL (Use a distinct variable: $check_stmt)
    $check_stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = 'register';
        $check_stmt->close(); // Close the check tool
    } else {
        $check_stmt->close(); // Close the check tool before moving on

       // 3. INSERT USER (With profile_pic)
        $user_stmt = $conn->prepare("INSERT INTO user (name, email, password, major, profile_pic) VALUES (?, ?, ?, ?, ?)");
        // Note: "sssss" = 5 strings (name, email, password, major, pic)
        $user_stmt->bind_param("sssss", $name, $email, $password, $major, $profile_pic);

        if ($user_stmt->execute()) {
            // Get the new ID
            $new_user_id = $user_stmt->insert_id;

            // 3. INSERT ROLE (Use a distinct variable: $role_stmt)
            // $role_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
            // $role_stmt->bind_param("is", $new_user_id, $role);
            // $role_stmt->execute();
            
            // // Close the role tool
            // $role_stmt->close();
        }
        // Close the user tool
        $user_stmt->close();
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $user_id = $user['id'];
            $stmt_role = $conn->prepare("SELECT role FROM role WHERE user_id = ?");
            $stmt_role->bind_param("i", $user_id);
            $stmt_role->execute();
            $result_role = $stmt_role->get_result();

            // Default to 'user' if something goes wrong, otherwise grab the role from DB
            if ($result_role->num_rows > 0) {
                $row = $result_role->fetch_assoc();
                $user_role = $row['role'];
            } else {
                $user_role = 'member';
            }
            $stmt_role->close();

            // 4. Set Session Variables
            $_SESSION['id'] = $user['id'];      
            $_SESSION['name'] = $user['name'];  
            $_SESSION['email'] = $user['email']; 
            $_SESSION['role'] = $user_role; 

             if (strtolower($user_role) == 'admin') {
                header("Location: manage_roles.php");
            
            } elseif (strtolower($user_role) == 'manager') {
                header("Location: meeting_list.php");
            
            } else {
                // Default for 'member', 'officer', or 'user'
                header("Location: user_page.php");
            }
            
            exit();
        } 
    }
    $_SESSION['login_error'] = "Invalid email or password.";
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}

?>
