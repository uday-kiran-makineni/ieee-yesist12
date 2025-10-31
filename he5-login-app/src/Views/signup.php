<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YESIST12 - Sign Up</title>
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
            animation: fadeIn 0.5s ease-in-out;
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

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 212, 170, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            border-left: 4px solid;
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #4caf50;
            border-left-color: #4caf50;
        }

        .message.error {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
            border-left-color: #f44336;
        }

        .auth-links {
            text-align: center;
            margin-top: 25px;
        }

        .auth-links a {
            color: #00d4aa;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #1e90ff;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <div class="logo-text">YESIST12</div>
            <div class="logo-subtitle">Join us today! Create your account to get started</div>
        </div>

        <div id="message"></div>

        <form id="signupForm">
            <div class="form-group">
                <label class="form-label" for="fullName">Full Name <span class="required">*</span></label>
                <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email address" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="Enter your phone number" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Create a strong password" required minlength="8">
            </div>
            
            <button type="submit" class="submit-btn" id="signupBtn">Create Account</button>
        </form>

        <div class="auth-links">
            <a href="/login">Already have an account? Sign in here</a>
        </div>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('signupBtn');
            const messageDiv = document.getElementById('message');
            
            btn.disabled = true;
            btn.textContent = 'Creating account...';
            messageDiv.innerHTML = '';
            
            try {
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                const response = await fetch('/api/signup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageDiv.innerHTML = '<div class="message success">' + result.message + '</div>';
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    messageDiv.innerHTML = '<div class="message error">' + (result.message || result.error || 'Signup failed') + '</div>';
                }
            } catch (error) {
                messageDiv.innerHTML = '<div class="message error">Network error. Please try again.</div>';
            } finally {
                btn.disabled = false;
                btn.textContent = 'Sign Up';
            }
        });
    </script>
</body>
</html>