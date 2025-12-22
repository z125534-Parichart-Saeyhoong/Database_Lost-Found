<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ===============================
// 1. connect to the database
// ===============================
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "LostandFound";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// ===============================
// 2. receive data from signup
// ===============================
$email    = $_POST['email'];
$username = $_POST['username'];
$tel      = $_POST['phone'];  
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];


// ===============================
// 3. check password
// ===============================
if ($password !== $confirm_password) {
    die("❌ Password and Confirm Password do not match.");
}


// ===============================
// 4. enter the password
// ===============================
$hashed_password = password_hash($password, PASSWORD_DEFAULT);


// ===============================
// 5. check the duplicate (email / username / tel)
// ===============================
$sql_check = "SELECT * FROM User WHERE email = ? OR username = ? OR tel = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("sss", $email, $username, $tel);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("❌ Email, Username or Phone already exists.");
}


// ===============================
// 6. INSERT data
// ===============================
$sql_insert = "INSERT INTO User (email, username, tel, password)
               VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ssss", $email, $username, $tel, $hashed_password);

if ($stmt->execute()) {
    header("Location: /lostandfound/home.php");
    exit();
} else {
    echo "❌ Error: " . $stmt->error;
}


// ===============================
// 7. close connecntion
// ===============================
$conn->close();
?>
