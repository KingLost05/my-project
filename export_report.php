<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=report_grades_'.date('Y-m-d').'.csv');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, array('Student Name', 'Subject', 'Score'));

    $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
            FROM grades g
            JOIN students s ON g.student_id = s.id
            JOIN users u ON s.user_id = u.id
            JOIN subjects sub ON g.subject_id = sub.id
            ORDER BY u.name, sub.name";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงาน - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .export-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-2xl);
            flex-wrap: wrap;
        }
        
        .export-btn {
            flex: 1;
            min-width: 200px;
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, var(--success) 0%, #218838 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-base);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--spacing-sm);
            box-shadow: var(--shadow-md);
        }
        
        .export-btn:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }
        
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }
        
        .stat-box {
            background: linear-gradient(135deg, var(--burgundy-primary) 0%, var(--burgundy-dark) 100%);
            color: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            text-align: center;
        }
        
        .stat-box .number {
            font-size: 3rem;
            font-weight: 800;
            font-family: var(--font-display);
            line-height: 1;
            margin-bottom: var(--spacing-sm);
        }
        
        .stat-box .label {
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
            <h1>📊 รายงานสรุปผลการเรียน</h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <?php
        // Calculate statistics
        $total_students = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT student_id) as total FROM grades"))['total'];
        $total_subjects = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT subject_id) as total FROM grades"))['total'];
        $avg_score = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(score) as avg FROM grades"))['avg'];
        ?>

        <div class="stats-summary">
            <div class="stat-box">
                <div class="number"><?php echo $total_students; ?></div>
                <div class="label">นักเรียนทั้งหมด</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo $total_subjects; ?></div>
                <div class="label">รายวิชาทั้งหมด</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo number_format($avg_score, 2); ?></div>
                <div class="label">คะแนนเฉลี่ย</div>
            </div>
        </div>

        <div class="export-actions">
            <a href="?export=csv" class="export-btn">
                <span style="font-size: 1.5rem;">📥</span>
                ดาวน์โหลดเป็น Excel (CSV)
            </a>
        </div>

        <h2>รายงานแบบละเอียด</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ชื่อนักเรียน</th>
                    <th>รายวิชา</th>
                    <th style="text-align: center;">คะแนน</th>
                    <th style="text-align: center;">ผลการประเมิน</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT u.name as s_name, sub.name as sub_name, g.score 
                        FROM grades g
                        JOIN students s ON g.student_id = s.id
                        JOIN users u ON s.user_id = u.id
                        JOIN subjects sub ON g.subject_id = sub.id
                        ORDER BY u.name, sub.name";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $score = $row['score'];
                        $status = $score >= 50 ? 
                            "<span class='grade-pass'>✅ ผ่าน</span>" : 
                            "<span class='grade-fail'>❌ ไม่ผ่าน</span>";
                        
                        echo "<tr>
                                <td><strong>{$row['s_name']}</strong></td>
                                <td>{$row['sub_name']}</td>
                                <td style='text-align: center;'><span style='font-weight: 700; font-size: 1.125rem;'>{$score}</span></td>
                                <td style='text-align: center;'>{$status}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align: center; padding: var(--spacing-2xl); color: var(--gray-600);'>
                            <div style='font-size: 4rem; margin-bottom: var(--spacing-md);'>📊</div>
                            ยังไม่มีข้อมูลคะแนนในระบบ
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>