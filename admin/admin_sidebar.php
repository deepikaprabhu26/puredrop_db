<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <h2>Pure Drop</h2>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard Overview</a></li>
        <li><a href="user-records.php" class="<?php echo ($current_page == 'user-records.php') ? 'active' : ''; ?>">User Records</a></li>
        <li><a href="tank-management.php" class="<?php echo ($current_page == 'tank-management.php') ? 'active' : ''; ?>">Tank Management</a></li>
        <li><a href="water-analytics.php" class="<?php echo ($current_page == 'water-analytics.php') ? 'active' : ''; ?>">Water Analytics</a></li>
        
        <li class="logout-link">
            <a href="logout.php" style="color: #ef4444; font-weight: bold;">Logout</a>
        </li>
    </ul>
</div>