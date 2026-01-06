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

// ===================================================
// receive data 
// ===================================================
$userID     = $_SESSION['userID'];
$item_name  = $_POST['item_name'];
$category   = $_POST['category'];
$location   = $_POST['location'];
$reportDate = $_POST['reportDate'];   // <input type="date" name="reportDate">
$details    = $_POST['detail'];       // <textarea name="detail"></textarea>

// ===================================================
// ---------------- IMAGE UPLOAD ----------------
// ===================================================
$picture = null;
if (!empty($_FILES['picture']['name'])) {

    $picture = time() . "_" . basename($_FILES['picture']['name']);

    // [ADDED] safety check
    if (!move_uploaded_file(
        $_FILES['picture']['tmp_name'],
        "uploads/" . $picture
    )) {
        die("Image upload failed");
    }
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

// [ADDED] check insert item
if (!$stmtItem->execute()) {
    die("Item insert error: " . $stmtItem->error);
}

// itemID that already insert
$itemID = $stmtItem->insert_id;

// ===================================================
// 2) INSERT INTO report
// ===================================================
// reportType has DEFAULT 'reported'

$sqlReport = "INSERT INTO report
    (userID, itemID, reportDate, location, picture, detail)
    VALUES (?, ?, ?, ?, ?, ?)";

$stmtReport = $conn->prepare($sqlReport);
$stmtReport->bind_param(
    "iissss",
    $userID,
    $itemID,
    $reportDate,
    $location,
    $picture,
    $details
);

if ($stmtReport->execute()) {
    // come back to homepage if successful
    header("Location: home.php?report=success");
    exit();
} else {
    echo "Error: " . $stmtReport->error;
}
?>
