<?php
require_once '../config/db.php';

$database = new Database();
$conn = $database->connect();

// Get table structure
$sql = "DESCRIBE Property";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Property Table Structure:</h2>";
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
    echo "</pre>";
} else {
    echo "Error getting table structure: " . $conn->error;
}

// Also show create table statement
$sql = "SHOW CREATE TABLE Property";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Create Table Statement:</h2>";
    echo "<pre>";
    $row = $result->fetch_assoc();
    echo $row['Create Table'];
    echo "</pre>";
} else {
    echo "Error getting create table statement: " . $conn->error;
}

$conn->close();
?> 