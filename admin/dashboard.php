<?php 
include '../config.php'; 

// Session Protection
if(!isset($_SESSION['admin'])) { 
    header("Location: login.php"); 
    exit(); 
}

// LIVE DATA QUERIES (Chatbot Removed)
$active_users = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$total_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks")->fetch_assoc()['c'];
$low_tanks = $conn->query("SELECT COUNT(*) as c FROM tanks WHERE water_level < 30")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Pure Drop</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* FORCE HORIZONTAL LAYOUT FOR STATS */
        .stats-summary-bar {
            display: grid !important;
            /* Creates 5 equal columns side-by-side */
            grid-template-columns: repeat(5, 1fr) !important; 
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
            /* Ensures contents are centered */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .summary-card p {
            color: #94a3b8;
            font-size: 0.75rem;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .summary-card h3 {
            color: #0ea5e9;
            font-size: 1.8rem;
            margin: 0;
        }

        /* Chart Layout */
        .row { display: flex; gap: 20px; margin-bottom: 30px; }
        .chart-box {
            background: #1e293b; border-radius: 12px; padding: 20px;
            border: 1px solid #334155; flex: 1; height: 320px;
        }
        
        /* Table Layout */
        .data-container {
            background: #1e293b; border-radius: 12px; padding: 25px;
            border: 1px solid #334155;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; color: #0ea5e9; padding: 12px; border-bottom: 1px solid #334155; }
        td { padding: 12px; border-bottom: 1px solid #334155; color: #cbd5e1; font-size: 0.9rem; }
    </style>
</head>
<body>

    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <h1 style="margin-bottom: 30px;">Admin Dashboard Overview</h1>
        
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
                <h3>1</h3>
            </div>
            <div class="summary-card">
                <p style="color: #10b981;">System Uptime</p>
                <h3 style="color: #10b981;">99.8%</h3>
            </div>
        </div>

        <div class="row">
            <div class="chart-box">
                <h3 style="margin-top:0; color:white; font-size:1.1rem;">Water Levels Overview</h3>
                <canvas id="levelChart"></canvas>
            </div>
            <div class="chart-box">
                <h3 style="margin-top:0; color:white; font-size:1.1rem;">Daily Water Usage</h3>
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <div class="data-container">
            <h3 style="margin-top:0; color:white;">Recent User Activity</h3>
            <table>
                <tr><th>Name</th><th>Role</th><th>Class</th><th>Time</th></tr>
                <?php
                $res = $conn->query("SELECT * FROM users ORDER BY login_time DESC LIMIT 5");
                while($row = $res->fetch_assoc()){
                    echo "<tr>
                        <td>{$row['fullname']}</td>
                        <td><span style='background:rgba(14,165,233,0.1); color:#0ea5e9; padding:2px 8px; border-radius:4px; font-size:0.8rem;'>{$row['role']}</span></td>
                        <td>{$row['class_name']}</td>
                        <td>{$row['login_time']}</td>
                    </tr>";
                } ?>
            </table>
        </div>
    </div>

    <script>
    // 1. Water Levels Chart
    new Chart(document.getElementById('levelChart'), {
        type: 'bar',
        data: {
            labels: ['Main', 'Library', 'Hostel A', 'Lab', 'Audi', 'Gym'],
            datasets: [{
                data: [85, 60, 45, 20, 65, 55],
                backgroundColor: ['#10b981', '#f59e0b', '#f59e0b', '#ef4444', '#10b981', '#10b981'],
                borderRadius: 4
            }]
        },
        options: { 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, max: 100, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }, x: { grid: { display: false }, ticks: { color: '#94a3b8' } } }
        }
    });

    // 2. Water Usage Chart
    new Chart(document.getElementById('usageChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Liters',
                data: [8000, 9200, 8500, 10000, 7800, 8900, 9500],
                borderColor: '#0ea5e9',
                backgroundColor: 'rgba(14, 165, 233, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }, x: { grid: { display: false }, ticks: { color: '#94a3b8' } } }
        }
    });
    </script>

</body>
</html>