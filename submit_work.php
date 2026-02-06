<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
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
            <h2>หน้านี้สำหรับนักเรียนเท่านั้น</h2>
            <a href='admin_menu.php' class='btn-primary'>กลับหน้าหลัก</a>
        </div>
    </body>
    </html>
    ");
}

// Handle file upload
if (isset($_POST['upload'])) {
    $assignment_id = $_POST['assignment_id'];
    $student_id_query = mysqli_query($conn, "SELECT id FROM students WHERE user_id = '{$_SESSION['user_id']}'");
    $student_data = mysqli_fetch_assoc($student_id_query);
    $student_id = $student_data['id'];
    
    $target_dir = "uploads/submissions/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_link = $target_dir . time() . "_" . basename($_FILES["fileToUpload"]["name"]);
    
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $file_link)) {
        $sql = "INSERT INTO submissions (student_id, assignment_id, file_link) VALUES ('$student_id', '$assignment_id', '$file_link')";
        if (mysqli_query($conn, $sql)) {
            $success_message = "ส่งงานสำเร็จ!";
        }
    }
}

$query = "SELECT a.*, s.name as subject_name FROM assignments a JOIN subjects s ON a.subject_id = s.id ORDER BY a.due_date ASC";
$assignments = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ส่งงาน - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .selected-assignment {
            background: var(--burgundy-ultra-light);
            padding: var(--spacing-lg);
            border-radius: var(--radius-lg);
            margin-bottom: var(--spacing-lg);
            border: 2px solid var(--burgundy-primary);
        }
        
        .upload-section {
            background: var(--white);
            padding: var(--spacing-xl);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
            margin-top: var(--spacing-2xl);
        }
        
        .file-upload-area {
            position: relative;
            padding: var(--spacing-2xl);
            border: 3px dashed var(--burgundy-primary);
            border-radius: var(--radius-xl);
            text-align: center;
            transition: all var(--transition-base);
            background: var(--burgundy-ultra-light);
            cursor: pointer;
        }
        
        .file-upload-area:hover {
            background: var(--cream);
            border-color: var(--burgundy-dark);
        }
        
        .file-upload-area input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            cursor: pointer;
        }
    </style>
    <script>
        function selectAssignment(id, title, subject) {
            document.getElementById('as_id').value = id;
            document.getElementById('selected-info').innerHTML = 
                '<strong>📌 งานที่เลือก:</strong> ' + subject + ' - ' + title;
            document.getElementById('selected-display').style.display = 'block';
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📤 ส่งงานและข้อสอบ</h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <h2>รายการงานที่ได้รับมอบหมาย</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ประเภท</th>
                    <th>วิชา</th>
                    <th>หัวข้อ</th>
                    <th style="text-align: center;">ไฟล์โจทย์</th>
                    <th>กำหนดส่ง</th>
                    <th style="text-align: center;">ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($assignments) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($assignments)): ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo ($row['type'] == 'exam') ? 'bg-exam' : 'bg-work'; ?>">
                                <?php echo ($row['type'] == 'exam') ? '📝 ข้อสอบ' : '📖 การบ้าน'; ?>
                            </span>
                        </td>
                        <td><strong><?php echo $row['subject_name']; ?></strong></td>
                        <td><?php echo $row['title']; ?></td>
                        <td style="text-align: center;">
                            <?php if($row['attachment_link']): ?>
                                <a href="<?php echo $row['attachment_link']; ?>" target="_blank" class="btn-question">📄 ดาวน์โหลด</a>
                            <?php else: ?>
                                <span style="color: var(--gray-400);">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($row['due_date'])); ?></td>
                        <td style="text-align: center;">
                            <button onclick="selectAssignment(<?php echo $row['id']; ?>, '<?php echo addslashes($row['title']); ?>', '<?php echo addslashes($row['subject_name']); ?>')" class="btn-primary">
                                เลือกส่งงานนี้
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: var(--spacing-2xl); color: var(--gray-600);">
                            <div style="font-size: 4rem; margin-bottom: var(--spacing-md);">📚</div>
                            <p>ยังไม่มีงานที่ได้รับมอบหมาย</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="upload-section">
            <h3>📎 ฟอร์มส่งไฟล์งาน</h3>
            
            <div id="selected-display" class="selected-assignment" style="display: none;">
                <p id="selected-info"></p>
            </div>
            
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" id="as_id" name="assignment_id" required>
                
                <div class="form-group">
                    <label>เลือกไฟล์คำตอบ</label>
                    <div class="file-upload-area">
                        <div style="font-size: 4rem; margin-bottom: var(--spacing-md);">📁</div>
                        <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: var(--spacing-sm);">
                            คลิกเพื่อเลือกไฟล์
                        </div>
                        <div style="color: var(--gray-600);">หรือลากไฟล์มาวางที่นี่</div>
                        <input type="file" name="fileToUpload" required>
                    </div>
                </div>
                
                <button type="submit" name="upload" class="btn-primary" style="width: 100%;">
                    🚀 ส่งงาน
                </button>
            </form>
        </div>
    </div>
</body>
</html>