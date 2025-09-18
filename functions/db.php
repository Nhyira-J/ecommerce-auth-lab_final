<?php
// functions/db.php
// Edit credentials if needed
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';        // change if you use a password
$DB_NAME = 'shoppn';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
