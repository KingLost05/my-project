<?php
include 'config.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    die("
    <!DOCTYPE html>
    <html lang='th'>
    <head>
        <meta charset='UTF-8'>
        <link rel='stylesheet' href='style.css'>
    </head>
    <body>
        <div class='container' style='text-align: center; padding: var(--spacing-2xl);'>
            <div style='font-size: 5rem; margin-bottom: var(--spacing-lg);'>🔒</div>
            <h2>หน้านี้สำหรับครูเท่านั้น</h2>
            <p>คุณไม่มีสิทธิ์เข้าถึงหน้านี้</p>
            <a href='admin_menu.php' class='btn-primary'>กลับหน้าหลัก</a>
        </div>
    </body>
    </html>
    ");
}

$sql = "SELECT 
            s.id AS submission_id,
            u.name AS student_name,
            a.title AS task_title,
            a.type AS task_type,
            a.attachment_link AS question_file,
            s.file_link AS student_file,
            s.submitted_at,
            subj.name AS subject_name
        FROM submissions s
        JOIN students st ON s.student_id = st.id
        JOIN users u ON st.user_id = u.id
        JOIN assignments a ON s.assignment_id = a.id
        JOIN subjects subj ON a.subject_id = subj.id
        ORDER BY s.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจงาน - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--burgundy-primary) 0%, var(--burgundy-dark) 100%);
            color: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            text-align: center;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            font-family: var(--font-display);
            line-height: 1;
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ ตรวจรายการส่งงาน</h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <div style="background: var(--burgundy-ultra-light); padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-xl);">
            <p style="margin: 0;">👨‍🏫 <strong>ครูผู้ตรวจ:</strong> <?php echo $_SESSION['name']; ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo mysqli_num_rows($result); ?></div>
                <div class="stat-label">งานที่ส่งทั้งหมด</div>
            </div>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>วัน-เวลาที่ส่ง</th>
                        <th>นักเรียน</th>
                        <th>วิชา</th>
                        <th>หัวข้อ</th>
                        <th>ประเภท</th>
                        <th style="text-align: center;">ไฟล์โจทย์</th>
                        <th style="text-align: center;">ไฟล์งานนักเรียน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['submitted_at'])); ?></td>
                        <td><strong><?php echo $row['student_name']; ?></strong></td>
                        <td><?php echo $row['subject_name']; ?></td>
                        <td><?php echo $row['task_title']; ?></td>
                        <td>
                            <span class="badge <?php echo ($row['task_type'] == 'exam') ? 'bg-exam' : 'bg-homework'; ?>">
                                <?php echo ($row['task_type'] == 'exam') ? '📝 ข้อสอบ' : '📖 การบ้าน'; ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if($row['question_file']): ?>
                                <a href="<?php echo $row['question_file']; ?>" target="_blank" class="btn-question">📄 โจทย์</a>
                            <?php else: ?>
                                <span style="color: var(--gray-400);">-</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <a href="<?php echo $row['student_file']; ?>" target="_blank" class="btn-view">🔍 เปิดตรวจ</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: var(--spacing-2xl); color: var(--gray-600);">
                <div style="font-size: 5rem; margin-bottom: var(--spacing-lg);">📭</div>
                <h3>ยังไม่มีงานที่ส่งในขณะนี้</h3>
                <p>เมื่อนักเรียนส่งงาน จะแสดงที่นี่</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>