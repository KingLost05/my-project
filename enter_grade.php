<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($role == 'teacher') {
    if (isset($_POST['submit_grade'])) {
        $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
        $sub_id = mysqli_real_escape_string($conn, $_POST['subject_id']);
        $score = mysqli_real_escape_string($conn, $_POST['score']);

        $check_sql = "SELECT id FROM grades WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        $check_res = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_res) > 0) {
            $sql = "UPDATE grades SET score = '$score' WHERE student_id = '$student_id' AND subject_id = '$sub_id'";
        } else {
            $sql = "INSERT INTO grades (student_id, subject_id, score) VALUES ('$student_id', '$sub_id', '$score')";
        }

        if (mysqli_query($conn, $sql)) {
            $success_message = "บันทึกคะแนนเรียบร้อยแล้ว";
        }
    }

    $students_list = mysqli_query($conn, "SELECT s.id, u.name FROM students s JOIN users u ON s.user_id = u.id ORDER BY u.name");
    $subjects_list = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการเกรด - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .grade-card {
            background: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            margin-bottom: var(--spacing-xl);
        }
        
        .grade-pass {
            color: var(--success);
            font-weight: 700;
        }
        
        .grade-fail {
            color: var(--error);
            font-weight: 700;
        }
        
        .score-display {
            font-size: 2rem;
            font-weight: 800;
            font-family: var(--font-display);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo ($role == 'teacher') ? '👨‍🏫 กรอกคะแนนนักเรียน' : '🎓 ผลการเรียนของคุณ'; ?></h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($role == 'teacher'): ?>
            <div class="grade-card">
                <h2>บันทึกคะแนน</h2>
                <form method="post">
                    <div class="form-group">
                        <label for="student_id">เลือกนักเรียน</label>
                        <select id="student_id" name="student_id" required>
                            <option value="">-- เลือกรายชื่อนักเรียน --</option>
                            <?php while($row = mysqli_fetch_assoc($students_list)): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject_id">เลือกรายวิชา</label>
                        <select id="subject_id" name="subject_id" required>
                            <option value="">-- เลือกรายวิชา --</option>
                            <?php while($row = mysqli_fetch_assoc($subjects_list)): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="score">คะแนน (0-100)</label>
                        <input type="number" id="score" name="score" min="0" max="100" step="0.01" placeholder="กรอกคะแนน" required>
                    </div>

                    <button type="submit" name="submit_grade" class="btn-primary">💾 บันทึกคะแนน</button>
                </form>
            </div>

        <?php else: ?>
            <h2>ผลการเรียนของ: <?php echo $_SESSION['name']; ?></h2>
            
            <table>
                <thead>
                    <tr>
                        <th>รายวิชา</th>
                        <th style="text-align: center;">คะแนนที่ได้</th>
                        <th style="text-align: center;">การประเมิน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT s.name as subject_name, g.score 
                            FROM grades g 
                            JOIN subjects s ON g.subject_id = s.id 
                            WHERE g.student_id = (SELECT id FROM students WHERE user_id = '$user_id')
                            ORDER BY s.name";
                    $res = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($res) > 0) {
                        $total_score = 0;
                        $count = 0;
                        while($row = mysqli_fetch_assoc($res)) {
                            $score = $row['score'];
                            $total_score += $score;
                            $count++;
                            $grade = ($score >= 50) ? "<span class='grade-pass'>✅ ผ่าน</span>" : "<span class='grade-fail'>❌ ไม่ผ่าน</span>";
                            echo "<tr>
                                    <td><strong>{$row['subject_name']}</strong></td>
                                    <td style='text-align: center;'><span class='score-display'>{$score}</span></td>
                                    <td style='text-align: center;'>{$grade}</td>
                                  </tr>";
                        }
                        $average = $total_score / $count;
                        echo "<tr style='background: var(--burgundy-ultra-light); font-weight: 700;'>
                                <td>เฉลี่ย</td>
                                <td colspan='2' style='text-align: center;'><span class='score-display'>" . number_format($average, 2) . "</span></td>
                              </tr>";
                    } else {
                        echo "<tr><td colspan='3' style='text-align:center; padding: var(--spacing-2xl); color: var(--gray-600);'>
                                <div style='font-size: 3rem; margin-bottom: var(--spacing-md);'>📊</div>
                                ยังไม่มีข้อมูลคะแนน
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>