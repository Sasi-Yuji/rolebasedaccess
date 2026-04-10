<?php
$conn = new mysqli('localhost', 'root', '', 'rolebasedacess');
$users = $conn->query("SELECT * FROM users WHERE name='Duize'")->fetch_assoc();
print_r($users);
$marks = $conn->query("SELECT * FROM marks WHERE student_id=" . $users['id'])->fetch_assoc();
print_r($marks);
