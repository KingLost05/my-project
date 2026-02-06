// profile.php
function get_user_profile($conn, $user_id) {
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result); // RETURN user
}

// การเรียกใช้งาน
$user_data = get_user_profile($conn, $_SESSION['user_id']);
echo "ชื่อ: " . $user_data['name'];