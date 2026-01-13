<?php 
include '../config.php'; 

// Session Protection
if(!isset($_SESSION['admin'])) { 
    header("Location: login.php"); 
    exit(); 
}

// =================================================================
// 1. DATABASE FIX: Ensure 'login_time' stores Date AND Time
// =================================================================
// This command changes the column type to DATETIME so it captures hours/minutes
$conn->query("ALTER TABLE users MODIFY COLUMN login_time DATETIME DEFAULT CURRENT_TIMESTAMP");

// =================================================================
// 2. LIVE DATA QUERIES
// =================================================================
$active_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks")->fetch_assoc()['c'];
$low_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks WHERE water_level < 30")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Records | Pure Drop</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* FORCE HORIZONTAL LAYOUT (Inline to override any caching) */
        .stats-summary-bar {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important; /* 5 Columns */
            gap: 20px;
            margin-bottom: 30px;
            width: 100%;
        }

        .summary-card {
            background: #1e293b;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #334155;
            text-align: center;
        }

        .summary-card h3 { color: #0ea5e9; font-size: 1.8rem; margin: 0; }
        .summary-card p { color: #94a3b8; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 5px; font-weight: bold; }

        /* TABLE STYLES */
        .data-container {
            background: #1e293b;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #334155;
        }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #0ea5e9; padding: 15px; border-bottom: 2px solid #334155; font-size: 0.9rem; }
        td { padding: 15px; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; }

        /* BADGES */
        .role-badge { padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .role-student { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
        .role-staff { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .role-admin { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <h1 style="margin-bottom: 30px;">User Access Records</h1>

        <div class="stats-summary-bar">
            <div class="summary-card">
                <p>Active Users</p>
                <h3><?php echo $active_users; ?></h3>
            </div>
            <div class="summary-card">
                <p>Total Tanks</p>
                <h3><?php echo $total_tanks; ?></h3>
            </div>
            <div class="summary-card">
                <p style="color: #ef4444;">Low Level</p>
                <h3 style="color: #ef4444;"><?php echo $low_tanks; ?></h3>
            </div>
            <div class="summary-card">
                <p>Cleaning Today</p>
                <h3>3</h3>
            </div>
            <div class="summary-card">
                <p style="color: #10b981;">System Uptime</p>
                <h3 style="color: #10b981;">99.8%</h3>
            </div>
        </div>

        <div class="data-container">
            <div style="margin-bottom: 20px;">
                <h3 style="margin: 0; color: white;">Registered Users & Logs</h3>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Role</th>
                        <th>Last Login (Date & Time)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch Users from Database
                    $res = $conn->query("SELECT * FROM users ORDER BY login_time DESC");
                    
                    if($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()){
                            // Badge Color Logic
                            $roleClass = 'role-student';
                            if($row['role'] == 'Staff') $roleClass = 'role-staff';
                            if($row['role'] == 'Admin') $roleClass = 'role-admin';

                            // Format Date and Time
                            $raw_time = $row['login_time'];
                            // Converts '2026-01-04 14:30:00' to '04-Jan-2026 02:30:00 PM'
                            $formatted_time = date("d-M-Y h:i:s A", strtotime($raw_time));

                            echo "<tr>
                                <td>#{$row['id']}</td>
                                <td style='font-weight: 600; color: #f8fafc;'>{$row['fullname']}</td>
                                <td><span class='role-badge $roleClass'>{$row['role']}</span></td>
                                <td>{$formatted_time}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No user logs found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>