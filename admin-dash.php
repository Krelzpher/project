<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLD · Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="app-wrapper">
        <aside class="sidebar">
            <div class="logo-area"><i class="fas fa-qrcode"></i><span>KLD-<span
                        style="color: var(--yellow-highlight)">A</span></span></div>
            <div class="nav-links" id="sidebarNav">
                <div class="nav-item active" data-page="admin-dash"><i class="fas fa-user-shield"></i> Dashboard</div>
                <div class="nav-item" onclick="navigateTo('profile')"><i class="fas fa-user-edit"></i> Profile</div>
            </div>
        </aside>

        <div id="overlay" class="overlay"></div>

        <main class="main-content" id="mainContent">
            <div class="page active-page" id="admin-dash">

                <header class="dashboard-header">
                    <div class="header-title">
                        <h3 style="color: var(--yellow-highlight)">Admin</h3>
                        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
                    </div>

                    <div class="header-icon-tray">
                        <div class="icon-btn" onclick="showNotification('Messaging system coming soon!', 'info')"
                            title="Messages"><i class="fas fa-envelope"></i></div>
                        <div class="icon-btn" onclick="showNotification('Notification system coming soon!', 'info')"
                            title="Notifications"><i class="fas fa-bell" style="color: var(--warning)"></i></div>
                        <div class="icon-btn icon-logout" onclick="confirmLogout()" title="Logout"><i
                                class="fas fa-sign-out-alt"></i></div>
                    </div>
                </header>

                <div class="glass-card" style="padding:1.5rem; margin-bottom: 2rem;">
                    <h3 style="color: var(--deep-green); margin-bottom: 1rem;">Class Rosters</h3>
                    <select id="admin-course-select" onchange="loadCourseStudents()"
                        style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); margin-bottom: 1rem; width: 100%; max-width: 300px; background: white;">
                        <option value="">-- Select a Course --</option>
                    </select>
                    <table class="table-modern" id="admin-roster-table" style="display: none; margin-top: 1rem;">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody id="roster-tbody"></tbody>
                    </table>
                </div>

                <div class="glass-card" style="padding:1.5rem;">
                    <h3 style="color: var(--deep-green); margin-bottom: 1rem;">User Management</h3>
                    <table class="table-modern" id="admin-users-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody"></tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>
    <script src="script.js"></script>
    <script>
        window.addEventListener('load', () => {
            loadAdminUsers();
            loadAdminCourses();
        });
    </script>
</body>

</html>