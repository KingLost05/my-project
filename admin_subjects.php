<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

if ($role == 'teacher' && isset($_POST['add_subject'])) {
    $name = mysqli_real_escape_string($conn, $_POST['subject_name']);
    mysqli_query($conn, "INSERT INTO subjects (name) VALUES ('$name')");
    $success_message = "สร้างหลักสูตรสำเร็จ!";
}

$result = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายวิชา - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: var(--spacing-lg);
            margin: var(--spacing-xl) 0;
        }
        
        .subject-card {
            background: linear-gradient(135deg, var(--white) 0%, var(--off-white) 100%);
            border: 2px solid var(--burgundy-ultra-light);
            border-radius: var(--radius-xl);
            padding: var(--spacing-xl);
            text-align: center;
            transition: all var(--transition-base);
            position: relative;
            overflow: hidden;
        }
        
        .subject-card::before {
            content: '📚';
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 6rem;
            opacity: 0.1;
        }
        
        .subject-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--burgundy-primary);
        }
        
        .subject-card h3 {
            margin: 0;
            color: var(--burgundy-primary);
            font-size: 1.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 รายวิชาทั้งหมด</h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <div class="subjects-grid">
            <?php 
            $count = 0;
            while($row = mysqli_fetch_assoc($result)): 
                $count++;
            ?>
                <div class="subject-card">
                    <h3><?php echo $row['name']; ?></h3>
                </div>
            <?php endwhile; ?>
            
            <?php if ($count == 0): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: var(--spacing-2xl); color: var(--gray-600);">
                    <p style="font-size: 1.25rem;">📚 ยังไม่มีรายวิชาในระบบ</p>
                    <?php if ($role == 'teacher'): ?>
                        <p>กรุณาเพิ่มรายวิชาด้านล่าง</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($role == 'teacher'): ?>
            <div style="margin-top: var(--spacing-2xl); padding-top: var(--spacing-2xl); border-top: 2px solid var(--burgundy-ultra-light);">
                <h2>➕ สร้างหลักสูตรใหม่</h2>
                <p style="color: var(--gray-600); margin-bottom: var(--spacing-lg);">เฉพาะครูเท่านั้นที่สามารถเพิ่มรายวิชาได้</p>
                
                <form method="post">
                    <div class="form-group">
                        <label for="subject_name">ชื่อวิชา</label>
                        <input type="text" id="subject_name" name="subject_name" placeholder="ระบุชื่อรายวิชา" required>
                    </div>
                    <button type="submit" name="add_subject" class="btn-primary">💾 บันทึกรายวิชา</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>