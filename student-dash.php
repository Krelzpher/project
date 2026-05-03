<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLD · Student Dashboard</title>
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
                <div class="nav-item active" data-page="student-dash"><i class="fas fa-graduation-cap"></i> Dashboard
                </div>
                <div class="nav-item" onclick="navigateTo('profile')"><i class="fas fa-user-edit"></i> Profile</div>
            </div>
        </aside>

        <div id="overlay" class="overlay"></div>

        <main class="main-content" id="mainContent">
            <div class="page active-page" id="student-dash">

                <header class="dashboard-header">
                    <div class="header-title">
                        <h3 style="color: var(--yellow-highlight)">Student</h3>
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

                <div class="glass-card" style="padding: 2rem; margin-bottom: 1rem;">
                    <h3 style="color: var(--deep-green)">Enroll in a Course</h3>
                    <p style="margin-bottom: 20px; color: var(--text-secondary); font-size: 0.9rem;">Enter the exact
                        details provided by your teacher to join the class.</p>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <input type="text" id="enroll-code" placeholder="Course Code"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 1;">
                        <input type="text" id="enroll-name" placeholder="Course Name"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 1;">
                        <input type="text" id="enroll-section" placeholder="Section"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 1;">
                        <button class="btn btn-primary" id="enroll-btn" onclick="handleEnrollCourse()"
                            style="padding: 12px; border: none; border-radius: 6px; background: var(--main-green, #2ecc71); color: white; cursor: pointer; transition: 0.3s;">Join
                            Course</button>
                    </div>
                </div>

                <div class="glass-card"
                    style="background: var(--glass-bg); backdrop-filter: blur(10px); padding: 25px; border-radius: 12px; border: 1px solid var(--glass-border); max-width: 500px; margin-bottom: 30px;">
                    <h3 style="margin-bottom: 15px; color: var(--text-primary);">Log Attendance</h3>

                    <select id="course-select" onchange="handleCourseSelection()"
                        style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background: rgba(255,255,255,0.7); margin-bottom: 15px; font-size: 1rem;">
                        <option value="">-- Loading your courses... --</option>
                    </select>

                    <div id="attendance-action-area" style="transition: all 0.3s ease;">
                        <div id="otp-section" style="margin-bottom: 15px;">
                            <label style="color: var(--text-primary); font-size: 0.9rem; font-weight: bold;">Enter OTP
                                or Scan QR:</label>
                            <div style="display: flex; gap: 10px; margin-top: 5px;">
                                <input type="text" id="attendance-otp" placeholder="Enter OTP"
                                    style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background: rgba(255,255,255,0.7); font-size: 1rem;">

                                <button onclick="startQRScanner()"
                                    style="padding: 10px 15px; border: none; border-radius: 6px; background: #3498db; color: white; cursor: pointer; transition: 0.3s;"
                                    title="Scan QR Code">
                                    <i class="fas fa-qrcode"></i> Scan
                                </button>
                            </div>

                            <div id="qr-reader"
                                style="width: 100%; margin-top: 15px; border-radius: 8px; overflow: hidden; border: 2px solid var(--glass-border);">
                            </div>
                        </div>

                        <button id="log-attendance-btn" onclick="submitAttendance()"
                            style="width: 100%; padding: 12px; border: none; border-radius: 6px; background: var(--main-green, #2ecc71); color: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
                            <i class="fas fa-check-circle"></i> Submit Attendance
                        </button>

                        <div id="attendance-status-display"
                            style="display: none; padding: 15px; background: rgba(46, 204, 113, 0.15); border-left: 4px solid var(--success); border-radius: 6px; margin-top: 10px;">
                            <strong style="color: var(--text-primary);">Status: <span id="status-text"
                                    style="color: var(--success);">Present</span></strong><br>
                            <small style="color: var(--text-secondary);">Logged at: <span
                                    id="timestamp-text"></span></small>
                        </div>

                    </div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        loadEnrolledCourses();
                    });
                </script>
            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

</html>