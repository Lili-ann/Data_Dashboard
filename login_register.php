<?php
//starting a session to save user information across pages
session_start();
// Load database configuration and connection which is inside the config.php file
require_once 'config.php';

//this handles user registration
if (isset($_POST['register'])) {
    //get data from the registration form
    $name = $_POST['name'];
    $email = $_POST['email'];  
    //hashing the password for security
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    
    //default role for new users
    $role = 'Member'; 
    
    // Get the major data from form through dropdown option
    $major = $_POST['major'];

    //for assigning random avatar to new user as profile picture
    $avatar_list = [
        'avatar1.jpg', 'avatar2.jpg', 'avatar3.jpg', 
        'avatar4.jpg', 'avatar5.jpg', 'avatar6.jpg'
    ];
    //randomize avatar from the list
    $random_key = array_rand($avatar_list);
    $profile_pic = $avatar_list[$random_key];

    //this checks if email already exists in database - club_db
    $check_stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    //show error if email already exists
    if ($check_stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = 'register';
        $check_stmt->close(); 
    } else {
        //ensure email is unique, then proceed with registration
        $check_stmt->close(); 

        //save new user to database in user table
        $user_stmt = $conn->prepare("INSERT INTO user (name, email, password, major, profile_pic) VALUES (?, ?, ?, ?, ?)");
        $user_stmt->bind_param("sssss", $name, $email, $password, $major, $profile_pic);

        if ($user_stmt->execute()) {
            //get user id of new user account
            $new_user_id = $user_stmt->insert_id;

            //assign user role in database in role table
            $role_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
            $role_stmt->bind_param("is", $new_user_id, $role);
            $role_stmt->execute();
            $role_stmt->close();

            //log this registration activity into activity_log table, which displays "this user logged in"
            if (function_exists('logActivity')) {
                //takes record of new user created an account in the system
                logActivity($conn, $new_user_id, $name, 'Register', 'User registered new account.');
            }
        }
        $user_stmt->close();
    }

    //redirect back to home page after registration which is the login page
    header("Location: index.php");
    exit();
}

//this is for handling user login
if (isset($_POST['login'])) {
    //get email and password from login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    //search for user with this email in database in user table
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    //if user found, check if password matches 
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        //verify if password match the hashed password in database
        if (password_verify($password, $user['password'])) {
            $user_id = $user['id'];
            
            //get the user's role (Admin, Manager, or Member) from role table
            $stmt_role = $conn->prepare("SELECT role FROM role WHERE user_id = ?");
            $stmt_role->bind_param("i", $user_id);
            $stmt_role->execute();
            $result_role = $stmt_role->get_result();

            //extracts user's role from database in role table
            if ($result_role->num_rows > 0) {
                $row = $result_role->fetch_assoc();
                $user_role = $row['role'];
            } else {
                //if no role found, default to 'member'
                $user_role = 'member';
            }
            $stmt_role->close();

            //this stores user information in session variables
            $_SESSION['id'] = $user['id'];      
            $_SESSION['name'] = $user['name'];  
            $_SESSION['email'] = $user['email']; 
            $_SESSION['role'] = $user_role; 

            //log this login activity
            if (function_exists('logActivity')) {
                //record that user logged in into activity_log table
                logActivity($conn, $user['id'], $user['name'], 'Login', 'User logged in.');
            }

            // this is for assigning the first page based on role after login
            if (strtolower($user_role) == 'admin') {
                //Admin goes to admin_page.php
                header("Location: admin_page.php");
            } elseif (strtolower($user_role) == 'manager') {
                //manager goes to meeting_list.php
                header("Location: meeting_list.php");
            } else {
                //member goes to user_page.php
                header("Location: user_page.php");
            }
            exit();
        } 
    }
    
    //when Login failed, show this error message
    $_SESSION['login_error'] = "Invalid email or password.";
    $_SESSION['active_form'] = 'login';
    //then redirect back to home page to show error
    header("Location: index.php");
    exit();
}
?>