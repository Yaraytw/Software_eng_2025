<?php
/**
 * Common Header Include File
 */
session_start();
require_once __DIR__ . '/../config/db_config.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['memberID']) && !empty($_SESSION['memberID']);
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'memberID' => $_SESSION['memberID'],
        'username' => $_SESSION['username'] ?? '',
        'fullName' => $_SESSION['fullName'] ?? ''
    ];
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

// Display success message
function showSuccess($message) {
    return '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

// Display error message
function showError($message) {
    return '<div class="alert alert-error">' . htmlspecialchars($message) . '</div>';
}
?>
