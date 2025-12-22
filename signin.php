<?php
session_start();

// connect database
$conn = new mysqli("localhost", "root", "", "LostandFound");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// receive value from signin form
$username = $_POST['username'];
$password = $_POST['password'];

// find user
$sql = "SELECT * FROM User WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// check user
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // check password (IMPORTANT)
    if (password_verify($password, $user['password'])) {

        // login success → save session
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];

        header("Location: home.php");
        exit();
    } else {
        echo "❌ Wrong password";
    }
} else {
    echo "❌ User not found";
}
?>
