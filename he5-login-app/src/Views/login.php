<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YESIST12 - Authentication</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3b4d66 50%, #4a5f7a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            background: rgba(45, 62, 80, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 450px;
            position: relative;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-text {
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .logo-subtitle {
            color: #a0a9b8;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .tab-container {
            display: flex;
            margin-bottom: 30px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 5px;
        }

        .tab-button {
            flex: 1;
            padding: 12px 20px;
            background: transparent;
            border: none;
            color: #a0a9b8;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .tab-button.active {
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            color: white;
            box-shadow: 0 5px 15px rgba(0, 212, 170, 0.3);
        }

        .tab-button:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-container {
            position: relative;
            overflow: hidden;
        }

        .form-content {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }

        .form-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            display: block;
            color: #a0a9b8;
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .required {
            color: #ff6b6b;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input::placeholder {
            color: #6c7b7f;
        }

        .form-input:focus {
            border-color: #00d4aa;
            box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.2);
            background: rgba(0, 0, 0, 0.5);
        }

        .form-input:valid {
            border-color: #00d4aa;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #a0a9b8;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 5px;
        }

        .password-toggle:hover {
            color: #00d4aa;
        }

        .submit-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            position: relative;
            overflow: hidden;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 212, 170, 0.3);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .submit-button.loading {
            pointer-events: none;
        }

        .submit-button.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: #00d4aa;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #1e90ff;
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: #a0a9b8;
            font-size: 0.9rem;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        .divider span {
            background: rgba(45, 62, 80, 0.95);
            padding: 0 15px;
        }

        .social-login {
            display: flex;
            gap: 10px;
        }

        .social-button {
            flex: 1;
            padding: 12px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: transparent;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .social-button:hover {
            border-color: #00d4aa;
            background: rgba(0, 212, 170, 0.1);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: none;
        }

        .alert.success {
            background: rgba(0, 212, 170, 0.2);
            border: 1px solid #00d4aa;
            color: #00d4aa;
        }

        .alert.error {
            background: rgba(255, 107, 107, 0.2);
            border: 1px solid #ff6b6b;
            color: #ff6b6b;
        }

        .alert.info {
            background: rgba(30, 144, 255, 0.2);
            border: 1px solid #1e90ff;
            color: #1e90ff;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .logo-text {
                font-size: 2rem;
            }
            
            .tab-button {
                padding: 10px 15px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <div class="logo-text">YESIST12</div>
            <div class="logo-subtitle">IEEE Student Branch Authentication</div>
        </div>

        <div class="tab-container">
            <button class="tab-button active" onclick="switchTab('signin')">Sign In</button>
            <button class="tab-button" onclick="switchTab('signup')">Sign Up</button>
        </div>

        <div id="alerts"></div>

        <!-- Sign In Form -->
        <div id="signin-form" class="form-content active">
            <form id="signinForm">
                <div class="form-group">
                    <label class="form-label">Email ID <span class="required">*</span></label>
                    <input type="email" class="form-input" name="email" placeholder="Enter your Email Address" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <div style="position: relative;">
                        <input type="password" class="form-input" name="password" placeholder="Enter your Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
                    </div>
                </div>

                <button type="submit" class="submit-button">Sign In</button>
            </form>

            <div class="divider">
                <span>or</span>
            </div>

            <div class="social-login">
                <button class="social-button" onclick="socialLogin('google')">
                    <span>🔍</span> Google
                </button>
                <button class="social-button" onclick="socialLogin('github')">
                    <span>⚡</span> GitHub
                </button>
            </div>
        </div>

        <!-- Sign Up Form -->
        <div id="signup-form" class="form-content">
            <form id="signupForm">
                <div class="form-group">
                    <label class="form-label">Email ID <span class="required">*</span></label>
                    <input type="email" class="form-input" name="email" placeholder="Enter your Email Address" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <div style="position: relative;">
                        <input type="password" class="form-input" name="password" placeholder="Enter your Password" required minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Re-enter Password <span class="required">*</span></label>
                    <div style="position: relative;">
                        <input type="password" class="form-input" name="confirmPassword" placeholder="Re-Enter your Password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword(this)">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" class="form-input" name="fullName" placeholder="Enter your First Name & Last Name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Contact Number with Country Code <span class="required">*</span></label>
                    <input type="tel" class="form-input" name="phone" placeholder="Enter your Contact Number" required>
                </div>

                <button type="submit" class="submit-button">Create Account</button>
            </form>
        </div>
    </div>

    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Remove active class from all tabs and forms
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.form-content').forEach(form => form.classList.remove('active'));

            // Add active class to clicked tab
            event.target.classList.add('active');

            // Show corresponding form
            document.getElementById(tabName + '-form').classList.add('active');

            // Clear alerts
            clearAlerts();

            // Reset forms
            resetForms();
        }

        // Password toggle functionality
        function togglePassword(button) {
            const input = button.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }

        // Alert functions
        function showAlert(message, type = 'info') {
            const alertsContainer = document.getElementById('alerts');
            const alert = document.createElement('div');
            alert.className = `alert ${type}`;
            alert.style.display = 'block';
            alert.textContent = message;
            alertsContainer.innerHTML = '';
            alertsContainer.appendChild(alert);

            // Auto hide after 5 seconds
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }

        function clearAlerts() {
            document.getElementById('alerts').innerHTML = '';
        }

        // Form reset function
        function resetForms() {
            document.querySelectorAll('form').forEach(form => form.reset());
        }

        // Loading button state
        function setLoading(button, isLoading) {
            if (isLoading) {
                button.classList.add('loading');
                button.textContent = '';
            } else {
                button.classList.remove('loading');
            }
        }

        // Sign In Form Handler
        document.getElementById('signinForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = this.querySelector('.submit-button');
            const originalText = button.textContent;
            
            setLoading(button, true);
            
            const formData = new FormData(this);
            const email = formData.get('email');
            const password = formData.get('password');

            try {
                const response = await fetch('/api/signin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Sign in successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);
                } else {
                    showAlert(result.error || 'Sign in failed. Please try again.', 'error');
                }
            } catch (error) {
                showAlert('Network error. Please try again.', 'error');
            } finally {
                setLoading(button, false);
                button.textContent = originalText;
            }
        });

        // Sign Up Form Handler
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = this.querySelector('.submit-button');
            const originalText = button.textContent;

            // Validate password match
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirmPassword');

            if (password !== confirmPassword) {
                showAlert('Passwords do not match!', 'error');
                return;
            }

            setLoading(button, true);

            const email = formData.get('email');
            const fullName = formData.get('fullName');
            const phone = formData.get('phone');

            try {
                const response = await fetch('/api/signup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        fullName: fullName,
                        phone: phone
                    })
                });

                const result = await response.json();

                if (result.success) {
                    showAlert('Account created successfully! Please sign in.', 'success');
                    setTimeout(() => {
                        switchTab('signin');
                    }, 2000);
                } else {
                    showAlert(result.error || 'Registration failed. Please try again.', 'error');
                }
            } catch (error) {
                showAlert('Network error. Please try again.', 'error');
            } finally {
                setLoading(button, false);
                button.textContent = originalText;
            }
        });

        // Social login function
        function socialLogin(provider) {
            showAlert(`Redirecting to ${provider.charAt(0).toUpperCase() + provider.slice(1)} login...`, 'info');
            // Implement social login logic here
            setTimeout(() => {
                showAlert('Social login feature coming soon!', 'info');
            }, 1500);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Focus first input of active form
            const activeForm = document.querySelector('.form-content.active');
            const firstInput = activeForm.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>
</html>