<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header('Content-Type: application/json');

require_once '../functions/db.php';
require_once '../settings/core.php';

if (!isLoggedIn() || !isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Unauthorized']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;

if ($product_id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Product ID required']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'No image uploaded or upload error']);
    exit;
}

$file = $_FILES['image'];
$allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];

if (!in_array($file['type'], $allowed)) {
    echo json_encode(['status'=>'error','message'=>'Invalid image type. Allowed: JPEG, PNG, GIF, WEBP']);
    exit;
}

// IMPORTANT: Follow lab requirements - store in uploads/u{user_id}/p{product_id}/
$baseUploadDir = __DIR__ . '/../uploads/';

// Security check: ensure uploads directory exists and is the only allowed location
if (!is_dir($baseUploadDir)) {
    echo json_encode(['status'=>'error','message'=>'Uploads directory does not exist. Contact administrator.']);
    exit;
}

// Create user-specific subdirectory: uploads/u{user_id}/
$userDir = $baseUploadDir . 'u' . $user_id . '/';
if (!is_dir($userDir)) {
    if (!mkdir($userDir, 0755, true)) {
        echo json_encode(['status'=>'error','message'=>'Failed to create user directory']);
        exit;
    }
}

// Create product-specific subdirectory: uploads/u{user_id}/p{product_id}/
$productDir = $userDir . 'p' . $product_id . '/';
if (!is_dir($productDir)) {
    if (!mkdir($productDir, 0755, true)) {
        echo json_encode(['status'=>'error','message'=>'Failed to create product directory']);
        exit;
    }
}

// Generate filename with timestamp to avoid conflicts
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'image_' . time() . '.' . $ext;
$destination = $productDir . $filename;

// Security: Verify the resolved path is still inside uploads/
$realDestination = realpath(dirname($destination));
$realBaseUpload = realpath($baseUploadDir);

if ($realDestination === false || strpos($realDestination, $realBaseUpload) !== 0) {
    echo json_encode(['status'=>'error','message'=>'Security violation: Invalid upload path']);
    exit;
}

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $destination)) {
    // Store the RELATIVE path in database (for portability when going live)
    // Path format: uploads/u{user_id}/p{product_id}/image_timestamp.ext
    $relativePath = 'uploads/u' . $user_id . '/p' . $product_id . '/' . $filename;
    
    // Update database with image path
    $sql = "UPDATE products SET product_image = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $relativePath, $product_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status'=>'success',
            'message'=>'Image uploaded successfully',
            'filename'=>$filename,
            'path'=>$relativePath
        ]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Image saved but database update failed']);
    }
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to move uploaded file']);
}