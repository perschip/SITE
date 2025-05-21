<?php
// includes/functions.php

/**
 * Sanitize input data
 *
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format date
 *
 * @param string $date Date string
 * @param string $format Format string (default: 'F j, Y')
 * @return string Formatted date
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Create slug from title
 *
 * @param string $string Input string
 * @return string URL-friendly slug
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

/**
 * Truncate text to specified length
 *
 * @param string $text Input text
 * @param int $length Maximum length
 * @param string $append Text to append if truncated (default: '...')
 * @return string Truncated text
 */
function truncateText($text, $length, $append = '...') {
    $text = strip_tags($text);
    
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Get active navigation class
 *
 * @param string $page Current page
 * @param string $section Navigation section
 * @return string CSS class if active, empty string otherwise
 */
function isActive($page, $section) {
    return ($page === $section) ? 'active' : '';
}

/**
 * Get meta tags for SEO
 *
 * @param string $title Page title
 * @param string $description Meta description
 * @param string $keywords Meta keywords
 * @return string HTML meta tags
 */
function getMetaTags($title, $description = '', $keywords = '') {
    $tags = '<title>' . $title . ' - ' . SITE_NAME . '</title>' . "\n";
    $tags .= '<meta name="description" content="' . $description . '">' . "\n";
    
    if (!empty($keywords)) {
        $tags .= '<meta name="keywords" content="' . $keywords . '">' . "\n";
    }
    
    return $tags;
}

/**
 * Check if user is logged in
 *
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Redirect to URL
 *
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Debug helper function
 *
 * @param mixed $data Data to debug
 * @param bool $die Whether to die after output (default: true)
 * @return void
 */
function debug($data, $die = true) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}