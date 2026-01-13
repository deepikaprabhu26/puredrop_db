<?php 
include '../config.php'; 
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $level = intval($_POST['level']);
    $auto = intval($_POST['auto']); // New Field
    
    $sql = "INSERT INTO tanks (name, location, water_level, status, cleaning_method, autocleaning, last_cleaned, next_cleaning) 
            VALUES ('$name', '$location', '$level', 'Active', 'Filtration', '$auto', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY))";
    
    if($conn->query($sql)){
        echo "<script>alert('Tank added!'); window.location.href='tank-management.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Tank</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #0f172a; margin: 0; font-family: 'Segoe UI'; }
        .form-box { background: #1e293b; padding: 40px; border-radius: 15px; width: 450px; border: 1px solid #334155; }
        label { color: #94a3b8; display: block; margin-bottom: 5px; }
        input, select { width: 100%; padding: 12px; margin-bottom: 20px; background: #0f172a; color: white; border: 1px solid #334155; border-radius: 8px; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 15px; background: #0ea5e9; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .btn-cancel { display: block; text-align: center; color: #94a3b8; margin-top: 15px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2 style="color: #0ea5e9; text-align: center;">Add New Tank</h2>
        <form method="POST">
            <label>Tank Name</label>
            <input type="text" name="name" required placeholder="e.g. Roof Tank A">
            
            <label>Location</label>
            <input type="text" name="location" required placeholder="e.g. Block C">
            
            <label>Initial Level %</label>
            <input type="number" name="level" min="0" max="100" required>
            
            <label>Auto Clean System</label>
            <select name="auto">
                <option value="0">Disabled</option>
                <option value="1">Enabled</option>
            </select>
            
            <button type="submit" class="btn-submit">Add Tank</button>
            <a href="tank-management.php" class="btn-cancel">Cancel</a>
        </form>
    </div>
</body>
</html>