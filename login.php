<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_username = htmlspecialchars(trim($_POST['email_or_username']));
    $password = $_POST['password'];

    if (empty($email_or_username) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Check if user exists (by email or username)
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email_or_username OR username = :email_or_username");
            $stmt->execute(['email_or_username' => $email_or_username]);

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password correct, create session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $message = "Invalid password.";
                }
            } else {
                $message = "User not found.";
            }
        } catch (PDOException $e) {
            $message = "Connection failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
        }

        .login-container {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 40px rgba(0, 210, 211, 0.1);
            animation: fadeIn 0.8s ease;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            letter-spacing: 2px;
            color: var(--primary-color);
            text-transform: uppercase;
            text-shadow: 0 0 15px rgba(0, 210, 211, 0.5);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .input-group input {
            width: 100%;
            padding: 14px;
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input::placeholder {
            color: rgba(0, 210, 211, 0.3);
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 20px rgba(0, 210, 211, 0.25);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(45deg, #00a8a8, #00d2d3);
            border: none;
            border-radius: 8px;
            color: #001a1a;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 210, 211, 0.5);
            background: linear-gradient(45deg, #00d2d3, #00a8a8);
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .signup-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .signup-link a:hover {
            color: #fff;
            text-shadow: 0 0 10px var(--primary-color);
        }

        .status-msg {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }
        .error { 
            background: rgba(255, 0, 0, 0.15); 
            color: #ff6b6b; 
            border: 1px solid rgba(255, 0, 0, 0.3); 
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Welcome Back</h2>
        
        <?php if (!empty($message)): ?>
            <div class="status-msg error">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label for="email_or_username">Email or Username</label>
                <input type="text" id="email_or_username" name="email_or_username" placeholder="Enter email or username" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </div>
    </div>

</body>
</html>