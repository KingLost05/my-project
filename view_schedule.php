<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

$sql = "SELECT s.*, subj.name as subject_name FROM schedules s 
        JOIN subjects subj ON s.subject_id = subj.id 
        WHERE s.type = 'class' ORDER BY s.day_of_week, s.start_time";

$result = mysqli_query($conn, $sql);

// Group by day
$days = ['Monday' => 'จันทร์', 'Tuesday' => 'อังคาร', 'Wednesday' => 'พุธ', 
         'Thursday' => 'พฤหัสบดี', 'Friday' => 'ศุกร์', 'Saturday' => 'เสาร์', 'Sunday' => 'อาทิตย์'];
$schedule_by_day = [];

while($row = mysqli_fetch_assoc($result)) {
    $day = $row['day_of_week'];
    if (!isset($schedule_by_day[$day])) {
        $schedule_by_day[$day] = [];
    }
    $schedule_by_day[$day][] = $row;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางเรียน - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .schedule-grid {
            display: grid;
            gap: var(--spacing-lg);
        }
        
        .day-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            border: 2px solid var(--burgundy-ultra-light);
        }
        
        .day-header {
            background: linear-gradient(135deg, var(--burgundy-primary) 0%, var(--burgundy-dark) 100%);
            color: var(--white);
            padding: var(--spacing-lg);
            font-size: 1.25rem;
            font-weight: 700;
            font-family: var(--font-display);
        }
        
        .classes-list {
            padding: var(--spacing-lg);
        }
        
        .class-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            padding: var(--spacing-md);
            background: var(--burgundy-ultra-light);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            transition: all var(--transition-base);
        }
        
        .class-item:last-child {
            margin-bottom: 0;
        }
        
        .class-item:hover {
            background: var(--cream);
            transform: translateX(4px);
        }
        
        .class-time {
            font-weight: 700;
            color: var(--burgundy-primary);
            min-width: 120px;
            font-family: var(--font-display);
        }
        
        .class-subject {
            flex: 1;
            font-weight: 600;
            color: var(--gray-700);
        }
        
        .class-room {
            color: var(--gray-600);
            font-size: 0.875rem;
            background: var(--white);
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
        }
        
        .no-classes {
            text-align: center;
            padding: var(--spacing-xl);
            color: var(--gray-500);
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 ตารางเรียน</h1>
            <div>
                <span class="role-badge"><?php 
                    if($role == 'student') echo '🎓 นักเรียน';
                    elseif($role == 'teacher') echo '👨‍🏫 ครู';
                    else echo '⚙️ ผู้ดูแล';
                ?></span>
                <a href="admin_menu.php" class="btn-secondary" style="margin-left: var(--spacing-md);">← กลับหน้าหลัก</a>
            </div>
        </div>

        <?php if (empty($schedule_by_day)): ?>
            <div style="text-align: center; padding: var(--spacing-2xl); color: var(--gray-600);">
                <div style="font-size: 5rem; margin-bottom: var(--spacing-lg);">📅</div>
                <h3>ยังไม่มีตารางเรียนในระบบ</h3>
                <p>กรุณาติดต่อผู้ดูแลระบบเพื่อเพิ่มตารางเรียน</p>
            </div>
        <?php else: ?>
            <div class="schedule-grid">
                <?php foreach ($days as $day_en => $day_th): ?>
                    <?php if (isset($schedule_by_day[$day_en])): ?>
                        <div class="day-card">
                            <div class="day-header">
                                <?php echo $day_th; ?>
                            </div>
                            <div class="classes-list">
                                <?php foreach ($schedule_by_day[$day_en] as $class): ?>
                                    <div class="class-item">
                                        <div class="class-time">
                                            🕐 <?php echo substr($class['start_time'], 0, 5); ?> - <?php echo substr($class['end_time'], 0, 5); ?>
                                        </div>
                                        <div class="class-subject">
                                            📚 <?php echo $class['subject_name']; ?>
                                        </div>
                                        <div class="class-room">
                                            🚪 ห้อง <?php echo $class['room']; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>