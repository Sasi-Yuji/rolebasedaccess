<?php
$conn = new mysqli('localhost', 'root', '', 'attendance_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$result = $conn->query("SELECT * FROM answer_sheets");
while($row = $result->fetch_assoc()) {
    print_r($row);
}
