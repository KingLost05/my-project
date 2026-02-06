<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit();
}
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - Education Platform</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Education Platform</h1>
            <div class="user-info">
                <span><?php echo $_SESSION['name']; ?></span>
                <span class="role-badge"><?php 
                    if($role == 'student') echo '🎓 นักเรียน';
                    elseif($role == 'teacher') echo '👨‍🏫 ครู';
                    else echo '⚙️ ผู้ดูแล';
                ?></span>
            </div>
        </div>

        <h2>เมนูหลัก</h2>
        
        <ul class="nav-menu">
            <li>
                <a href="admin_subjects.php">
                    📚 รายวิชา/หลักสูตร
                </a>
            </li>
            
            <?php if ($role == 'teacher'): ?>
                <li>
                    <a href="enter_grade.php">
                        📝 บันทึกเกรดและประเมินผล
                    </a>
                </li>
                <li>
                    <a href="create_assignment.php">
                        ✍️ ออกข้อสอบ/สั่งการบ้าน
                    </a>
                </li>
                <li>
                    <a href="check_submissions.php">
                        ✅ ตรวจงาน/เช็คการส่งงาน
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="enter_grade.php">
                        📊 ดูผลการเรียน
                    </a>
                </li>
                <li>
                    <a href="submit_work.php">
                        📤 ดูข้อสอบและส่งการบ้าน
                    </a>
                </li>
            <?php endif; ?>
            
            <li>
                <a href="export_report.php">
                    📈 Report / Export ข้อมูล
                </a>
            </li>
            
            <li>
                <a href="view_schedule.php">
                    📅 ตารางเรียน
                </a>
            </li>
        </ul>

        <div style="text-align: center; margin-top: var(--spacing-2xl); padding-top: var(--spacing-xl); border-top: 2px solid var(--burgundy-ultra-light);">
            <a href="logout.php" style="color: var(--error); font-weight: bold; font-size: 1.125rem;">
                🚪 ออกจากระบบ
            </a>
        </div>
    </div>
</body>
</html>