// ==========================================
// NAVIGATION & SIDEBAR LOGIC
// ==========================================
function navigateTo(pageId) {
    if (pageId.includes('dash') || pageId === 'profile') {
        window.location.href = pageId + '.php';
    } else {
        window.location.href = pageId === 'home' ? 'index.html' : pageId + '.html';
    }
}
window.addEventListener('load', () => {
    const path = window.location.pathname;
    const filename = path.split('/').pop() || 'index.html';
    const activePageId = filename === 'index.html' ? 'home' : filename.replace('.html', '');

    document.querySelectorAll('.nav-item').forEach(item => {
        if (item.dataset.page === activePageId) item.classList.add('active');
        else item.classList.remove('active');
    });
});

const menuBtn = document.getElementById("menu-btn");
const sidebar = document.querySelector(".sidebar");
const overlay = document.getElementById("overlay");

if (menuBtn) {
    menuBtn.addEventListener("click", () => {
        sidebar.classList.toggle("active");
        overlay.classList.toggle("active");
    });
}
if (overlay) {
    overlay.addEventListener("click", () => {
        sidebar.classList.remove("active");
        overlay.classList.remove("active");
    });
}

// --- Custom Toast Notification System ---
function showNotification(message, type = 'info') {
    // 1. Create the container if it doesn't exist yet
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    // 2. Create the toast element
    const toast = document.createElement('div');
    toast.className = `toast-msg ${type}`;
    
    // 3. Set the appropriate FontAwesome icon
    let icon = 'fa-info-circle';
    if (type === 'error') icon = 'fa-exclamation-circle';
    if (type === 'success') icon = 'fa-check-circle';

    toast.innerHTML = `<i class="fas ${icon}"></i> <span>${message}</span>`;
    
    // 4. Add to screen and trigger animation
    container.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);

    // 5. Remove automatically after 3.5 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300); // Wait for slide-out animation to finish
    }, 3500);
}

// ==========================================
// AUTHENTICATION LOGIC (Register / Login)
// ==========================================
function handleRegister() {
    const name = document.getElementById('reg-name').value;
    const email = document.getElementById('reg-email').value;
    const password = document.getElementById('reg-password').value;
    const role = document.getElementById('reg-role').value;

    // Use our new notification for missing fields
    if(!name || !email || !password) {
        return showNotification("Please fill out all fields.", "error");
    }

    const regBtn = document.querySelector('button[onclick="handleRegister()"]');
    
    if(regBtn) {
        regBtn.disabled = true;
        regBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Please wait...';
        regBtn.style.opacity = '0.7';
    }

    const formData = new FormData();
    formData.append('name', name); 
    formData.append('email', email);
    formData.append('password', password); 
    formData.append('role', role);

    fetch('api/register_action.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        
        // Show the success/error message beautifully
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            // Wait 2 seconds so they can read the success message before redirecting
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            showNotification(data.message, 'error');
            if(regBtn) {
                regBtn.disabled = false;
                regBtn.innerHTML = 'Register';
                regBtn.style.opacity = '1';
            }
        }
    })
    .catch(e => {
        console.error(e);
        showNotification("A server error occurred.", "error");
        if(regBtn) {
            regBtn.disabled = false;
            regBtn.innerHTML = 'Register';
            regBtn.style.opacity = '1';
        }
    });
}

// ==========================================
// AUTHENTICATION LOGIC (Login)
// ==========================================
function handleLogin() {
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    if (!email || !password) {
        return showNotification("Please enter both email and password.", "error");
    }

    const loginBtn = document.querySelector('button[onclick="handleLogin()"]');
    if (loginBtn) {
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        loginBtn.style.opacity = '0.7';
    }

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);

    fetch('api/login_action.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            // FIX: Uses data.message if it exists, otherwise uses default string
            showNotification(data.message || "Login successful! Redirecting...", "success");
            
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            showNotification(data.message || "Invalid credentials.", "error");
            if (loginBtn) {
                loginBtn.disabled = false;
                loginBtn.innerHTML = 'Login';
                loginBtn.style.opacity = '1';
            }
        }
    })
    .catch(e => {
        console.error(e);
        showNotification("A server error occurred.", "error");
        if (loginBtn) {
            loginBtn.disabled = false;
            loginBtn.innerHTML = 'Login';
            loginBtn.style.opacity = '1';
        }
    });
}



function submitOTP() {
    const otp = document.getElementById('otp-input').value;
    if (!otp) return alert("Please enter or scan an OTP first.");

    const formData = new FormData(); formData.append('otp', otp);

    fetch('api/verify_otp.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(data => {
        alert(data.message);
        if (data.status === 'success') document.getElementById('otp-input').value = '';
    });
}

// ==========================================
// TEACHER LOGIC
// ==========================================
// --- TEACHER: Create Course ---
function handleCreateCourse() {
    const code = document.getElementById('course-code').value.trim();
    const name = document.getElementById('course-name').value.trim();
    const section = document.getElementById('course-section').value.trim();

    if (!code || !name || !section) {
        return showNotification('Please fill in all fields: Course Code, Course Name, and Section.', 'error');
    }

    const formData = new FormData();
    formData.append('course_code', code);
    formData.append('course_name', name);
    formData.append('course_section', section);

    fetch('api/create_course.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            
            // Clear your input fields here...
            
            // THE MAGIC FIX: Instantly refresh the teacher's OTP dropdown!
            if (typeof loadTeacherCourses === 'function') {
                loadTeacherCourses();
            }
        }
    })
    .catch(error => {
        console.error('Error creating course:', error);
        showNotification('An error occurred while creating the course.', 'error');
    });
}


// Ensure the courses load automatically as soon as the teacher opens the dashboard
window.addEventListener('load', () => {
    if (document.getElementById('teacher-course-select')) {
        loadTeacherCourses();
    }
});

function generateSessionQR() {
    const displayContainer = document.getElementById('active-session-display');
    if (!displayContainer) return;

    const newOTP = Math.floor(100000 + Math.random() * 900000).toString();
    document.getElementById('teacher-otp-text').innerText = newOTP;
    document.getElementById('qr-code-img').src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(newOTP)}&color=0F5132`;
    
    displayContainer.style.display = 'block';
}

// ==========================================
// ADMIN LOGIC
// ==========================================
function loadAdminUsers() {
    const tbody = document.getElementById('users-tbody');
    if (!tbody) return;

    fetch('api/get_users.php').then(r => r.json()).then(data => {
        if (data.status === 'success') {
            tbody.innerHTML = '';
            data.data.forEach(user => {
                const badge = user.is_verified == 1 ? '<span style="color:var(--success);">Verified</span>' : '<span style="color:var(--warning);">Pending</span>';
                tbody.innerHTML += `
                    <tr>
                        <td>${user.full_name}</td><td>${user.email}</td>
                        <td>${user.role}</td><td>${badge}</td>
                        <td>
                            <button onclick="editUserPrompt(${user.id}, '${user.full_name}', '${user.role}')" class="btn btn-outline-green" style="padding: 4px 8px; margin-right:5px;">Edit</button>
                            <button onclick="deleteUser(${user.id})" class="btn" style="background:var(--error); color:white; padding: 4px 8px;">Delete</button>
                        </td>
                    </tr>`;
            });
        }
    });
}

function deleteUser(userId) {
    if (!confirm("Delete this user?")) return;
    const formData = new FormData(); formData.append('user_id', userId);
    fetch('api/delete_user.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(data => { alert(data.message); if (data.status === 'success') loadAdminUsers(); });
}

function editUserPrompt(userId, currentName, currentRole) {
    const newName = prompt("Enter new full name:", currentName);
    if (!newName) return;
    const newRole = prompt("Enter new role (student/teacher/admin):", currentRole);
    if (!['student', 'teacher', 'admin'].includes(newRole?.toLowerCase())) return alert("Invalid role.");

    const formData = new FormData();
    formData.append('user_id', userId); formData.append('full_name', newName); formData.append('role', newRole.toLowerCase());

    fetch('api/edit_user.php', { method: 'POST', body: formData })
    .then(r => r.json()).then(data => { alert(data.message); if (data.status === 'success') loadAdminUsers(); });
}

// ==========================================
// LOGOUT SYSTEM
// ==========================================
function confirmLogout() {
    // Create the overlay container
    const overlay = document.createElement('div');
    overlay.id = 'logout-modal-overlay';
    overlay.innerHTML = `
        <div class="logout-modal">
            <h3>Confirm Logout</h3>
            <p>Are you sure you want to log out?</p>
            <div class="logout-modal-actions">
                <button class="btn-cancel" onclick="closeLogoutModal()">Cancel</button>
                <button class="btn-confirm" onclick="executeLogout()">Yes, Logout</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    // Small delay to trigger the fade-in animation properly
    setTimeout(() => overlay.classList.add('show'), 10);
}

function closeLogoutModal() {
    const overlay = document.getElementById('logout-modal-overlay');
    if (overlay) {
        overlay.classList.remove('show');
        setTimeout(() => overlay.remove(), 300); // Wait for fade-out
    }
}

function executeLogout() {
    closeLogoutModal(); // Instantly hide the modal
    
    fetch('api/logout.php')
    .then(response => response.json())
    .then(data => {
        // Use our beautiful toast notification
        showNotification("Logging out...", "success");
        
        // Send them to the Home Page after 1 second
        setTimeout(() => {
            window.location.href = 'index.html'; 
        }, 1000);
    })
    .catch(error => {
        console.error('Logout error:', error);
        showNotification("An error occurred while logging out.", "error");
    });
}

// ==========================================
// STUDENT: ENROLL IN COURSE
// ==========================================
function handleEnrollCourse() {
    const code = document.getElementById('enroll-code').value.trim();
    const name = document.getElementById('enroll-name').value.trim();
    const section = document.getElementById('enroll-section').value.trim();

    if (!code || !name || !section) {
        return showNotification("Please enter Course Code, Name, and Section.", "error");
    }

    const enrollBtn = document.getElementById('enroll-btn');
    if (enrollBtn) {
        enrollBtn.disabled = true;
        enrollBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enrolling...';
    }

    const formData = new FormData();
    formData.append('course_code', code);
    formData.append('course_name', name);
    formData.append('course_section', section);

    fetch('api/enroll_course.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Show success, error, or info notification
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            
            // Clear the enrollment inputs
            document.getElementById('enroll-code').value = '';
            document.getElementById('enroll-name').value = '';
            document.getElementById('enroll-section').value = '';
            
            // THE MAGIC FIX: Instantly refresh the dropdown list!
            if (typeof loadEnrolledCourses === 'function') {
                loadEnrolledCourses();
            }
        }
    })
    .catch(error => {
        console.error('Enrollment error:', error);
        showNotification("A server error occurred.", "error");
    })
    .finally(() => {
        // Reset the button
        if (enrollBtn) {
            enrollBtn.disabled = false;
            enrollBtn.innerHTML = 'Join Course';
        }
    });
}

// ==========================================
// STUDENT: DYNAMIC ATTENDANCE SYSTEM
// ==========================================
let myCourses = []; // Stores courses to prevent asking database multiple times

function loadEnrolledCourses() {
    fetch('api/get_enrolled_courses.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('course-select');
        select.innerHTML = '<option value="">-- Select a Course --</option>';

        if (data.status === 'success' && data.courses.length > 0) {
            myCourses = data.courses;
            myCourses.forEach(course => {
                select.innerHTML += `<option value="${course.id}">${course.course_code} - ${course.course_name}</option>`;
            });
        } else {
            select.innerHTML = '<option value="">You are not enrolled in any courses yet.</option>';
        }
    })
    .catch(err => console.error('Error loading courses:', err));
}

function handleCourseSelection() {
    const courseId = document.getElementById('course-select').value;
    const actionArea = document.getElementById('attendance-action-area'); // Contains OTP/QR
    const logBtn = document.getElementById('log-attendance-btn');
    const statusDisplay = document.getElementById('attendance-status-display');
    const otpSection = document.getElementById('otp-section');
    
    // 1. If NO course is selected (the default state) -> HIDE EVERYTHING
    if (!courseId) {
        actionArea.style.display = 'none';
        return;
    }

    // 2. If a course IS selected -> SHOW THE ACTION AREA
    actionArea.style.display = 'block';
    
    // Find the specific course from our saved list
    const selectedCourse = myCourses.find(c => c.id == courseId);

    // 3. Check if they already logged attendance for this specific course today
    if (selectedCourse && selectedCourse.time_logged) {
        otpSection.style.display = 'none';     // Hide OTP/Scanner
        logBtn.style.display = 'none';         // Hide the Submit button
        statusDisplay.style.display = 'block'; // Show the "Present" status
        
        // Format timestamp nicely
        let dateObj = new Date(selectedCourse.time_logged);
        document.getElementById('timestamp-text').innerText = dateObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        document.getElementById('status-text').innerText = selectedCourse.status;
    } else {
        // They haven't logged in yet today
        otpSection.style.display = 'block';    // Show OTP/Scanner
        logBtn.style.display = 'block';        // Show the Submit button
        statusDisplay.style.display = 'none';  // Hide the status
    }
}

function submitAttendance() {
    const courseId = document.getElementById('course-select').value;
    const otp = document.getElementById('attendance-otp').value.trim(); // Grab the OTP

    if (!courseId) return;
    if (!otp) {
        return showNotification("Please enter the OTP or scan the QR code.", "error");
    }

    const logBtn = document.getElementById('log-attendance-btn');
    logBtn.disabled = true;
    logBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

    const formData = new FormData();
    formData.append('course_id', courseId);
    formData.append('otp', otp); // Send the OTP to PHP

    fetch('api/log_attendance.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message, 'success');
            document.getElementById('attendance-otp').value = ''; // Clear OTP box
            loadEnrolledCourses();
            setTimeout(() => { handleCourseSelection(); }, 500); 
        } else if (data.status === 'info') {
            showNotification(data.message, 'info');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Failed to log attendance.", "error");
    })
    .finally(() => {
        logBtn.disabled = false;
        logBtn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Attendance';
    });
}

// ==========================================
// QR CODE SCANNER LOGIC
// ==========================================
let html5QrcodeScanner;

function startQRScanner() {
    const qrReader = document.getElementById('qr-reader');
    qrReader.style.display = 'block';

    // Initialize the scanner
    html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { 
        fps: 10, 
        qrbox: { width: 250, height: 250 } 
    });
    
    html5QrcodeScanner.render(onScanSuccess, onScanError);
}

function onScanSuccess(decodedText, decodedResult) {
    // When a QR code is found, put the text into the OTP input box
    document.getElementById('attendance-otp').value = decodedText;
    
    // Stop scanning and hide the camera
    html5QrcodeScanner.clear();
    document.getElementById('qr-reader').style.display = 'none';
    
    showNotification("QR Code Scanned!", "success");
}

function onScanError(errorMessage) {
    // Silently ignore errors (it scans multiple times a second until it finds one)
}

// ==========================================
// TEACHER: DYNAMIC OTP & QR GENERATOR
// ==========================================

function loadTeacherCourses() {
    fetch('api/get_teacher_courses.php')
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('teacher-course-select');
        if (!select) return; 
        
        select.innerHTML = '<option value="">-- Select a Course --</option>';
        if (data.status === 'success' && data.courses.length > 0) {
            data.courses.forEach(course => {
                select.innerHTML += `<option value="${course.id}">${course.course_code} - ${course.course_name}</option>`;
            });
        } else {
            select.innerHTML = '<option value="">You have not created any courses yet.</option>';
        }
    })
    .catch(err => console.error('Error loading teacher courses:', err));
}

function generateOTP() {
    const courseId = document.getElementById('teacher-course-select').value;
    if (!courseId) {
        return showNotification("Please select a course first.", "error");
    }

    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    const formData = new FormData();
    formData.append('course_id', courseId);

    fetch('api/generate_otp.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            // Show the display area
            document.getElementById('otp-display-area').style.display = 'block';
            
            // Set the huge text
            document.getElementById('generated-otp').innerText = data.otp;
            
            // Generate QR code dynamically using the free qrserver API
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${data.otp}`;
            document.getElementById('generated-qr').src = qrUrl;
            
            showNotification("OTP Generated! Ready for students.", "success");
        } else {
            showNotification(data.message, "error");
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Failed to generate OTP.", "error");
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-qrcode"></i> Generate New OTP & QR Code';
    });
}

// Make sure to load the courses automatically when the teacher logs in
document.addEventListener("DOMContentLoaded", () => {
    // Only run this if the teacher dropdown actually exists on the page
    if (document.getElementById('teacher-course-select')) {
        loadTeacherCourses();
    }
});