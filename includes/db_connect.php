<?php
$host = 'localhost';
$db   = 'electronic_shop';
$user = 'root'; // Change if your MySQL username is different
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
?> 