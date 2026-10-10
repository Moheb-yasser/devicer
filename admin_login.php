<?php
session_start();
$error_message = '';

// Hardcoded admin credentials (no database needed)
$admins = [
    'moheb' => 'ghost',
    'hussein' => 'yoss',
    'mamdouh' => 'mydo',
    'samy' => 'smsm',
    'admin' => 'admin123'  // Add a default admin
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Check if username exists and password matches
    if (isset($admins[$username]) && $admins[$username] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error_message = 'Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <title>Admins Login</title>
    <style>
        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff6b6b;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid rgba(255, 0, 0, 0.3);
        }
        .logo img {
            max-width: 200px;
            max-height: 100px;
            width: auto;          
            height: auto;          
            display: block;        
            margin: 0 auto;        
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <form action="" method="POST">
            <div class="logo">
                <a href="index.html">
                    <img src="icons/Picsart_25-12-15_00-18-40-374.png" alt="Devicer Logo">
                </a>
            </div>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="input-box">
                <input type="text" name="username" placeholder="Admin name" required>
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required>
                <i class="fa-solid fa-key"></i>
            </div>

            <div class="rmmbr-frgt">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <div class="register">
                <p>Don't have an account? <a href="#">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>