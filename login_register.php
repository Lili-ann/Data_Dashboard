<?php

session_start();
require_once 'config.php';

// --- REGISTER LOGIC ---
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    // Default everyone to 'Member' automatically
    $role = 'Member'; 
    
    $major = $_POST['major'];

    // 1. RANDOM AVATAR LOGIC
    $avatar_list = [
        'avatar1.jpg', 'avatar2.jpg', 'avatar3.jpg', 
        'avatar4.jpg', 'avatar5.jpg', 'avatar6.jpg'
    ];
    $random_key = array_rand($avatar_list);
    $profile_pic = $avatar_list[$random_key];

    // 2. CHECK EMAIL
    $check_stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = 'register';
        $check_stmt->close(); 
    } else {
        $check_stmt->close(); 

        // 3. INSERT USER
        $user_stmt = $conn->prepare("INSERT INTO user (name, email, password, major, profile_pic) VALUES (?, ?, ?, ?, ?)");
        $user_stmt->bind_param("sssss", $name, $email, $password, $major, $profile_pic);

        if ($user_stmt->execute()) {
            // Get the ID of the new user
            $new_user_id = $user_stmt->insert_id;

            // 4. INSERT ROLE
            $role_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
            $role_stmt->bind_param("is", $new_user_id, $role);
            $role_stmt->execute();
            $role_stmt->close();

            // --- [FIX] LOG THE REGISTRATION ---
            if (function_exists('logActivity')) {
                // We log it as the new user themselves doing the action
                logActivity($conn, $new_user_id, $name, 'Register', 'User registered new account.');
            }
        }
        $user_stmt->close();
    }

    header("Location: index.php");
    exit();
}

// --- LOGIN LOGIC ---
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
            
            // Get Role
            $stmt_role = $conn->prepare("SELECT role FROM role WHERE user_id = ?");
            $stmt_role->bind_param("i", $user_id);
            $stmt_role->execute();
            $result_role = $stmt_role->get_result();

            if ($result_role->num_rows > 0) {
                $row = $result_role->fetch_assoc();
                $user_role = $row['role'];
            } else {
                $user_role = 'member'; // Fallback
            }
            $stmt_role->close();

            $_SESSION['id'] = $user['id'];      
            $_SESSION['name'] = $user['name'];  
            $_SESSION['email'] = $user['email']; 
            $_SESSION['role'] = $user_role; 

            // --- [FIX] LOG THE LOGIN ---
            if (function_exists('logActivity')) {
                logActivity($conn, $user['id'], $user['name'], 'Login', 'User logged in.');
            }

            if (strtolower($user_role) == 'admin') {
                header("Location: admin_page.php");
            } elseif (strtolower($user_role) == 'manager') {
                header("Location: meeting_list.php");
            } else {
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