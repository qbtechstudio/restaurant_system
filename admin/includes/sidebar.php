<?php

$currentPage = basename($_SERVER["PHP_SELF"]);

?>

<!-- ================= SIDEBAR ================= -->
<aside class="sidebar" id="sidebar">

    <div class="brand">
        <div class="brand-icon">
            <i class="bi bi-shop"></i>
        </div>

        <div>
            <h4>Bite <span>&</span> Bliss</h4>
            <small>ADMIN PANEL</small>
        </div>

        <button class="close-sidebar" id="closeSidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-content">

        <p class="sidebar-label">MAIN MENU</p>

        <a href="dashboard.php" class="sidebar-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i>
            <span>Dashboard</span>
        </a>

        <a href="orders.php" class="sidebar-link <?= in_array($currentPage, ['orders.php', 'order_view.php', 'order_edit.php']) ? 'active' : '' ?>">
            <i class="bi bi-receipt"></i>
            <span>Orders</span>
        </a>

        <a href="menu.php" class="sidebar-link <?= in_array($currentPage, ['menu.php', 'menu_add.php', 'menu_edit.php']) ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>
            <span>Menu Management</span>
        </a>

        <a href="reservations.php" class="sidebar-link <?= $currentPage === 'reservations.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i>
            <span>Reservations</span>
        </a>

        <a href="users.php" class="sidebar-link <?= $currentPage === 'users.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>Customers</span>
        </a>

        <a href="contact_messages.php" class="sidebar-link <?= $currentPage === 'contact_messages.php' ? 'active' : '' ?>">
            <i class="bi bi-envelope"></i>
            <span>Contact Messages</span>
        </a>

        <p class="sidebar-label management-label">MANAGEMENT</p>

        <a href="reports.php" class="sidebar-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>Reports</span>
        </a>

        <a href="staff.php" class="sidebar-link">
            <i class="bi bi-person-badge"></i>
            <span>Staff</span>
        </a>

        <a href="settings.php" class="sidebar-link <?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>

    </div>

    <div class="sidebar-bottom">

        <button type="button" class="sidebar-link theme-toggle" id="themeToggle">
            <i class="bi bi-sun"></i>
            <span>Light Mode</span>
        </button>

        <a href="../index.php" class="sidebar-link">
            <i class="bi bi-house-door"></i>
            <span>Back to Website</span>
        </a>

        <a href="/restaurant_system/auth/logout.php" class="sidebar-link logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>

    </div>

</aside>