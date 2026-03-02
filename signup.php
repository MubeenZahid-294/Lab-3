<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_system";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $user_username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    if (empty($full_name) || empty($email) || empty($user_username) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
            $stmt->execute(['email' => $email, 'username' => $user_username]);

            if ($stmt->rowCount() > 0) {
                $message = "Email or Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (full_name, username, email, password) VALUES (:full_name, :username, :email, :password)";
                $stmt = $conn->prepare($sql);
                
                if ($stmt->execute(['full_name' => $full_name, 'username' => $user_username, 'email' => $email, 'password' => $hashed_password])) {
                    $message = "success|Account created successfully! <a href='login.php'>Login here</a>";
                } else {
                    $message = "Something went wrong. Please try again.";
                }
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
    <title>Sign Up</title>
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

        .signup-container {
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

        .btn-signup {
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

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 210, 211, 0.5);
            background: linear-gradient(45deg, #00d2d3, #00a8a8);
        }

        .btn-signup:active {
            transform: scale(0.98);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .login-link a:hover {
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
        .success { 
            background: rgba(0, 210, 211, 0.15); 
            color: #00d2d3; 
            border: 1px solid rgba(0, 210, 211, 0.3); 
        }
        .success a {
            color: #fff;
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <h2>Create Account</h2>
        
        <?php if (!empty($message)): ?>
            <?php 
                $msgClass = (strpos($message, 'success') !== false) ? 'success' : 'error';
                $msgText = str_replace('success|', '', $message); 
            ?>
            <div class="status-msg <?= $msgClass ?>">
                <?= $msgText ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="John Doe" required>
            </div>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="johndoe123" required>
            </div>

            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="name@example.com" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-signup">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

</body>
</html>