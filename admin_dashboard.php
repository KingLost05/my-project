// admin_dashboard.php
function admin_dashboard($conn) {
    // total_students = DATABASE.count("students")
    $res1 = mysqli_query($conn, "SELECT COUNT(*) as total FROM students");
    $total_students = mysqli_fetch_assoc($res1)['total'];

    // total_teachers = DATABASE.count("teachers")
    $res2 = mysqli_query($conn, "SELECT COUNT(*) as total FROM teachers");
    $total_teachers = mysqli_fetch_assoc($res2)['total'];

    // total_courses = DATABASE.count("subjects")
    $res3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM subjects");
    $total_courses = mysqli_fetch_assoc($res3)['total'];

    return [
        "students" => $total_students,
        "teachers" => $total_teachers,
        "courses" => $total_courses
    ];
}