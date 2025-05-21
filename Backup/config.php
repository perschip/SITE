<?php
// config.php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'tscadmin');
define('DB_PASS', '$Yankees100');
define('DB_NAME', 'tsc_website');

// eBay API Configuration
define('EBAY_OAUTH_TOKEN', 'v^1.1#i^1#I^3#p^1#r^0#f^0#t^H4sIAAAAAAAA/+VYe2wURRi/a69tCi0kSAF55VggGvD2Zvfu9u42vYPrA7jkuF65o2BVcB9zdOne7rEz2/b4Q2o1EJUEg6+koUjARyJq0GqqMWDsX6IkJGpEgrFGJUJSFR9gIgm6uz3KtRJevcQm3j+X+eabb36/33zfzOyA7vLKZTvX7Pyz2l5RcqAbdJfY7dRUUFletnxaacncMhsocLAf6F7S7egpPVeLuIycZddBlFUVBJ1dGVlBrGUMEbqmsCqHJMQqXAYiFgtsMrI2xtIkYLOailVBlQlntCFEeASK8gNGhEHggV7Ga1iVqzFTaojgOU6EvJAWGMpL0zxj9COkw6iCMKfgEEED2ucCPhdFpagAS3lYGpB0gG4lnC1QQ5KqGC4kIMIWXNYaqxVgvTFUDiGoYSMIEY5GViWbItGGxniq1l0QK5zXIYk5rKOxrXpVhM4WTtbhjadBljeb1AUBIkS4wyMzjA3KRq6CuQP4ltRehvGmOcHL+IBA0zBQFClXqVqGwzfGYVok0ZW2XFmoYAnnbqaooQa/FQo434obIaINTvOvWedkKS1BLUQ01kXujyQSRDjB6XLCAOlKJevrmlKuxLoGlwCgn2J4kXNB4AdpI7ny04zEyos8bp56VRElUzLkjKu4DhqY4XhlQIEyhlOT0qRF0tjEU+BHg6sK+n2t5pKOrKGO2xRzVWHGkMFpNW+u/+hojDWJ1zEcjTC+wxIoRHDZrCQS4zutTMwnTxcKEW0YZ1m3u7Ozk+z0kKq2xU0DQLk3ro0lhTaY4QjL16x101+6+QCXZFERoDESSSzOZQ0sXUamGgCULUTYR/s8AU9e97GwwuOt/zIUcHaPrYdi1YcfCj6BYTxBkWKClMAXoz7C+RR1mzggz+VcGU5rhzgrcwJ0CUae6RmoSSLr8aVpTyANXSITTLu8wXTaxftExkWlIQQQ8rwQDPx/yuRWEz0JBQ3iImV6kbJ8W2M0F29oXl2Xiq4VNV+iHnr8EtccX84n3EF/DMXiq7MN2nYpUtccutVauC75elkylEkZ8xdLALPWiyPCGhVhKE6IXlJQszChypKQm1wL7NHEBKfhXBLKsmGYEMlINhst1k5dJHq3tUncGetink//ydl0XVbITNjJxcocj4wAXFYizdOHFNSMW+WMa4fbrHXDvNlCPSHeknFnnVSsDZIjbCVx5LJJWpRJ1CGQGkSqrhn3bLLJvH2l1HaoGKcZ1lRZhloLNeFqzmR0zPEynGxlXYQEl7hJdtRSfi8TDPiNQRPiJVgH6ebJtiUVbyN2rLjNC7V77Md92Gb9qB77IOixHyux20EtWEotBovKS9c7SqvmIglDUuLSJJK2KMY3qwbJdpjLcpJWcpft5LSY+Oia2MVuXh/Y8MeKgK264G3hwENgzujrQmUpNbXgqQHMv9ZTRk2fXU37gI+iqADloUErWHyt10HNcsx8xTujuX1wxe8tww+mzk6/suTk3R/4QfWok91eZnP02G2HHx9GXXtPL2Fe+mrerJnD+ovnhk9c/umTeTPqy18+3z9/Tu9CvPy+heeZpUO0stu94/hCrfLz0/FT8t/7Xlv/SB/9YaJxwZVBur+l4r3nhk5/1vrqx3VfPH35Gzz3t/4zMx6+VDGzv3zKDtvZDT8Gq3bXXhxeNHist+zIUN3Kt16wDb29/rsa2/FZtdFf9tM1sz3vy6/vGXi35mD7tt2p9gcCP/y8/0LHMlz1zlFH37Pf73zKfvjExjcGID0Qb23dS5+qOfJr36ae7R8909d4cOPF+NLYmYq2Tb2HFh3t6Nv37Yldzx/661NU9fV2Vcr12GcvuPfJ8vlbe58IklM63ry0Kz10z5ePXVh2eZ+48ujIWv4Dy5lazfURAAA=');
define('EBAY_CLIENT_ID', 'PaulPers-TSCBOT-PRD-c0e716bda-e070fe46');
define('EBAY_DEV_ID', '25a2a87a-6273-4022-855d-66da51ab543e');
define('EBAY_CLIENT_SECRET', 'PRD-0e716bdad618-d1f5-45d0-8ff1-53af');
define('EBAY_STORE_URL', 'https://www.ebay.com/str/tristatecardsnj');

// Whatnot Configuration
define('WHATNOT_CHANNEL', 'tscardbreaks');

// Site Configuration
define('SITE_NAME', 'Tri-State Cards NJ');
define('SITE_URL', 'https://tristatecards.com'); // Change this to your actual domain
define('THEME_MODE_COOKIE', 'theme_mode');

// Database connection with UTF8MB4 support for emoji
try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, $options);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Helper Functions
function sanitize($data) {
    if ($data === null) return null;
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit();
    }
}

function getThemeMode() {
    return isset($_COOKIE[THEME_MODE_COOKIE]) ? $_COOKIE[THEME_MODE_COOKIE] : 'light';
}

// Logging function
function logError($message, $context = []) {
    $logDir = __DIR__ . '/logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' | Context: ' . json_encode($context) : '';
    $logEntry = "[{$timestamp}] {$message}{$contextStr}\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Error reporting (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    logError("PHP Error: {$errstr} in {$errfile} on line {$errline}", [
        'errno' => $errno,
        'errstr' => $errstr,
        'errfile' => $errfile,
        'errline' => $errline
    ]);
    return false; // Let PHP handle the error normally
});

// Custom exception handler
set_exception_handler(function($exception) {
    logError("Uncaught Exception: " . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
});
?>