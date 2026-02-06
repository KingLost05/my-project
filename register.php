<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        $error_message = "Email นี้มีในระบบแล้ว กรุณาใช้อีเมลอื่น";
    } else {
        $sql_user = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql_user)) {
            $user_id = mysqli_insert_id($conn);
            
            if ($role == 'student') {
                mysqli_query($conn, "INSERT INTO students (user_id, class_level) VALUES ('$user_id', 'Unassigned')");
            } else if ($role == 'teacher') {
                mysqli_query($conn, "INSERT INTO teachers (user_id, department) VALUES ('$user_id', 'General')");
            }
            
            $success_message = "ลงทะเบียนสำเร็จ! กำลังพาคุณไปยังหน้าเข้าสู่ระบบ...";
            header("refresh:2;url=login.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .register-container {
            max-width: 600px;
            margin: 5vh auto;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: var(--spacing-2xl);
        }
        
        .register-header h1 {
            font-size: 2.5rem;
            margin-bottom: var(--spacing-sm);
        }
        
        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        
        .role-option {
            position: relative;
        }
        
        .role-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .role-option label {
            display: block;
            padding: var(--spacing-lg);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-base);
            font-weight: 600;
        }
        
        .role-option input[type="radio"]:checked + label {
            background: linear-gradient(135deg, var(--burgundy-primary) 0%, var(--burgundy-dark) 100%);
            color: var(--white);
            border-color: var(--burgundy-primary);
            box-shadow: var(--shadow-lg);
        }
        
        .role-option label:hover {
            border-color: var(--burgundy-primary);
            transform: translateY(-2px);
        }
        
        .role-icon {
            font-size: 2rem;
            display: block;
            margin-bottom: var(--spacing-sm);
        }
        
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border-left: 4px solid var(--success);
            animation: slideDown 0.5s;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .register-footer {
            text-align: center;
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-xl);
            border-top: 1px solid var(--gray-200);
        }
    </style>
</head>
<body>
    <div class="register-container container">
        <div class="register-header">
            <h1>สมัครสมาชิก</h1>
            <p>เริ่มต้นการเรียนรู้กับเรา</p>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                ⚠️ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success_message)): ?>
            <div class="success-message">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="name">👤 ชื่อ-นามสกุล</label>
                <input type="text" id="name" name="name" placeholder="กรอกชื่อ-นามสกุล" required>
            </div>
            
            <div class="form-group">
                <label for="email">📧 อีเมล</label>
                <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
            </div>
            
            <div class="form-group">
                <label for="password">🔒 รหัสผ่าน</label>
                <input type="password" id="password" name="password" placeholder="สร้างรหัสผ่าน" required>
            </div>
            
            <div class="form-group">
                <label>เลือกบทบาท</label>
                <div class="role-selector">
                    <div class="role-option">
                        <input type="radio" id="student" name="role" value="student" checked>
                        <label for="student">
                            <span class="role-icon">🎓</span>
                            นักเรียน
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="teacher" name="role" value="teacher">
                        <label for="teacher">
                            <span class="role-icon">👨‍🏫</span>
                            ครู
                        </label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="admin" name="role" value="admin">
                        <label for="admin">
                            <span class="role-icon">⚙️</span>
                            ผู้ดูแล
                        </label>
                    </div>
                </div>
            </div>
            
            <button type="submit" name="register" class="btn-primary" style="width: 100%;">สมัครสมาชิก</button>
            
            <div class="register-footer">
                <p>มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบที่นี่</a></p>
            </div>
        </form>
    </div>
</body>
</html>