<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// prevent open directly
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: home.php");
    exit();
}

// check login
if (!isset($_SESSION['userID'])) {
    header("Location: home.php");
    exit();
}

// connect database
$conn = new mysqli("localhost", "root", "", "LostandFound");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// receive data 
$userID    = $_SESSION['userID'];
$item_name = $_POST['item_name'];
$category  = $_POST['category'];
$location  = $_POST['location'];
$date_lost = $_POST['date_lost'];
$details   = $_POST['details'];


// ---------------- IMAGE UPLOAD ----------------
$image_path = null;
if (!empty($_FILES['item_image']['name'])) {


    $image_path = time() . "_" . basename($_FILES['item_image']['name']);

    move_uploaded_file(
        $_FILES['item_image']['tmp_name'],
        "uploads/" . $image_path
    );
}

// ===================================================
// 1) INSERT INTO item
// ===================================================
$status = "reported"; // default status (admin will be fix later)

$sqlItem = "INSERT INTO item (userID, name, category, status)
            VALUES (?, ?, ?, ?)";

$stmtItem = $conn->prepare($sqlItem);
$stmtItem->bind_param(
    "isss",
    $userID,
    $item_name,
    $category,
    $status
);

$stmtItem->execute();

// ดึง itemID ที่เพิ่ง insert
$itemID = $stmtItem->insert_id;

// ===================================================
// 2) INSERT INTO report
// ===================================================
$sqlReport = "INSERT INTO report
    (userID, itemID, location, date_lost, details, image_path)
    VALUES (?, ?, ?, ?, ?, ?)";

$stmtReport = $conn->prepare($sqlReport);
$stmtReport->bind_param(
    "iissss",
    $userID,
    $itemID,
    $location,
    $date_lost,
    $details,
    $image_path
);

if ($stmtReport->execute()) {
    // come back to homepage if successful
    header("Location: home.php?report=success");
    exit();
} else {
    echo "Error: " . $stmtReport->error;
}
