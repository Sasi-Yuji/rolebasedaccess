<?php
$mysqli = new mysqli("localhost", "root", "", "rolebasedacess");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Clean up first
$mysqli->query("DELETE FROM users WHERE email = 'superadmin@erp.com'");

// Use single quotes for the hash string to avoid PHP variable interpolation
$password_hash = '$2y$10$FDLEJXdyFF.rgF4Mt5dNL.BX2fD5zyjd8INSYFg41FPxEy05T1tH6';

$stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$name = 'Super Admin';
$email = 'superadmin@erp.com';
$role = 'superadmin';

$stmt->bind_param("ssss", $name, $email, $password_hash, $role);

if ($stmt->execute()) {
    echo "User seeded successfully using MySQLi.\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$mysqli->close();
?>
