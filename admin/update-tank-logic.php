<?php
include '../config.php';

if(isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $lvl = intval($_POST['lvl']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $auto = intval($_POST['auto']); // New Field (0 or 1)
    $last = mysqli_real_escape_string($conn, $_POST['last']);
    $next = mysqli_real_escape_string($conn, $_POST['next']);

    // Upsert Logic (Update if exists, Insert if new)
    $check = $conn->query("SELECT id FROM tanks WHERE id = $id");
    
    if ($check->num_rows > 0) {
        $sql = "UPDATE tanks SET 
                name = '$name', 
                water_level = '$lvl', 
                status = '$status', 
                cleaning_method = '$method', 
                autocleaning = '$auto',
                last_cleaned = '$last',
                next_cleaning = '$next'
                WHERE id = $id";
    } else {
        $sql = "INSERT INTO tanks (id, name, water_level, status, cleaning_method, autocleaning, last_cleaned, next_cleaning) 
                VALUES ($id, '$name', '$lvl', '$status', '$method', '$auto', '$last', '$next')";
    }
    
    if($conn->query($sql)) {
        echo "Tank saved successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>