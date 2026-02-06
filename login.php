<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, name, role, password FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && $password == $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        header("Location: admin_menu.php");
        exit();
    } else {
        $error_message = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Education Platform</title>
    <link rel="stylesheet" href="/ed_system/style.css">
    <style>
        .login-container {
            max-width: 480px;
            margin: 10vh auto;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }
        
        .login-header h1 {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .login-header p {
            color: var(--gray-600);
            font-size: 1.125rem;
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #991b1b;
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--error);
            animation: shake 0.5s;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .login-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--gray-200);
        }
        
        .welcome-icon {
            font-size: 4rem;
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }
    </style>
</head>
<body>
    <div class="login-container container">
        <div class="welcome-icon">🎓</div>
        <div class="login-header">
            <h1>ยินดีต้อนรับ🤟😔</h1>
            <p>เข้าสู่ระบบจัดการการศึกษา เดี๋ยวนี้</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                ⚠️ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="email">📧 อีเมล</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 รหัสผ่าน</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">เข้าสู่ระบบ</button>
            
            <div class="login-footer">
                <p>ยังไม่มีบัญชีใช่ไหม? <a href="register.php">สมัครสมาชิกที่นี่</a></p>
            </div>
        </form>
    </div>
</body>
</html>