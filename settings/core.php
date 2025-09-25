<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// For header redirection
ob_start();

/**
 * Function to check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Function to get user ID from session
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Function to check if user has a specific role (by number)
 * @param int $role
 * @return bool
 */
function hasRole($role) {
    return isset($_SESSION['role']) && (int)$_SESSION['role'] === (int)$role;
}

/**
 * Function to check if user is admin
 * @return bool
 */
function isAdmin() {
    return hasRole(1); // 1 = admin
}
?>
