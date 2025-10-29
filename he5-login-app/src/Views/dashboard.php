<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IEEE YESIST12</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #2c3e50 0%, #3b4d66 50%, #4a5f7a 100%); min-height: 100vh; }
        .header { background: rgba(45, 62, 80, 0.95); color: white; padding: 1rem 0; box-shadow: 0 2px 20px rgba(0,0,0,0.3); backdrop-filter: blur(10px); }
        .header .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 1.5rem; background: linear-gradient(45deg, #00d4aa, #1e90ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .header .user-menu { display: flex; align-items: center; gap: 1rem; }
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 10px; cursor: pointer; text-decoration: none; display: inline-block; transition: all 0.3s ease; font-weight: 600; }
        .btn-secondary { background: linear-gradient(45deg, #00d4aa, #1e90ff); color: white; }
        .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 212, 170, 0.3); }
        .main { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .welcome-card { background: rgba(45, 62, 80, 0.95); padding: 2rem; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); margin-bottom: 2rem; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .welcome-card h2 { color: #00d4aa; margin-bottom: 1rem; }
        .welcome-card p { color: #a0a9b8; }
        .profile-card { background: rgba(45, 62, 80, 0.95); padding: 2rem; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .profile-card h3 { color: #00d4aa; margin-bottom: 1rem; }
        .profile-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; }
        .info-item { padding: 1.5rem; background: rgba(0, 0, 0, 0.3); border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.1); }
        .info-item label { font-weight: 600; color: #a0a9b8; font-size: 0.9rem; }
        .info-item value { display: block; color: white; font-size: 1rem; margin-top: 0.5rem; }
        .loading { text-align: center; color: #a0a9b8; padding: 2rem; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>IEEE YESIST12 Dashboard</h1>
            <div class="user-menu">
                <span id="userName">Welcome!</span>
                <button class="btn btn-secondary" onclick="logout()">Logout</button>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="welcome-card">
            <h2>Welcome to IEEE YESIST12</h2>
            <p>You are successfully logged in to your account. This is your personal dashboard where you can manage your profile and access various features.</p>
        </div>

        <div class="profile-card">
            <h3>Your Profile</h3>
            <div id="profileContent" class="loading">Loading profile...</div>
        </div>
    </main>

    <script>
        // Token management functions
        function getAuthToken() {
            return localStorage.getItem('yesist12_auth_token');
        }

        function removeAuthToken() {
            localStorage.removeItem('yesist12_auth_token');
            document.cookie = 'auth_token=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
        }

        // Authenticated fetch function
        async function authenticatedFetch(url, options = {}) {
            const token = getAuthToken();
            
            const headers = {
                'Content-Type': 'application/json',
                ...options.headers
            };

            // Add token to headers if available
            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
                headers['X-Auth-Token'] = token;
            }

            return fetch(url, {
                ...options,
                headers
            });
        }

        async function loadProfile() {
            try {
                const response = await authenticatedFetch('/api/profile');
                const result = await response.json();
                
                if (result.success) {
                    const user = result.data;
                    document.getElementById('userName').textContent = `Welcome, ${user.full_name}!`;
                    
                    const profileHtml = `
                        <div class="profile-info">
                            <div class="info-item">
                                <label>Full Name</label>
                                <value>${user.full_name}</value>
                            </div>
                            <div class="info-item">
                                <label>Email</label>
                                <value>${user.email}</value>
                            </div>
                            <div class="info-item">
                                <label>Phone</label>
                                <value>${user.phone || 'Not provided'}</value>
                            </div>
                            <div class="info-item">
                                <label>Member Since</label>
                                <value>${new Date(user.created_at).toLocaleDateString()}</value>
                            </div>
                            <div class="info-item">
                                <label>Authentication</label>
                                <value>🔐 Token-based (Secure)</value>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('profileContent').innerHTML = profileHtml;
                } else {
                    if (result.error && result.error.includes('not logged in')) {
                        // Token expired or invalid, redirect to login
                        removeAuthToken();
                        window.location.href = '/login';
                    } else {
                        document.getElementById('profileContent').innerHTML = '<p style="color: #f44336;">Failed to load profile</p>';
                    }
                }
            } catch (error) {
                document.getElementById('profileContent').innerHTML = '<p style="color: #f44336;">Error loading profile</p>';
            }
        }
        
        async function logout() {
            try {
                const response = await authenticatedFetch('/api/logout', {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Remove token from local storage
                    removeAuthToken();
                    console.log('✅ Logged out successfully, token removed');
                    window.location.href = '/login';
                } else {
                    alert('Logout failed');
                }
            } catch (error) {
                // Even if logout fails on server, remove token locally
                removeAuthToken();
                alert('Network error during logout, but logged out locally');
                window.location.href = '/login';
            }
        }
        
        // Load profile on page load
        document.addEventListener('DOMContentLoaded', loadProfile);
    </script>
</body>
</html>