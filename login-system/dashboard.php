<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit();
}

$userName = $_SESSION['user_name'] ?? 'User';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YESIST12 - Dashboard</title>
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
            color: white;
        }

        .navbar {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .logout-btn {
            padding: 8px 16px;
            background: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: #ff6b6b;
            color: white;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-title {
            font-size: 3rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            font-size: 1.2rem;
            color: #a0a9b8;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #00d4aa;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #a0a9b8;
            font-size: 1rem;
        }

        .features-section {
            margin-top: 50px;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0, 212, 170, 0.2);
        }

        .feature-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #00d4aa;
        }

        .feature-description {
            color: #a0a9b8;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .primary-btn {
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            color: white;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 212, 170, 0.3);
        }

        .secondary-btn {
            background: transparent;
            border: 2px solid #00d4aa;
            color: #00d4aa;
        }

        .secondary-btn:hover {
            background: #00d4aa;
            color: white;
        }

        .user-profile {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #00d4aa, #1e90ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2rem;
        }

        .profile-details h3 {
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .profile-details p {
            color: #a0a9b8;
            margin-bottom: 3px;
        }

        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 300px;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">YESIST12</div>
        <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
            <span><?php echo htmlspecialchars($userName); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section">
            <h1 class="welcome-title">Welcome to YESIST12!</h1>
            <p class="welcome-subtitle">IEEE Student Branch Authentication System</p>
        </div>

        <div class="user-profile">
            <div class="profile-info">
                <div class="profile-avatar"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
                <div class="profile-details">
                    <h3><?php echo htmlspecialchars($userName); ?></h3>
                    <p>📧 <?php echo htmlspecialchars($userEmail); ?></p>
                    <p>🕒 Last login: <?php echo date('F j, Y \a\t g:i A'); ?></p>
                    <p>✅ Account Status: Verified</p>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-number">1,250</div>
                <div class="stat-label">Active Members</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-number">42</div>
                <div class="stat-label">Upcoming Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏆</div>
                <div class="stat-number">18</div>
                <div class="stat-label">Achievements</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-number">95</div>
                <div class="stat-label">Resources</div>
            </div>
        </div>

        <div class="features-section">
            <h2 class="section-title">Available Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3 class="feature-title">🎯 Event Management</h3>
                    <p class="feature-description">
                        Register for IEEE events, workshops, and technical sessions. Track your participation and earn certificates.
                    </p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">📊 Progress Tracking</h3>
                    <p class="feature-description">
                        Monitor your learning progress, completed courses, and achieved milestones in your IEEE journey.
                    </p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">🤝 Networking Hub</h3>
                    <p class="feature-description">
                        Connect with fellow IEEE members, join study groups, and collaborate on technical projects.
                    </p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">📖 Resource Library</h3>
                    <p class="feature-description">
                        Access exclusive IEEE publications, research papers, and technical resources for your studies.
                    </p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">🎓 Certification Portal</h3>
                    <p class="feature-description">
                        Enroll in certification programs, take assessments, and earn IEEE recognized credentials.
                    </p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">💬 Discussion Forums</h3>
                    <p class="feature-description">
                        Participate in technical discussions, ask questions, and share knowledge with the community.
                    </p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="#" class="action-btn primary-btn">Explore Events</a>
            <a href="#" class="action-btn secondary-btn">My Profile</a>
            <a href="#" class="action-btn secondary-btn">Resource Library</a>
            <a href="../index.html" class="action-btn secondary-btn">Form Builder</a>
        </div>
    </div>

    <script>
        // Welcome animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .feature-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });

            // Show welcome message
            setTimeout(() => {
                showWelcomeToast();
            }, 1000);
        });

        function showWelcomeToast() {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(45deg, #00d4aa, #1e90ff);
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
                z-index: 1000;
                transform: translateX(400px);
                transition: transform 0.3s ease;
            `;
            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span>🎉</span>
                    <span>Welcome back, <?php echo htmlspecialchars($userName); ?>!</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            setTimeout(() => {
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }

        // Add click effects to cards
        document.querySelectorAll('.stat-card, .feature-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
    </script>
</body>
</html>