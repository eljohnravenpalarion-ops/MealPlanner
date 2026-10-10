<?php
$host = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_NAME') ?: 'meal_planner';
$dbPort = (int) (getenv('DB_PORT') ?: 3306);

$conn = mysqli_connect(
    $host,
    $dbUser,
    $dbPassword,
    $dbName,
    $dbPort
);

if (!$conn) {
    error_log('Database connection failed: ' . mysqli_connect_error());
    die('Database connection failed.');
}

mysqli_set_charset($conn, 'utf8mb4');
