<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLD · Teacher Dashboard</title>
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
                <div class="nav-item active" data-page="teacher-dash"><i class="fas fa-chalkboard-teacher"></i>
                    Dashboard</div>
                <div class="nav-item" onclick="navigateTo('profile')"><i class="fas fa-user-edit"></i> Profile</div>
            </div>
        </aside>

        <div id="overlay" class="overlay"></div>

        <main class="main-content" id="mainContent">
            <div class="page active-page" id="teacher-dash">

                <header class="dashboard-header">
                    <div class="header-title">
                        <h3 style="color: var(--yellow-highlight)">Teacher</h3>
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

                <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;">
                    <h3 style="color: var(--deep-green); margin-bottom: 1rem;">Create a New Course</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <input type="text" id="course-code" placeholder="Course Code (e.g., BSCS-101)"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 1;">
                        <input type="text" id="course-name" placeholder="Course Name (e.g., Intro to Programming)"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 2;">
                        <input type="text" id="course-section" placeholder="Section (e.g., Block A)"
                            style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); flex-grow: 1;">
                        <button class="btn btn-outline-green" onclick="handleCreateCourse()">Create Course</button>
                    </div>
                </div>

                <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;">
                    <h3 style="color: var(--deep-green); margin-bottom: 1rem;">Generate OTP & QRCode</h3>
                    <select id="teacher-course-select" onchange="teacherCourseChanged()"
                        style="padding: 12px 20px; border-radius: 60px; border: 1px solid var(--border); width: 100%; max-width: 300px; background: white; margin-bottom: 1rem;">
                        <option value="">-- Select a Course --</option>
                    </select>

                    <div id="teacher-course-tools" style="margin-top: 1rem;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <button id="generate-btn" onclick="generateOTP()"
                                style="width: 100%; padding: 12px; border: none; border-radius: 6px; background: var(--main-green, #2ecc71); color: white; font-weight: bold; cursor: pointer; transition: 0.3s;">
                                <i class="fas fa-qrcode"></i> Generate OTP & QR Code
                            </button>

                        </div>
                    </div>
                    
                        <div id="otp-display-area"
                            style="display: none; margin-top: 25px; text-align: center; border-top: 1px solid var(--glass-border); padding-top: 20px;">
                            <p style="color: var(--text-secondary); margin-bottom: 10px; font-weight: bold;">Ask
                                students to
                                scan or type this code:</p>

                            <div id="generated-otp"
                                style="font-size: 2.8rem; font-weight: bold; letter-spacing: 8px; color: var(--text-primary); background: rgba(0,0,0,0.05); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            </div>

                            <img id="generated-qr" src="" alt="Class QR Code"
                                style="display: block; margin: 0 auto; border-radius: 8px; border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 200px; height: 200px;">
                        </div>
                        
                    <div class="glass-card"
                        style="display: none; background: var(--glass-bg); backdrop-filter: blur(10px); padding: 25px; border-radius: 12px; border: 1px solid var(--glass-border); max-width: 500px; margin-bottom: 30px;">
                        <h3 style="margin-bottom: 15px; color: var(--text-primary);">Generate Attendance OTP</h3>
                        <p style="margin-bottom: 20px; color: var(--text-secondary); font-size: 0.9rem;">Select your
                            course
                            to generate a unique QR code and 6-digit OTP for today's session.</p>

                        <select id="teacher-course-select"
                            style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; background: rgba(255,255,255,0.7); margin-bottom: 15px; font-size: 1rem;">
                            <option value="">-- Loading your courses... --</option>
                        </select>

                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        if (typeof loadTeacherCourses === 'function') {
                            loadTeacherCourses();
                        }
                    });
                </script>

            </div>
        </main>
    </div>
    <script src="script.js"></script>
</body>

</html>