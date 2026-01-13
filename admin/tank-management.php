<?php 
include '../config.php'; 

// Session Protection
if(!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }

// 1. DELETE LOGIC
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tanks WHERE id = $id");
    header("Location: tank-management.php");
    exit();
}

// 2. LIVE STATS
$active_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks")->fetch_assoc()['c'];
$low_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks WHERE water_level < 30")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tank Management | Pure Drop</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* HORIZONTAL STATS BAR */
        .stats-summary-bar {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important; 
            gap: 20px;
            margin-bottom: 30px;
            width: 100%;
        }

        .summary-card {
            background: #1e293b; padding: 20px; border-radius: 12px;
            border: 1px solid #334155; text-align: center;
        }
        .summary-card h3 { color: #0ea5e9; font-size: 1.8rem; margin: 0; }
        .summary-card p { color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 5px; }

        /* TABLE STYLES */
        .management-container { 
            background: #1e293b; border-radius: 12px; padding: 25px; 
            border: 1px solid #334155; overflow-x: auto; 
        }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        th { text-align: left; color: #0ea5e9; padding: 12px; border-bottom: 2px solid #334155; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.85rem; vertical-align: middle; }
        
        .edit-input { background: #0f172a; color: white; border: 1px solid #334155; border-radius: 4px; padding: 8px; width: 100%; }
        
        /* Auto Clean Dropdown Specifics */
        .auto-select { font-weight: bold; }
        option.enabled { color: #10b981; } /* Green */
        option.disabled { color: #ef4444; } /* Red */

        .btn-add { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .btn-save { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; border: 1px solid #0ea5e9; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-right: 5px; }
        .btn-save:hover { background: #0ea5e9; color: white; }
        .btn-delete { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid #ef4444; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .btn-delete:hover { background: #ef4444; color: white; }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <h1>Water Tank Management</h1>

        <div class="stats-summary-bar">
            <div class="summary-card"><p>Active Users</p><h3><?php echo $active_users; ?></h3></div>
            <div class="summary-card"><p>Total Tanks</p><h3><?php echo $total_tanks; ?></h3></div>
            <div class="summary-card"><p style="color: #ef4444;">Low Level</p><h3 style="color: #ef4444;"><?php echo $low_tanks; ?></h3></div>
            <div class="summary-card"><p>Cleaning Today</p><h3>1</h3></div>
            <div class="summary-card"><p style="color: #10b981;">System Uptime</p><h3 style="color: #10b981;">99.8%</h3></div>
        </div>

        <div class="management-container">
            <div class="header-flex">
                <h2 style="margin: 0; color: white;">Manage Water Tanks</h2>
                <button class="btn-add" onclick="location.href='add-tank.php'">+ Add New Tank</button>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 15%;">Tank Name</th>
                        <th style="width: 8%;">Level %</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 12%;">Method</th>
                        <th style="width: 12%;">Auto Clean</th> <th style="width: 12%;">Last Cleaned</th>
                        <th style="width: 12%;">Next Due</th>
                        <th style="width: 14%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM tanks ORDER BY id ASC");
                    
                    if ($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()){
                            $method = $row['cleaning_method'] ?? 'Filtration';
                            $next = $row['next_cleaning'] ?? ''; 
                            $auto = $row['autocleaning'] ?? 0; // Default to 0 (Disabled)

                            echo "<tr>
                                <td>{$row['id']}</td>
                                <td><input type='text' id='name_{$row['id']}' value='{$row['name']}' class='edit-input'></td>
                                <td><input type='number' id='lvl_{$row['id']}' value='{$row['water_level']}' class='edit-input'></td>
                                <td>
                                    <select id='status_{$row['id']}' class='edit-input'>
                                        <option value='Active' ".($row['status']=='Active'?'selected':'').">Active</option>
                                        <option value='Cleaning' ".($row['status']=='Cleaning'?'selected':'').">Cleaning</option>
                                        <option value='Maintenance' ".($row['status']=='Maintenance'?'selected':'').">Maintenance</option>
                                    </select>
                                </td>
                                <td>
                                    <select id='method_{$row['id']}' class='edit-input'>
                                        <option value='Filtration' ".($method=='Filtration'?'selected':'').">Filtration</option>
                                        <option value='Chlorination' ".($method=='Chlorination'?'selected':'').">Chlorination</option>
                                        <option value='Manual Scrubbing' ".($method=='Manual Scrubbing'?'selected':'').">Manual Scrubbing</option>
                                        <option value='UV Treatment' ".($method=='UV Treatment'?'selected':'').">UV Treatment</option>
                                        <option value='Boiling' ".($method=='Boiling'?'selected':'').">Boiling</option>
                                    </select>
                                </td>
                                <td>
                                    <select id='auto_{$row['id']}' class='edit-input auto-select'>
                                        <option value='0' class='disabled' ".($auto==0?'selected':'').">Disabled ✗</option>
                                        <option value='1' class='enabled' ".($auto==1?'selected':'').">Enabled ✓</option>
                                    </select>
                                </td>
                                <td><input type='date' id='last_{$row['id']}' value='{$row['last_cleaned']}' class='edit-input'></td>
                                <td><input type='date' id='next_{$row['id']}' value='{$next}' class='edit-input'></td>
                                <td>
                                    <button class='btn-save' onclick='updateTank({$row['id']})'>Save</button>
                                    <button class='btn-delete' onclick='confirmDelete({$row['id']})'>Delete</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' style='text-align:center;'>No tanks found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function updateTank(id) {
        var data = {
            id: id,
            name: $('#name_' + id).val(),
            lvl: $('#lvl_' + id).val(),
            status: $('#status_' + id).val(),
            method: $('#method_' + id).val(),
            auto: $('#auto_' + id).val(), // Send Auto Clean value
            last: $('#last_' + id).val(),
            next: $('#next_' + id).val()
        };

        $.post('update-tank-logic.php', data, function(response) {
            alert(response); 
            location.reload();
        });
    }

    function confirmDelete(id) {
        if(confirm("Permanently delete this tank?")) {
            window.location.href = "tank-management.php?delete=" + id;
        }
    }
    </script>

</body>
</html>