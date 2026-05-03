<?php
session_start();
require 'api/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$stmt = $pdo->prepare("SELECT full_name, email, role, contact_number, id_number, profile_pic FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KLD · Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="app-wrapper">
        <aside class="sidebar">
            <div class="logo-area"><i class="fas fa-qrcode"></i><span>KLD</span></div>
            <div class="nav-links" id="sidebarNav">
                <div class="nav-item" onclick="navigateTo('<?php echo $_SESSION['role']; ?>-dash')"><i
                        class="fas fa-arrow-left"></i> Back to Dash</div>
                <div class="nav-item active" data-page="profile"><i class="fas fa-user-edit"></i> Profile</div>
            </div>
        </aside>

        <div id="overlay" class="overlay"></div>

        <main class="main-content" id="mainContent">
            <div class="page active-page">

                <div class="page-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center;">
                        <button id="menu-btn" class="menu-btn"
                            style="background:none; border:none; font-size:1.5rem; margin-right:1rem; cursor:pointer; color:var(--deep-green);"><i
                                class="fas fa-bars"></i></button>
                        <h1 style="margin: 0;">My Profile</h1>
                    </div>

                    <div class="header-actions" style="display: flex; gap: 1.5rem; align-items: center;">
                        <div class="header-icon-tray">
                            <div class="icon-btn" onclick="openMessages()"
                                style="position: relative; cursor: pointer; color: var(--deep-green); font-size: 1.2rem;">
                                <i class="fas fa-envelope"></i></div>
                            <div class="icon-btn" onclick="openNotifications()"
                                style="position: relative; cursor: pointer; color: var(--deep-green); font-size: 1.2rem;">
                                <i class="fas fa-bell"></i></div>
                            <div class="icon-btn" onclick="confirmLogout()"
                                style="position: relative; cursor: pointer; color: var(--error); font-size: 1.2rem;"
                                title="Logout"><i class="fas fa-sign-out-alt"></i></div>
                        </div>
                    </div>
                </div>

                <div class="glass-card" style="padding: 2rem; max-width: 600px; margin: 0 auto;">
                    <form id="profileForm" enctype="multipart/form-data">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <img src="api/uploads/<?php echo htmlspecialchars($user['profile_pic'] ?: 'default.png'); ?>"
                                alt=""
                                style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--main-green); margin-bottom: 1rem; background: white;">
                            <br>
                            <input type="file" id="profile_pic" name="profile_pic" accept="image/*"
                                style="font-size: 0.9rem;">
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <label style="color: var(--text-secondary); font-size: 0.9rem;">Full Name</label>
                            <input type="text" name="full_name"
                                value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                style="padding: 12px; border-radius: 10px; border: 1px solid var(--border);">

                            <label style="color: var(--text-secondary); font-size: 0.9rem;">Email</label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                style="padding: 12px; border-radius: 10px; border: 1px solid var(--border); background: #e9ecef;">

                            <label style="color: var(--text-secondary); font-size: 0.9rem;">ID Number</label>
                            <input type="text" name="id_number"
                                value="<?php echo htmlspecialchars($user['id_number']); ?>"
                                placeholder="Enter ID Number"
                                style="padding: 12px; border-radius: 10px; border: 1px solid var(--border);">

                            <label style="color: var(--text-secondary); font-size: 0.9rem;">Contact Number
                                (Optional)</label>
                            <input type="text" name="contact_number"
                                value="<?php echo htmlspecialchars($user['contact_number']); ?>"
                                placeholder="Enter Contact Number"
                                style="padding: 12px; border-radius: 10px; border: 1px solid var(--border);">

                            <hr style="border: 0; border-top: 1px solid var(--border); margin: 1rem 0;">

                            <h4 style="color: var(--deep-green);">Change Password</h4>
                            <input type="password" name="new_password"
                                placeholder="New Password (leave blank to keep current)"
                                style="padding: 12px; border-radius: 10px; border: 1px solid var(--border);">

                            <button type="button" class="btn btn-primary" onclick="updateProfile()"
                                style="margin-top: 1rem; justify-content: center;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="script.js"></script>
    <script>
        function updateProfile() {
            const form = document.getElementById('profileForm');
            const formData = new FormData(form);

            fetch('api/update_profile.php', { method: 'POST', body: formData })
                .then(r => r.json()).then(data => {
                    if (data.status === 'success') {
                        showNotification(data.message, 'success');
                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        showNotification(data.message, 'error');
                    }
                }).catch(e => {
                    console.error(e);
                    showNotification("Failed to update profile.", "error");
                });
        }

        function openMessages() { showNotification("Messaging system coming soon!", "info"); }
        function openNotifications() { showNotification("Notification system coming soon!", "info"); }
    </script>
</body>

</html>