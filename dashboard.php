<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mock data - in real app, fetch from database
$full_name = $_SESSION['full_name'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];

// Extract first name for greeting
$first_name = explode(' ', $full_name)[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #001a1a, #003333, #001f1f);
            --card-bg: rgba(0, 51, 51, 0.35);
            --input-bg: rgba(0, 20, 30, 0.5);
            --primary-color: #00d2d3;
            --primary-hover: #00a8a8;
            --text-color: #ffffff;
            --text-muted: #7fcccc;
            --border-color: rgba(0, 210, 211, 0.2);
            --sidebar-bg: rgba(0, 40, 40, 0.9);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            color: var(--text-color);
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--border-color);
            padding: 30px 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
        }

        .sidebar-logo {
            text-align: center;
            padding: 0 20px 30px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .sidebar-logo h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 10px;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .sidebar-menu a i {
            width: 25px;
            margin-right: 12px;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(0, 210, 211, 0.15);
            color: var(--primary-color);
            border: 1px solid var(--border-color);
        }

        .sidebar-menu a.active {
            background: linear-gradient(90deg, rgba(0, 210, 211, 0.2), transparent);
        }

        .logout-btn {
            margin-top: auto;
            padding: 0 10px;
        }

        .logout-btn a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            color: #ff6b6b;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .logout-btn a i {
            width: 25px;
            margin-right: 12px;
        }

        .logout-btn a:hover {
            background: rgba(255, 107, 107, 0.15);
            border-color: rgba(255, 107, 107, 0.3);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        /* Welcome Banner */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(0, 210, 211, 0.2), rgba(0, 168, 168, 0.1));
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            animation: fadeIn 0.8s ease;
        }

        .welcome-text h1 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .welcome-text h1 span {
            color: var(--primary-color);
        }

        .welcome-text p {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .profile-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #001a1a;
        }

        /* Status Cards */
        .status-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .status-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            animation: fadeIn 0.8s ease;
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 210, 211, 0.2);
        }

        .status-card .icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-right: 15px;
        }

        .status-card .icon.blue {
            background: rgba(0, 210, 211, 0.2);
            color: var(--primary-color);
        }

        .status-card .icon.green {
            background: rgba(0, 255, 127, 0.2);
            color: #00ff7f;
        }

        .status-card .icon.purple {
            background: rgba(138, 43, 226, 0.2);
            color: #8a2be2;
        }

        .status-card .icon.orange {
            background: rgba(255, 165, 0, 0.2);
            color: #ffa500;
        }

        .status-card .info h3 {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 5px;
            font-weight: 500;
        }

        .status-card .info p {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-color);
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        /* Profile Info Card */
        .profile-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            animation: fadeIn 0.8s ease;
        }

        .profile-card h2 {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 15px;
            background: var(--input-bg);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-row i {
            width: 40px;
            height: 40px;
            background: rgba(0, 210, 211, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            margin-right: 15px;
        }

        .info-row .label {
            flex: 1;
        }

        .info-row .label span {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 3px;
        }

        .info-row .label p {
            font-size: 1rem;
            font-weight: 500;
        }

        /* User Card */
        .user-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        .user-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #001a1a;
            margin: 0 auto 20px;
            box-shadow: 0 10px 30px rgba(0, 210, 211, 0.3);
        }

        .user-card h3 {
            font-size: 1.3rem;
            margin-bottom: 5px;
        }

        .user-card .username {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 25px;
        }

        .user-card .username span {
            color: var(--primary-color);
        }

        .user-card-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
        }

        .btn-edit {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
            color: #001a1a;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 210, 211, 0.4);
        }

        .btn-logout {
            background: transparent;
            border: 2px solid #ff6b6b;
            color: #ff6b6b;
        }

        .btn-logout:hover {
            background: #ff6b6b;
            color: #001a1a;
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.4);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 900px) {
            .sidebar {
                width: 80px;
                padding: 20px 0;
            }
            
            .sidebar-logo h2, .sidebar-menu a span, .logout-btn a span {
                display: none;
            }
            
            .sidebar-menu a, .logout-btn a {
                justify-content: center;
                padding: 15px;
            }
            
            .sidebar-menu a i, .logout-btn a i {
                margin: 0;
            }
            
            .main-content {
                margin-left: 80px;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-logo">
                <h2>Dashboard</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="active">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fas fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
            </ul>
            
            <div class="logout-btn">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Welcome back, <span><?= htmlspecialchars($first_name) ?></span> 🎉</h1>
                    <p>Here's what's happening with your account today!</p>
                </div>
                <div class="profile-icon">
                    <?= strtoupper(substr($full_name, 0, 1)) ?>
                </div>
            </div>

            <!-- Status Cards -->
            <div class="status-cards">
                <div class="status-card">
                    <div class="icon blue">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="info">
                        <h3>Profile Status</h3>
                        <p>Active</p>
                    </div>
                </div>
                
                <div class="status-card">
                    <div class="icon green">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info">
                        <h3>Member Since</h3>
                        <p><?= date('Y') ?></p>
                    </div>
                </div>
                
                <div class="status-card">
                    <div class="icon purple">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info">
                        <h3>Verification</h3>
                        <p>Verified</p>
                    </div>
                </div>
                
                <div class="status-card">
                    <div class="icon orange">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="info">
                        <h3>Rating</h3>
                        <p>5.0</p>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="content-grid">
                <!-- Profile Information -->
                <div class="profile-card">
                    <h2>Profile Information</h2>
                    <div class="profile-info">
                        <div class="info-row">
                            <i class="fas fa-user"></i>
                            <div class="label">
                                <span>Full Name</span>
                                <p><?= htmlspecialchars($full_name) ?></p>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-envelope"></i>
                            <div class="label">
                                <span>Email Address</span>
                                <p><?= htmlspecialchars($email) ?></p>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-at"></i>
                            <div class="label">
                                <span>Username</span>
                                <p><?= htmlspecialchars($username) ?></p>
                            </div>
                        </div>
                        
                        <div class="info-row">
                            <i class="fas fa-birthday-cake"></i>
                            <div class="label">
                                <span>Age</span>
                                <p>20 years old</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Card -->
                <div class="user-card">
                    <div class="