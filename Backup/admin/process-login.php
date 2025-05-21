<?php
// admin/process-login.php

// Start session at the very beginning
session_start();

// Include config
require_once '../config.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

try {
    // Get form data
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Basic validation
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=1&message=Please enter both username and password');
        exit();
    }
    
    // Check credentials
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
            
            // Store token in database
            $stmt = $pdo->prepare("INSERT INTO admin_sessions (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, date('Y-m-d H:i:s', time() + (86400 * 30))]);
        }
        
        // Log successful login
        error_log("Admin login successful: " . $username);
        
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Login failed
        error_log("Admin login failed: " . $username);
        
        // Add delay to prevent brute force
        sleep(2);
        
        header('Location: login.php?error=1');
        exit();
    }
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: login.php?error=1&message=Database error. Please try again.');
    exit();
} catch (Exception $e) {
    error_log("Login exception: " . $e->getMessage());
    header('Location: login.php?error=1&message=An error occurred. Please try again.');
    exit();
}
?>