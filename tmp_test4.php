<?php
$conn = new mysqli('localhost', 'root', '', 'rolebasedacess');
$users = $conn->query("SELECT * FROM users WHERE id=12")->fetch_assoc();
print_r($users);
