// get_timetable.php
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

if ($role == "student") {
    // ตามซูโดโค้ด: RETURN DATABASE.query_student_timetable
    $sql = "SELECT * FROM timetable WHERE class_id = (SELECT class_level FROM students WHERE user_id = $user_id)";
} else if ($role == "teacher") {
    // ตามซูโดโค้ด: RETURN DATABASE.query_teacher_timetable
    $sql = "SELECT * FROM timetable t 
            JOIN subjects s ON t.subject_id = s.id 
            JOIN subject_teacher st ON s.id = st.subject_id 
            WHERE st.teacher_id = (SELECT id FROM teachers WHERE user_id = $user_id)";
}
$result = mysqli_query($conn, $sql);