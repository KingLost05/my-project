<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['create'])) {
    $subject_id = $_POST['subject_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $type = $_POST['type'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    
    $attachment = "";
    if (!empty($_FILES["attachment"]["name"])) {
        $target_dir = "uploads/questions/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $attachment = $target_dir . time() . "_" . basename($_FILES["attachment"]["name"]);
        move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment);
    }

    $sql = "INSERT INTO assignments (subject_id, title, type, description, attachment_link, due_date) 
            VALUES ('$subject_id', '$title', '$type', '$description', '$attachment', '$due_date')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = "ประกาศงานสำเร็จ!";
    }
}

$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name");
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สร้างงาน - Education Platform</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .type-selector {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        
        .type-option {
            flex: 1;
            position: relative;
        }
        
        .type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .type-option label {
            display: block;
            padding: var(--spacing-lg);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-lg);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-base);
            font-weight: 600;
        }
        
        .type-option input[type="radio"]:checked + label {
            background: linear-gradient(135deg, var(--burgundy-primary) 0%, var(--burgundy-dark) 100%);
            color: var(--white);
            border-color: var(--burgundy-primary);
            box-shadow: var(--shadow-lg);
        }
        
        .type-icon {
            font-size: 2.5rem;
            display: block;
            margin-bottom: var(--spacing-sm);
        }
        
        .file-upload-wrapper {
            position: relative;
            padding: var(--spacing-xl);
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-lg);
            text-align: center;
            transition: all var(--transition-base);
            cursor: pointer;
        }
        
        .file-upload-wrapper:hover {
            border-color: var(--burgundy-primary);
            background: var(--burgundy-ultra-light);
        }
        
        .file-upload-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✍️ สร้างข้อสอบ / การบ้าน</h1>
            <a href="admin_menu.php" class="btn-secondary">← กลับหน้าหลัก</a>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="subject_id">📚 เลือกวิชา</label>
                <select id="subject_id" name="subject_id" required>
                    <option value="">-- เลือกรายวิชา --</option>
                    <?php while($s = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">📝 หัวข้อ</label>
                <input type="text" id="title" name="title" placeholder="ระบุหัวข้องาน" required>
            </div>

            <div class="form-group">
                <label>ประเภทงาน</label>
                <div class="type-selector">
                    <div class="type-option">
                        <input type="radio" id="homework" name="type" value="homework" checked>
                        <label for="homework">
                            <span class="type-icon">📖</span>
                            การบ้าน
                        </label>
                    </div>
                    <div class="type-option">
                        <input type="radio" id="exam" name="type" value="exam">
                        <label for="exam">
                            <span class="type-icon">📝</span>
                            ข้อสอบ
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">📄 รายละเอียด</label>
                <textarea id="description" name="description" rows="5" placeholder="อธิบายรายละเอียดงาน..."></textarea>
            </div>

            <div class="form-group">
                <label>📎 ไฟล์โจทย์ (ถ้ามี)</label>
                <div class="file-upload-wrapper">
                    <div style="font-size: 3rem; margin-bottom: var(--spacing-sm);">📁</div>
                    <div>คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</div>
                    <input type="file" name="attachment">
                </div>
            </div>

            <div class="form-group">
                <label for="due_date">📅 กำหนดส่งวันที่</label>
                <input type="date" id="due_date" name="due_date" required>
            </div>

            <button type="submit" name="create" class="btn-primary" style="width: 100%;">
                🚀 ประกาศงาน
            </button>
        </form>
    </div>
</body>
</html>